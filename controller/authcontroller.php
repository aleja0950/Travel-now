<?php
// Se incluye el modelo de autenticación
require_once __DIR__ ."/../models/auth.php";

// Se define la clase authcontroller
class authcontroller{

    // Método para iniciar sesión
    public function login(){

        // Verifica si se enviaron datos por POST (desde el formulario)
        if($_POST){

            // Se crea una instancia del modelo auth
            $model = new auth();

            // Se llama al método login del modelo enviando usuario y contraseña
            $login = $model->login($_POST['usuario'], $_POST['contrasena']);

            // Si el login es correcto
            if($login){

                // Se guardan datos del usuario en la sesión
                $_SESSION['user'] = $login['nombre'];
                $_SESSION['rol']  = $login['rol'];

                // Se verifica el rol del usuario
                if ($_SESSION['rol'] === 'admin'){
                    // Redirección al panel de administrador
                    header("location: index.php?controller=login&action=admin&msg=bienvenido");
                }

                 if ($_SESSION['rol'] === 'user'){
                    // Redirección al panel del usuario normal
                    header("location: index.php?controller=login&action=user&msg=sesion activa");
                 }

                // Detiene la ejecución del script después de redirigir
                exit;

            } else {
                // Mensaje si los datos son incorrectos
                echo "Los datos ingresados no son correctos";
            }
        }

        // Carga la vista del formulario de login
        require_once __DIR__."/../views/auth/login.php";
    }

    // Método para cerrar sesión
    public function logout(){
        // Destruye todas las variables de sesión
        session_destroy();

        // Redirecciona a la página principal
        header("location: index.php");
    }

    // Método para mostrar la vista del administrador
    public function admin(){
        // Carga la vista del panel de administrador
        require_once __DIR__."/../Admin/hostpage.php";
    }
     // Método para mostrar la vista del user
    public function user(){
        // Carga la vista del panel de user
        require_once __DIR__."/../user/home.php";
    }
}
