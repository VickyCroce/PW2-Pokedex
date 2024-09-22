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

        $sql = "SELECT p.nombre, p.numero, p.imagen, GROUP_CONCAT(t.nombre_p SEPARATOR ',') as tipos
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
                echo '<a href="editar.php?id=' . $row["numero"] . '" class="action-btn"><img src="img/editar.png" alt="Editar" title="Editar" class="action-icon"></a>';
                echo '<a href="borrar.php?id=' . $row["numero"] . '" class="action-btn"><img src="img/eliminar.png" alt="Borrar" title="Borrar" class="action-icon"></a>';
                echo '</div>';

                echo '<img src="img/pokemon/' . $row["imagen"] . '" alt="' . $row["nombre"] . '" class="pokemon-img">';
                echo '<a href="Detalles.php?id=' . $row["numero"] . '" class="pokemon-name">'. $row["nombre"] . '</a>';
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
}
?>



