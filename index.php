<?php 
//Inicia la sesión y carga las dependencias para Google Authenticator
session_start();
require_once 'vendor/autoload.php';


//Si se ha enviado un error se guarda en una variable para mostrarlo luego
$mensaje_error = isset($_GET["error"]) ? $_GET["error"] : '';

//Comprueba si se ha enviado el formulario por POST y recoge los datos del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $username = $_POST['username'];
    $password = $_POST['password'];
    $auth_code = $_POST['auth_code'];

//Solo continua si los 3 campos han sido completados
    if (!empty($username) && !empty($password) && !empty($auth_code)) {
        $con = new mysqli("192.168.20.35","proyecto","proyecto","keysafe"); //Conexión a la base de datos

        //Verificación de errores
        if ($con->connect_error) {
            die("Error de conexión: " . $con->connect_error);
        }

        // Obtiene la contraseña y el código secreto de Google Authenticator del usuario
        $sql = "SELECT id, contraseña, google_auth_code FROM usuarios WHERE username = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        //Comprueba si el usuario existe y si la contraseña coincide
        if ($result->num_rows>0) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['contraseña'])) {
                // Verificación de Google Authenticator
                $ga = new PHPGangsta_GoogleAuthenticator();
                $secret = $row['google_auth_code'];
                $validCode = $ga->verifyCode($secret, $auth_code, 2); // 2=tolerancia en minutos

                //Si todo esta bien guarda la sesión y redirige al dashboard del usuario
                if ($validCode) {
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['user_name'] = $username;
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $mensaje_error = "Código de autenticación incorrecto."; //Mensaje de error si el codigo es incorrecto
                }
            } else {
                $mensaje_error = "Usuario o contraseña incorrectos."; //Mensaje de error si la contraseña es incorrecta
            }
        } else {
            $mensaje_error = "Usuario o contraseña incorrectos."; //Mensaje de error si el usuario es incorrecto
        }

        //Cierre de conexión
        $stmt->close();
        $con->close();
    } else {
        $mensaje_error = "Complete usuario, contraseña y código de autenticación."; //Mensaje de error si no se completa alguno de los campos
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="styles-login.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Iniciar sesión</h2>

            <?php if (!empty($mensaje_error)): ?>
                <p class="mensaje-error"><?php echo $mensaje_error; ?></p>
            <?php endif; ?>

            <form action="index.php" method="POST">
                <label for="username">Usuario:</label>
                <input type="text" id="username" name="username" placeholder="Ingresa tu usuario" required>

                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña" required>

                <label for="auth_code">Código de autenticación:</label>
                <input type="text" id="auth_code" name="auth_code" placeholder="Ingresa tu código de Google Authenticator" required>

                <button type="submit">Iniciar sesión</button>
            </form>

            <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
        </div>
    </div>
</body>
</html>
