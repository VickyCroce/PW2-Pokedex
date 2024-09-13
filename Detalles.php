<?php
include("BaseDeDatos/Database.php");

/*$conexion = conectar();
echo "se realizo un conexion con la base de datos";*/

$pokemon = fetch();

if ($pokemon) {
    echo $pokemon['nombre'];
    echo $pokemon['imagen'];
    echo "Tipo: " . $pokemon['tipo'] . "<br>";
    echo "Descripción:". $pokemon['descripcion'];
} else {
    echo "<p>Pokémon no encontrado.</p>";
}
?>