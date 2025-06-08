<?php
//Inicia la sesión y verifica que el usuario haya iniciado sesión, si no lo redirige al login
session_start();
if (!isset($_SESSION['user_id'])) {
   header("Location: index.php?error=Debes iniciar sesión para acceder");
   exit();
}
//Si el usuario pulsa el botón de cerrar sesión, la sesión se elimina y se redirige al login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php?error=Has cerrado sesión correctamente");
    exit();
}

//Incluye el script de cifrado y descifrado de contraseñas
require_once 'encriptacion.php';

//Establece conexión con la base de datos y si hay error detiene la ejecución
$con = new mysqli("192.168.20.35", "proyecto", "proyecto", "keysafe");
if ($con->connect_error) {
    die("Error de conexión: " . $con->connect_error);
}

//Se pasa el id de la contraseña desde el dashboard y se edita esa contraseña
if (isset($_POST['id'])) {
    $id_pagina = $_POST['id'];
    $sentencia = "SELECT * FROM contraseñas WHERE id = ?";
    $stmt = $con->prepare($sentencia);
    $stmt->bind_param('i', $id_pagina);
}

//Verifica si el formulario fue enviado
if (isset($_POST['update_id'])) {
    $update_id = $_POST['update_id'];
    $pagina = $_POST['nombre'];
    $usuario = $_POST['usuario'];
    $contrasena_cifrada = encryptPassword($_POST['contrasena']); //Cifra la contraseña

    //Hace la actualización de los datos en la base de datos
    $stmtt = $con->prepare("UPDATE contraseñas SET pagina = ?, usuario = ?, contraseña = ? WHERE id = ?");
    $stmtt->bind_param("sssi", $pagina, $usuario, $contrasena_cifrada, $update_id);
    //Si se ejecuta correctamente redirige al dashboard, si no muestra un error
    if ($stmtt->execute()) {
        $stmtt->close();
        $id_usuario = $_SESSION['user_id'];
        header("Location: dashboard.php?id_usuario=$id_usuario");
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
        <?php
        $id_usuario = $_SESSION['user_id'];
        echo "<button class='back-button' onclick=\"location.href='dashboard.php'\">Volver al Menú</button>";
        ?>
    </div>
</header>

<main>
    <form class="form-container" method="POST">
        <?php
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            while ($recorrido = $result->fetch_assoc()) {
                $contraseña_descifrada = decryptPassword($recorrido["contraseña"]);
                echo '
                <input type="hidden" name="update_id" value="' . $recorrido["id"] . '">
                
                <label for="nombre">Nombre de la Página:</label>
                <input type="text" id="nombre" name="nombre" value="' . $recorrido["pagina"] . '" required>

                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario" value="' . $recorrido["usuario"] . '" required>

                <label for="contrasena">Contraseña:</label>
                <div class="password-container">
                    <input type="password" id="contrasena" name="contrasena" value="' . $contraseña_descifrada . '" required>
                    <button type="button" class="toggle-password" onclick="togglePassword()">Mostrar</button>
                </div>

                <button type="submit" class="save-button">Guardar Cambios</button>';
            }
        }
        ?>
    </form>
</main>

<script>
    //Alterna entre mostrar y ocultar la contraseña
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