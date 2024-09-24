<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="estilos/detalles.css">
    <link rel="icon" href="img/pokebolaLogo.png" type="image/png">
    <title>Detalles</title>
</head>
<body>

<?php require_once 'header.php';?>

<!-- Descripcion de pokemones -->

<?php
include("BaseDeDatos/Database.php");

$conexion = new database();

if(isset($_GET['id'])){
    $id = $_GET['id'];
    $sql = "SELECT p.nombre, p.numero, p.imagen, p.descripcion, 
            GROUP_CONCAT(t.nombre_p SEPARATOR ',') as tipos
            FROM pokemon p
            JOIN pokemon_tipo pt ON p.id = pt.pokemon_id
            JOIN tipo t ON pt.tipo_id = t.id
            WHERE p.id = $id
            GROUP BY p.id";
    $resultado = $conexion->query($sql);
    $pokemon = $resultado->fetch_assoc();//convierte a datos manejables

    echo "<div class='contenedor'>";
    if ($pokemon) {
        // Mostrar los detalles del Pokémon
        echo '<div class="pokemon">';
        echo '<h1>' . $pokemon['nombre'] . '</h1>';
        echo '<div class="imagen">';
        echo '<img id="imgPokemon" src="img/pokemon/' . $pokemon['imagen'] . '" alt="' . $pokemon['nombre'] . '">';
        echo '</div>';
        echo '<div class="tipo">';
        $tipos = explode(',', $pokemon["tipos"]);
        foreach ($tipos as $tipo) {
            echo '<img id="imgTipo" src="img/TipoPokemon/' . "tipo_".strtolower($tipo) . '.png" class="pokemon-type">';
        }
        echo '</div>';
        echo '<div class="poke_info">';
        echo '<p id="descripcion">Descripción: ' . $pokemon['descripcion'] . '</p>';
        echo '</div>';
        echo '</div>';
    }
    else {
        echo "<p>Pokémon no encontrado.</p>";
    }
}
echo '</div>';
?>

<?php require_once 'footer.php';?>

</body>
</html>
