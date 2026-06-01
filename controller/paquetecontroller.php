<?php
require_once __DIR__ . "/../models/paquete.php";
require_once __DIR__ . "/../models/pago.php";

class paquetecontroller
{
    private function ProcesarImagenPaquete()
    {
        if (!isset($_FILES['imagen_archivo']) || !is_array($_FILES['imagen_archivo'])) {
            return ['ok' => true, 'imagen' => ($_POST['imagen'] ?? 'paquete.jpg')];
        }

        $archivo = $_FILES['imagen_archivo'];

        if (($archivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return ['ok' => true, 'imagen' => ($_POST['imagen'] ?? 'paquete.jpg')];
        }

        if (($archivo['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'error' => 'no fue posible cargar la imagen del paquete'];
        }

        $extension = strtolower(pathinfo($archivo['name'] ?? '', PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (!in_array($extension, $permitidas, true)) {
            return ['ok' => false, 'error' => 'la imagen debe ser jpg, jpeg, png, webp o gif'];
        }

        $directorio = __DIR__ . "/../public/css/img/uploads";

        if (!is_dir($directorio) && !mkdir($directorio, 0777, true) && !is_dir($directorio)) {
            return ['ok' => false, 'error' => 'no fue posible crear la carpeta de imagenes'];
        }

        $nombre = 'paquete_' . uniqid() . '.' . $extension;
        $destino = $directorio . "/" . $nombre;

        if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
            return ['ok' => false, 'error' => 'no fue posible guardar la imagen del paquete'];
        }

        return ['ok' => true, 'imagen' => 'uploads/' . $nombre];
    }

    private function LeerComponentesPost()
    {
        $tipos = $_POST['componente_tipo'] ?? [];
        $titulos = $_POST['componente_titulo'] ?? [];
        $descripciones = $_POST['componente_descripcion'] ?? [];
        $habitaciones = $_POST['componente_habitacion'] ?? [];
        $precios = $_POST['componente_precio'] ?? [];
        $componentes = [];

        if (!is_array($titulos)) {
            return $componentes;
        }

        foreach ($titulos as $indice => $titulo) {
            if (trim((string) $titulo) === '') {
                continue;
            }

            $componentes[] = [
                'tipo' => $tipos[$indice] ?? 'otro',
                'titulo' => $titulo,
                'descripcion' => $descripciones[$indice] ?? '',
                'id_habitacion' => $habitaciones[$indice] ?? 0,
                'precio_componente' => $precios[$indice] ?? 0
            ];
        }

        return $componentes;
    }

    public function admin()
    {
        $paquete = new paquete();
        $datos = $paquete->GetTodosPaquetes();
        $servicios = $paquete->GetTodosServiciosAdicionales();
        $habitaciones = $paquete->GetHabitacionesParaSelector();
        $paquete_editar = null;
        $componentes_editar = [];
        $errores = [];

        if (isset($_GET['id'])) {
            $paquete_editar = $paquete->GetById($_GET['id']);
            $componentes_editar = $paquete->GetComponentes((int) $_GET['id']);
        }

        require_once __DIR__ . "/../views/admin/paquetes.php";
    }

    public function guardar()
    {
        $paquete = new paquete();
        $errores = [];

        if (!$_POST) {
            header("location: index.php?controller=paquete&action=admin");
            exit;
        }

        $imagen_resultado = $this->ProcesarImagenPaquete();

        if (!$imagen_resultado['ok']) {
            header("location: index.php?controller=paquete&action=admin&msg=" . urlencode($imagen_resultado['error']));
            exit;
        }

        $componentes = $this->LeerComponentesPost();
        $id_editar = (int) ($_POST['id_paquete'] ?? 0);

        if ($id_editar > 0) {
            $resultado = $paquete->ActualizarPaquete(
                $id_editar,
                $_POST['nombre'] ?? '',
                $_POST['descripcion'] ?? '',
                $_POST['precio_base'] ?? 0,
                $_POST['dias'] ?? 1,
                $imagen_resultado['imagen'],
                $_POST['estado'] ?? 'activo',
                $componentes
            );
        } else {
            $resultado = $paquete->GuardarPaquete(
                $_POST['nombre'] ?? '',
                $_POST['descripcion'] ?? '',
                $_POST['precio_base'] ?? 0,
                $_POST['dias'] ?? 1,
                $imagen_resultado['imagen'],
                $_POST['estado'] ?? 'activo',
                $componentes
            );
        }

        if (is_array($resultado)) {
            header("location: index.php?controller=paquete&action=admin&msg=" . urlencode(implode(' | ', $resultado)));
            exit;
        }

        header("location: index.php?controller=paquete&action=admin&msg=paquete guardado");
        exit;
    }

    public function guardar_servicio()
    {
        $paquete = new paquete();

        if ($_POST) {
            $paquete->GuardarServicioAdicional(
                $_POST['nombre_servicio'] ?? '',
                $_POST['precio_servicio'] ?? 0,
                isset($_POST['activo_servicio'])
            );
        }

        header("location: index.php?controller=paquete&action=admin&msg=servicio adicional guardado");
        exit;
    }

    public function catalogo()
    {
        $paquete = new paquete();
        $datos = $paquete->GetPaquetesActivos();

        require_once __DIR__ . "/../views/paquete/catalogo.php";
    }

    public function detalle()
    {
        $paquete = new paquete();
        $id = (int) ($_GET['id'] ?? 0);
        $datos = $paquete->GetById($id);
        $componentes = [];
        $servicios = $paquete->GetServiciosAdicionalesActivos();
        $precio_paquete = 0;
        $errores = [];

        if ($datos) {
            $componentes = $paquete->GetComponentes($id);
            $precio_paquete = $paquete->CalcularPrecioPaquete($id);
        }

        if ($_POST && $datos) {
            $ids_servicios = $_POST['servicios_extra'] ?? [];
            if (!is_array($ids_servicios)) {
                $ids_servicios = [];
            }

            $reserva = $paquete->ReservarPaquete(
                $id,
                $_SESSION['Id_user'] ?? 0,
                $_POST['fecha_inicio'] ?? '',
                $_POST['fecha_fin'] ?? '',
                $ids_servicios,
                $_POST['metodo_pago'] ?? 'transferencia'
            );

            if (is_array($reserva)) {
                $errores = $reserva;
            } else {
                $pago = new pago();
                $monto_paquete = $paquete->CalcularPrecioPaquete($id);
                $monto_extras = $paquete->CalcularMontoExtras($ids_servicios);
                $pago->AsegurarPagoPendientePaquete($reserva, $monto_paquete + $monto_extras, $_POST['metodo_pago'] ?? 'transferencia');

                header("location: index.php?controller=reserva&action=misreservas&msg=reserva de paquete registrada");
                exit;
            }
        }

        require_once __DIR__ . "/../views/paquete/detalle.php";
    }

    public function mis_paquetes()
    {
        header("location: index.php?controller=reserva&action=misreservas");
        exit;
    }

    public function reservas_host()
    {
        header("location: index.php?controller=reserva&action=host");
        exit;
    }

    public function aprobar()
    {
        $paquete = new paquete();

        if ($_POST && isset($_POST['id_reserva_paquete'])) {
            $paquete->UpdateEstadoReservaPaquete($_POST['id_reserva_paquete'], 'aprobada');
        }

        header("location: index.php?controller=reserva&action=host&msg=reserva de paquete aprobada");
        exit;
    }

    public function rechazar()
    {
        $paquete = new paquete();

        if ($_POST && isset($_POST['id_reserva_paquete'])) {
            $paquete->UpdateEstadoReservaPaquete($_POST['id_reserva_paquete'], 'rechazada');
        }

        header("location: index.php?controller=reserva&action=host&msg=reserva de paquete rechazada");
        exit;
    }
}
