<?php
require_once 'BaseDeDatos/Database.php';
session_start();

$database = new Database();
$conn = $database->conexion;

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['username']) && isset($_POST['password'])) {

        $username = $database->escape($_POST['username']);
        $password = $_POST['password'];

        $sql = "SELECT * FROM usuarios WHERE username = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die("Error en la preparación de la consulta: " . $conn->error);
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if ($password === $user['password']) {
                $_SESSION['username'] = $username;
                header("Location: index.php");
                exit;
            } else {
                $message = "Contraseña incorrecta.";
            }
        } else {
            $message = "Usuario no encontrado.";
        }

        $stmt->close();
    } else {
        $message = "Por favor, completa todos los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Página de inicio de sesión segura y optimizada para usuarios. Inicia sesión para acceder a tu cuenta de usuario.">
    <meta name="author" content="Tu Nombre">
    <meta name="keywords" content="login, sistema de login, usuario, contraseña, inicio de sesión">
    <title>Inicio de Sesión</title>
    <link rel="icon" href="img/pokebolaLogo.png" type="image/png">
    <link rel="stylesheet" href="estilos/login.css">

</head>
<body>

    <?php require_once 'header.php'; ?>

    <main>
        <div class="container">
            <h1>Inicio de Sesión</h1>

            <form action="login.php" method="POST">
                <label for="username">Nombre de Usuario:</label>
                <input type="text" id="username" name="username" aria-label="Nombre de Usuario" required>

                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" aria-label="Contraseña" required>

                <input type="submit" value="Iniciar Sesión">
            </form>

            <?php if ($message): ?>
                <p class="error"><?php echo $message; ?></p>
            <?php endif; ?>
        </div>
    </main>

    <?php require_once 'footer.php'; ?>

</body>
</html>
