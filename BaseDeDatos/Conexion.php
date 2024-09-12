<?php

function conectar (){
    $sever ="localhost";
    $user ="root";
    $password= "";
    $database= "pokedex";
    $conexion = mysqli_connect($sever,$user,$password) or die("No se pudo conectar").mysqli_error();
    mysqli_select_db($database,$conexion);
    return $conexion;
}



