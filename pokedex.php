<?php
require_once 'BaseDeDatos/Database.php';

class Pokedex {
    private $conexion;

    public function __construct() {
        $this->conexion = new database();
    }

    public function buscarPokemon($buscador, $filtro) {
        $order_by = "numero ASC";

        if (isset($filtro)) {
            switch ($filtro) {
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

        $sql = "SELECT p.id,p.nombre, p.numero, p.imagen, GROUP_CONCAT(t.nombre_p SEPARATOR ',') as tipos
                FROM pokemon p
                JOIN pokemon_tipo pt ON p.id = pt.pokemon_id
                JOIN tipo t ON pt.tipo_id = t.id";

        if ($buscador != "") {
            if (is_numeric($buscador)) {
                $sql .= " WHERE p.numero = '" . $this->conexion->escape($buscador) . "'";
            } else {
                $sql .= " WHERE p.nombre LIKE '%" . $this->conexion->escape($buscador) . "%'";
            }
        }

        $sql .= " GROUP BY p.id ORDER BY $order_by";

        return $this->conexion->query($sql);
    }

    //Métodopara mostrar la lista de Pokémon
    public function mostrarListaPokemon($buscador, $filtro) {
        $result2 = $this->buscarPokemon($buscador, $filtro);

        if ($result2->num_rows > 0) {
            while($row = $result2->fetch_assoc()) {
                echo '<div class="pokemon-item">';

                // Botones de editar y borrar
                echo '<div class="pokemon-actions">';
                echo '<a href="editar.php?id=' . $row["id"] . '" class="action-btn"><img src="img/editar.png" alt="Editar" title="Editar" class="action-icon"></a>';
                echo '<a href="borrar.php?id=' . $row["id"] . '" class="action-btn"><img src="img/eliminar.png" alt="Borrar" title="Borrar" class="action-icon"></a>';
                echo '</div>';

                
                echo '<a href="Detalles.php?id=' . $row["numero"] . '" class="pokemon-name">'. $row["nombre"] . '</a>';
                echo '<img src="img/pokemon/' . $row["imagen"] . '" alt="' . $row["nombre"] . '" class="pokemon-img">';
=======

                echo '<img src="img/pokemon/' . $row["imagen"] . '" alt="' . $row["nombre"] . '" class="pokemon-img">';
                echo '<a href="Detalles.php?id=' . $row["id"] . '" class="pokemon-name">'. $row["nombre"] . '</a>';
>>>>>>> feature/home
                echo '<p class="pokemon-number">Número: ' . $row["numero"] . '</p>';

                // Mostrar tipos de Pokémon
                $tipos = explode(',', $row["tipos"]);
                foreach ($tipos as $tipo) {
                    echo '<img src="img/TipoPokemon/tipo_' . strtolower($tipo) . '.png" class="pokemon-type">';
                }

                echo '</div>';
            }
        } else {
            echo "<p>No se encontraron Pokémon.</p>";
        }
    }

    public function agregarPokemon($nombre, $numero, $descripcion, $imagen, $tipos) {
        $nombreImagen = basename($imagen['name']);
        $rutaImagen = 'img/pokemon/' . $nombreImagen;

        if (!move_uploaded_file($imagen['tmp_name'], $rutaImagen)) {
            return "Error al subir la imagen.";
        }

        $sql = "INSERT INTO pokemon (nombre, numero, descripcion, imagen) VALUES ('$nombre', '$numero', '$descripcion', '$nombreImagen')";

        if ($this->conexion->query($sql)) {
            $pokemon_id = $this->conexion->conexion->insert_id;

            foreach ($tipos as $tipo) {
                $sql_tipo = "SELECT id FROM tipo WHERE nombre_p = '$tipo'";
                $resultado_tipo = $this->conexion->query($sql_tipo);

                if ($resultado_tipo->num_rows > 0) {
                    $row = $resultado_tipo->fetch_assoc();
                    $tipo_id = $row['id'];

                    $sql_relacion = "INSERT INTO pokemon_tipo (pokemon_id, tipo_id) VALUES ('$pokemon_id', '$tipo_id')";
                    $this->conexion->query($sql_relacion);
                }
            }

            return "Pokémon agregado correctamente";
        } else {
            return "Error al insertar en la base de datos.";
        }
    }

    public function eliminarPokemon($numero) {
        $sql_eliminar_relaciones = "DELETE FROM pokemon_tipo WHERE pokemon_id = (SELECT id FROM pokemon WHERE numero = '$numero')";
        if ($this->conexion->query($sql_eliminar_relaciones)) {

            $sql_eliminar_pokemon = "DELETE FROM pokemon WHERE numero = '$numero'";
            if ($this->conexion->query($sql_eliminar_pokemon)) {
                return "Pokémon eliminado correctamente.";
            } else {
                return "Error al eliminar el Pokémon.";
            }
        } else {
            return "Error al eliminar las relaciones del Pokémon.";
        }
    }
}
?>