<?php
// Esto debe ir SIEMPRE al inicio del archivo
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="visual_login.css">
</head>

<body>

<style>
html, body {
    width: 100%;
    height: 100%;
    margin: 0;
}

body {
    background-image: url("fondoinicio.png");
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}
</style>

<div class="login-container">
    <div class="login-card">

        <h2>Bienvenido</h2>
        <p class="subtitle">Para continuar ingrese usuario y contraseña</p>

        <!-- 🔴 NOTIFICACIÓN DE ERROR -->
        <?php if (isset($_GET['error'])) { ?>
            <div class="error-msg">
                ❌ Matrícula o contraseña incorrecta
            </div>
        <?php } ?>

        <!-- FORMULARIO -->
        <form action="pruebaLogin.php" method="post">

            <div class="input-box">
                <input type="text" name="matricula" placeholder="Matricula" required>
            </div>

            <div class="input-box">
                <input type="password" name="pwd" placeholder="Password" required>
            </div>

            <button type="submit">Sign In</button>

        </form>

        <br>

        <a href="Index.php">
            <button type="button">Regresar</button>
        </a>

    </div>
</div>

</body>
</html>