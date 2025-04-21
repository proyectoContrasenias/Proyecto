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
// Se conecta a la base de datos y si no muestra el error
$con = new mysqli("localhost", "proyecto", "proyecto", "keysafe");
if ($con->connect_error) {
    die("Error de conexión: " . $con->connect_error);
}
// Revisa si se ha pasado correctamente el id del juego desde la página juegos.php y si se ha pasado bien el id, muestra todos los datos del juego
if(isset($_GET['id'])) {
    $id_pagina=$_GET['id'];
    $sentencia="SELECT * FROM contraseñas WHERE id = ?;";
    $stmt=$con->prepare($sentencia);
    $stmt->bind_param('i',$id_pagina);
}
// Si se pulsa el botón guardar cambios actualiza el juego de la base de datos 
if (isset($_POST['update_id'])) {
    $update_id = $_POST['update_id'];
    $stmtt = $con->prepare("UPDATE contraseñas SET pagina = ?, usuario = ?, contraseña = ? WHERE id = ?");
    $stmtt->bind_param("sssi", $_POST['nombre'], $_POST['usuario'], $_POST['contrasena'], $update_id);
    if ($stmtt->execute()) {
        $stmtt->close();
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<p>Error al actualizar: " . $stmtt->error . "</p>";
    }
}
?>
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
            <form method="POST" style="display:inline;">
                <button type="submit" name="logout" class="logout">Cerrar sesión</button>
            </form>
            <button class="back-button" onclick="location.href='dashboard.php'">Volver al Menú</button>
        </div>
    </header>
    

    <main>
        <form class="form-container" method="POST">
        <?php
                // Recorre la base de datos para mostrar la información del juego
                if($stmt->execute()){
                    $result=$stmt->get_result();
                    while($recorrido=$result->fetch_assoc()){
                        echo '
                        <input type="hidden" name="update_id" value="' . $recorrido["id"] . '">
                        
                        <label for="nombre">Nombre de la Página:</label>
                        <input type="text" id="nombre" name="nombre" value="' . $recorrido["pagina"] . '" required>

                        <label for="usuario">Usuario:</label>
                        <input type="text" id="usuario" name="usuario" value="' . $recorrido["usuario"] . '" required>

                        <label for="contrasena">Contraseña:</label>
                        <div class="password-container">
                            <input type="password" id="contrasena" name="contrasena" value="' . $recorrido["contraseña"] . '" required>
                            <button type="button" class="toggle-password" onclick="togglePassword()">Mostrar</button>
                        </div>

                        <button type="submit" class="save-button">Guardar Cambios</button>';
                }
                }
                ?>
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

