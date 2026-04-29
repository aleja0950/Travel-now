<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="user.css/style1.css">
    <title>Registro</title>
</head>
<body>
    <!-- Mensaje de Errores -->
    <?php if (!empty($errores)): ?>
        <div class="errores">
            <ul>
               <?php foreach($errores as $e):?>
                <li> <?= $e ?></li>
                <?php endforeach ?>
            </ul>
        </div>
    <?php endif ?>

    
     <form action="" method="post" onsubmit="mostrarMensaje()">
        <p>Si ya tienes una cuenta <a href="views/auth/login.php">Iniciar sesión</a></p>
        <label>Nombre</label>
        <input type="text" pattern="[A-Za-z\s]+" name="nombre" required>

        <label>Apellido</label>
        <input type="text" pattern="[A-Za-z\s]+" name="apellido" required>

        <label>Telefono</label>
        <input type="number" name="telefono" required>

        <label>Correo</label>
        <input type="email" name="correo" required>

        <label>contraseña</label>
        <input type="password" minlength="8" name="contrasena" required>

        <input type="submit" value="Crear cuenta">
    </form>
        
         <p id="mensaje" style="color:green;"></p>
    </form>
        <script>
            function mostrarMensaje(){
            document.getElementById("mensaje").innerHTML = "Registro exitoso";
            }
</script>
</body>
</html>