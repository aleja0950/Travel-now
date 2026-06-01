<?php
require_once __DIR__ . "/../models/reserva.php";
require_once __DIR__ . "/../models/pago.php";
require_once __DIR__ . "/../models/paquete.php";

class reservacontroller
{
    
    public function index()
    {
        // Arma el detalle de la habitacion junto con reservas visibles,
        // reservas propias y pagos del usuario autenticado.
        $reserva = new reserva();
        $pago = new pago();
        $errores = [];
        $id_user = $_SESSION['Id_user'] ?? 0;
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $primera = $reserva->GetFirst();
            $id = $primera['Id_habitacion'] ?? null;
        }

        $habitacion = null;
        $reservas = [];
        $mis_reservas = [];
        $pagos_reserva = [];

        if ($id) {
            $habitacion = $reserva->GetById($id);
            $reservas = $reserva->GetReservasByHabitacion($id);
            $mis_reservas = $reserva->GetMisReservasByHabitacion($id, $id_user);
            $pagos_reserva = $pago->GetPagosMapByReservasUsuario($id_user);
        }

        require_once __DIR__ . "/../views/reserva/index.php";
    }

    public function guardar()
    {
        $reserva = new reserva();
        $pago = new pago();
        $errores = [];

        if ($_POST) {
            // La reserva y el pago pendiente nacen juntos para que
            // el flujo financiero exista desde la creacion del rango.
            $guardar = $reserva->save(
                $_POST['fecha_ingreso'],
                $_POST['fecha_salida'],
                $_POST['tipo_habitacion'],
                $_POST['servicio_especial'],
                $_POST['id_habitacion'],
                $_SESSION['Id_user'] ?? 0
            );

            if (is_array($guardar)) {
                $errores = $guardar;
                $id = $_POST['id_habitacion'];
                $habitacion = $reserva->GetById($id);
                $reservas = $reserva->GetReservasByHabitacion($id);
                $mis_reservas = $reserva->GetMisReservasByHabitacion($id, $_SESSION['Id_user'] ?? 0);
                $pagos_reserva = $pago->GetPagosMapByReservasUsuario($_SESSION['Id_user'] ?? 0);
                require_once __DIR__ . "/../views/reserva/index.php";
                return;
            }

            $pago->AsegurarPagoPendiente(
                $guardar,
                $_POST['id_habitacion'],
                $_POST['metodo_pago'] ?? 'por definir',
                $_POST['fecha_ingreso'] ?? null,
                $_POST['fecha_salida'] ?? null
            );

            header("location: index.php?controller=reserva&action=index&id=" . $_POST['id_habitacion'] . "&msg=reserva guardada");
            exit;
        }

        header("location: index.php?controller=reserva&action=index");
    }

    public function host()
    {
        $reserva = new reserva();
        $paquete = new paquete();
        $estado = $_GET['estado'] ?? '';
        $desde = $_GET['desde'] ?? '';
        $hasta = $_GET['hasta'] ?? '';

        $habitaciones_totales = $reserva->GetReservasHostFiltradas('', '', '');
        $paquetes_totales = $paquete->GetReservasPaqueteHostFiltradas('', '', '');
        $habitaciones_datos = $reserva->GetReservasHostFiltradas($estado, $desde, $hasta);
        $paquetes_datos = $paquete->GetReservasPaqueteHostFiltradas($estado, $desde, $hasta);

        foreach (['upcoming', 'past', 'rejected'] as $grupo) {
            foreach ($habitaciones_totales[$grupo] as $indice => $item) {
                $habitaciones_totales[$grupo][$indice]['tipo_reserva'] = 'habitacion';
            }
            foreach ($habitaciones_datos[$grupo] as $indice => $item) {
                $habitaciones_datos[$grupo][$indice]['tipo_reserva'] = 'habitacion';
            }
        }

        $totales = $paquete->CombinarReservasHost($habitaciones_totales, $paquetes_totales);
        $datos = $paquete->CombinarReservasHost($habitaciones_datos, $paquetes_datos);

        require_once __DIR__ . "/../views/admin/reservas.php";
    }
    public function aprobar()
    {
        $reserva = new reserva();

        if ($_POST && isset($_POST['id_reserva'])) {
            $reserva->UpdateEstado($_POST['id_reserva'], 'aprobada');
        }

        header("location: index.php?controller=reserva&action=host&msg=reserva aprobada");
        exit;
    }

    public function rechazar()
    {
        $reserva = new reserva();
        $pago = new pago();

        if ($_POST && isset($_POST['id_reserva'])) {
            $reserva->UpdateEstado($_POST['id_reserva'], 'rechazada');
            $pago->CancelarPorReserva($_POST['id_reserva']);
        }

        header("location: index.php?controller=reserva&action=host&msg=reserva rechazada");
        exit;
    }

    public function history()
    {
        // Carga la vista financiera del host con filtros por estado y fecha.
        $pago = new pago();
        $estado = $_GET['estado_pago'] ?? '';
        $desde = $_GET['desde'] ?? '';
        $hasta = $_GET['hasta'] ?? '';
        $datos = $pago->GetHistorialHostFiltrado($estado, $desde, $hasta);

        require_once __DIR__ . "/../views/admin/history.php";
    }

    public function pagar()
    {
        // Centraliza las acciones del pago para usuario y host:
        // enviar a revision, marcar pagado o marcar fallido.
        $pago = new pago();
        $redirect = "index.php?controller=reserva&action=history";

        if (($_POST['return_view'] ?? '') === 'user' && isset($_POST['id_habitacion'])) {
            $redirect = "index.php?controller=reserva&action=index&id=" . (int) $_POST['id_habitacion'];
        }

        if (($_POST['return_view'] ?? '') === 'user_misreservas') {
            $redirect = "index.php?controller=reserva&action=misreservas";
        }

        if ($_POST && isset($_POST['id_reserva'])) {
            $metodo_pago = $_POST['metodo_pago'] ?? 'transferencia';
            $accion_pago = $_POST['accion_pago'] ?? (
                in_array(($_POST['return_view'] ?? ''), ['user', 'user_misreservas'], true)
                    ? 'revision'
                    : 'pagado'
            );
            $detalle_pago = $_POST['detalle_pago'] ?? '';
            $referencia = $pago->GenerarReferenciaPago(
                $_POST['id_reserva'],
                $metodo_pago,
                $_POST['referencia'] ?? '',
                $accion_pago === 'revision' ? 'en revision' : ($accion_pago === 'fallido' ? 'fallido' : 'pagado')
            );
            $validacion = in_array(($_POST['return_view'] ?? ''), ['user', 'user_misreservas'], true)
                ? $pago->ValidarMarcadoPagoUsuario($_POST['id_reserva'], $_SESSION['Id_user'] ?? 0, $accion_pago)
                : $pago->ValidarMarcadoPago($_POST['id_reserva'], $accion_pago);

            if ($validacion === true) {
                $mensaje = "pago registrado";

                if ($accion_pago === 'revision') {
                    $pago->MarcarEnRevision($_POST['id_reserva'], $metodo_pago, $referencia, $detalle_pago);
                    $mensaje = "pago enviado a revision";
                } elseif ($accion_pago === 'fallido') {
                    $pago->MarcarFallido($_POST['id_reserva'], $metodo_pago, $referencia, $detalle_pago);
                    $mensaje = "pago marcado como fallido";
                } else {
                    $pago->MarcarPagado($_POST['id_reserva'], $metodo_pago, $referencia);
                }

                header("location: " . $redirect . "&msg=" . urlencode($mensaje));
                exit;
            }

            header("location: " . $redirect . "&msg=" . urlencode($validacion));
            exit;
        }

        header("location: " . $redirect);
        exit;
    }

    public function misreservas()
    {
        // Combina reservas de habitacion y paquetes con sus pagos.
        $reserva = new reserva();
        $paquete = new paquete();
        $pago = new pago();
        $id_user = $_SESSION['Id_user'] ?? 0;

        $datos = $reserva->GetMisReservas($id_user);
        $paquetes = $paquete->GetMisReservasPaquete($id_user);
        $pagos_reserva = $pago->GetPagosMapByReservasUsuario($id_user);
        $pagos_paquete = $pago->GetPagosMapByReservasPaqueteUsuario($id_user);
        $politicas_cancelacion = [];

        foreach ($datos as $item) {
            $estado_pago = $pagos_reserva[$item['Id_reserva']]['estado_pago'] ?? 'pendiente';
            $detalle = $reserva->GetPoliticaCancelacionDetalle(
                $item['estado_reserva'] ?? '',
                $item['fecha_ingreso'] ?? '',
                $estado_pago
            );
            $monto_pago = (int) ($pagos_reserva[$item['Id_reserva']]['monto'] ?? 0);
            $detalle['monto_estimado_reembolso'] = (int) round($monto_pago * (($detalle['porcentaje_reembolso'] ?? 0) / 100));
            $politicas_cancelacion[$item['Id_reserva']] = $detalle;
        }

        foreach ($paquetes as $indice => $item) {
            $paquetes[$indice]['extras_detalle'] = $paquete->NombresServiciosPorIds($item['servicios_extra'] ?? '');
        }

        require_once __DIR__ . "/../views/reserva/misreservas.php";
    }

    public function cancelar()
    {
        $reserva = new reserva();
        $pago = new pago();

        // Cuando una reserva se cancela, tambien se ajusta el estado
        // financiero asociado segun la politica de reembolso.
        if ($_POST && isset($_POST['id_reserva'])) {
            $detalle_cancelacion = $reserva->GetDetalleCancelacionUsuario($_POST['id_reserva'], $_SESSION['Id_user'] ?? 0);
            $cancelar = $reserva->CancelarReservaUsuario($_POST['id_reserva'], $_SESSION['Id_user'] ?? 0);

            if ($cancelar === true) {
                $pago->ProcesarCancelacionPorReserva($_POST['id_reserva'], is_array($detalle_cancelacion) ? $detalle_cancelacion : []);
                header("location: index.php?controller=reserva&action=misreservas&msg=reserva cancelada");
                exit;
            }

            header("location: index.php?controller=reserva&action=misreservas&msg=" . urlencode($cancelar));
            exit;
        }

        header("location: index.php?controller=reserva&action=misreservas");
        exit;
    }
}
