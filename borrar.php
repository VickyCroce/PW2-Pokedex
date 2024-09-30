<?php
require_once 'BaseDeDatos/Database.php';
require_once 'pokedex.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    $pokedex = new Pokedex();
    $resultado = $pokedex->eliminarPokemon($id);

    if ($resultado === "Pokémon eliminado correctamente.") {
        header("Location: index.php?mensaje=" . urlencode($resultado));
        exit();
    } else {
        // Muestra el mensaje de error
        echo "<p>Error: $resultado</p>";
        echo '<a href="index.php">Volver a la lista de Pokémon</a>';
    }
} else {
    echo "<p>ID no válido.</p>";
    echo '<a href="index.php">Volver a la lista de Pokémon</a>';
}
?>
