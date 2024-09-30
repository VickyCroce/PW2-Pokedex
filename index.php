<?php
session_start();
require_once 'pokedex.php';

$pokedex = new Pokedex();

$buscador = isset($_GET['search']) ? $_GET['search'] : '';
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : "";

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokedex</title>
    <link rel="stylesheet" href="estilos/style.css">
    <link rel="icon" href="img/pokebolaLogo.png" type="image/png">
    <link rel="stylesheet" href="estilos/popup.css">
</head>
<body>

<?php require_once 'header.php'; ?>

<div class="container">
    <!-- Buscador -->
    <div class="search-bar">
        <form method="GET" action="">
            <input type="text" name="search" class="buscador" placeholder="Buscar Pokémon por nombre o número" value="<?php echo htmlspecialchars($buscador); ?>">
            <button type="submit">Buscar</button>
        </form>
    </div>

    <!-- Filtro de orden -->
    <div class="filter">
        <form method="GET" action="">
            <input type="hidden" name="search" value="<?php echo htmlspecialchars($buscador); ?>">
            Ordenar por:
            <select name="filtro" onchange="this.form.submit()">
                <option value="numero_asc" <?php echo ($filtro == 'numero_asc') ? 'selected' : ''; ?>>Número Inferior
                </option>
                <option value="numero_desc" <?php echo ($filtro == 'numero_desc') ? 'selected' : ''; ?>>Número
                    Superior
                </option>
                <option value="nombre_asc" <?php echo ($filtro == 'nombre_asc') ? 'selected' : ''; ?>>A-Z</option>
                <option value="nombre_desc" <?php echo ($filtro == 'nombre_desc') ? 'selected' : ''; ?>>Z-A</option>
            </select>
        </form>
    </div>
</div>

<div id="popup-confirm" class="popup" style="display:none;">
    <div class="popup-content">
        <h3>¿Estás seguro de que quieres eliminar este Pokémon?</h3>
        <div class="popup-section">
            <button id="btn-cancelar" class="btn-cancelar">Cancelar</button>
            <button id="btn-confirmar" class="btn-confirmar">Eliminar</button>
        </div>

    </div>
</div>

<script>
    let pokemonId;

    function confirmarEliminacion(id) {
        pokemonId = id; // Guarda el ID del Pokémon a eliminar
        document.getElementById('popup-confirm').style.display = 'flex'; // Muestra el popup
    }

    document.getElementById('btn-confirmar').addEventListener('click', function () {
        window.location.href = 'borrar.php?id=' + pokemonId; // Redirige a borrar.php
    });

    document.getElementById('btn-cancelar').addEventListener('click', function () {
        document.getElementById('popup-confirm').style.display = 'none'; // Oculta el popup
    });
</script>

<div class="container-msj" style="
    display: flex;
    justify-content: center;
    margin-top: 2rem;
">
    <?php if (isset($_GET['mensaje'])): ?>
        <div id="mensaje" class="mensaje <?php echo (strpos($_GET['mensaje'], 'correctamente') !== false) ? 'exito' : 'error'; ?>">
            <?php echo htmlspecialchars($_GET['mensaje']); ?>
        </div>
    <?php endif; ?>

</div>

<!-- Lista de Pokémon -->
<div class="pokemon-list">
    <?php
    $pokedex->mostrarListaPokemon($buscador, $filtro);
    ?>
</div>

<?php if (isset($_SESSION['username'])){
    echo '<div class="add-pokemon">';
    echo '    <a href="agregar.php" class="add-pokemon-btn">+ Agregar Pokémon</a> ';
    echo '</div>';}

?>
<?php require_once 'footer.php'; ?>

<script>
    // Esperar a que la página cargue completamente
    window.onload = function() {
        var mensaje = document.getElementById('mensaje');

        if (mensaje) {
            // Ocultar el mensaje después de 3 segundos
            setTimeout(function() {
                mensaje.classList.add('oculto');
            }, 3500); // 3000ms = 3 segundos

            // Remover el elemento del DOM después de la animación
            setTimeout(function() {
                mensaje.remove();
            }, 3500); // 3500ms para dar tiempo a la animación
        }
    };
</script>

</body>
</html>

