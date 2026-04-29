<?php
require_once __DIR__ . "/../models/propiedad.php";

class propiedadcontroller
{
    private function ProcesarImagenSubida($imagen_actual = '')
    {
        // Procesa la subida real de imagen y mantiene la seleccion anterior
        // cuando el admin no adjunta un archivo nuevo.
        if (!isset($_FILES['imagen_archivo']) || !is_array($_FILES['imagen_archivo'])) {
            return ['ok' => true, 'imagen' => ($_POST['imagen'] ?? $imagen_actual)];
        }

        $archivo = $_FILES['imagen_archivo'];

        if (($archivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return ['ok' => true, 'imagen' => ($_POST['imagen'] ?? $imagen_actual)];
        }

        if (($archivo['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'error' => 'no fue posible cargar la imagen'];
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

        $nombre = 'propiedad_' . uniqid() . '.' . $extension;
        $destino = $directorio . "/" . $nombre;

        if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
            return ['ok' => false, 'error' => 'no fue posible guardar la imagen subida'];
        }

        return ['ok' => true, 'imagen' => 'uploads/' . $nombre];
    }

    public function index()
    {
        // Prepara el listado general y, si llega un id, rellena el formulario
        // en modo edicion para la propiedad seleccionada.
        $propiedad = new propiedad();
        $datos = $propiedad->GetPropiedades();
        $imagenes = $propiedad->GetImagenesDisponibles();
        $errores = [];
        $propiedad_editar = null;

        if (isset($_GET['id'])) {
            $propiedad_editar = $propiedad->GetPropiedadByHabitacion($_GET['id']);
        }

        require_once __DIR__ . "/../views/admin/propiedades.php";
    }

    public function guardar()
    {
        $propiedad = new propiedad();
        $errores = [];

        if ($_POST) {
            // Primero resuelve la imagen y luego envia la ficha completa
            // al modelo para crear la propiedad.
            $imagen_resultado = $this->ProcesarImagenSubida();

            if (!$imagen_resultado['ok']) {
                $errores = [$imagen_resultado['error']];
                $datos = $propiedad->GetPropiedades();
                $imagenes = $propiedad->GetImagenesDisponibles();
                $propiedad_editar = null;
                require_once __DIR__ . "/../views/admin/propiedades.php";
                return;
            }

            $guardar = $propiedad->CrearPropiedad(
                $_POST['nombre'] ?? '',
                $_POST['ubicacion'] ?? '',
                $_POST['tipo_habitacion'] ?? 0,
                $_POST['precio'] ?? 0,
                $_POST['estado'] ?? '',
                $imagen_resultado['imagen'] ?? ($_POST['imagen'] ?? ''),
                $_POST['descripcion_texto'] ?? '',
                $_POST['capacidad'] ?? 0,
                $_POST['banos'] ?? 0,
                $_POST['cupos'] ?? 0,
                $_POST['servicios'] ?? ''
            );

            if ($guardar === true) {
                header("location: index.php?controller=propiedad&action=index&msg=propiedad creada");
                exit;
            }

            $errores = $guardar;
        }

        $datos = $propiedad->GetPropiedades();
        $imagenes = $propiedad->GetImagenesDisponibles();
        $propiedad_editar = null;
        require_once __DIR__ . "/../views/admin/propiedades.php";
    }

    public function actualizar()
    {
        $propiedad = new propiedad();
        $errores = [];

        if ($_POST && isset($_POST['id_habitacion'])) {
            // En actualizacion se conserva la imagen actual si el admin
            // no reemplaza el archivo en este envio.
            $actual = $propiedad->GetPropiedadByHabitacion($_POST['id_habitacion']);
            $imagen_resultado = $this->ProcesarImagenSubida($actual['imagen'] ?? '');

            if (!$imagen_resultado['ok']) {
                $errores = [$imagen_resultado['error']];
                $propiedad_editar = $actual;
                $datos = $propiedad->GetPropiedades();
                $imagenes = $propiedad->GetImagenesDisponibles();
                require_once __DIR__ . "/../views/admin/propiedades.php";
                return;
            }

            $actualizar = $propiedad->ActualizarPropiedad(
                $_POST['id_habitacion'],
                $_POST['nombre'] ?? '',
                $_POST['ubicacion'] ?? '',
                $_POST['tipo_habitacion'] ?? 0,
                $_POST['precio'] ?? 0,
                $_POST['estado'] ?? '',
                $imagen_resultado['imagen'] ?? ($_POST['imagen'] ?? ''),
                $_POST['descripcion_texto'] ?? '',
                $_POST['capacidad'] ?? 0,
                $_POST['banos'] ?? 0,
                $_POST['cupos'] ?? 0,
                $_POST['servicios'] ?? ''
            );

            if ($actualizar === true) {
                header("location: index.php?controller=propiedad&action=index&msg=propiedad actualizada");
                exit;
            }

            $errores = $actualizar;
            $propiedad_editar = $propiedad->GetPropiedadByHabitacion($_POST['id_habitacion']);
            $datos = $propiedad->GetPropiedades();
            $imagenes = $propiedad->GetImagenesDisponibles();
            require_once __DIR__ . "/../views/admin/propiedades.php";
            return;
        }

        header("location: index.php?controller=propiedad&action=index");
        exit;
    }

    public function eliminar()
    {
        $propiedad = new propiedad();

        // El modelo decide si la propiedad puede borrarse segun sus reservas
        // y este controlador devuelve el resultado como mensaje visible.
        if ($_POST && isset($_POST['id_habitacion'])) {
            $eliminar = $propiedad->EliminarPropiedad($_POST['id_habitacion']);

            if ($eliminar === true) {
                header("location: index.php?controller=propiedad&action=index&msg=propiedad eliminada");
                exit;
            }

            $mensaje = is_array($eliminar) ? ($eliminar[0] ?? 'no fue posible eliminar la propiedad') : $eliminar;
            header("location: index.php?controller=propiedad&action=index&msg=" . urlencode($mensaje));
            exit;
        }

        header("location: index.php?controller=propiedad&action=index");
        exit;
    }
}
