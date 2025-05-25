<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?error=Debes iniciar sesión para acceder");
    exit();
}
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
        <?php
        $id_usuario = $_SESSION['user_id'];
        echo "<button class='back-button' onclick=\"location.href='dashboard.php?id_usuario=$id_usuario'\">Volver al Menú</button>";
        ?>
    </div>
</header>

<?php
require_once 'encriptacion.php';

$conn = new mysqli("192.168.20.35", "proyecto", "proyecto", "keysafe");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nombre'], $_POST['usuario'], $_POST['contrasena'])) {
    $usuario_id = $_SESSION['user_id'];
    $pagina = $_POST['nombre'];
    $usuario = $_POST['usuario'];
    $contrasena_cifrada = encryptPassword($_POST['contrasena']);

    $stmt = $conn->prepare("INSERT INTO contraseñas(pagina, usuario, contraseña, usuario_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $pagina, $usuario, $contrasena_cifrada, $usuario_id);
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
            <button type="button" class="generate-password" onclick="generatePassword()">Generar contraseña</button>
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

    function generatePassword() {
        const length = 12;
        const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=[]{}|;:,.<>?";
        let password = "";
        for (let i = 0; i < length; i++) {
            const randomIndex = Math.floor(Math.random() * charset.length);
            password += charset[randomIndex];
        }
        document.getElementById('contrasena').value = password;
    }
</script>

<?php $conn->close(); ?>
</body>
</html>
