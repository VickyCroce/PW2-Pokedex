<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Pokemón</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php require_once 'header.php'; ?>

<div class="container-add">
    <h1>Agregar Pokémon</h1>

    <div id="mensaje" class="mensaje" style="display: none;"></div>
    <form action="agregar.php" method="POST" enctype="multipart/form-data" class="form-add">
        <div class="form-group-doble">
            <div class="form-group-add-nombre">
                <label for="nombre">Nombre del Pokémon:</label>
                <input type="text" name="nombre" id="nombre" required>
            </div>

            <div class="form-group-add">
                <label for="numero">Número del Pokémon:</label>
                <input type="number" name="numero" id="numero" required>
            </div>
        </div>
        <div class="form-group-add">
            <label for="descripcion">Descripción del Pokémon:</label>
            <textarea name="descripcion" id="descripcion" required></textarea>
        </div>

        <div class="form-group-add">
            <label for="imagen">Imagen del Pokémon:</label>
            <input type="file" name="imagen" id="imagen" accept="image/*" required>
        </div>

        <div class="form-group-add">
            <label>Selecciona los tipos del Pokémon:</label>

            <div class="pokemon-types">
                <?php
                $dir = 'img/TipoPokemon/';
                $imagenes = scandir($dir);

                foreach ($imagenes as $imagen) {
                    if ($imagen !== '.' && $imagen !== '..' && preg_match('/\.(png)$/', $imagen) && strpos($imagen, '_icono') === false) {
                        $nombreTipo = str_replace('tipo_', '', pathinfo($imagen, PATHINFO_FILENAME));  // Eliminar 'tipo_' del nombre

                        echo '<label class="tipo-label" data-tipo="' . $nombreTipo . '">';
                        echo '<img src="' . $dir . $imagen . '" alt="' . ucfirst($nombreTipo) . '" class="tipo-img" />';
                        echo '</label>';
                    }
                }
                ?>
                <input type="hidden" id="tipos_seleccionados" name="tipos">

            </div>
        </div>

        <button type="submit" class="add-pokemon-btn">Agregar Pokémon</button>

        <?php
        require_once 'Pokedex.php';

        $pokedex = new Pokedex();
        $mensaje = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['nombre'], $_POST['numero'], $_POST['descripcion']) && isset($_FILES['imagen']) && !empty($_POST['tipos'])) {
                $nombre = $_POST['nombre'];
                $numero = $_POST['numero'];
                $descripcion = $_POST['descripcion'];
                $tipos = explode(',', $_POST['tipos']);
                $imagen = $_FILES['imagen'];

                $resultado = $pokedex->agregarPokemon($nombre, $numero, $descripcion, $imagen, $tipos);

                $mensaje = $resultado;
                $tipo_mensaje = (strpos($resultado, 'correctamente') !== false) ? 'exito' : 'error';
            } else {
                $mensaje = "Por favor, complete todos los campos.";
                $tipo_mensaje = 'error';
            }
        }
        ?>

    </form>

</div>
<?php if (!empty($mensaje)): ?>
    <script>
        document.getElementById('mensaje').style.display = 'block';
        document.getElementById('mensaje').classList.add('<?php echo $tipo_mensaje; ?>');
        document.getElementById('mensaje').innerHTML = '<?php echo $mensaje; ?>';
    </script>
<?php endif; ?>

<?php require_once 'footer.php'; ?>

<script src="js/type-selector.js"></script>

</body>
</html>