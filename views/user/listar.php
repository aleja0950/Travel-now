<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- Permite que la página sea responsive -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Usuarios</title>
</head>
<body>

    <!-- Enlace para crear un nuevo usuario -->
    <a href="index.php?controller=user&action=crear">Crear</a>

    <!-- Enlace para cerrar sesión -->
    <a href="index.php?controller=login&action=logout">Cerrar</a>  

    <!-- Muestra el nombre del usuario autenticado -->
    <?= $_SESSION['user']; ?>

    <!-- Muestra el rol del usuario -->
    <?= $_SESSION['rol']; ?>

    <!-- Tabla de usuarios -->
    <table >
        <thead>
            <tr>
                <th>NOMBRE</th>
                <th>APELLIDO</th>
                <th>TELEFONO</th>
                <th>CORREO</th>
                <th>CONTRASEÑA</th>
                <th>ACCIONES</th>
            </tr>
        </thead>

        <tbody>
            <!-- Recorre el arreglo $datos que llega desde el controlador -->
            <?php foreach ($datos as $u): ?>
                <tr>
                    <!-- Muestra los datos del usuario -->
                    <td><?= $u['nombre'] ?></td>
                    <td><?= $u['apellido'] ?></td>
                    <td><?= $u['telefono'] ?></td>
                    <td><?= $u['correo'] ?></td>
                    <td><?= $u['contrasena'] ?></td>
                    <td>
                        
                        <!-- Enlace para editar el usuario -->
                        <a href="index.php?controller=user&action=editar&id=<?= $u['Id_user'] ?>">
                            Editar
                        </a>

                        <!-- Enlace para eliminar el usuario -->
                        <a href="index.php?controller=user&action=eliminar&id=<?= $u['Id_user'] ?>"
                           onclick="return confirm('¿Estás seguro de eliminar este usuario?')">
                            Eliminar
                        </a>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>

</body>
</html>