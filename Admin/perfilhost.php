
 <?php
 session_start()

 
 
 
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../public/css/host.css">
    <title>Host Page</title>
</head>
<body>
      
<div class="profile-wrapper">

    <div class="profile-card">
<!-- imagen de usuario -->
        <label for="photoInput">
            <img id="profilePhoto" src="/img/user.jpg" alt="Profile Photo">
        </label>
        <input type="file" id="photoInput" accept="image/*" style="display:none">

        <div class="upload-text">Actualiza la foto</div>

        <h3>Verificacion de identidad</h3>
        <p>Administrador del Hotel palmar</p>
 <?= $_SESSION['user']; ?>
   <?= $_SESSION['rol']; ?>
 <?= $_SESSION['correo']; ?>

        <ul>
            <li>Email Confirmado</li>
            <li>Numero Confirmado</li>
        </ul>
    </div>

    <div class="details">
         <?= $_SESSION['user']; ?>
         
        <div class="subtext">Ingresado en 2025</div>
<a href="/html_Admin/edithost.html">
        <button class="edit-btn">Editar Perfil</button></a>

        <div class="divider"></div>

        <p><strong>⭐ Puntaje</strong></p>

        <div class="divider"></div>

</div>

</body>
</html>