<?php
require_once __DIR__ . "/../models/map.php";

class mapcontroller
{
    public function index()
    {
        $map = new map();
        $locations = $map->GetLocations();
        $errores = [];

        require_once __DIR__ . "/../views/admin/map.php";
    }

    public function guardar()
    {
        $map = new map();

        if ($_POST) {
            $name = $_POST['name'];
            $lat  = $_POST['latitude'];
            $lng  = $_POST['longitude'];

            $res = $map->CreateLocation($name, $lat, $lng);

            // 🔥 REDIRECCIÓN CON COORDENADAS
            header("location: index.php?controller=map&action=index&lat=$lat&lng=$lng");
            exit;
        }
    }

    public function actualizar()
    {
        $map = new map();

        if ($_POST) {
            $map->UpdateLocation(
                $_POST['id_map'],
                $_POST['name'],
                $_POST['latitude'],
                $_POST['longitude']
            );

            header("location: index.php?controller=map&action=index");
            exit;
        }
    }

    public function eliminar()
    {
        $map = new map();

        if ($_POST) {
            $map->DeleteLocation($_POST['id_map']);
        }

        header("location: index.php?controller=map&action=index");
        exit;
    }
}