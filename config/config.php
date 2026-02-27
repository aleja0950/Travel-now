
<?php
class Database {
    public static function conectar(){
        $conexion= new mysqli(
            "localhost",
            "root",
            "",
            "travelnow1"
        );
        if($conexion ->connect_errno){
            die($conexion ->connect_error);
        }
        return $conexion;
    }
}
// conexiones con las bases de datos