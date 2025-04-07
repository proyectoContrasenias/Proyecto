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
    $conn->close();
    ?>
    <header>
        <h1>KeySafe - Gestor de Contraseñas</h1>
        <button class="logout" onclick="location.href='index.php'">Cerrar Sesión</button>
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
                <tr>
                    <td><a href="editar.php">Facebook</a></td>
                    <td>********</td>
                    <td><button class="delete">Borrar</button></td>
                </tr>
                <tr>
                    <td><a href="editar.php">Twitter</a></td>
                    <td>********</td>
                    <td><button class="delete">Borrar</button></td>
                </tr>
                <tr>
                    <td><a href="editar.php">Instagram</a></td>
                    <td>********</td>
                    <td><button class="delete">Borrar</button></td>
                </tr>
            </tbody>
        </table>
        <button class="add-password" onclick="location.href='crear.php'">Crear Nueva Contraseña</button>
    </main>
</body>

</html>