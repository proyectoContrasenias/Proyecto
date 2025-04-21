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
// Hace la consulta y recorre la base de datos
$sql = "SELECT * FROM contraseñas;";
$resultado = $con->query($sql);
$row = $resultado->fetch_assoc();
// Si se pulsa el botón borrar elimina el juego de la base de datos 
if (isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $stmt = $con->prepare("DELETE FROM contraseñas WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
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
                if ($resultado->num_rows > 0) {
                    while ($row = $resultado->fetch_assoc()) {
                        $id_pagina = $row['id'];
                        echo "<tr>
                            <td><a href='editar.php?id=$id_pagina'>".$row['pagina']."</a></td>
                            <td>********</td>
                            <td>
                                <form method='POST' style='display:inline;'>
                                    <input type='hidden' name='delete_id' value='".$row['id']."'>
                                    <button type='submit' class='delete'>Borrar</button>
                                </form>
                            </td>
                        </tr>";
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
    $con->close();
    ?>
</body>

</html>