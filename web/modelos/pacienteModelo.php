<?php
if(is_dir("../modelos")){
    require_once "../modelos/loginModelo.php" ;
} else{
    require_once "../../modelos/loginModelo.php" ;
}
$rol=$_SESSION['rol'];
if($rol=='Paciente'){
    require_once "../conexion/db.php";
}  else if($rol=='Admin'){
    require_once "../../conexion/db.php";
}


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Paciente
{
    public $dni;
    public $nombre;
    public $password;
    public $email;
    public $mysqli;
    public $edad;
    public $telefono;
    public $accion="Paciente";

    function __construct($dni = "", $nombre = "", $password = "", $email = "",$edad='', $telefono='')
    {

        $this->dni = $dni;
        $this->nombre = $nombre;
        $this->password = $password;
        $this->email = $email;
        $this->edad = $edad;
        $this->telefono = $telefono;
        $con = new Connection();
        $this->mysqli = $con->con();
    }

    function crearPaciente()
    {
        $sql = "SELECT * from usuario where dni = '{$this->dni}'";
        $resultado = $this->mysqli->query($sql);
        if (mysqli_num_rows($resultado) == 0) {
            $sql = "INSERT INTO usuario (dni, nombre,password, email) VALUES ('{$this->dni}','{$this->nombre}','{$this->password}','{$this->email}','{$this->edad}','{$this->telefono}')";
            $resultado = $this->mysqli->query($sql);
            return "exito";
        } else {
            return "El usuario ya existe";
        }
    }
    function modificarPaciente()
    {
        if($_SESSION['rol']=='Paciente'){
           $dni=$_SESSION['dni'];
            $sql = "select * from usuario where dni = '{$dni}'";
        $resultado = $this->mysqli->query($sql);
        if (mysqli_num_rows($resultado) != 0) {
            $sql = "UPDATE usuario SET nombre = '{$_POST['nombre-modificarPaciente']}', 
            password = '{$_POST['password-modificarPaciente']}', email = '{$_POST['mail-modificarPaciente']}'
            , fecha_nacimiento = '{$_POST['fecha-modificarPaciente']}' , telefono_movil = '{$_POST['telefono-modificarPaciente']}'
            WHERE dni= '{$dni}'"; 
            $resultado = $this->mysqli->query($sql);
            return "exito";
        } else {
            return "No existe el Paciente";
        }
        }
        else{
            $sql = "select * from usuario where dni = '{$this->dni}'";
            $resultado = $this->mysqli->query($sql);
            if (mysqli_num_rows($resultado) != 0) {
                $sql = "UPDATE usuario SET nombre = '{$this->nombre}', 
                password = '{$this->password}', email = '{$this->email}'
                , fecha_nacimiento = '{$this->edad}' , telefono_movil = '{$this->telefono}'
                WHERE dni= '{$this->dni}'";
                $resultado = $this->mysqli->query($sql);
                return "exito";
            } else {
                return "No existe el Paciente";
            }
        }

    }
    function eliminarPaciente()
    {
        $sql = "select * from usuario where dni = '{$this->dni}'";
        $resultado = $this->mysqli->query($sql); //COMPARAR CON LA SESION PARA NO PODER BORRARSE A SI MISMO
        if (mysqli_num_rows($resultado) != 0) {
            $sql = "DELETE FROM usuario WHERE dni = '{$this->dni}'";
            $resultado = $this->mysqli->query($sql);
            return "exito";
        } else {
            return "No existe el Paciente";
        }
    }
    function mostrarPaciente(){
        if(!isset($_POST['dni'])){
            $this->dni=$_SESSION['dni'];
        }  
        $sql ="SELECT * from usuario where dni = '{$this->dni}'";
        $resultado=$this->mysqli->query($sql);
        if(mysqli_num_rows($resultado)==0){
            return "No existe el paciente";
        }
        $datos=array();
        while ($row = $resultado->fetch_assoc()) {
            $datos[] = $row;
        }
        return $datos;
    }
    function mostrarTodoPaciente(){
        $sql="SELECT * from usuario;";
        $resultado=$this->mysqli->query($sql);
        $fechas=array();
        while ($row = $resultado->fetch_assoc()) {
            $fechas[] = $row;
        }
        foreach ($fechas as $key => $value) {

            $editar = array("E" => "<button type='button' onclick='rellenarModal(this.id,`{$this->accion}`)' class='btn btn-sm btn-link edicion' id='{$value['dni']}' data-bs-toggle='modal' data-bs-target='#editarModal' accion='{$this->accion}'><i class='bx bxs-edit'></i></button>");
            $eliminar = array("B" => "<button type='button' onclick='eliminar(this.id,`{$this->accion}`)' class='btn btn-sm btn-link edicion' id='{$value['dni']}' accion='{$this->accion}'><i class='bx bxs-trash' ></i></button>");
            $fechas[$key] = array_merge($eliminar, $editar, $value);
        }  
        $columns = array_keys($fechas[0]);
        

        $i = 0;

        $visibleColumns = ['dni','nombre','email', 'fecha_nacimiento','telefono_movil','E','B'];
        $bool = false;

        foreach ($columns as $key => $value) {

            $bool = (in_array($value, $visibleColumns)) ? true : false;

            $columns[$key] = array('data' => $value);
            $columnsDefs[] = array('title' => $value, 'targets' => $i, 'visible' => $bool, 'searchable' => $bool);
            $i++;
        }

        $datos = array(
            'data' => $fechas,
            'columns' => $columns,
            'columnsDefs' => $columnsDefs,
            // 'combos' => $this->combos
        );
        return $datos;
    }

}
