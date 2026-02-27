<?php
// Se incluye el archivo de configuración (conexión a la base de datos)
require_once __DIR__."/../config/config.php";

// Se define la clase Auth (modelo de autenticación)
class Auth {

    // Propiedad privada para almacenar la conexión a la base de datos
    private $travelnow1;

    // Constructor de la clase
    public function __construct() {

        // Se establece la conexión a la base de datos
        $this->travelnow1 = Database::conectar();
    }

    /**
     * Método para validar el inicio de sesión
     * @param string $user  Nombre de usuario
     * @param string $pass  Contraseña
     * @return array|false  Retorna los datos del usuario o false si falla
     */
    public function login($user, $pass){

        // Consulta SQL que obtiene el usuario junto con su rol
        $sql = "SELECT *
                FROM rol
                INNER JOIN rol_user ON rol.Id_rol = rol_user.id_rol
                INNER JOIN user ON rol_user.id_user = user.Id_user
                WHERE nombre = '$user' AND contrasena = '$pass'";

        // Se ejecuta la consulta
        $resul = $this->travelnow1->query($sql);

        // Si se encontró al menos un registro
        if($resul->num_rows > 0){

            // Se obtienen los datos del usuario en un arreglo asociativo
            $datos = $resul->fetch_assoc();

            // Se retornan los datos al controlador
            return $datos;
        }

        // Si no hay coincidencias, retorna false
        return false;
    }
}



