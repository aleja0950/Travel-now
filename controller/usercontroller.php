<?php
require_once __DIR__ ."/../models/user.php";
 class usercontroller{

  public function index(){
  $user= new user();
  $datos= $user->index();

// Carga la vista que lista los usuarios
require_once __DIR__ ."/../views/user/listar.php";
}

/**
 * Método para crear un nuevo usuario
 */
public function crear() {

  if ($_POST){

    $user = new user();

    $u = $user->save(
      $_POST['nombre'],
      $_POST['apellido'],
      $_POST['telefono'],
      $_POST['correo'],
      password_hash($_POST['contrasena'], PASSWORD_DEFAULT)
    
    );

 if(!$u){
    die("Error al guardar");
} 

 if ($_SESSION['rol'] === 'admin'){
                    // Redirección al panel de administrador
                    header("location: index.php?controller=login&action=admin");
                }

                 if ($_SESSION['rol'] === 'user'){
                    // Redirección al panel del usuario normal
                    header("location: index.php?controller=user&action=user");
                 }
  }

  

  require_once __DIR__."/../views/user/crear.php";
}


/**
 * Método para editar un usuario existente
 */
public function editar() {

  // Se crea una instancia del modelo user
  $user = new user();

  // Si se envían datos por POST (formulario de edición)
  if ($_POST){

    // Se actualizan los datos del usuario
    $u = $user->update(
      $_POST['Id_user'],
      $_POST['nombre'],
      $_POST['apellido'],
      $_POST['telefono'],
      $_POST['correo'],
      $_POST['contrasena']
    );

    // Redirecciona a la lista de usuarios
    header("Location:index.php?controller=user&action=index");
  }

  // Obtiene los datos del usuario por su ID (enviado por GET)
  $datos = $user->GetById($_GET['id']);

  // Carga la vista del formulario de edición
  require_once __DIR__."/../views/user/editar.php";
}

/**
 * Método para eliminar un usuario
 */
public function eliminar(){

  // Se crea una instancia del modelo user
  $user = new user();

  // Se elimina el usuario según el ID recibido por GET
  $u = $user->delete($_GET['id']);

  // Redirecciona a la lista de usuarios
  header("Location: index.php?controller=user&action=index");
}

       public function admin(){
        // Carga la vista del panel de administrador
        require_once __DIR__."/../views/auth/login.php";
    }
     public function user(){
        // Carga la vista del panel de user
        require_once __DIR__."/../views/auth/login.php";
    }
}
