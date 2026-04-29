<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>editar</title>
     <link rel="stylesheet" href="user.css/style1.css">
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

    <form action=""method='POST'>
<form action="" method="post">
    <input type="text"name="Id_user" placeholder="" value="<?=$datos['Id_user']?>">
    <input type="text" name="nombre" value="<?=$datos['nombre']?>">
     <input type="text" name="apellido" value="<?=$datos['apellido']?>">
      <input type="number" name="telefono" value="<?=$datos['telefono']?>">
       <input type="email" name="correo" value="<?=$datos['correo']?>">
        <input type="text" name="contrasena" value="<?=$datos['contrasena']?>">
        <input type="submit" value="enviar" >
        <p id="mensaje" style="color:green;"></p>
</form>
<script>
            function mostrarMensaje(){
            document.getElementById("mensaje").innerHTML = "Guardando datos...";
            }
</script>
</form>
 
</body>
</html>       