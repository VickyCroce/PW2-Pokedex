<?php

require_once 'pokedex.php';

$pokedex = new Pokedex();

$buscador = isset($_GET['buscador']) ? $_GET['buscador'] : "";
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : "";

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

<?php require_once 'header.php';?>

<div class="container">
    <!-- Buscador -->
    <div class="search-bar">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Buscar Pokémon por nombre o número" value="<?php echo htmlspecialchars($buscador); ?>">
            <button type="submit">Buscar</button>
        </form>
    </div>

    <!-- Filtro de orden -->
    <div class="filter">
        <form method="GET" action="">
            <input type="hidden" name="buscador" value="<?php echo htmlspecialchars($buscador); ?>">
            Ordenar por:
            <select name="filtro" onchange="this.form.submit()">
                <option value="numero_asc" <?php echo ($filtro == 'numero_asc') ? 'selected' : ''; ?>>Número Inferior</option>
                <option value="numero_desc" <?php echo ($filtro == 'numero_desc') ? 'selected' : ''; ?>>Número Superior</option>
                <option value="nombre_asc" <?php echo ($filtro == 'nombre_asc') ? 'selected' : ''; ?>>A-Z</option>
                <option value="nombre_desc" <?php echo ($filtro == 'nombre_desc') ? 'selected' : ''; ?>>Z-A</option>
            </select>
        </form>
    </div>
</div>

<!-- Lista de Pokémon -->
<div class="pokemon-list">
    <?php
    $pokedex->mostrarListaPokemon($buscador, $filtro);
    ?>
</div>

<div class="add-pokemon">
    <a href="agregar.php" class="add-pokemon-btn">+ Agregar Pokémon</a>
</div>

<?php //require_once 'footer.php';?>
</body>
</html>
