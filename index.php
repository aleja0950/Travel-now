<?php
// Inicia la sesión (necesario para usar $_SESSION)
session_start();

// Se incluyen los controladores del sistema
require_once "controller/usercontroller.php";
require_once "controller/authcontroller.php";
require_once "controller/reservacontroller.php";
require_once "controller/searchcontroller.php";
require_once "controller/propiedadcontroller.php";
require_once "controller/mapcontroller.php";

// Se obtienen el controlador y la acción desde la URL (GET)
// Si no existen, se asigna null
$controller = $_GET['controller'] ?? null;
$action     = $_GET['action'] ?? null;

// Valores por defecto
$controller = $controller ?? 'login';
$action     = $action ?? 'login';

// Verifica si el usuario NO ha iniciado sesión
if (!isset($_SESSION['user'])) {

    // Si no hay sesión, siempre se envía al login
    $controller = 'login';
    $action     = 'login';
} else {

    // Si hay sesión iniciada, se envía al módulo de usuario
    $controller = $controller ?? "user";
    $action     = $action ?? "index";
}

// Se decide qué controlador instanciar según el valor recibido
switch ($controller) {

    case 'user':
        // Instancia el controlador de usuarios
        $controller = new usercontroller();
        break;

    case 'login':
        // Instancia el controlador de autenticación
        $controller = new authcontroller();
        break;

    case 'reserva':
        // Instancia el controlador de reservas
        $controller = new reservacontroller();
        break;

    case 'search':
        // Instancia el controlador de busqueda
        $controller = new searchcontroller();
        break;

    case 'propiedad':
        $controller = new propiedadcontroller();
        break;

    default:
        // Controlador por defecto si no coincide ninguno
        $controller = new usercontroller();
        break;

    case 'map':
        $controller = new mapcontroller();
        break;
}

// Verifica si el método (acción) existe en el controlador
if (method_exists($controller, $action)) {

    // Ejecuta el método solicitado
    $controller->$action();
} else {

    // Mensaje de error si la acción no existe o no está permitida
    echo "La acción no está permitida o no existe";
}
