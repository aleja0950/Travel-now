
<?php
define('API_URL', 'http://localhost/api/location');
class Database {
    public static function conectar(){
        $conexion= new mysqli(
            "localhost",
            "victor",
            "27Vmrs",
            "travelnow1"
        );
        if($conexion ->connect_errno){
            die($conexion ->connect_error);
            
        }
        return $conexion;
    }

}
// conexiones con las bases de datos