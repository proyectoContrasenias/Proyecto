<?php
//Carga las dependencias y se activa la visualización de errores
require_once 'vendor/autoload.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Conexión a la base de datos
$conex = new mysqli("192.168.20.35", "proyecto", "proyecto", "keysafe");
//Muestra errores si falla
if ($conex->connect_error) {
    die("Error de conexión: " . $conex->connect_error);
}

//Variables que se usarán para mostrar mesnajes después de enviar el formulario
$mensaje = "";
$mensajeError = "";

//Se recogen los datos del formulario
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $nombrenuevo = $_POST['nombre'];
    $apellido = $_POST['apellidos'];
    $usunuevo = $_POST['usuario'];
    $emailnuevo = $_POST['email'];
    $connueva = $_POST['contrasena'];
    $connueva2 = $_POST['repite-contrasena'];

    //Si las contraseñas no coinciden envia un mensaje de error
    if ($connueva !== $connueva2) {
    $mensajeError = "Las contraseñas no coinciden.";
} else {
    // Verifica si el nombre de usuario ya existe
    $stmtUsuario = $conex->prepare("SELECT id FROM usuarios WHERE username = ?");
    $stmtUsuario->bind_param("s", $usunuevo);
    $stmtUsuario->execute();
    $stmtUsuario->store_result();
    
    // Verifica si el correo ya existe
    $stmtCorreo = $conex->prepare("SELECT id FROM usuarios WHERE correo = ?");
    $stmtCorreo->bind_param("s", $emailnuevo);
    $stmtCorreo->execute();
    $stmtCorreo->store_result();

    //Mensajes de error por si se duplica el usuario o correo
    if ($stmtUsuario->num_rows > 0) {
        $mensajeError = "El nombre de usuario ya está en uso.";
    } elseif ($stmtCorreo->num_rows > 0) {
        $mensajeError = "El correo electrónico ya está registrado.";
    } else {
        // Generar código secreto de Google Authenticator
        $ga = new PHPGangsta_GoogleAuthenticator();
        $secret = $ga->createSecret();

        $passwordHash = password_hash($connueva, PASSWORD_DEFAULT); //Contraseña se cifra
        // Guardar el usuario y su código secreto en la base de datos
        $stmt = $conex->prepare('INSERT INTO usuarios (nombre, apellidos, username, contraseña, correo, google_auth_code) VALUES(?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssss', $nombrenuevo, $apellido, $usunuevo, $passwordHash, $emailnuevo, $secret);

        //Si el insert se completa se prepara un mensaje y se muestra el QR, si no se muestra un mensaje de error
        if ($stmt->execute()) {
            $mensaje = "Registro completado correctamente. Escanea el código QR con Google Authenticator.";
        } else {
            $mensajeError = "Error al crear el usuario: " . $stmt->error;
        }

        $stmt->close();
    }

    //Se cierran las consultas y la conexión
    $stmtUsuario->close();
    $stmtCorreo->close();
}
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="styles-register.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Crear cuenta</h2>
            <form action="registro.php" method="POST">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>

                <label for="apellidos">Apellidos:</label>
                <input type="text" id="apellidos" name="apellidos" required>

                <label for="usuario">Nombre de usuario:</label>
                <input type="text" id="usuario" name="usuario" required>

                <label for="email">Correo electrónico:</label>
                <input type="email" id="email" name="email" required>

                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required>

                <label for="repite-contrasena">Repite la contraseña:</label>
                <input type="password" id="repite-contrasena" name="repite-contrasena" required>

                <button type="submit">Registrar</button>
            </form>

            <?php if ($mensaje): ?>
                <p class="success-message"><?php echo $mensaje; ?></p>

                <?php 
                // Mostrar código QR solo si el registro se completa
                if (isset($secret)) {
                    $qrCodeUrl = $ga->getQRCodeGoogleUrl("KeySafe: " . $usunuevo, $secret);
                    echo "<p>Escanea este código QR con Google Authenticator:</p>";
                    echo '<img src="' . $qrCodeUrl . '" />';
                }
                ?>
            <?php endif; ?>

            <?php if ($mensajeError): ?>
                <p class="error-message"><?php echo $mensajeError; ?></p>
            <?php endif; ?>

            <p>¿Ya tienes cuenta? <a href="index.php">Inicia sesión aquí</a></p>
        </div>
    </div>
</body>
</html>
