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
            <form action="index.php" method="POST">
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
                <input type="password" id="repite-contrasena" name="repite-contrasena" placeholder="Repite la contraseña" required>

                <button type="submit">Registrar</button>
            </form>

            <p>¿Ya tienes cuenta? <a href="index.php">Inicia sesión aquí</a></p>
        </div>
    </div>
</body>
</html>
