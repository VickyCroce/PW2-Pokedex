<?php
require_once 'BaseDeDatos/Database.php';

class Pokedex
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = new database();
    }

    public function buscarPokemon($buscador, $filtro)
    {
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
    public function mostrarListaPokemon($buscador, $filtro)
    {
        $result2 = $this->buscarPokemon($buscador, $filtro);

        if ($result2->num_rows > 0) {
            while ($row = $result2->fetch_assoc()) {
                echo '<div class="pokemon-item">';

                echo '<div class="pokemon-actions">';

                echo '<a href="agregar.php?id=' . $row["id"] . '" class="action-btn"><img src="img/editar.png" alt="Editar" title="Editar" class="action-icon"></a>';
                echo '<a href="#" class="action-btn" onclick="confirmarEliminacion(' . $row["id"] . ')"><img src="img/eliminar.png" alt="Borrar" title="Borrar" class="action-icon"></a>';
                echo '</div>';

                echo '<img src="img/pokemon/' . $row["imagen"] . '" alt="' . $row["nombre"] . '" class="pokemon-img">';
                echo '<a href="Detalles.php?id=' . $row["id"] . '" class="pokemon-name">' . $row["nombre"] . '</a>';

                echo '<p class="pokemon-number">Número: ' . $row["numero"] . '</p>';

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

    public function obtenerIdTipo($nombreTipo) {
        $sql = "SELECT id FROM tipo WHERE nombre_p = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('s', $nombreTipo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();
            return $row['id'];
        } else {
            return null;
        }
    }

    public function obtenerTipos() {
        $query = "SELECT id, nombre, imagen FROM tipo";
        $result = $this->conexion->query($query);

        $tipos = [];
        while ($row = $result->fetch_assoc()) {
            $tipos[] = $row;
        }

        return $tipos;
    }

    public function agregarPokemon($nombre, $numero, $descripcion, $imagen, $tipos)
    {
        $nombreImagen = basename($imagen['name']);
        $rutaImagen = 'img/pokemon/' . $nombreImagen;

        if (!move_uploaded_file($imagen['tmp_name'], $rutaImagen)) {
            return "La imagen es obligatoria.";
        }

        $sql = "INSERT INTO pokemon (nombre, numero, descripcion, imagen) VALUES ('$nombre', '$numero', '$descripcion', '$nombreImagen')";

        if ($this->conexion->query($sql)) {
            $pokemon_id = $this->conexion->conexion->insert_id;

            if (!is_array($tipos)) {
                $tipos = explode(',', $tipos);
            }

            foreach ($tipos as $tipo) {
                $tipo = trim($tipo);

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


    public function obtenerTiposPorPokemonId($id)
    {
        $sqlTipos = "SELECT tipo.id, tipo.nombre_p FROM pokemon_tipo 
                 JOIN tipo ON pokemon_tipo.tipo_id = tipo.id 
                 WHERE pokemon_tipo.pokemon_id = ?";

        $stmtTipos = $this->conexion->prepare($sqlTipos);

        $stmtTipos->bind_param("i", $id);

        $stmtTipos->execute();

        $resultTipos = $stmtTipos->get_result();

        $tipos = [];

        while ($row = $resultTipos->fetch_assoc()) {
            $tipos[] = [
                'id' => $row['id'],
                'nombre' => $row['nombre_p']
            ];
        }

        return $tipos;
    }

    function buscarPokemonPorId($id) {
        $sql = "SELECT * FROM pokemon WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $pokemon = $resultado->fetch_assoc();

        $pokemon['tipos'] = $this->obtenerTiposPorPokemonId($id);

        return $pokemon;
    }

    public function editarPokemon($id, $nombre, $numero, $descripcion, $imagen, $tipos)
    {
        $pokemonActual = $this->buscarPokemonPorId($id);

        $sql = "UPDATE pokemon SET ";
        $params = [];
        $types = '';
        $updates = [];
        if ($nombre !== $pokemonActual['nombre']) {
            $updates[] = "nombre = ?";
            $params[] = $nombre;
            $types .= 's';
        }

        if ($numero !== $pokemonActual['numero']) {
            $updates[] = "numero = ?";
            $params[] = $numero;
            $types .= 's';
        }

        if ($descripcion !== $pokemonActual['descripcion']) {
            $updates[] = "descripcion = ?";
            $params[] = $descripcion;
            $types .= 's';
        }

        if ($imagen['error'] !== UPLOAD_ERR_NO_FILE) {
            $imagenActual = $imagen['name'];
            $updates[] = "imagen = ?";
            $params[] = $imagenActual;
            $types .= 's';
        }


        $sql_eliminar_tipos = "DELETE FROM pokemon_tipo WHERE pokemon_id = ?";
        $stmt_eliminar = $this->conexion->prepare($sql_eliminar_tipos);
        $stmt_eliminar->bind_param('i', $id);
        $stmt_eliminar->execute();

        if (is_string($tipos)) {
            $tipos = explode(',', $tipos);
        }

        foreach ($tipos as $tipo) {
            $sql_tipo = "SELECT id FROM tipo WHERE nombre_p = ?";
            $stmt_tipo = $this->conexion->prepare($sql_tipo);
            $stmt_tipo->bind_param('s', $tipo);
            $stmt_tipo->execute();
            $resultado_tipo = $stmt_tipo->get_result();

            if ($resultado_tipo->num_rows > 0) {
                $row = $resultado_tipo->fetch_assoc();
                $tipo_id = $row['id'];

                $sql_relacion = "INSERT INTO pokemon_tipo (pokemon_id, tipo_id) VALUES (?, ?)";
                $stmt_relacion = $this->conexion->prepare($sql_relacion);
                $stmt_relacion->bind_param('ii', $id, $tipo_id);
                $stmt_relacion->execute();
            }
        }


        if (count($updates) > 0) {
            $sql .= implode(", ", $updates);
            $sql .= " WHERE id = ?";
            $params[] = $id;
            $types .= 'i';

            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                return "Pokémon editado correctamente.";
            } else {
                return "Error al editar el Pokémon: " . $stmt->error;
            }
        } else {
            return "No se han realizado cambios en el Pokémon.";
        }
    }


    public function eliminarPokemon($id)
    {
        $sql_eliminar_relaciones = "DELETE FROM pokemon_tipo WHERE pokemon_id = '$id'";
        if ($this->conexion->query($sql_eliminar_relaciones)) {

            $sql_eliminar_pokemon = "DELETE FROM pokemon WHERE id = '$id'";
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
