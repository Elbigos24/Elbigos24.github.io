<?php

$conexion="localhost";
$port="3306";
$user="root";
$password="";
$nam_bd="mariscos";
$con=new mysqli($conexion,$user,$password,$nam_bd);
if($con->connect_error){
    die("no se pudo conectar la BD ");
}
?>