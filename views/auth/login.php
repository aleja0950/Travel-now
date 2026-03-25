
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>User</title>
    <style>
        /* ==============================
   RESET BÁSICO
   ============================== */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, Helvetica, sans-serif;
}

/* ==============================
   ESTILOS GENERALES DEL BODY
   ============================== */
body {
    min-height: 100vh;
    background: linear-gradient(135deg, #ffffe6);
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ==============================
   FORMULARIO
   ============================== */
form {
    background-color: #fffeb4;
    width: 350px;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

/* ==============================
   TÍTULO
   ============================== */
form p {
    text-align: center;
    font-size: 22px;
    font-weight: bold;
    margin-bottom: 20px;
    text-transform: capitalize;
    color: #333;
}

/* ==============================
   LABELS
   ============================== */
label {
    display: block;
    margin-bottom: 5px;
    font-size: 14px;
    color: #555;
}

/* ==============================
   INPUTS
   ============================== */
input[type="text"],
input[type="password"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 18px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
    transition: border-color 0.3s, box-shadow 0.3s;
}

input:focus {
    outline: none;
    border-color: #4e73df;
    box-shadow: 0 0 5px rgba(78, 115, 223, 0.5);
}

/* ==============================
   BOTÓN
   ============================== */
button {
    width: 100%;
    padding: 12px;
    background-color: #93aeff;
    color: #000000;
    border: none;
    border-radius: 6px;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.2s;
}

button:hover {
    background-color: rgb(194, 200, 253);
    transform: scale(1.02);
}

/* ==============================
   RESPONSIVE
   ============================== */
@media (max-width: 400px) {
    form {
        width: 90%;
        padding: 20px;
    }
}
    </style>
</head>
<body>
    <form action="" method="post" onsubmit="mostrarMensaje()">
        <p>inicio</p>
       
        <label>usuario</label>
        <input type="text" pattern="[A-Za-z\s]+" name="usuario" required>
        <label >contraseña</label>
        <input type="password" id="contra"  minlength="8" name="contrasena"required>
       <button  type="submit">Ingresar</button>  
     
      <p id="mensaje" style="color:green;"></p>

        </form>

        <script>
            function mostrarMensaje(){
            document.getElementById("mensaje").innerHTML = "Iniciando sesión...";
            }
</script>
   
</body>
</html>