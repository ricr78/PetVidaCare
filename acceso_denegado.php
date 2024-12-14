<?php 
session_start();

// Datos de conexión a la base de datos
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'petvida';

// Crear conexión a la base de datos
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}

// Obtener datos de mascotas
$stmt = $conn->prepare("SELECT nombre, especie, raza, edad, fecha_registro ,idus FROM mascotas");
$stmt->execute();
$result = $stmt->get_result();
$mascotas = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Mascotas</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            margin: 20px auto;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
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
        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
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
<div class="container">
    <h1>Listado de Mascotas</h1>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Especie</th>
                <th>Raza</th>
                <th>Edad</th>
                <th>Fecha de Registro</th>
                <th>Id Usuario</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($mascotas) > 0): ?>
                <?php foreach ($mascotas as $mascota): ?>
                    <tr>
                        <td><?= htmlspecialchars($mascota['nombre']) ?></td>
                        <td><?= htmlspecialchars($mascota['especie']) ?></td>
                        <td><?= htmlspecialchars($mascota['raza']) ?></td>
                        <td><?= htmlspecialchars($mascota['edad']) ?></td>
                        <td><?= htmlspecialchars($mascota['fecha_registro']) ?></td>
                        <td><?= htmlspecialchars($mascota['idus']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No hay mascotas registradas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
