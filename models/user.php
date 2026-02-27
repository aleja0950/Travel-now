<?php
require_once __DIR__.("/../config/config.php");
class user{
private $travelnow1;                     //seguridad/
public function __construct(){
    $this ->travelnow1=Database::conectar();
}
public function index(){
    $sql="SELECT* FROM user";

    $resul=$this->travelnow1->query($sql);

    return $resul->fetch_all(MYSQLI_ASSOC);

    }

public function save($nombre,$apellido,$telefono,$correo,$contrasena){

$sql="INSERT INTO user(nombre, apellido, telefono, correo, contrasena)
VALUES('$nombre','$apellido','$telefono','$correo','$contrasena')";

if(!$this->travelnow1->query($sql)){
    die("Error: " . $this->travelnow1->error);
}

return true;
}

  public function GetById($id){ 
              //buscar
    $sql ="SELECT* FROM user WHERE Id_user=$id";

        $resul= $this->travelnow1->query($sql);

        return $resul->fetch_assoc();
        
    
  }

  public function update ($id,$nombre,$apellido,$telefono,$correo,$contrasena){      //actualizar
    $sql="UPDATE user SET nombre='$nombre', apellido='$apellido',
     telefono='$telefono', correo='$correo', contrasena='$contrasena' 
    WHERE Id_user= $id";

 $resul=$this->travelnow1->query($sql);
    
  }
  public function delete($id){
  $sql="DELETE FROM user WHERE Id_user=$id";

   return $this->travelnow1->query($sql);
   }
}
