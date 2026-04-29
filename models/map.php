<?php
require_once __DIR__ . "/../config/config.php";

class map
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::conectar();
    }

    public function GetLocations()
    {
        $result = $this->conn->query("SELECT * FROM map");

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function GetLocationById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM map WHERE id_map=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function CreateLocation($name, $lat, $lng)
    {
        $stmt = $this->conn->prepare("INSERT INTO map (name, latitude, longitude) VALUES (?, ?, ?)");

        if (!$stmt) {
            return ["Error: " . $this->conn->error];
        }

        $stmt->bind_param("sdd", $name, $lat, $lng);

        return $stmt->execute() ? true : ["Error al guardar"];
    }

    public function UpdateLocation($id, $name, $lat, $lng)
    {
        $stmt = $this->conn->prepare("UPDATE map SET name=?, latitude=?, longitude=? WHERE id_map=?");

        $stmt->bind_param("sddi", $name, $lat, $lng, $id);

        return $stmt->execute() ? true : ["Error al actualizar"];
    }

    public function DeleteLocation($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM map WHERE id_map=?");

        $stmt->bind_param("i", $id);

        return $stmt->execute() ? true : ["Error al eliminar"];
    }
}