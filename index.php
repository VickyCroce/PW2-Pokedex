<?php
require_once 'BaseDeDatos/Database.php';

$conexion = new database();

// Inicializamos variables de búsqueda y filtro
$search = "";
$order_by = "numero ASC"; // Orden por defecto

// Verificamos si hay búsqueda
if (isset($_GET['search'])) {
    // Escapamos el valor de búsqueda para evitar inyecciones SQL
    $search = $conexion->escape($_GET['search']);
}

// Verificamos si se ha seleccionado un filtro
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

// Construimos la consulta SQL combinando búsqueda y filtro
$sql = "SELECT p.nombre, p.numero, p.imagen, GROUP_CONCAT(t.nombre_p SEPARATOR ',') as tipos
        FROM pokemon p
        JOIN pokemon_tipo pt ON p.id = pt.pokemon_id
        JOIN tipo t ON pt.tipo_id = t.id";

// Añadimos la condición de búsqueda si existe
if ($search != "") {
    if (is_numeric($search)) {
        $sql .= " WHERE p.numero = '$search'";
    } else {
        $sql .= " WHERE p.nombre LIKE '%$search%'";
    }
}

// Ordenamos según el filtro
$sql .= " GROUP BY p.id ORDER BY $order_by";

// Ejecutamos la consulta
$result2 = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokedex Modificada</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="header">
    <img src="img/pokedexLogo.png" alt="Pokedex Logo">
</div>

<div class="container">
    <!-- buscador -->
    <div class="search-bar">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Buscar Pokémon por nombre o número" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Buscar</button>
        </form>
    </div>

    <!-- Filtro de orden -->
    <div class="filter">
        <form method="GET" action="">
            <!-- Mantener el valor de búsqueda -->
            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">

            Ordenar por:
            <select name="filter" onchange="this.form.submit()">
                <option value="numero_asc" <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'numero_asc') ? 'selected' : ''; ?>>Número Inferior</option>
                <option value="numero_desc" <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'numero_desc') ? 'selected' : ''; ?>>Número Superior</option>
                <option value="nombre_asc" <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'nombre_asc') ? 'selected' : ''; ?>>A-Z</option>
                <option value="nombre_desc" <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'nombre_desc') ? 'selected' : ''; ?>>Z-A</option>
            </select>
        </form>
    </div>
</div>


<!-- pokemones  -->
<div class="pokemon-list">
    <?php

    if ($result2->num_rows > 0) {

        // Mostrar los datos de cada Pokémon
        while($row = $result2->fetch_assoc()) {
            echo '<div class="pokemon-item">';
            echo '<a href="Detalles.php?id=' . $row["numero"] . '">';
            echo '<img src="img/pokemon/' . $row["imagen"] . '" alt="' . $row["nombre"] . '" class="pokemon-img">';
            echo '</a>';
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
        <img src="img/pokedexLogo.png" alt="PokeInfo Logo">
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


