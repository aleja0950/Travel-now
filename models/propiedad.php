<?php
require_once __DIR__ . "/../config/config.php";

class propiedad
{
    private $travelnow1;

    public function __construct()
    {
        $this->travelnow1 = Database::conectar();
    }

    public function GetImagenesDisponibles()
    {
        return [
            'habitacion1.jpg',
            'habitacion2.png',
            'imagen3.jpg',
            'piso1.jpg',
            'piso2.jpg',
            'piso3.jpg',
            'hotel1.jpg',
            '1246280_16061017110043391702.jpg'
        ];
    }

    private function EsImagenValida($imagen)
    {
        // Permite seguir usando imagenes predefinidas y tambien
        // las rutas nuevas que se suben al directorio uploads.
        if (in_array($imagen, $this->GetImagenesDisponibles(), true)) {
            return true;
        }

        return preg_match('/^uploads\/[A-Za-z0-9._-]+$/', $imagen) === 1;
    }

    public function GetPropiedades()
    {
        // Trae la ficha ampliada de cada propiedad para reutilizarla
        // tanto en admin como en las vistas del usuario.
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

    public function GetPropiedadByHabitacion($id_habitacion)
    {
        $id_habitacion = (int) $id_habitacion;

        // Devuelve una sola propiedad con todos los campos necesarios
        // para rellenar el formulario de edicion en admin.
        $sql = "SELECT
                    habitacion.Id_habitacion,
                    habitacion.Id_empresa,
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
                WHERE habitacion.Id_habitacion = $id_habitacion
                LIMIT 1";

        $resul = $this->travelnow1->query($sql);

        if (!$resul || $resul->num_rows === 0) {
            return null;
        }

        return $resul->fetch_assoc();
    }

    private function ValidarPropiedad($nombre, $ubicacion, $tipo_habitacion, $precio, $estado, $imagen, $descripcion_texto, $capacidad, $banos, $cupos, $servicios)
    {
        // Centraliza las validaciones del formulario para que crear
        // y actualizar compartan exactamente las mismas reglas.
        $errores = [];

        if ($nombre === '') {
            $errores[] = "el nombre es obligatorio";
        }

        if ($ubicacion === '') {
            $errores[] = "la ubicacion es obligatoria";
        }

        if ($tipo_habitacion <= 0) {
            $errores[] = "el tipo de habitacion no es valido";
        }

        if ($precio <= 0) {
            $errores[] = "el precio debe ser mayor a 0";
        }

        if ($estado === '') {
            $errores[] = "el estado es obligatorio";
        }

        if (!$this->EsImagenValida($imagen)) {
            $errores[] = "la imagen seleccionada no es valida";
        }

        if ($descripcion_texto === '') {
            $errores[] = "la descripcion es obligatoria";
        }

        if ($capacidad <= 0) {
            $errores[] = "la capacidad debe ser mayor a 0";
        }

        if ($banos <= 0) {
            $errores[] = "la cantidad de banos debe ser mayor a 0";
        }

        if ($cupos <= 0) {
            $errores[] = "los cupos deben ser mayores a 0";
        }

        if ($servicios === '') {
            $errores[] = "los servicios son obligatorios";
        }

        return $errores;
    }

    public function CrearPropiedad($nombre, $ubicacion, $tipo_habitacion, $precio, $estado, $imagen, $descripcion_texto, $capacidad, $banos, $cupos, $servicios)
    {
        // Crea la empresa, la habitacion y el precio dentro del mismo flujo
        // para publicar una propiedad completa desde admin.
        $nombre = trim($nombre);
        $ubicacion = trim($ubicacion);
        $tipo_habitacion = (int) $tipo_habitacion;
        $precio = (int) $precio;
        $estado = trim($estado);
        $imagen = trim($imagen);
        $descripcion_texto = trim($descripcion_texto);
        $capacidad = (int) $capacidad;
        $banos = (int) $banos;
        $cupos = (int) $cupos;
        $servicios = trim($servicios);
        $errores = $this->ValidarPropiedad($nombre, $ubicacion, $tipo_habitacion, $precio, $estado, $imagen, $descripcion_texto, $capacidad, $banos, $cupos, $servicios);

        if (count($errores) > 0) {
            return $errores;
        }

        $nombre = $this->travelnow1->real_escape_string($nombre);
        $ubicacion = $this->travelnow1->real_escape_string($ubicacion);
        $estado = $this->travelnow1->real_escape_string($estado);
        $imagen = $this->travelnow1->real_escape_string($imagen);
        $descripcion_texto = $this->travelnow1->real_escape_string($descripcion_texto);
        $servicios = $this->travelnow1->real_escape_string($servicios);

        $this->travelnow1->begin_transaction();

        try {
            $sql = "INSERT INTO empresa(nombre, ubicacion)
                    VALUES('$nombre', '$ubicacion')";
            $resul = $this->travelnow1->query($sql);

            if (!$resul) {
                throw new Exception("no fue posible crear la empresa");
            }

            $id_empresa = (int) $this->travelnow1->insert_id;

            $sql = "INSERT INTO habitacion(Id_empresa, tipo_habitacion, imagen, descripcion_texto, capacidad, banos, cupos, servicios)
                    VALUES($id_empresa, $tipo_habitacion, '$imagen', '$descripcion_texto', $capacidad, $banos, $cupos, '$servicios')";
            $resul = $this->travelnow1->query($sql);

            if (!$resul) {
                throw new Exception("no fue posible crear la habitacion");
            }

            $id_habitacion = (int) $this->travelnow1->insert_id;

            $sql = "INSERT INTO precio(Id_habitacion, precio, estado)
                    VALUES($id_habitacion, $precio, '$estado')";
            $resul = $this->travelnow1->query($sql);

            if (!$resul) {
                throw new Exception("no fue posible crear el precio");
            }

            $this->travelnow1->commit();
            return true;
        } catch (Exception $e) {
            $this->travelnow1->rollback();
            return [$e->getMessage()];
        }
    }

    public function ActualizarPropiedad($id_habitacion, $nombre, $ubicacion, $tipo_habitacion, $precio, $estado, $imagen, $descripcion_texto, $capacidad, $banos, $cupos, $servicios)
    {
        // Mantiene sincronizados los datos repartidos entre empresa,
        // habitacion y precio cuando el admin edita una propiedad.
        $id_habitacion = (int) $id_habitacion;
        $actual = $this->GetPropiedadByHabitacion($id_habitacion);

        if (!$actual) {
            return ["la propiedad no existe"];
        }

        $nombre = trim($nombre);
        $ubicacion = trim($ubicacion);
        $tipo_habitacion = (int) $tipo_habitacion;
        $precio = (int) $precio;
        $estado = trim($estado);
        $imagen = trim($imagen);
        $descripcion_texto = trim($descripcion_texto);
        $capacidad = (int) $capacidad;
        $banos = (int) $banos;
        $cupos = (int) $cupos;
        $servicios = trim($servicios);
        $errores = $this->ValidarPropiedad($nombre, $ubicacion, $tipo_habitacion, $precio, $estado, $imagen, $descripcion_texto, $capacidad, $banos, $cupos, $servicios);

        if (count($errores) > 0) {
            return $errores;
        }

        $nombre = $this->travelnow1->real_escape_string($nombre);
        $ubicacion = $this->travelnow1->real_escape_string($ubicacion);
        $estado = $this->travelnow1->real_escape_string($estado);
        $imagen = $this->travelnow1->real_escape_string($imagen);
        $descripcion_texto = $this->travelnow1->real_escape_string($descripcion_texto);
        $servicios = $this->travelnow1->real_escape_string($servicios);
        $id_empresa = (int) $actual['Id_empresa'];

        $this->travelnow1->begin_transaction();

        try {
            $sql = "UPDATE empresa
                    SET nombre = '$nombre',
                        ubicacion = '$ubicacion'
                    WHERE id_alojamieto = $id_empresa";
            if (!$this->travelnow1->query($sql)) {
                throw new Exception("no fue posible actualizar la empresa");
            }

            $sql = "UPDATE habitacion
                    SET tipo_habitacion = $tipo_habitacion,
                        imagen = '$imagen',
                        descripcion_texto = '$descripcion_texto',
                        capacidad = $capacidad,
                        banos = $banos,
                        cupos = $cupos,
                        servicios = '$servicios'
                    WHERE Id_habitacion = $id_habitacion";
            if (!$this->travelnow1->query($sql)) {
                throw new Exception("no fue posible actualizar la habitacion");
            }

            $sql = "UPDATE precio
                    SET precio = $precio,
                        estado = '$estado'
                    WHERE Id_habitacion = $id_habitacion";
            if (!$this->travelnow1->query($sql)) {
                throw new Exception("no fue posible actualizar el precio");
            }

            $this->travelnow1->commit();
            return true;
        } catch (Exception $e) {
            $this->travelnow1->rollback();
            return [$e->getMessage()];
        }
    }

    public function EliminarPropiedad($id_habitacion)
    {
        $id_habitacion = (int) $id_habitacion;
        $actual = $this->GetPropiedadByHabitacion($id_habitacion);

        if (!$actual) {
            return ["la propiedad no existe"];
        }

        $sql = "SELECT COUNT(*) AS total
                FROM reserva
                WHERE id_habitacion = $id_habitacion";
        $resul = $this->travelnow1->query($sql);
        $total_reservas = 0;

        if ($resul && $resul->num_rows > 0) {
            $total_reservas = (int) (($resul->fetch_assoc())['total'] ?? 0);
        }

        if ($total_reservas > 0) {
            return ["no puedes eliminar una propiedad que ya tiene reservas asociadas"];
        }

        $id_empresa = (int) $actual['Id_empresa'];
        $this->travelnow1->begin_transaction();

        try {
            if (!$this->travelnow1->query("DELETE FROM precio WHERE Id_habitacion = $id_habitacion")) {
                throw new Exception("no fue posible eliminar el precio");
            }

            if (!$this->travelnow1->query("DELETE FROM habitacion WHERE Id_habitacion = $id_habitacion")) {
                throw new Exception("no fue posible eliminar la habitacion");
            }

            if (!$this->travelnow1->query("DELETE FROM empresa WHERE id_alojamieto = $id_empresa")) {
                throw new Exception("no fue posible eliminar la empresa");
            }

            $this->travelnow1->commit();
            return true;
        } catch (Exception $e) {
            $this->travelnow1->rollback();
            return [$e->getMessage()];
        }
    }
}
