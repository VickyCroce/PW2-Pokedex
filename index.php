<?php
require_once 'BaseDeDatos/Database.php';

$conexion = new database();

// Consulta para obtener los Pokémon
$sql = "SELECT p.nombre, p.numero, p.imagen, GROUP_CONCAT(t.nombre_p SEPARATOR ',') as tipos
        FROM pokemon p
        JOIN pokemon_tipo pt ON p.id = pt.pokemon_id
        JOIN tipo t ON pt.tipo_id = t.id
        GROUP BY p.id
        ORDER BY p.numero ASC";
$result2 = $conexion->query($sql);

//busqueda
$search = "";
if (isset($_GET['search'])) {
    // Escapa el valor de búsqueda para evitar inyecciones SQL
    $search = $conexion->escape($_GET['search']);
}

// Verifica si se ha seleccionado un filtro
$order_by = "numero ASC"; // Orden por defecto
if (isset($_GET['filter'])) {
    $filter = $_GET['filter'];

    switch ($filter) {
        case 'numero_asc':
            $order_by = "numero ASC";
            break;
        case 'numero_desc':
            $order_by = "numero DESC";
            break;
        case 'nombre_asc':
            $order_by = "nombre ASC";
            break;
        case 'nombre_desc':
            $order_by = "nombre DESC";
            break;
    }
}

//  cambia según el valor de búsqueda y el filtro
$sql = "SELECT numero, nombre, imagen FROM pokemon";
if ($search != "") {
    // Búsqueda por nombre o número
    if (is_numeric($search)) {
        $sql .= " WHERE numero = '$search'";
    } else {
        $sql .= " WHERE nombre LIKE '%$search%'";
    }
}
$sql .= " ORDER BY $order_by";
$result = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokedex Modificada</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="header">
    <img src="../pokedexLogo.png" alt="Pokedex Logo">
</div>

<div class="container">
    <!-- buscador -->
    <div class="search-bar"">
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Buscar Pokémon por nombre o número" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Buscar</button>
    </form>
</div>


<!-- Filtro de orden -->
<div class="filter">
    Ordenar por:
    <select>
        <option value="numero-inferior">Número Inferior</option>
        <option value="numero-superior">Número Superior</option>
        <option value="a-z">A-Z</option>
        <option value="z-a">Z-A</option>
    </select>
</div>
</div>


<!-- pokemones  -->
<div class="pokemon-list">
    <?php

    if ($result2->num_rows > 0) {

        // Mostrar los datos de cada Pokémon
        while($row = $result2->fetch_assoc()) {
            echo '<div class="pokemon-item">';
            echo '<img src="img/pokemon/' . $row["imagen"] . '" alt="' . $row["nombre"] . '" class="pokemon-img">';
            echo '<h3 class="pokemon-name">' . $row["nombre"] . '</h3>';
            echo '<p class="pokemon-number">Número: ' . $row["numero"] . '</p>';
            $tipos = explode(',', $row["tipos"]);
            foreach ($tipos as $tipo) {
                echo '<img src="img/TipoPokemon/' . "tipo_".strtolower($tipo) . '.png" class="pokemon-type">';
            }
            echo '</div>';
        }
    } else {
        echo "No se encontraron Pokémon.";
    }
    ?>
</div>

<!-- Footer -->
<div class="footer">
    <div class="footer-logo">
        <img src="../pokedexLogo.png" alt="PokeInfo Logo">
    </div>
    <div class="footer-content">
        <div class="section">
            <h4>Integrantes</h4>
            <p>Victoria Croce</p>
            <p>Avril Sandoval</p>
            <p>Pessino Milagros</p>
            <p>Franco coppola</p>

        </div>
        <div class="section">
            <h4>Materia</h4>
            <p>Programación Web II</p>
        </div>
        <div class="section">
            <h4>Universidad</h4>
            <p>© Universidad Nacional de La Matanza</p>
        </div>
    </div>
</div>
</body>
</html>


