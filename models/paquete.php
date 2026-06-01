<?php
require_once __DIR__ . "/../config/config.php";

class paquete
{
    private $travelnow1;

    public function __construct()
    {
        $this->travelnow1 = Database::conectar();
    }

    public function GetPaquetesActivos()
    {
        $sql = "SELECT *
                FROM paquete_turistico
                WHERE estado = 'activo'
                ORDER BY Id_paquete DESC";

        $resul = $this->travelnow1->query($sql);

        if (!$resul) {
            return [];
        }

        return $resul->fetch_all(MYSQLI_ASSOC);
    }

    public function GetTodosPaquetes()
    {
        $sql = "SELECT *
                FROM paquete_turistico
                ORDER BY Id_paquete DESC";

        $resul = $this->travelnow1->query($sql);

        if (!$resul) {
            return [];
        }

        return $resul->fetch_all(MYSQLI_ASSOC);
    }

    public function GetById($id)
    {
        $id = (int) $id;

        $sql = "SELECT *
                FROM paquete_turistico
                WHERE Id_paquete = $id
                LIMIT 1";

        $resul = $this->travelnow1->query($sql);

        if (!$resul || $resul->num_rows === 0) {
            return null;
        }

        return $resul->fetch_assoc();
    }

    public function GetComponentes($id_paquete)
    {
        $id_paquete = (int) $id_paquete;

        $sql = "SELECT
                    paquete_componente.*,
                    habitacion.tipo_habitacion,
                    empresa.nombre AS nombre_hotel
                FROM paquete_componente
                LEFT JOIN habitacion ON paquete_componente.id_habitacion = habitacion.Id_habitacion
                LEFT JOIN empresa ON habitacion.Id_empresa = empresa.id_alojamieto
                WHERE paquete_componente.Id_paquete = $id_paquete
                ORDER BY paquete_componente.orden ASC, paquete_componente.Id_componente ASC";

        $resul = $this->travelnow1->query($sql);

        if (!$resul) {
            return [];
        }

        return $resul->fetch_all(MYSQLI_ASSOC);
    }

    public function GetServiciosAdicionalesActivos()
    {
        $sql = "SELECT *
                FROM servicio_adicional
                WHERE activo = 1
                ORDER BY nombre ASC";

        $resul = $this->travelnow1->query($sql);

        if (!$resul) {
            return [];
        }

        return $resul->fetch_all(MYSQLI_ASSOC);
    }

    public function GetTodosServiciosAdicionales()
    {
        $sql = "SELECT *
                FROM servicio_adicional
                ORDER BY nombre ASC";

        $resul = $this->travelnow1->query($sql);

        if (!$resul) {
            return [];
        }

        return $resul->fetch_all(MYSQLI_ASSOC);
    }

    public function CalcularPrecioPaquete($id_paquete)
    {
        $datos = $this->GetById($id_paquete);

        if (!$datos) {
            return 0;
        }

        $componentes = $this->GetComponentes($id_paquete);
        $suma_componentes = 0;

        foreach ($componentes as $item) {
            $suma_componentes += (int) ($item['precio_componente'] ?? 0);
        }

        $precio_base = (int) ($datos['precio_base'] ?? 0);

        if ($precio_base > 0) {
            return $precio_base;
        }

        return $suma_componentes;
    }

    public function CalcularMontoExtras(array $ids_servicios)
    {
        if (count($ids_servicios) === 0) {
            return 0;
        }

        $ids = array_map('intval', $ids_servicios);
        $ids = array_filter($ids, function ($id) {
            return $id > 0;
        });

        if (count($ids) === 0) {
            return 0;
        }

        $lista = implode(',', $ids);
        $sql = "SELECT SUM(precio) AS total
                FROM servicio_adicional
                WHERE activo = 1
                AND Id_servicio IN ($lista)";

        $resul = $this->travelnow1->query($sql);

        if (!$resul || $resul->num_rows === 0) {
            return 0;
        }

        $datos = $resul->fetch_assoc();

        return (int) ($datos['total'] ?? 0);
    }

    public function GetHabitacionesParaSelector()
    {
        $sql = "SELECT
                    habitacion.Id_habitacion,
                    habitacion.tipo_habitacion,
                    empresa.nombre,
                    empresa.ubicacion,
                    precio.precio
                FROM habitacion
                INNER JOIN empresa ON habitacion.Id_empresa = empresa.id_alojamieto
                LEFT JOIN precio ON habitacion.Id_habitacion = precio.Id_habitacion
                ORDER BY empresa.nombre ASC, habitacion.Id_habitacion ASC";

        $resul = $this->travelnow1->query($sql);

        if (!$resul) {
            return [];
        }

        return $resul->fetch_all(MYSQLI_ASSOC);
    }

    public function GuardarPaquete($nombre, $descripcion, $precio_base, $dias, $imagen, $estado, $componentes)
    {
        $errores = [];

        if (trim($nombre) === '') {
            $errores[] = 'el nombre del paquete es obligatorio';
        }

        if ((int) $dias <= 0) {
            $errores[] = 'los dias del paquete deben ser mayores a cero';
        }

        if (count($errores) > 0) {
            return $errores;
        }

        $nombre_sql = $this->travelnow1->real_escape_string(trim($nombre));
        $descripcion_sql = $this->travelnow1->real_escape_string(trim((string) $descripcion));
        $imagen_sql = $this->travelnow1->real_escape_string(trim((string) $imagen));
        $estado_sql = $this->travelnow1->real_escape_string(trim((string) $estado));
        $precio_base = (int) $precio_base;
        $dias = (int) $dias;

        $sql = "INSERT INTO paquete_turistico(nombre, descripcion, precio_base, dias, imagen, estado)
                VALUES('$nombre_sql', '$descripcion_sql', $precio_base, $dias, '$imagen_sql', '$estado_sql')";

        if (!$this->travelnow1->query($sql)) {
            return ['no fue posible guardar el paquete'];
        }

        $id_paquete = (int) $this->travelnow1->insert_id;
        $this->GuardarComponentes($id_paquete, $componentes);

        return $id_paquete;
    }

    public function ActualizarPaquete($id_paquete, $nombre, $descripcion, $precio_base, $dias, $imagen, $estado, $componentes)
    {
        $id_paquete = (int) $id_paquete;
        $errores = [];

        if ($id_paquete <= 0) {
            $errores[] = 'el paquete no es valido';
        }

        if (trim($nombre) === '') {
            $errores[] = 'el nombre del paquete es obligatorio';
        }

        if (count($errores) > 0) {
            return $errores;
        }

        $nombre_sql = $this->travelnow1->real_escape_string(trim($nombre));
        $descripcion_sql = $this->travelnow1->real_escape_string(trim((string) $descripcion));
        $imagen_sql = $this->travelnow1->real_escape_string(trim((string) $imagen));
        $estado_sql = $this->travelnow1->real_escape_string(trim((string) $estado));
        $precio_base = (int) $precio_base;
        $dias = (int) $dias;

        $sql = "UPDATE paquete_turistico
                SET nombre = '$nombre_sql',
                    descripcion = '$descripcion_sql',
                    precio_base = $precio_base,
                    dias = $dias,
                    imagen = '$imagen_sql',
                    estado = '$estado_sql'
                WHERE Id_paquete = $id_paquete";

        if (!$this->travelnow1->query($sql)) {
            return ['no fue posible actualizar el paquete'];
        }

        $this->travelnow1->query("DELETE FROM paquete_componente WHERE Id_paquete = $id_paquete");
        $this->GuardarComponentes($id_paquete, $componentes);

        return $id_paquete;
    }

    private function GuardarComponentes($id_paquete, $componentes)
    {
        $id_paquete = (int) $id_paquete;

        if (!is_array($componentes)) {
            return;
        }

        $orden = 0;

        foreach ($componentes as $item) {
            $tipo = $this->travelnow1->real_escape_string(trim((string) ($item['tipo'] ?? 'otro')));
            $titulo = $this->travelnow1->real_escape_string(trim((string) ($item['titulo'] ?? '')));
            $descripcion = $this->travelnow1->real_escape_string(trim((string) ($item['descripcion'] ?? '')));
            $id_habitacion = (int) ($item['id_habitacion'] ?? 0);
            $precio_componente = (int) ($item['precio_componente'] ?? 0);

            if ($titulo === '') {
                continue;
            }

            $id_habitacion_sql = $id_habitacion > 0 ? $id_habitacion : 'NULL';
            $orden++;

            $sql = "INSERT INTO paquete_componente(Id_paquete, tipo, titulo, descripcion, id_habitacion, precio_componente, orden)
                    VALUES($id_paquete, '$tipo', '$titulo', '$descripcion', $id_habitacion_sql, $precio_componente, $orden)";

            $this->travelnow1->query($sql);
        }
    }

    public function GetIdsHabitacionesPaquete($id_paquete)
    {
        $id_paquete = (int) $id_paquete;
        $ids = [];

        $sql = "SELECT DISTINCT id_habitacion
                FROM paquete_componente
                WHERE Id_paquete = $id_paquete
                AND id_habitacion IS NOT NULL
                AND id_habitacion > 0";

        $resul = $this->travelnow1->query($sql);

        if (!$resul) {
            return $ids;
        }

        while ($fila = $resul->fetch_assoc()) {
            $ids[] = (int) $fila['id_habitacion'];
        }

        return $ids;
    }

    public function RangoPaqueteADatetime($fecha_inicio, $fecha_fin)
    {
        return [
            'ingreso' => date('Y-m-d 00:00:00', strtotime($fecha_inicio)),
            'salida' => date('Y-m-d 23:59:59', strtotime($fecha_fin)),
        ];
    }

    public function GetCrucesPaquetePorHabitacion($fecha_inicio, $fecha_fin, $id_habitacion, $excluir_reserva_paquete = 0)
    {
        $id_habitacion = (int) $id_habitacion;
        $excluir_reserva_paquete = (int) $excluir_reserva_paquete;
        $fecha_inicio = $this->travelnow1->real_escape_string(date('Y-m-d', strtotime($fecha_inicio)));
        $fecha_fin = $this->travelnow1->real_escape_string(date('Y-m-d', strtotime($fecha_fin)));

        $sql = "SELECT
                    reserva_paquete.Id_reserva_paquete,
                    reserva_paquete.fecha_inicio,
                    reserva_paquete.fecha_fin,
                    reserva_paquete.estado_reserva,
                    paquete_turistico.nombre AS nombre_paquete
                FROM reserva_paquete
                INNER JOIN paquete_componente ON reserva_paquete.Id_paquete = paquete_componente.Id_paquete
                INNER JOIN paquete_turistico ON reserva_paquete.Id_paquete = paquete_turistico.Id_paquete
                WHERE paquete_componente.id_habitacion = $id_habitacion
                AND reserva_paquete.estado_reserva NOT IN ('rechazada', 'cancelada')
                AND reserva_paquete.fecha_inicio <= '$fecha_fin'
                AND reserva_paquete.fecha_fin >= '$fecha_inicio'";

        if ($excluir_reserva_paquete > 0) {
            $sql .= " AND reserva_paquete.Id_reserva_paquete != $excluir_reserva_paquete";
        }

        $sql .= " ORDER BY reserva_paquete.fecha_inicio ASC";

        $resul = $this->travelnow1->query($sql);

        if (!$resul) {
            return [];
        }

        return $resul->fetch_all(MYSQLI_ASSOC);
    }

    public function GetConflictosHabitacionPorPaquetes($fecha_ingreso, $fecha_salida, $id_habitacion, $excluir_reserva_paquete = 0)
    {
        $fecha_inicio = date('Y-m-d', strtotime($fecha_ingreso));
        $fecha_fin = date('Y-m-d', strtotime($fecha_salida));
        $cruces = $this->GetCrucesPaquetePorHabitacion($fecha_inicio, $fecha_fin, $id_habitacion, $excluir_reserva_paquete);
        $mensajes = [];

        foreach ($cruces as $cruce) {
            $mensajes[] = 'habitacion #' . (int) $id_habitacion . ' ocupada por paquete "' . ($cruce['nombre_paquete'] ?? '') . '" del ' . date('d/m/Y', strtotime($cruce['fecha_inicio'])) . ' al ' . date('d/m/Y', strtotime($cruce['fecha_fin']));
        }

        return $mensajes;
    }

    public function ValidarDisponibilidadPaquete($id_paquete, $fecha_inicio, $fecha_fin, $excluir_reserva_paquete = 0)
    {
        require_once __DIR__ . '/reserva.php';

        $id_paquete = (int) $id_paquete;
        $errores = [];
        $habitaciones = $this->GetIdsHabitacionesPaquete($id_paquete);

        if (count($habitaciones) === 0) {
            return $errores;
        }

        $rango = $this->RangoPaqueteADatetime($fecha_inicio, $fecha_fin);
        $reservaModel = new reserva();

        foreach ($habitaciones as $id_habitacion) {
            $cruces_habitacion = $reservaModel->GetCrucesReserva(
                $rango['ingreso'],
                $rango['salida'],
                $id_habitacion
            );

            foreach ($cruces_habitacion as $cruce) {
                $errores[] = 'la habitacion #' . $id_habitacion . ' ya tiene reserva de otro cliente del ' . date('d/m/Y H:i', strtotime($cruce['fecha_ingreso'])) . ' al ' . date('d/m/Y H:i', strtotime($cruce['fecha_salida']));
            }

            $cruces_paquete = $this->GetCrucesPaquetePorHabitacion(
                $fecha_inicio,
                $fecha_fin,
                $id_habitacion,
                $excluir_reserva_paquete
            );

            foreach ($cruces_paquete as $cruce) {
                $errores[] = 'la habitacion #' . $id_habitacion . ' ya esta reservada por el paquete "' . ($cruce['nombre_paquete'] ?? '') . '" del ' . date('d/m/Y', strtotime($cruce['fecha_inicio'])) . ' al ' . date('d/m/Y', strtotime($cruce['fecha_fin']));
            }
        }

        return $errores;
    }

    public function GuardarServicioAdicional($nombre, $precio, $activo)
    {
        if (trim($nombre) === '') {
            return ['el nombre del servicio es obligatorio'];
        }

        $nombre_sql = $this->travelnow1->real_escape_string(trim($nombre));
        $precio = (int) $precio;
        $activo = $activo ? 1 : 0;

        $sql = "INSERT INTO servicio_adicional(nombre, precio, activo)
                VALUES('$nombre_sql', $precio, $activo)";

        if (!$this->travelnow1->query($sql)) {
            return ['no fue posible guardar el servicio adicional'];
        }

        return (int) $this->travelnow1->insert_id;
    }

    public function ReservarPaquete($id_paquete, $id_user, $fecha_inicio, $fecha_fin, $ids_servicios, $metodo_pago)
    {
        $errores = [];
        $id_paquete = (int) $id_paquete;
        $id_user = (int) $id_user;

        if ($id_paquete <= 0) {
            $errores[] = 'el paquete no es valido';
        }

        if ($id_user <= 0) {
            $errores[] = 'debes iniciar sesion para reservar';
        }

        if (empty($fecha_inicio) || empty($fecha_fin)) {
            $errores[] = 'debes indicar fecha de inicio y fin del paquete';
        }

        if (!empty($fecha_inicio) && !empty($fecha_fin) && strtotime($fecha_fin) < strtotime($fecha_inicio)) {
            $errores[] = 'la fecha fin debe ser igual o posterior a la fecha inicio';
        }

        $paquete = $this->GetById($id_paquete);

        if (!$paquete) {
            $errores[] = 'el paquete no existe';
        } elseif (($paquete['estado'] ?? '') !== 'activo') {
            $errores[] = 'el paquete no esta disponible';
        }

        if (count($errores) === 0) {
            $disponibilidad = $this->ValidarDisponibilidadPaquete($id_paquete, $fecha_inicio, $fecha_fin);

            if (count($disponibilidad) > 0) {
                $errores = array_merge($errores, $disponibilidad);
            }
        }

        if (count($errores) > 0) {
            return $errores;
        }

        $monto_paquete = $this->CalcularPrecioPaquete($id_paquete);
        $monto_extras = $this->CalcularMontoExtras($ids_servicios);
        $monto_total = $monto_paquete + $monto_extras;

        $fecha_inicio_sql = $this->travelnow1->real_escape_string(date('Y-m-d', strtotime($fecha_inicio)));
        $fecha_fin_sql = $this->travelnow1->real_escape_string(date('Y-m-d', strtotime($fecha_fin)));
        $metodo_pago_sql = $this->travelnow1->real_escape_string(trim((string) $metodo_pago));
        $servicios_extra = $this->travelnow1->real_escape_string(implode(',', array_map('intval', $ids_servicios)));

        $sql = "INSERT INTO reserva_paquete(
                    Id_paquete, Id_user, fecha_inicio, fecha_fin, servicios_extra,
                    monto_paquete, monto_extras, monto_total, estado_reserva, metodo_pago
                ) VALUES(
                    $id_paquete, $id_user, '$fecha_inicio_sql', '$fecha_fin_sql', '$servicios_extra',
                    $monto_paquete, $monto_extras, $monto_total, 'pendiente', '$metodo_pago_sql'
                )";

        if (!$this->travelnow1->query($sql)) {
            return ['no fue posible registrar la reserva del paquete'];
        }

        return (int) $this->travelnow1->insert_id;
    }

    public function GetMisReservasPaquete($id_user)
    {
        $id_user = (int) $id_user;

        $sql = "SELECT
                    reserva_paquete.*,
                    paquete_turistico.nombre,
                    paquete_turistico.imagen,
                    paquete_turistico.dias
                FROM reserva_paquete
                INNER JOIN paquete_turistico ON reserva_paquete.Id_paquete = paquete_turistico.Id_paquete
                WHERE reserva_paquete.Id_user = $id_user
                ORDER BY reserva_paquete.Id_reserva_paquete DESC";

        $resul = $this->travelnow1->query($sql);

        if (!$resul) {
            return [];
        }

        return $resul->fetch_all(MYSQLI_ASSOC);
    }

    public function GetReservasPaqueteHost()
    {
        return $this->GetReservasPaqueteHostFiltradas('', '', '');
    }

    public function GetReservasPaqueteHostFiltradas($estado, $desde, $hasta)
    {
        $where = [];

        if (!empty($estado)) {
            $estado = $this->travelnow1->real_escape_string($estado);
            $where[] = "reserva_paquete.estado_reserva = '$estado'";
        }

        if (!empty($desde)) {
            $desde_sql = $this->travelnow1->real_escape_string($desde);
            $where[] = "reserva_paquete.fecha_fin >= '$desde_sql'";
        }

        if (!empty($hasta)) {
            $hasta_sql = $this->travelnow1->real_escape_string($hasta);
            $where[] = "reserva_paquete.fecha_inicio <= '$hasta_sql'";
        }

        $sql = "SELECT
                    reserva_paquete.Id_reserva_paquete,
                    reserva_paquete.fecha_inicio,
                    reserva_paquete.fecha_fin,
                    reserva_paquete.estado_reserva,
                    reserva_paquete.monto_total,
                    reserva_paquete.metodo_pago,
                    reserva_paquete.Id_paquete,
                    paquete_turistico.nombre,
                    user.nombre AS nombre_usuario,
                    user.apellido,
                    user.correo
                FROM reserva_paquete
                INNER JOIN paquete_turistico ON reserva_paquete.Id_paquete = paquete_turistico.Id_paquete
                INNER JOIN user ON reserva_paquete.Id_user = user.Id_user";

        if (count($where) > 0) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY reserva_paquete.fecha_inicio DESC";

        $resul = $this->travelnow1->query($sql);

        if (!$resul) {
            return [
                'upcoming' => [],
                'past' => [],
                'rejected' => []
            ];
        }

        $datos = $resul->fetch_all(MYSQLI_ASSOC);
        $hoy = date('Y-m-d');
        $agrupadas = [
            'upcoming' => [],
            'past' => [],
            'rejected' => []
        ];

        foreach ($datos as $item) {
            $item['tipo_reserva'] = 'paquete';
            $item['fecha_ingreso'] = $item['fecha_inicio'];
            $item['fecha_salida'] = $item['fecha_fin'];
            $item['servicio_especial'] = 'Paquete turistico';
            $item['precio'] = $item['monto_total'] ?? 0;
            $item['ubicacion'] = 'Paquete #' . (int) ($item['Id_paquete'] ?? 0);
            $item['id_habitacion'] = '-';
            $item['tipo_habitacion'] = 'paquete';

            if ($item['estado_reserva'] === 'rechazada' || $item['estado_reserva'] === 'cancelada') {
                $agrupadas['rejected'][] = $item;
            } elseif (($item['fecha_fin'] ?? '') >= $hoy) {
                $agrupadas['upcoming'][] = $item;
            } else {
                $agrupadas['past'][] = $item;
            }
        }

        return $agrupadas;
    }

    private function OrdenarReservasPorFechaDesc(array $items)
    {
        usort($items, function ($a, $b) {
            return strtotime($b['fecha_ingreso'] ?? '') <=> strtotime($a['fecha_ingreso'] ?? '');
        });

        return $items;
    }

    public function CombinarReservasHost(array $habitaciones, array $paquetes)
    {
        return [
            'upcoming' => $this->OrdenarReservasPorFechaDesc(array_merge($habitaciones['upcoming'] ?? [], $paquetes['upcoming'] ?? [])),
            'past' => $this->OrdenarReservasPorFechaDesc(array_merge($habitaciones['past'] ?? [], $paquetes['past'] ?? [])),
            'rejected' => $this->OrdenarReservasPorFechaDesc(array_merge($habitaciones['rejected'] ?? [], $paquetes['rejected'] ?? [])),
        ];
    }

    public function UpdateEstadoReservaPaquete($id_reserva_paquete, $estado)
    {
        $id_reserva_paquete = (int) $id_reserva_paquete;
        $estado = $this->travelnow1->real_escape_string(trim($estado));

        $sql = "UPDATE reserva_paquete
                SET estado_reserva = '$estado'
                WHERE Id_reserva_paquete = $id_reserva_paquete";

        return $this->travelnow1->query($sql);
    }

    public function NombresServiciosPorIds($ids_csv)
    {
        $ids_csv = trim((string) $ids_csv);

        if ($ids_csv === '') {
            return [];
        }

        $ids = array_filter(array_map('intval', explode(',', $ids_csv)));

        if (count($ids) === 0) {
            return [];
        }

        $lista = implode(',', $ids);
        $sql = "SELECT nombre, precio
                FROM servicio_adicional
                WHERE Id_servicio IN ($lista)";

        $resul = $this->travelnow1->query($sql);

        if (!$resul) {
            return [];
        }

        return $resul->fetch_all(MYSQLI_ASSOC);
    }
}
