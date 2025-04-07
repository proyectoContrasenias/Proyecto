<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Contraseña - KeySafe</title>
    <link rel="stylesheet" href="estiloeditar.css">
</head>
<body>
    <header>
        <h1>KeySafe - Editar Contraseña</h1>
        <div>
            <button class="logout" onclick="location.href='index.php'">Cerrar Sesión</button>
            <button class="back-button" onclick="location.href='dashboard.php'">Volver al Menú</button>
        </div>
    </header>
    

    <main>
        <form class="form-container" onsubmit="event.preventDefault(); location.href='dashboard.php';">
            <label for="nombre">Nombre de la Página:</label>
            <input type="text" id="nombre" name="nombre" value="Facebook" required>

            <label for="usuario">Usuario:</label>
            <input type="text" id="usuario" name="usuario" value="usuario@ejemplo.com" required>

            <label for="contrasena">Contraseña:</label>
            <div class="password-container">
                <input type="password" id="contrasena" name="contrasena" value="12345678" required>
                <button type="button" class="toggle-password" onclick="togglePassword()">Mostrar</button>
            </div>

            <button type="submit" class="save-button">Guardar Cambios</button>
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
</body>
</html>

