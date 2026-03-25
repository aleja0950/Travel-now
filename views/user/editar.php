<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>editar</title>
</head>
<body>
    <form action=""method='POST'>
<form action="" method="post">
    <input type="text"name="Id_user" placeholder="" value="<?=$datos['Id_user']?>">
    <input type="text" name="nombre" value="<?=$datos['nombre']?>">
     <input type="text" name="apellido" value="<?=$datos['apellido']?>">
      <input type="number" name="telefono" value="<?=$datos['telefono']?>">
       <input type="email" name="correo" value="<?=$datos['correo']?>">
        <input type="text" name="contraseña" value="<?=$datos['contrasena']?>">
        <input type="submit" value="enviar" >
</form>
</form>
</body>
</html>

         <p id="mensaje" style="color:green;"></p>
    </form>
        <script>
            function mostrarMensaje(){
            document.getElementById("mensaje").innerHTML = "Registro exitoso";
            }
</script>