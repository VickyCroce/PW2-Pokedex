<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($_GET['id']) ? 'Editar Pokémon' : 'Agregar Pokémon'; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php require_once 'header.php'; ?>

<a href="index.php" class="back-arrow">&#8592; Volver</a>

<div class="container-add">
    <h1><?php echo isset($_GET['id']) ? 'Editar Pokémon' : 'Agregar Pokémon'; ?></h1>


    <?php
    require_once 'pokedex.php';
    $pokedex = new Pokedex();

    $pokemon = null;
    $id = null;

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = $_GET['id'];
        $pokemon = $pokedex->buscarPokemonPorId($id);
    }
    ?>

    <div id="mensaje" class="mensaje" style="display: none;"></div>
    <form action="agregar.php" method="POST" enctype="multipart/form-data" class="form-add">
        <?php if (isset($pokemon)): ?>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
        <?php endif; ?>

        <div class="form-group-doble">
            <div class="form-group-add-nombre">
                <label for="nombre">Nombre del Pokémon:</label>
                <input type="text" name="nombre" id="nombre" required
                       value="<?php echo isset($pokemon) ? htmlspecialchars($pokemon['nombre']) : ''; ?>">
            </div>

            <div class="form-group-add">
                <label for="numero">Número del Pokémon:</label>
                <input type="number" name="numero" id="numero" required
                       value="<?php echo isset($pokemon) ? htmlspecialchars($pokemon['numero']) : ''; ?>">
            </div>
        </div>
        <div class="form-group-add">
            <label for="descripcion">Descripción del Pokémon:</label>
            <textarea name="descripcion" id="descripcion"
                      required><?php echo isset($pokemon) ? htmlspecialchars($pokemon['descripcion']) : ''; ?></textarea>
        </div>

        <div class="form-group-add">
            <label for="imagen">Imagen del Pokémon:</label>
            <div class="form-group-img">
                <?php if (isset($pokemon) && !empty($pokemon['imagen'])): ?>
                    <div class="imagen-actual">
                        <img src="/PW2-Pokedex/img/pokemon/<?php echo htmlspecialchars($pokemon['imagen']); ?>"
                              alt="<?php echo htmlspecialchars($pokemon['nombre']); ?>"
                              style="max-width: 150px; max-height: 150px;">
                    </div>
                <?php endif; ?>
                <input type="file" name="imagen" id="imagen" class="input-img" accept="image/*">
            </div>
        </div>

        <div class="form-group-add">
            <label>Selecciona los tipos del Pokémon:</label>
            <div class="pokemon-types">
                <?php
                $dir = 'img/TipoPokemon/';
                $imagenes = scandir($dir);

                $tiposSeleccionados = isset($pokemon) ? $pokedex->obtenerTiposPorPokemonId($id) : [];
                $tiposSeleccionadosNombres = array_column($tiposSeleccionados, 'nombre');

                foreach ($imagenes as $imagen) {
                    if ($imagen !== '.' && $imagen !== '..' && preg_match('/\.(png)$/', $imagen)  && strpos($imagen, '_icono') === false) {
                        $nombreTipo = str_replace('tipo_', '', pathinfo($imagen, PATHINFO_FILENAME));

                        $tipoSeleccionado = in_array(ucfirst($nombreTipo), $tiposSeleccionadosNombres) ? 'selected' : '';

                        echo '<label class="tipo-label ' . $tipoSeleccionado . '" data-tipo="' . ucfirst($nombreTipo) . '">';
                        echo '<img src="' . $dir . $imagen . '" alt="' . ucfirst($nombreTipo) . '" class="tipo-img" />';
                        echo '</label>';
                    }
                }
                ?>

                <input type="hidden" id="tipos_seleccionados" name="tipos"
                       value="<?php echo isset($pokemon) ? htmlspecialchars(implode(',', $tiposSeleccionadosNombres)) : ''; ?>">
            </div>
        </div>


        <?php
        require_once 'pokedex.php';

        $pokedex = new Pokedex();
        $mensaje = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['nombre'], $_POST['numero'], $_POST['descripcion']) && !empty($_POST['tipos'])) {
                $nombre = $_POST['nombre'];
                $numero = $_POST['numero'];
                $descripcion = $_POST['descripcion'];
                $tipos = $_POST['tipos'];
                $imagen = $_FILES['imagen'];

                if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                    $id = $_POST['id'];
                    $resultado = $pokedex->editarPokemon($id, $nombre, $numero, $descripcion, $imagen, $tipos);
                } else {
                    $resultado = $pokedex->agregarPokemon($nombre, $numero, $descripcion, $imagen, $tipos);
                }

                $mensaje = $resultado;
                $tipo_mensaje = (strpos($resultado, 'correctamente') !== false) ? 'exito' : 'error';

            } else {
                $mensaje = "Por favor, complete todos los campos.";
                $tipo_mensaje = 'error';
            }
        }
        ?>
        <button type="submit"
                class="add-pokemon-btn"><?php echo isset($pokemon) ? 'Guardar Cambios' : 'Agregar Pokémon'; ?></button>


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
