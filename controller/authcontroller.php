<?php
// Se incluye el modelo de autenticación
require_once __DIR__ . "/../models/auth.php";
require_once __DIR__ . "/../models/search.php";
require_once __DIR__ . "/../models/dashboard.php";

// Se define la clase authcontroller
class authcontroller
{

    // Método para iniciar sesión
    public function login()
    {

        // Verifica si se enviaron datos por POST (desde el formulario)
        if ($_POST) {

            // Se crea una instancia del modelo auth
            $model = new auth();

            // Se llama al método login del modelo enviando usuario y contraseña
            $login = $model->login($_POST['usuario'], $_POST['contrasena']);

            // Si el login es correcto
            if ($login) {

                $_SESSION['Id_user'] = $login['Id_user'];
                $_SESSION['user'] = $login['nombre'];
                $_SESSION['rol']  = $login['rol'];
                $_SESSION['correo'] = $login['correo'];
                $_SESSION['telefono'] = $login['telefono'];

                if ($_SESSION['rol'] === 'admin') {
                    header("location: index.php?controller=login&action=admin&msg=bienvenido");
                }

                if ($_SESSION['rol'] === 'user') {
                    header("location: index.php?controller=login&action=user&msg=sesion activa");
                }

                exit;
            } else {
                // Mensaje si los datos son incorrectos
                echo "Los datos ingresados no son correctos";
            }
        }

        // Carga la vista del formulario de login
        require_once __DIR__ . "/../views/auth/login.php";
    }

    // Método para cerrar sesión
    public function logout()
    {
        // Destruye todas las variables de sesión
        session_destroy();

        // Redirecciona a la página principal
        header("location: index.php");
    }

    // Método para mostrar el dashboard del administrador
    public function admin()
    {
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            header("location: index.php?controller=login&action=login");
            exit;
        }

        $dashboard = new dashboard();
        $resumen = $dashboard->GetResumen();

        require_once __DIR__ . "/../views/admin/dashboard.php";
    }
    // Método para mostrar la vista del user
    public function user()
    {
        $search = new search();
        $datos = $search->index();
        require_once __DIR__ . "/../user/home.php";
    }
    
}
