<?php
// Mensaje para mostrar resultado
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Conexión al servidor MySQL
    $conexion = new mysqli("localhost", "root", ""); // Cambia la contraseña si es necesaria

    // Verificar conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    if (isset($_POST['nombre_bd']) && !empty($_POST['nombre_bd'])) {
        // Crear una nueva base de datos
        $nombre_bd = $_POST['nombre_bd'];

        $sql = "CREATE DATABASE $nombre_bd";
        if ($conexion->query($sql) === TRUE) {
            $mensaje = "Base de datos '$nombre_bd' creada exitosamente.";
        } else {
            $mensaje = "Error al crear la base de datos: " . $conexion->error;
        }
    } elseif (isset($_POST['eliminar_bd']) && !empty($_POST['eliminar_bd'])) {
        // Eliminar una base de datos
        $eliminar_bd = $_POST['eliminar_bd'];

        $sql = "DROP DATABASE $eliminar_bd";
        if ($conexion->query($sql) === TRUE) {
            $mensaje = "Base de datos '$eliminar_bd' eliminada exitosamente.";
        } else {
            $mensaje = "Error al eliminar la base de datos: " . $conexion->error;
        }
    }

    // Cerrar conexión
    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear y Administrar Bases de Datos</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #fff;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 600px;
            text-align: center;
            margin-bottom: 20px;
        }
        h1 {
            margin-bottom: 20px;
            color: #333;
        }
        input[type="text"], select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-bottom: 10px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .mensaje {
            margin-top: 15px;
            color: #28a745;
        }
        .error {
            color: #dc3545;
        }
        .list-container {
            margin-top: 20px;
            text-align: left;
        }
        .list-container ul {
            list-style: none;
            padding: 0;
        }
        .list-container ul li {
            padding: 5px 0;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #007bff;
            padding: 10px 20px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .navbar .logo {
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
        }
        .navbar .links {
            display: flex;
            gap: 15px;
        }
        .navbar .links a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            transition: color 0.3s;
        }
        .navbar .links a:hover {
            color: #ffdd57;
        }
        .navbar .logout {
            background-color: #dc3545;
            color: white;
            padding: 5px 15px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .navbar .logout:hover {
            background-color: #c82333;
        }
        .content {
            padding: 20px;
            font-size: 18px;
        }
    </style>
</head>
<body>
<nav class="navbar">
        <a href="cliente.php" class="logo">Cliente</a>
        <div class="links">
            <a href="usuario.php">inicio</a>
            <a href="vistaCliente.php">clientes</a>
            <a href="acceso_denegado.php">mascotas</a>
        </div>
        <form method="POST" action="logout.php">
            <button type="submit" class="logout">Cerrar Sesión</button>
        </form>
    </nav>
    <br><br>
    <div class="container">
        <h1>Crear Base de Datos</h1>
        <form method="POST" action="">
            <input type="text" name="nombre_bd" placeholder="Nombre de la Base de Datos" required>
            <button type="submit">Crear Base de Datos</button>
        </form>

        <!-- Mensaje de resultado -->
        <?php if (!empty($mensaje)): ?>
            <p class="mensaje <?php echo (strpos($mensaje, 'Error') !== false) ? 'error' : ''; ?>">
                <?php echo $mensaje; ?>
            </p>
        <?php endif; ?>
    </div>

    <!-- Listar bases de datos existentes -->
    <div class="container">
        <h1>Eliminar Base de Datos</h1>
        <form method="POST" action="">
            <select name="eliminar_bd" required>
                <option value="">Selecciona una Base de Datos</option>
                <?php
                // Listar bases de datos
                $conexion = new mysqli("localhost", "root", ""); // Cambia la contraseña si es necesaria
                if ($conexion->connect_error) {
                    die("Error de conexión: " . $conexion->connect_error);
                }
                $result = $conexion->query("SHOW DATABASES");
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['Database'] . "'>" . $row['Database'] . "</option>";
                }
                $conexion->close();
                ?>
            </select>
            <button type="submit">Eliminar Base de Datos</button>
        </form>
    </div>
</body>
</html>