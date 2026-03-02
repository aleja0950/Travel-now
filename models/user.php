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

    #array
    $errores=[];
    if (strlen($nombre)<=5){
      $errores[]="el nombre debe tener más de 6 caracteres";
}

$sql= "SELECT* FROM user WHERE correo='$correo'";

$resul=$this->travelnow1->query($sql);

if ($resul->num_rows>0){
    $errores[]="correo ya existe";
}

if (count($errores)>0){
    return $errores;
}

$sql="INSERT INTO user(nombre, apellido, telefono, correo, contrasena)
VALUES('$nombre','$apellido','$telefono','$correo','$contrasena')";

$this->travelnow1->query($sql);
    
$id_user=$this->travelnow1->insert_id;

$sql2="INSERT INTO rol_user(Id_user, Id_rol) VALUES ($id_user,1)";

$resul=$this->travelnow1->query($sql2);

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
