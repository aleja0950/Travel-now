<?php
require_once __DIR__ . "/../config/config.php";

class search
{
    private $travelnow1;

    public function __construct()
    {
        $this->travelnow1 = Database::conectar();
    }

    public function index()
    {
        // Expone la ficha ampliada de cada alojamiento para que la vista
        // de busqueda muestre datos reales y no contenido generico.
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
                ORDER BY habitacion.Id_habitacion DESC";

        $resul = $this->travelnow1->query($sql);

        if (!$resul) {
            return [];
        }

        return $resul->fetch_all(MYSQLI_ASSOC);
    }
}
