<?php
$conex = new mysqli("127.0.0.1", "proyecto", "proyecto", "keysafe");
if ($conex->connect_error) {
    die("Error de conexión: " . $conex->connect_error);
}

$mensaje = "";
$mensajeError = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $nombrenuevo = $_POST['nombre'];
    $apellido = $_POST['apellidos'];
    $usunuevo = $_POST['usuario'];
    $emailnuevo = $_POST['email'];
    $connueva = $_POST['contrasena'];
    $connueva2 = $_POST['repite-contrasena'];

    if ($connueva !== $connueva2) {
        $mensajeError = "Las contraseñas no coinciden.";
    } else {
        $passwordHash = password_hash($connueva, PASSWORD_DEFAULT);
        $stmt = $conex->prepare('INSERT INTO usuarios (nombre, apellidos, username, contraseña, correo) VALUES(?, ?, ?, ?, ?)');
        $stmt->bind_param('sssss', $nombrenuevo, $apellido, $usunuevo, $passwordHash, $emailnuevo);

        if ($stmt->execute()) {
            $mensaje = "Registro completado correctamente";
        } else {
            $mensajeError = "Error al crear el usuario: " . $stmt->error;
        }

        $stmt->close();
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
    <script>
        function validarFormulario(event) {
            const contrasena = document.getElementById('contrasena').value;
            const repiteContrasena = document.getElementById('repite-contrasena').value;

            // Compara las contraseñas
            if (contrasena !== repiteContrasena) {
                event.preventDefault();
                alert("Las contraseñas no coinciden. Por favor, intenta de nuevo.");
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Crear cuenta</h2>
            <form action="registro.php" method="POST" onsubmit="validarFormulario(event)">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" placeholder="Ingresa tu nombre" required>

                <label for="apellidos">Apellidos:</label>
                <input type="text" id="apellidos" name="apellidos" placeholder="Ingresa tus apellidos" required>

                <label for="usuario">Nombre de usuario:</label>
                <input type="text" id="usuario" name="usuario" placeholder="Elige un nombre de usuario" required>

                <label for="email">Correo electrónico:</label>
                <input type="email" id="email" name="email" placeholder="Ingresa tu correo" required>

                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" placeholder="Crea una contraseña" required>

                <label for="repite-contrasena">Repite la contraseña:</label>
                <input type="password" id="repite-contrasena" name="repite-contrasena"
                    placeholder="Repite la contraseña" required>

                <button type="submit">Registrar</button>

                <?php if ($mensaje): ?>
                    <p class="success-message"><?php echo $mensaje; ?></p>
                <?php endif; ?>

                <?php if ($mensajeError): ?>
                    <p class="error-message"><?php echo $mensajeError; ?></p>
                <?php endif; ?>

            </form>

            <p>¿Ya tienes cuenta? <a href="index.php">Inicia sesión aquí</a></p>
        </div>
    </div>
</body>

</html>