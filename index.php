<?php
require_once 'BaseDeDatos/Database.php';

$conexion = new database();

// Inicializamos variables de búsqueda y filtro
$busqueda = "";
$ordenarPor = "numero ASC";


if (isset($_GET['busqueda'])) {
    // Escapamos el valor de búsqueda para evitar inyecciones SQL
    $busqueda = $conexion->escape($_GET['busqueda']);
}

// Verifica si se ha seleccionado un filtro
if (isset($_GET['filtro'])) {
    $filtro = $_GET['filtro'];

    switch ($filtro) {
        case 'numero_asc':
            $ordenarPor = "numero ASC";
            break;
        case 'numero_desc':
            $ordenarPor = "numero DESC";
            break;
        case 'nombre_asc':
            $ordenarPor = "nombre ASC";
            break;
        case 'nombre_desc':
            $orden = "nombre DESC";
            break;
    }
}

// Construye la consulta SQL combinando búsqueda y filtro
$sql = "SELECT p.nombre, p.numero, p.imagen, GROUP_CONCAT(t.nombre_p SEPARATOR ',') as tipos
        FROM pokemon p
        JOIN pokemon_tipo pt ON p.id = pt.pokemon_id
        JOIN tipo t ON pt.tipo_id = t.id";

// Añade la condición de búsqueda si existe
if ($busqueda != "") {
    if (is_numeric($busqueda)) {
        $sql .= " WHERE p.numero = '$busqueda'";
    } else {
        $sql .= " WHERE p.nombre LIKE '%$busqueda%'";
    }
}

// Ordena según el filtro
$sql .= " GROUP BY p.id ORDER BY $ordenarPor";
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
            <input type="text" name="busqueda" placeholder="Buscar Pokémon por nombre o número" value="<?php echo htmlspecialchars($busqueda); ?>">
            <button type="submit">Buscar</button>
        </form>
    </div>

    <!-- Filtro de orden -->
    <div class="filter">
        <form method="GET" action="">
            <!-- Mantener el valor de búsqueda -->
            <input type="hidden" name="busqueda" value="<?php echo htmlspecialchars($busqueda); ?>">

            Ordenar por:
            <select name="filtro" onchange="this.form.submit()">
                <option value="numero_asc" <?php echo (isset($_GET['filtro']) && $_GET['filtro'] == 'numero_asc') ? 'selected' : ''; ?>>Número Inferior</option>
                <option value="numero_desc" <?php echo (isset($_GET['filtro']) && $_GET['filtro'] == 'numero_desc') ? 'selected' : ''; ?>>Número Superior</option>
                <option value="nombre_asc" <?php echo (isset($_GET['filtro']) && $_GET['filtro'] == 'nombre_asc') ? 'selected' : ''; ?>>A-Z</option>
                <option value="nombre_desc" <?php echo (isset($_GET['filtro']) && $_GET['filtro'] == 'nombre_desc') ? 'selected' : ''; ?>>Z-A</option>
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

            // Botones de editar y borrar
            echo '<div class="pokemon-actions">';
            echo '<a href="editar.php?id=' . $row["numero"] . '" class="action-btn"><img src="img/editar.png" alt="Editar" title="Editar" class="action-icon"></a>';
            echo '<a href="borrar.php?id=' . $row["numero"] . '" class="action-btn"><img src="img/eliminar.png" alt="Borrar" title="Borrar" class="action-icon"></a>';
            echo '</div>';

            echo '<a href="Detalles.php?id=' . $row["numero"] . '"><img src="img/pokemon/' . $row["imagen"] . '" alt="' . $row["nombre"] . '" class="pokemon-img"></a>';
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

    <!-- Botón para agregar un nuevo Pokémon -->
    <div class="add-pokemon">
        <a href="agregar.php" class="add-pokemon-btn">+ Agregar Pokémon</a>
    </div>

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
