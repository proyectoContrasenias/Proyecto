<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="styles-login.css">
</head>
<body>
<?php 
session_start();
// Revisa si hay un mensaje de error y si hay lo muestra abajo
if (isset($_GET["error"])) {
    $mensaje_error = $_GET["error"];
} else {
    $mensaje_error = '';
}
// Revisa si el formulario se envio con POST y asigna a las variables username y password el valor del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $username = $_POST['username'];
    $password = $_POST['password'];
// Si las variables username y password no están vacias se conecta a la base de datos
    if (!empty($username) && !empty($password)) {
        $con = new mysqli("localhost","proyecto","proyecto","keysafe");
// Si no se conecta a la base de datos da un error
        if ($con->connect_error) {
            die("Error de conexión: " . $con->connect_error);
        }
// Hace la consulta para seleccionar el usuario y contraseña de la base de datos
        $sql = "SELECT id,contraseña FROM usuarios WHERE username = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s",$username);
        $stmt->execute();
        $result = $stmt->get_result();
/* Recorre la base de datos y comprueba si la contraseña aportada es igual que la que se encuentra en la base de datos,
si es igual redirige a juegos.php, si no da un mensaje de error*/
        if ($result->num_rows>0) {
            $row = $result->fetch_assoc();

            if ($password === $row['contraseña']) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $username;
                $id_usuario = $row['id'];
                header("Location: dashboard.php?id_usuario=$id_usuario");
                exit();
            } else {
                $mensaje_error = "Usuario o contraseña incorrectos";
            }
        } else {
            $mensaje_error = "Usuario o contraseña incorrectos";
        }
        $stmt->close();
        $con->close();
    } else {
        $mensaje_error = "Complete usuario y contraseña";
    }
}
?>
    <div class="container">
        <div class="form-container">
            <h2>Iniciar sesión</h2>
            <!-- Muestra el mensaje de error con estilos -->
            <?php if (!empty($mensaje_error)): ?>
                <p class="mensaje-error"><?php echo $mensaje_error; ?></p>
            <?php endif; ?>
            <form action="index.php" method="POST">
                <label for="username">Usuario:</label>
                <input type="text" id="username" name="username" placeholder="Ingresa tu usuario" required>

                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña" required>

                <button type="submit">Iniciar sesión</button>
            </form>

            <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
        </div>
    </div>
</body>
</html>
