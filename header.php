<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokedex</title>
    <link rel="icon" href="img/pokebolaLogo.png" type="image/png">
    <link rel="stylesheet" href="estilos/header.css">
</head>
<body>


<div class="header">
    <a href="index.php"><img src="img/pokedexLogo.png" alt="Pokedex Logo"></a>

    <div class="user-actions">
        <?php if (isset($_SESSION['username'])): ?>
            <p>Usuario: <?php echo htmlspecialchars($_SESSION['username']); ?></p>
            <a href="logout.php" class="logout-button">Cerrar Sesión</a>
        <?php else: ?>
            <a href="login.php" class="login-button">Iniciar Sesión</a>
        <?php endif; ?>
    </div>
</div>

</body>
</html>