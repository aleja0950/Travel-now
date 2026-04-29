<?php
session_start();
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
                <img id="profilePhoto" src="../public/css/img/user.jpg" alt="Profile Photo">
            </label>
            <input type="file" id="photoInput" accept="image/*" style="display:none">


            <h3>Verificacion de identidad</h3>
            <p>Administrador del Hotel palmar</p>
            <p>Usuario: <?= $_SESSION['user'] ?? ''; ?></p>
            <p>Rol: <?= $_SESSION['rol'] ?? ''; ?></p>
            <p>Correo: <?= $_SESSION['correo'] ?? ''; ?></p>
            <p>Telefono: <?= $_SESSION['telefono'] ?? ''; ?></p>
            <div class="details">
            <a href="../index.php?controller=user&action=editar&id=<?= $_SESSION['Id_user'] ?>">
                    <button class="edit-btn">Editar Perfil</button></a>

                <div class="divider"></div>

                <p><strong>⭐ Puntaje</strong></p>

                <div class="divider"></div>

            </div>

</body>

</html>