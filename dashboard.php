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
    <title>KeySafe - Gestor de Contraseñas</title>
    <link rel="stylesheet" href="estilolista.css">
</head>

<body>
    <?php
    $conn = new mysqli("127.0.0.1", "proyecto", "proyecto", "keysafe");
    if ($conn->connect_error) {
        die("Error de conexión" . $conn->connect_error);
    }
    ?>
    <header>
        <h1>KeySafe - Gestor de Contraseñas</h1>
        <form method="POST" style="display:inline;">
            <button type="submit" name="logout" class="logout">Cerrar sesión</button>
        </form>
    </header>
    <main>
        <table>
            <thead>
                <tr>
                    <th>Nombre de la Página</th>
                    <th>Contraseña</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $resultado = $conn->query("SELECT pagina, usuario, contraseña FROM contraseñas");
                if ($resultado->num_rows > 0) {
                    while ($row = $resultado->fetch_assoc()) {
                        echo "<tr><td><a href='editar.php'>".$row['pagina'] . "</a></td>
                    <td>********</td>
                    <td><button class='delete'>Borrar</button></td>";
                    }
                } else {
                    echo 'Sin resultados';
                }
                $resultado->close(); 
                ?>
            </tbody>
        </table>
        <button class="add-password" onclick="location.href='crear.php'">Crear Nueva Contraseña</button>
    </main>

    <?php
    $conn->close();
    ?>
</body>

</html>