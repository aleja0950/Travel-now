<?php
require_once __DIR__ . "/../config/config.php";

class reserva
{
    private $travelnow1;

    public function __construct()
    {
        $this->travelnow1 = Database::conectar();
    }

    public function GetById($id)
    {
        $id = (int) $id;

        // Carga la ficha detallada que alimenta el detalle de reserva:
        // imagen, descripcion, capacidad, banos, cupos y servicios.
        $sql = "SELECT
                    habitacion.Id_habitacion,
                    habitacion.tipo_habitacion,
                    habitacion.imagen,
                    habitacion.descripcion_texto,
                    habitacion.capacidad,
                    habitacion.banos,
                    habitacion.cupos,
                    habitacion.servicios,
                    empresa.nombre,
                    empresa.ubicacion,
                    precio.precio,
                    precio.estado
                FROM habitacion
                INNER JOIN empresa ON habitacion.Id_empresa = empresa.id_alojamieto
                LEFT JOIN precio ON habitacion.Id_habitacion = precio.Id_habitacion
                WHERE habitacion.Id_habitacion = $id
                LIMIT 1";

        $resul = $this->travelnow1->query($sql);

        if (!$resul || $resul->num_rows === 0) {
            return null;
        }

        return $resul->fetch_assoc();
    }

    public function GetFirst()
    {
        $sql = "SELECT
                    habitacion.Id_habitacion
                FROM habitacion
                ORDER BY habitacion.Id_habitacion ASC
                LIMIT 1";

        $resul = $this->travelnow1->query($sql);

        if (!$resul || $resul->num_rows === 0) {
            return null;
        }

        return $resul->fetch_assoc();
    }

    public function GetReservasByHabitacion($id)
    {
        $id = (int) $id;

        $sql = "SELECT *
                FROM reserva
                WHERE id_habitacion = $id
                ORDER BY fecha_ingreso DESC";

        $resul = $this->travelnow1->query($sql);

        if (!$resul) {
            return [];
        }

        return $resul->fetch_all(MYSQLI_ASSOC);
    }

    public function save($fecha_ingreso, $fecha_salida, $tipo_habitacion, $servicio_especial, $id_habitacion, $id_user)
    {
        // Valida datos basicos y despues comprueba cruces reales de agenda
        // antes de crear una nueva reserva en estado pendiente.
        $errores = [];
        $id_habitacion = (int) $id_habitacion;
        $id_user = (int) $id_user;

        if (empty($fecha_ingreso)) {
            $errores[] = "la fecha de ingreso es obligatoria";
        }

        if (empty($fecha_salida)) {
            $errores[] = "la fecha de salida es obligatoria";
        }

        if (!empty($fecha_ingreso) && !empty($fecha_salida) && strtotime($fecha_salida) <= strtotime($fecha_ingreso)) {
            $errores[] = "la fecha de salida debe ser mayor a la fecha de ingreso";
        }

        if ($id_habitacion <= 0) {
            $errores[] = "la habitacion no es valida";
        }

        if ($id_user <= 0) {
            $errores[] = "el usuario autenticado no es valido";
        }

        if (empty($errores)) {
            $fecha_ingreso_sql = date('Y-m-d H:i:s', strtotime($fecha_ingreso));
            $fecha_salida_sql = date('Y-m-d H:i:s', strtotime($fecha_salida));

            $cruces = $this->GetCrucesReserva($fecha_ingreso_sql, $fecha_salida_sql, $id_habitacion);

            if (count($cruces) > 0) {
                $rangos = [];

                foreach ($cruces as $cruce) {
                    $rangos[] = date('d/m/Y H:i', strtotime($cruce['fecha_ingreso'])) . " a " . date('d/m/Y H:i', strtotime($cruce['fecha_salida']));
                }

                $errores[] = "ya existe una reserva activa en esa habitacion para ese rango de fechas. Fechas ocupadas: " . implode(" | ", $rangos);
            }
        }

        if (count($errores) > 0) {
            return $errores;
        }

        $fecha_ingreso = $this->travelnow1->real_escape_string($fecha_ingreso_sql);
        $fecha_salida = $this->travelnow1->real_escape_string($fecha_salida_sql);
        $tipo_habitacion = $this->travelnow1->real_escape_string($tipo_habitacion);
        $servicio_especial = $this->travelnow1->real_escape_string($servicio_especial);

        $sql = "INSERT INTO reserva(fecha_ingreso, fecha_salida, tipo_habitacion, servicio_especial, estado_reserva, Id_user, id_habitacion)
                VALUES('$fecha_ingreso', '$fecha_salida', '$tipo_habitacion', '$servicio_especial', 'pendiente', $id_user, $id_habitacion)";

        $resul = $this->travelnow1->query($sql);

        if (!$resul) {
            return ["no fue posible guardar la reserva"];
        }

        return $this->travelnow1->insert_id;
    }

    public function TieneCruceReserva($fecha_ingreso, $fecha_salida, $id_habitacion)
    {
        return count($this->GetCrucesReserva($fecha_ingreso, $fecha_salida, $id_habitacion)) > 0;
    }

    public function GetCrucesReserva($fecha_ingreso, $fecha_salida, $id_habitacion)
    {
        // Busca reservas activas que se solapen con el rango enviado
        // para bloquear doble ocupacion de la misma habitacion.
        $id_habitacion = (int) $id_habitacion;
        $fecha_ingreso = $this->travelnow1->real_escape_string($fecha_ingreso);
        $fecha_salida = $this->travelnow1->real_escape_string($fecha_salida);

        $sql = "SELECT fecha_ingreso, fecha_salida, estado_reserva
                FROM reserva
                WHERE id_habitacion = $id_habitacion
                AND estado_reserva NOT IN ('rechazada', 'cancelada')
                AND fecha_ingreso < '$fecha_salida'
                AND fecha_salida > '$fecha_ingreso'
                ORDER BY fecha_ingreso ASC";

        $resul = $this->travelnow1->query($sql);

        if (!$resul) {
            return [];
        }

        return $resul->fetch_all(MYSQLI_ASSOC);
    }

    public function GetReservasHost()
    {
        return $this->GetReservasHostFiltradas('', '', '');
    }

    public function GetReservasHostFiltradas($estado, $desde, $hasta)
    {
        // Reune las reservas visibles para el host y luego las separa
        // en proximas, pasadas y rechazadas/canceladas.
        $where = [];

        if (!empty($estado)) {
            $estado = $this->travelnow1->real_escape_string($estado);
            $where[] = "reserva.estado_reserva = '$estado'";
        }

        if (!empty($desde)) {
            $desde_sql = $this->travelnow1->real_escape_string($desde . " 00:00:00");
            $where[] = "reserva.fecha_salida >= '$desde_sql'";
        }

        if (!empty($hasta)) {
            $hasta_sql = $this->travelnow1->real_escape_string($hasta . " 23:59:59");
            $where[] = "reserva.fecha_ingreso <= '$hasta_sql'";
        }

        $sql = "SELECT
                    reserva.Id_reserva,
                    reserva.fecha_ingreso,
                    reserva.fecha_salida,
                    reserva.tipo_habitacion,
                    reserva.servicio_especial,
                    reserva.estado_reserva,
                    reserva.id_habitacion,
                    empresa.nombre,
                    empresa.ubicacion,
                    precio.precio
                FROM reserva
                INNER JOIN habitacion ON reserva.id_habitacion = habitacion.Id_habitacion
                INNER JOIN empresa ON habitacion.Id_empresa = empresa.id_alojamieto
                LEFT JOIN precio ON habitacion.Id_habitacion = precio.Id_habitacion";

        if (count($where) > 0) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY reserva.fecha_ingreso DESC";

        $resul = $this->travelnow1->query($sql);

        if (!$resul) {
            return [
                'upcoming' => [],
                'past' => [],
                'rejected' => []
            ];
        }

        $datos = $resul->fetch_all(MYSQLI_ASSOC);
        $hoy = date('Y-m-d H:i:s');
        $agrupadas = [
            'upcoming' => [],
            'past' => [],
            'rejected' => []
        ];

        foreach ($datos as $item) {
            if ($item['estado_reserva'] === 'rechazada' || $item['estado_reserva'] === 'cancelada') {
                $agrupadas['rejected'][] = $item;
            } elseif ($item['fecha_salida'] >= $hoy) {
                $agrupadas['upcoming'][] = $item;
            } else {
                $agrupadas['past'][] = $item;
            }
        }

        return $agrupadas;
    }

    public function GetMisReservasByHabitacion($id_habitacion, $id_user)
    {
        $id_habitacion = (int) $id_habitacion;
        $id_user = (int) $id_user;

        $sql = "SELECT *
                FROM reserva
                WHERE id_habitacion = $id_habitacion
                AND Id_user = $id_user
                ORDER BY fecha_ingreso DESC";

        $resul = $this->travelnow1->query($sql);

        if (!$resul) {
            return [];
        }

        return $resul->fetch_all(MYSQLI_ASSOC);
    }

    public function GetMisReservas($id_user)
    {
        $id_user = (int) $id_user;

        $sql = "SELECT
                    reserva.Id_reserva,
                    reserva.fecha_ingreso,
                    reserva.fecha_salida,
                    reserva.tipo_habitacion,
                    reserva.servicio_especial,
                    reserva.estado_reserva,
                    reserva.id_habitacion,
                    empresa.nombre,
                    empresa.ubicacion,
                    precio.precio
                FROM reserva
                INNER JOIN habitacion ON reserva.id_habitacion = habitacion.Id_habitacion
                INNER JOIN empresa ON habitacion.Id_empresa = empresa.id_alojamieto
                LEFT JOIN precio ON habitacion.Id_habitacion = precio.Id_habitacion
                WHERE reserva.Id_user = $id_user
                ORDER BY reserva.fecha_ingreso DESC";

        $resul = $this->travelnow1->query($sql);

        if (!$resul) {
            return [];
        }

        return $resul->fetch_all(MYSQLI_ASSOC);
    }

    public function UpdateEstado($id, $estado)
    {
        $id = (int) $id;
        $estado = $this->travelnow1->real_escape_string($estado);

        $sql = "UPDATE reserva
                SET estado_reserva = '$estado'
                WHERE Id_reserva = $id";

        return $this->travelnow1->query($sql);
    }

    public function GetPoliticaCancelacionDetalle($estado_reserva, $fecha_ingreso, $estado_pago)
    {
        $ahora = time();
        $ingreso = strtotime($fecha_ingreso);

        $detalle = [
            'permitida' => false,
            'mensaje' => '',
            'horas_restantes' => 0,
            'plazo_horas' => 0,
            'porcentaje_reembolso' => 0,
            'tipo_reembolso' => 'sin cobro'
        ];

        if ($ingreso === false) {
            $detalle['mensaje'] = "no fue posible validar la fecha de la reserva";
            return $detalle;
        }

        if ($estado_reserva === 'rechazada') {
            $detalle['mensaje'] = "la reserva ya fue rechazada por el host";
            return $detalle;
        }

        if ($estado_reserva === 'cancelada') {
            $detalle['mensaje'] = "la reserva ya fue cancelada";
            return $detalle;
        }

        if ($ingreso <= $ahora) {
            $detalle['mensaje'] = "no puedes cancelar una reserva que ya inicio o ya paso";
            return $detalle;
        }

        $horas_restantes = ($ingreso - $ahora) / 3600;
        $detalle['horas_restantes'] = max(0, (int) floor($horas_restantes));

        if ($estado_pago === 'pagado') {
            if ($horas_restantes >= 48) {
                $detalle['plazo_horas'] = 48;
                $detalle['permitida'] = true;
                $detalle['porcentaje_reembolso'] = 100;
                $detalle['tipo_reembolso'] = 'reembolso total';
                $detalle['mensaje'] = "cancelacion permitida con reembolso total";
                return $detalle;
            }

            if ($horas_restantes >= 24) {
                $detalle['plazo_horas'] = 24;
                $detalle['permitida'] = true;
                $detalle['porcentaje_reembolso'] = 50;
                $detalle['tipo_reembolso'] = 'reembolso parcial';
                $detalle['mensaje'] = "cancelacion permitida con reembolso parcial del 50%";
                return $detalle;
            }

            if ($horas_restantes < 24) {
                $detalle['plazo_horas'] = 24;
                $detalle['tipo_reembolso'] = 'sin reembolso';
                $detalle['mensaje'] = "solo puedes cancelar reservas pagadas con minimo 24 horas de anticipacion";
                return $detalle;
            }
        }

        if ($estado_pago === 'pendiente') {
            $detalle['plazo_horas'] = 24;

            if ($horas_restantes < 24) {
                $detalle['mensaje'] = "solo puedes cancelar reservas pendientes con minimo 24 horas de anticipacion";
                return $detalle;
            }

            $detalle['permitida'] = true;
            $detalle['tipo_reembolso'] = 'sin cobro';
            $detalle['mensaje'] = "cancelacion permitida";
            return $detalle;
        }

        $detalle['permitida'] = true;
        $detalle['mensaje'] = "cancelacion permitida";
        return $detalle;
    }

    public function GetPoliticaCancelacion($fecha_ingreso, $estado_pago)
    {
        $detalle = $this->GetPoliticaCancelacionDetalle('pendiente', $fecha_ingreso, $estado_pago);

        return $detalle['permitida'] ? true : $detalle['mensaje'];
    }

    public function ValidarCancelacionUsuario($id_reserva, $id_user)
    {
        $detalle = $this->GetDetalleCancelacionUsuario($id_reserva, $id_user);

        return is_array($detalle) ? true : $detalle;
    }

    public function GetDetalleCancelacionUsuario($id_reserva, $id_user)
    {
        $id_reserva = (int) $id_reserva;
        $id_user = (int) $id_user;

        $sql = "SELECT reserva.Id_user, reserva.estado_reserva, reserva.fecha_ingreso, pago.estado_pago
                FROM reserva
                LEFT JOIN pago ON reserva.Id_reserva = pago.Id_reserva
                WHERE reserva.Id_reserva = $id_reserva
                LIMIT 1";

        $resul = $this->travelnow1->query($sql);

        if (!$resul || $resul->num_rows === 0) {
            return "la reserva no existe";
        }

        $datos = $resul->fetch_assoc();

        if ((int) ($datos['Id_user'] ?? 0) !== $id_user) {
            return "no puedes cancelar una reserva que no te pertenece";
        }

        $politica = $this->GetPoliticaCancelacionDetalle(
            $datos['estado_reserva'] ?? '',
            $datos['fecha_ingreso'] ?? '',
            $datos['estado_pago'] ?? 'pendiente'
        );

        if (!$politica['permitida']) {
            return $politica['mensaje'];
        }

        return $politica;
    }

    public function CancelarReservaUsuario($id_reserva, $id_user)
    {
        $validacion = $this->ValidarCancelacionUsuario($id_reserva, $id_user);

        if ($validacion !== true) {
            return $validacion;
        }

        $id_reserva = (int) $id_reserva;

        $sql = "UPDATE reserva
                SET estado_reserva = 'cancelada'
                WHERE Id_reserva = $id_reserva";

        $resul = $this->travelnow1->query($sql);

        if (!$resul) {
            return "no fue posible cancelar la reserva";
        }

        return true;
    }

    public function GetHistorialHost()
    {
        $sql = "SELECT
                    reserva.Id_reserva,
                    reserva.fecha_ingreso,
                    reserva.fecha_salida,
                    reserva.servicio_especial,
                    reserva.estado_reserva,
                    empresa.nombre,
                    precio.precio
                FROM reserva
                INNER JOIN habitacion ON reserva.id_habitacion = habitacion.Id_habitacion
                INNER JOIN empresa ON habitacion.Id_empresa = empresa.id_alojamieto
                LEFT JOIN precio ON habitacion.Id_habitacion = precio.Id_habitacion
                ORDER BY reserva.fecha_ingreso DESC";

        $resul = $this->travelnow1->query($sql);

        if (!$resul) {
            return [
                'completed' => [],
                'upcoming' => [],
                'gross' => 0
            ];
        }

        $datos = $resul->fetch_all(MYSQLI_ASSOC);
        $hoy = date('Y-m-d H:i:s');
        $historial = [
            'completed' => [],
            'upcoming' => [],
            'gross' => 0
        ];

        foreach ($datos as $item) {
            if (($item['estado_reserva'] ?? '') === 'aprobada') {
                $historial['gross'] += (int) ($item['precio'] ?? 0);
            }

            if (($item['estado_reserva'] ?? '') === 'pendiente') {
                $historial['upcoming'][] = $item;
                continue;
            }

            if (($item['estado_reserva'] ?? '') === 'aprobada' || $item['fecha_salida'] < $hoy) {
                $historial['completed'][] = $item;
            }
        }

        return $historial;
    }
}
