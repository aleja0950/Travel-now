
 <?php
 session_start()
 
 ?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>

    <!-- =======================
         HOJA DE ESTILOS
    ======================== -->
    <link rel="stylesheet" href="../user.css/style.css">
</head>
<body>

    <!-- =======================
         CONTENEDOR PRINCIPAL
    ======================== -->
    <div class="profile-container">

        <!-- =======================
             ENCABEZADO DEL PERFIL
        ======================== -->
        <div class="profile-header">

            <!-- Foto del usuario -->
            <img src="../public/css/img/user.jpg" alt="User Photo" class="profile-img">

            <!-- Nombre del usuario -->
            <h2> Usuario: <?= $_SESSION['user'] ?? ''; ?></h2>

            <!-- Email del usuario -->
            <p>correo:<?= $_SESSION['correo'] ?? ''; ?> </p>
        </div>

        <!-- =======================
             INFORMACIÓN PERSONAL
        ======================== -->
        <div class="profile-info">
            <h3>Información Personal</h3>

            <!-- Teléfono -->
            <p><strong>telefono:</strong><?= $_SESSION['telefono'] ?? ''; ?> </p>

            <!-- Rol del usuario -->
            <p><strong>Rol:</strong> <?= $_SESSION['rol'] ?? ''; ?></p>
        </div>

        <!-- =======================
             ACCIONES DEL PERFIL
        ======================== -->
        <div class="profile-actions">

            <!-- Botón editar -->
              <a href="../index.php?controller=user&action=editar&id=<?= $_SESSION['Id_user'] ?>">
               <button class="btn edit">Editar Perfil</button></a>

            <!-- Botón ingresar -->
            <button class="btn logout">Ingresar</button>
        </div>

    </div>

</body>
</html>
