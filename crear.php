<?php
session_start();
// Revisa si la sesión está creada, si no redirige a index.php pasando un mensaje de error que se mostrará en index.php
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?error=Debes iniciar sesión para acceder");
    exit();
}
/* Si el formulario fue enviado con POST y se ha pulsado el botón de cerrar sesión elimina las variables de la sesión,
destruye la sesión y redirige a index.php con un mensaje de error*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php?error=Has cerrado sesión correctamente");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nueva Contraseña - KeySafe</title>
    <link rel="stylesheet" href="estilocrear.css">
</head>

<body>
    <header>
        <h1>KeySafe - Crear Contraseña</h1>
        <div>
            <form method="POST" style="display:inline;">
                <button type="submit" name="logout" class="logout">Cerrar sesión</button>
            </form>
            <button class="back-button" onclick="location.href='dashboard.php'">Volver al Menú</button>
        </div>
    </header>

    <?php
    $conn = new mysqli("127.0.0.1", "proyecto", "proyecto", "keysafe");
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Comprobar si se ha enviado el formulario
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nombre'], $_POST['usuario'], $_POST['contrasena'])) {
        $stmt = $conn->prepare("INSERT INTO contraseñas(pagina, usuario, contraseña) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $_POST['nombre'], $_POST['usuario'], $_POST['contrasena']);
        $stmt->execute();
        $stmt->close();
        echo "<p>Contraseña guardada exitosamente.</p>";
    }
    ?>

    <main>
        <form class="form-container" method="post">
            <label for="nombre">Nombre de la Página:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="usuario">Usuario:</label>
            <input type="text" id="usuario" name="usuario" required>

            <label for="contrasena">Contraseña:</label>
            <div class="password-container">
                <input type="password" id="contrasena" name="contrasena" required>
                <button type="button" class="toggle-password" onclick="togglePassword()">Mostrar</button>
            </div>

            <button type="submit" class="save-button">Guardar Contraseña</button>
        </form>
    </main>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('contrasena');
            const toggleButton = document.querySelector('.toggle-password');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.textContent = 'Ocultar';
            } else {
                passwordInput.type = 'password';
                toggleButton.textContent = 'Mostrar';
            }
        }
    </script>

    <?php
    $conn->close();
    ?>
</body>

</html>
