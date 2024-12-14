<?php
// Conexión a la base de datos
function obtenerConexion() {
    $servidor = "localhost";
    $usuario = "root";
    $contraseña = ""; // Cambia esto si tienes contraseña
    $baseDatos = "petvida";

    $conexion = new mysqli($servidor, $usuario, $contraseña, $baseDatos);

    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    return $conexion;
}

// Conexión
$conexion = obtenerConexion();

// Obtener usuarios con su fecha de último inicio de sesión
$usuariosActivosQuery = "SELECT id, nombre, usuario, ultimo_inicio FROM usuario ORDER BY ultimo_inicio DESC";
$usuariosActivosResult = $conexion->query($usuariosActivosQuery);

// Número de usuarios activos en las últimas 3 semanas
$usuariosActivos3SemanasQuery = "SELECT COUNT(*) AS activos FROM usuario WHERE ultimo_inicio >= DATE_SUB(NOW(), INTERVAL 3 WEEK)";
$usuariosActivos3SemanasResult = $conexion->query($usuariosActivos3SemanasQuery);
$usuariosActivos3Semanas = $usuariosActivos3SemanasResult->fetch_assoc()['activos'];

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Usuarios Activos</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #007bff;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-weight: bold;
        }
        .navbar a:hover {
            text-decoration: underline;
        }
        .container {
            width: 90%;
            margin: 20px auto;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #007bff;
            color: #fff;
        }
        .report-container {
            margin-bottom: 40px;
        }
        .active-count {
            background-color: #28a745;
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<div class="navbar">
        <div class="logo">
            <a href="admin.php">Gestión de Usuarios</a>
        </div>
        <div class="links">
            <a href="admin.php">Inicio</a>
            <a href="reportes.php">Reportes</a>
            <a href="logout.php">Cerrar Sesión</a>
        </div>
    </div>
    <div class="container">
        <h1>Usuarios Activos</h1>

       
        <div class="report-container">
            <h2>Total de Usuarios Activos en las Últimas 3 Semanas</h2>
            <div class="active-count">
                <strong><?php echo $usuariosActivos3Semanas; ?></strong> Usuarios Activos
            </div>
        </div>

        
        <div class="report-container">
            <h2>Lista de Usuarios con Último Inicio de Sesión</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Usuario</th>
                        <th>Último Inicio de Sesión</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($fila = $usuariosActivosResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $fila['id']; ?></td>
                            <td><?php echo $fila['nombre']; ?></td>
                            <td><?php echo $fila['usuario']; ?></td>
                            <td><?php echo $fila['ultimo_inicio'] ? $fila['ultimo_inicio'] : 'Nunca'; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
