<?php
require_once __DIR__ . "/../config/config.php";

class dashboard
{
    private $travelnow1;

    public function __construct()
    {
        $this->travelnow1 = Database::conectar();
    }

    public function GetResumen()
    {
        return [
            'propiedades' => $this->ContarPropiedades(),
            'paquetes_activos' => $this->ContarPaquetesActivos(),
            'reservas_pendientes' => $this->ContarReservasPendientes(),
            'reservas_proximas' => $this->ContarReservasProximas(),
            'ingresos_pagados' => $this->SumarIngresosPagados(),
            'pagos_en_revision' => $this->ContarPagosEnRevision(),
            'total_reservas' => $this->ContarTotalReservas(),
            'movimientos' => $this->GetMovimientosRecientes(12),
        ];
    }

    private function ContarPropiedades()
    {
        $resul = $this->travelnow1->query("SELECT COUNT(*) AS total FROM habitacion");

        if (!$resul) {
            return 0;
        }

        return (int) (($resul->fetch_assoc())['total'] ?? 0);
    }

    private function ContarPaquetesActivos()
    {
        $resul = $this->travelnow1->query("SELECT COUNT(*) AS total FROM paquete_turistico WHERE estado = 'activo'");

        if (!$resul) {
            return 0;
        }

        return (int) (($resul->fetch_assoc())['total'] ?? 0);
    }

    private function ContarReservasPendientes()
    {
        $total = 0;
        $resul = $this->travelnow1->query("SELECT COUNT(*) AS total FROM reserva WHERE estado_reserva = 'pendiente'");

        if ($resul) {
            $total += (int) (($resul->fetch_assoc())['total'] ?? 0);
        }

        $resulPaquete = $this->travelnow1->query("SELECT COUNT(*) AS total FROM reserva_paquete WHERE estado_reserva = 'pendiente'");

        if ($resulPaquete) {
            $total += (int) (($resulPaquete->fetch_assoc())['total'] ?? 0);
        }

        return $total;
    }

    private function ContarReservasProximas()
    {
        $hoy = date('Y-m-d H:i:s');
        $total = 0;

        $resul = $this->travelnow1->query("SELECT COUNT(*) AS total
            FROM reserva
            WHERE estado_reserva NOT IN ('rechazada', 'cancelada')
            AND fecha_salida >= '$hoy'");

        if ($resul) {
            $total += (int) (($resul->fetch_assoc())['total'] ?? 0);
        }

        $hoyDate = date('Y-m-d');
        $resulPaquete = $this->travelnow1->query("SELECT COUNT(*) AS total
            FROM reserva_paquete
            WHERE estado_reserva NOT IN ('rechazada', 'cancelada')
            AND fecha_fin >= '$hoyDate'");

        if ($resulPaquete) {
            $total += (int) (($resulPaquete->fetch_assoc())['total'] ?? 0);
        }

        return $total;
    }

    private function ContarTotalReservas()
    {
        $total = 0;
        $resul = $this->travelnow1->query("SELECT COUNT(*) AS total FROM reserva");

        if ($resul) {
            $total += (int) (($resul->fetch_assoc())['total'] ?? 0);
        }

        $resulPaquete = $this->travelnow1->query("SELECT COUNT(*) AS total FROM reserva_paquete");

        if ($resulPaquete) {
            $total += (int) (($resulPaquete->fetch_assoc())['total'] ?? 0);
        }

        return $total;
    }

    private function SumarIngresosPagados()
    {
        $resul = $this->travelnow1->query("SELECT COALESCE(SUM(monto), 0) AS total
            FROM pago
            WHERE estado_pago = 'pagado'");

        if (!$resul) {
            return 0;
        }

        return (int) (($resul->fetch_assoc())['total'] ?? 0);
    }

    private function ContarPagosEnRevision()
    {
        $resul = $this->travelnow1->query("SELECT COUNT(*) AS total
            FROM pago
            WHERE estado_pago IN ('pendiente', 'en revision')");

        if (!$resul) {
            return 0;
        }

        return (int) (($resul->fetch_assoc())['total'] ?? 0);
    }

    public function GetMovimientosRecientes($limite = 12)
    {
        $limite = max(1, (int) $limite);
        $movimientos = [];

        $sqlReservas = "SELECT
                'habitacion' AS tipo,
                reserva.Id_reserva AS id_ref,
                reserva.fecha_ingreso AS fecha_evento,
                reserva.estado_reserva AS estado,
                reserva.servicio_especial AS detalle,
                empresa.nombre AS titulo,
                precio.precio AS monto
            FROM reserva
            INNER JOIN habitacion ON reserva.id_habitacion = habitacion.Id_habitacion
            INNER JOIN empresa ON habitacion.Id_empresa = empresa.id_alojamieto
            LEFT JOIN precio ON habitacion.Id_habitacion = precio.Id_habitacion
            ORDER BY reserva.fecha_ingreso DESC
            LIMIT $limite";

        $resul = $this->travelnow1->query($sqlReservas);

        if ($resul) {
            $movimientos = array_merge($movimientos, $resul->fetch_all(MYSQLI_ASSOC));
        }

        $sqlPaquetes = "SELECT
                'paquete' AS tipo,
                reserva_paquete.Id_reserva_paquete AS id_ref,
                CONCAT(reserva_paquete.fecha_inicio, ' 00:00:00') AS fecha_evento,
                reserva_paquete.estado_reserva AS estado,
                'Reserva de paquete turistico' AS detalle,
                paquete_turistico.nombre AS titulo,
                reserva_paquete.monto_total AS monto
            FROM reserva_paquete
            INNER JOIN paquete_turistico ON reserva_paquete.Id_paquete = paquete_turistico.Id_paquete
            ORDER BY reserva_paquete.Id_reserva_paquete DESC
            LIMIT $limite";

        $resulPaquete = $this->travelnow1->query($sqlPaquetes);

        if ($resulPaquete) {
            $movimientos = array_merge($movimientos, $resulPaquete->fetch_all(MYSQLI_ASSOC));
        }

        $sqlPagos = "SELECT
                'pago' AS tipo,
                pago.Id_pago AS id_ref,
                COALESCE(pago.fecha_pago, NOW()) AS fecha_evento,
                pago.estado_pago AS estado,
                pago.referencia AS detalle,
                COALESCE(empresa.nombre, paquete_turistico.nombre, 'Pago registrado') AS titulo,
                pago.monto AS monto
            FROM pago
            LEFT JOIN reserva ON pago.Id_reserva = reserva.Id_reserva
            LEFT JOIN habitacion ON reserva.id_habitacion = habitacion.Id_habitacion
            LEFT JOIN empresa ON habitacion.Id_empresa = empresa.id_alojamieto
            LEFT JOIN reserva_paquete ON pago.Id_reserva_paquete = reserva_paquete.Id_reserva_paquete
            LEFT JOIN paquete_turistico ON reserva_paquete.Id_paquete = paquete_turistico.Id_paquete
            ORDER BY pago.Id_pago DESC
            LIMIT $limite";

        $resulPago = $this->travelnow1->query($sqlPagos);

        if ($resulPago) {
            $movimientos = array_merge($movimientos, $resulPago->fetch_all(MYSQLI_ASSOC));
        }

        usort($movimientos, function ($a, $b) {
            return strtotime($b['fecha_evento'] ?? '') <=> strtotime($a['fecha_evento'] ?? '');
        });

        return array_slice($movimientos, 0, $limite);
    }
}
