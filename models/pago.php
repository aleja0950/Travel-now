<?php
require_once __DIR__ . "/../config/config.php";

class pago
{
    private $travelnow1;

    public function __construct()
    {
        $this->travelnow1 = Database::conectar();
    }

    public function GetMontoByHabitacion($id_habitacion)
    {
        $id_habitacion = (int) $id_habitacion;

        $sql = "SELECT precio
                FROM precio
                WHERE Id_habitacion = $id_habitacion
                ORDER BY Id_precio DESC
                LIMIT 1";

        $resul = $this->travelnow1->query($sql);

        if (!$resul || $resul->num_rows === 0) {
            return 0;
        }

        $datos = $resul->fetch_assoc();

        return (int) ($datos['precio'] ?? 0);
    }

    public function CalcularNochesEstadia($fecha_ingreso, $fecha_salida)
    {
        $ingreso = strtotime($fecha_ingreso);
        $salida = strtotime($fecha_salida);

        if ($ingreso === false || $salida === false || $salida <= $ingreso) {
            return 1;
        }

        $diferencia = $salida - $ingreso;

        return max(1, (int) ceil($diferencia / 86400));
    }

    public function CalcularMontoEstadia($fecha_ingreso, $fecha_salida, $id_habitacion)
    {
        $precio_noche = $this->GetMontoByHabitacion($id_habitacion);
        $noches = $this->CalcularNochesEstadia($fecha_ingreso, $fecha_salida);

        return $noches * $precio_noche;
    }

    public function AsegurarPagoPendiente($id_reserva, $id_habitacion, $metodo_pago = 'por definir', $fecha_ingreso = null, $fecha_salida = null)
    {
        // Crea el pago inicial apenas nace la reserva y deja registrado
        // desde el comienzo el metodo que el usuario eligio en el modal.
        $id_reserva = (int) $id_reserva;
        $id_habitacion = (int) $id_habitacion;
        $metodo_pago = trim((string) $metodo_pago);

        $sql = "SELECT Id_pago
                FROM pago
                WHERE Id_reserva = $id_reserva
                LIMIT 1";

        $resul = $this->travelnow1->query($sql);

        if ($resul && $resul->num_rows > 0) {
            return true;
        }

        if ($fecha_ingreso !== null && $fecha_salida !== null) {
            $monto = $this->CalcularMontoEstadia($fecha_ingreso, $fecha_salida, $id_habitacion);
        } else {
            $monto = $this->GetMontoByHabitacion($id_habitacion);
        }

        $referencia = "RES-" . $id_reserva;
        $metodo_pago_sql = $this->travelnow1->real_escape_string($metodo_pago !== '' ? $metodo_pago : 'por definir');

        $sql = "INSERT INTO pago(Id_reserva, metodo_pago, monto, estado_pago, fecha_pago, referencia)
                VALUES($id_reserva, '$metodo_pago_sql', $monto, 'pendiente', NULL, '$referencia')";

        return $this->travelnow1->query($sql);
    }

    public function AsegurarPagoPendientePaquete($id_reserva_paquete, $monto, $metodo_pago = 'por definir')
    {
        $id_reserva_paquete = (int) $id_reserva_paquete;
        $monto = (int) $monto;
        $metodo_pago = trim((string) $metodo_pago);
        $metodo_pago_sql = $this->travelnow1->real_escape_string($metodo_pago !== '' ? $metodo_pago : 'por definir');
        $referencia = 'PKG-' . $id_reserva_paquete;

        $sql = "SELECT Id_pago
                FROM pago
                WHERE Id_reserva_paquete = $id_reserva_paquete
                LIMIT 1";

        $resul = $this->travelnow1->query($sql);

        if ($resul && $resul->num_rows > 0) {
            return true;
        }

        $sql = "INSERT INTO pago(Id_reserva_paquete, metodo_pago, monto, estado_pago, fecha_pago, referencia)
                VALUES($id_reserva_paquete, '$metodo_pago_sql', $monto, 'pendiente', NULL, '$referencia')";

        return $this->travelnow1->query($sql);
    }

    public function CambiarEstadoPago($id_reserva, $metodo_pago, $referencia, $estado_pago, $detalle = null)
    {
        // Unifica las transiciones de estado para que pagado, revision
        // y fallido actualicen los mismos campos base del pago.
        $id_reserva = (int) $id_reserva;
        $metodo_pago = $this->travelnow1->real_escape_string($metodo_pago);
        $referencia = $this->travelnow1->real_escape_string($referencia);
        $estado_pago = $this->travelnow1->real_escape_string($estado_pago);
        $detalle_sql = $detalle === null
            ? "NULL"
            : "'" . $this->travelnow1->real_escape_string($detalle) . "'";

        $sql = "UPDATE pago
                SET metodo_pago = '$metodo_pago',
                    monto_reembolso = 0,
                    estado_pago = '$estado_pago',
                    fecha_pago = NOW(),
                    referencia = '$referencia',
                    detalle_reembolso = $detalle_sql
                WHERE Id_reserva = $id_reserva";

        return $this->travelnow1->query($sql);
    }

    public function MarcarPagado($id_reserva, $metodo_pago, $referencia)
    {
        return $this->CambiarEstadoPago($id_reserva, $metodo_pago, $referencia, 'pagado', null);
    }

    public function MarcarEnRevision($id_reserva, $metodo_pago, $referencia, $detalle = null)
    {
        $detalle = trim((string) $detalle);
        if ($detalle === '') {
            $detalle = 'pendiente de verificacion del host';
        }

        return $this->CambiarEstadoPago($id_reserva, $metodo_pago, $referencia, 'en revision', $detalle);
    }

    public function MarcarFallido($id_reserva, $metodo_pago, $referencia, $detalle = null)
    {
        $detalle = trim((string) $detalle);
        if ($detalle === '') {
            $detalle = 'pago marcado como fallido';
        }

        return $this->CambiarEstadoPago($id_reserva, $metodo_pago, $referencia, 'fallido', $detalle);
    }

    public function GenerarReferenciaPago($id_reserva, $metodo_pago, $referencia = '', $estado_pago = 'pagado')
    {
        // Si el usuario o el host no escriben comprobante, se genera
        // una referencia interna legible segun metodo y estado.
        $id_reserva = (int) $id_reserva;
        $metodo_pago = strtolower(trim((string) $metodo_pago));
        $referencia = trim((string) $referencia);
        $estado_pago = strtolower(trim((string) $estado_pago));

        if ($referencia !== '') {
            return $referencia;
        }

        $prefijo = 'CMP';

        if ($estado_pago === 'en revision') {
            $prefijo = 'REV';
        } elseif ($estado_pago === 'fallido') {
            $prefijo = 'ERR';
        }

        if ($metodo_pago === 'transferencia') {
            $prefijo = 'TRF';
        } elseif ($metodo_pago === 'efectivo') {
            $prefijo = 'EFE';
        } elseif ($metodo_pago === 'tarjeta') {
            $prefijo = 'TAR';
        }

        if ($estado_pago === 'en revision') {
            $prefijo = 'REV-' . $prefijo;
        } elseif ($estado_pago === 'fallido') {
            $prefijo = 'ERR-' . $prefijo;
        }

        return $prefijo . '-' . $id_reserva . '-' . date('YmdHis');
    }

    public function ValidarMarcadoPago($id_reserva, $accion_pago = 'pagado')
    {
        // El host solo puede mover pagos asociados a reservas aprobadas
        // y evita repetir el mismo estado sobre un pago ya resuelto.
        $id_reserva = (int) $id_reserva;
        $accion_pago = trim((string) $accion_pago);

        $sql = "SELECT reserva.estado_reserva, pago.estado_pago
                FROM pago
                INNER JOIN reserva ON pago.Id_reserva = reserva.Id_reserva
                WHERE pago.Id_reserva = $id_reserva
                LIMIT 1";

        $resul = $this->travelnow1->query($sql);

        if (!$resul || $resul->num_rows === 0) {
            return "no existe un pago asociado a la reserva";
        }

        $datos = $resul->fetch_assoc();

        if (($datos['estado_reserva'] ?? '') !== 'aprobada') {
            return "la reserva debe estar aprobada antes de registrar el pago";
        }

        $estado_actual = $datos['estado_pago'] ?? '';

        if ($estado_actual === 'pagado') {
            return "el pago ya fue registrado";
        }

        if ($estado_actual === 'en revision' && $accion_pago === 'revision') {
            return "el pago ya fue enviado a revision";
        }

        if ($estado_actual === 'fallido' && $accion_pago === 'fallido') {
            return "el pago ya fue marcado como fallido";
        }

        if ($estado_actual === 'cancelado') {
            return "el pago fue cancelado y no puede marcarse como pagado";
        }

        if ($estado_actual === 'reembolsado') {
            return "el pago ya fue reembolsado y no puede volver a marcarse";
        }

        return true;
    }

    public function ValidarMarcadoPagoUsuario($id_reserva, $id_user, $accion_pago = 'revision')
    {
        // El usuario solo puede reportar pagos de sus propias reservas
        // y siempre sobre reservas aprobadas.
        $id_reserva = (int) $id_reserva;
        $id_user = (int) $id_user;
        $accion_pago = trim((string) $accion_pago);

        $sql = "SELECT reserva.Id_user, reserva.estado_reserva, pago.estado_pago
                FROM pago
                INNER JOIN reserva ON pago.Id_reserva = reserva.Id_reserva
                WHERE pago.Id_reserva = $id_reserva
                LIMIT 1";

        $resul = $this->travelnow1->query($sql);

        if (!$resul || $resul->num_rows === 0) {
            return "no existe un pago asociado a la reserva";
        }

        $datos = $resul->fetch_assoc();

        if ((int) ($datos['Id_user'] ?? 0) !== $id_user) {
            return "no puedes pagar una reserva que no te pertenece";
        }

        if (($datos['estado_reserva'] ?? '') !== 'aprobada') {
            return "la reserva debe estar aprobada antes de registrar el pago";
        }

        $estado_actual = $datos['estado_pago'] ?? '';

        if ($estado_actual === 'pagado') {
            return "el pago ya fue registrado";
        }

        if ($estado_actual === 'en revision' && $accion_pago === 'revision') {
            return "el pago ya fue enviado a revision";
        }

        if ($estado_actual === 'fallido' && $accion_pago === 'fallido') {
            return "el pago ya fue marcado como fallido";
        }

        if ($estado_actual === 'cancelado') {
            return "el pago fue cancelado y no puede marcarse como pagado";
        }

        if ($estado_actual === 'reembolsado') {
            return "el pago ya fue reembolsado y no puede volver a marcarse";
        }

        return true;
    }

    public function CancelarPorReserva($id_reserva)
    {
        $id_reserva = (int) $id_reserva;

        $sql = "UPDATE pago
                SET estado_pago = 'cancelado'
                WHERE Id_reserva = $id_reserva";

        return $this->travelnow1->query($sql);
    }

    public function ProcesarCancelacionPorReserva($id_reserva, $politica_cancelacion = [])
    {
        // Traduce la cancelacion de la reserva a estado financiero:
        // cancelado sin cobro o reembolsado total/parcial.
        $id_reserva = (int) $id_reserva;

        $sql = "SELECT estado_pago
                FROM pago
                WHERE Id_reserva = $id_reserva
                LIMIT 1";

        $resul = $this->travelnow1->query($sql);

        if (!$resul || $resul->num_rows === 0) {
            return true;
        }

        $datos = $resul->fetch_assoc();
        $estado_actual = $datos['estado_pago'] ?? 'pendiente';
        $porcentaje_reembolso = (int) ($politica_cancelacion['porcentaje_reembolso'] ?? 0);
        $tipo_reembolso = $this->travelnow1->real_escape_string($politica_cancelacion['tipo_reembolso'] ?? 'sin cobro');

        if ($estado_actual === 'pagado') {
            $sql = "SELECT monto
                    FROM pago
                    WHERE Id_reserva = $id_reserva
                    LIMIT 1";

            $resMonto = $this->travelnow1->query($sql);
            $monto = 0;

            if ($resMonto && $resMonto->num_rows > 0) {
                $monto = (int) (($resMonto->fetch_assoc())['monto'] ?? 0);
            }

            $monto_reembolso = (int) round($monto * ($porcentaje_reembolso / 100));
            $sql = "UPDATE pago
                    SET estado_pago = 'reembolsado',
                        monto_reembolso = $monto_reembolso,
                        detalle_reembolso = '$tipo_reembolso'
                    WHERE Id_reserva = $id_reserva";

            return $this->travelnow1->query($sql);
        }

        if ($estado_actual === 'pendiente') {
            $sql = "UPDATE pago
                    SET estado_pago = 'cancelado',
                        monto_reembolso = 0,
                        detalle_reembolso = 'sin cobro'
                    WHERE Id_reserva = $id_reserva";

            return $this->travelnow1->query($sql);
        }

        return true;
    }

    public function GetHistorialHost()
    {
        return $this->GetHistorialHostFiltrado('', '', '');
    }

    public function GetHistorialHostFiltrado($estado_pago, $desde, $hasta)
    {
        // Prepara el historial financiero del host y agrupa cada pago
        // segun el estado que debe verse en cada tab del panel.
        $where = [];

        if (!empty($estado_pago)) {
            $estado_pago = $this->travelnow1->real_escape_string($estado_pago);
            $where[] = "pago.estado_pago = '$estado_pago'";
        }

        if (!empty($desde)) {
            $desde = $this->travelnow1->real_escape_string($desde . " 00:00:00");
            $where[] = "reserva.fecha_ingreso >= '$desde'";
        }

        if (!empty($hasta)) {
            $hasta = $this->travelnow1->real_escape_string($hasta . " 23:59:59");
            $where[] = "reserva.fecha_ingreso <= '$hasta'";
        }

        $sql = "SELECT
                    pago.Id_pago,
                    pago.Id_reserva,
                    pago.metodo_pago,
                    pago.monto,
                    pago.monto_reembolso,
                    pago.estado_pago,
                    pago.fecha_pago,
                    pago.referencia,
                    pago.detalle_reembolso,
                    reserva.fecha_ingreso,
                    reserva.fecha_salida,
                    reserva.servicio_especial,
                    reserva.estado_reserva,
                    empresa.nombre
                FROM pago
                INNER JOIN reserva ON pago.Id_reserva = reserva.Id_reserva
                INNER JOIN habitacion ON reserva.id_habitacion = habitacion.Id_habitacion
                INNER JOIN empresa ON habitacion.Id_empresa = empresa.id_alojamieto";

        if (count($where) > 0) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY reserva.fecha_ingreso DESC";

        $resul = $this->travelnow1->query($sql);

        if (!$resul) {
            return [
                'completed' => [],
                'upcoming' => [],
                'reverted' => [],
                'gross' => 0
            ];
        }

        $datos = $resul->fetch_all(MYSQLI_ASSOC);
        $historial = [
            'completed' => [],
            'upcoming' => [],
            'reverted' => [],
            'gross' => 0
        ];

        foreach ($datos as $item) {
            if (($item['estado_pago'] ?? '') === 'pagado') {
                $historial['completed'][] = $item;
                $historial['gross'] += (int) ($item['monto'] ?? 0);
                continue;
            }

            if (in_array(($item['estado_pago'] ?? ''), ['pendiente', 'en revision'], true)) {
                $historial['upcoming'][] = $item;
                continue;
            }

            if (in_array(($item['estado_pago'] ?? ''), ['cancelado', 'reembolsado', 'fallido'], true)) {
                $historial['reverted'][] = $item;
            }
        }

        return $historial;
    }

    public function GetPagosByHabitacion($id_habitacion)
    {
        $id_habitacion = (int) $id_habitacion;

        $sql = "SELECT
                    pago.Id_pago,
                    pago.Id_reserva,
                    pago.metodo_pago,
                    pago.monto,
                    pago.monto_reembolso,
                    pago.estado_pago,
                    pago.fecha_pago,
                    pago.referencia,
                    pago.detalle_reembolso
                FROM pago
                INNER JOIN reserva ON pago.Id_reserva = reserva.Id_reserva
                WHERE reserva.id_habitacion = $id_habitacion
                ORDER BY reserva.fecha_ingreso DESC";

        $resul = $this->travelnow1->query($sql);

        if (!$resul) {
            return [];
        }

        return $resul->fetch_all(MYSQLI_ASSOC);
    }

    public function GetPagosMapByHabitacion($id_habitacion)
    {
        $pagos = $this->GetPagosByHabitacion($id_habitacion);
        $mapa = [];

        foreach ($pagos as $item) {
            $mapa[$item['Id_reserva']] = $item;
        }

        return $mapa;
    }

    public function GetPagosMapByReservasUsuario($id_user)
    {
        $id_user = (int) $id_user;

        $sql = "SELECT
                    pago.Id_pago,
                    pago.Id_reserva,
                    pago.metodo_pago,
                    pago.monto,
                    pago.monto_reembolso,
                    pago.estado_pago,
                    pago.fecha_pago,
                    pago.referencia,
                    pago.detalle_reembolso
                FROM pago
                INNER JOIN reserva ON pago.Id_reserva = reserva.Id_reserva
                WHERE reserva.Id_user = $id_user
                ORDER BY reserva.fecha_ingreso DESC";

        $resul = $this->travelnow1->query($sql);

        if (!$resul) {
            return [];
        }

        $mapa = [];
        $datos = $resul->fetch_all(MYSQLI_ASSOC);

        foreach ($datos as $item) {
            $mapa[$item['Id_reserva']] = $item;
        }

        return $mapa;
    }

    public function GetPagosMapByReservasPaqueteUsuario($id_user)
    {
        $id_user = (int) $id_user;

        $sql = "SELECT
                    pago.Id_pago,
                    pago.Id_reserva_paquete,
                    pago.metodo_pago,
                    pago.monto,
                    pago.monto_reembolso,
                    pago.estado_pago,
                    pago.fecha_pago,
                    pago.referencia,
                    pago.detalle_reembolso
                FROM pago
                INNER JOIN reserva_paquete ON pago.Id_reserva_paquete = reserva_paquete.Id_reserva_paquete
                WHERE reserva_paquete.Id_user = $id_user
                ORDER BY reserva_paquete.fecha_inicio DESC";

        $resul = $this->travelnow1->query($sql);

        if (!$resul) {
            return [];
        }

        $mapa = [];
        $datos = $resul->fetch_all(MYSQLI_ASSOC);

        foreach ($datos as $item) {
            $mapa[$item['Id_reserva_paquete']] = $item;
        }

        return $mapa;
    }
}
