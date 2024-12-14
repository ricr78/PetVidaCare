<?php
session_start();
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'petvida';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['id']; // Obtener el ID del usuario logueado

// Verificar que el usuario tiene id_cargo = 3
$stmt = $conn->prepare("SELECT id_cargo FROM usuario WHERE id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();

if ($user_data['id_cargo'] != 3) {
    header("Location: acceso_denegado.php");
    exit;
}

// Variable para mensajes
$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_mascota = $_POST['nombre_mascota'];
    $especie = $_POST['especie'];
    $raza = $_POST['raza'];
    $edad = $_POST['edad'];

    // Insertar una nueva mascota vinculada al usuario logueado
    $stmt = $conn->prepare("INSERT INTO mascotas (nombre, especie, raza, edad, idus) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssii", $nombre_mascota, $especie, $raza, $edad, $usuario_id);
    if ($stmt->execute()) {
        $mensaje = "Mascota registrada con éxito.";
    } else {
        $mensaje = "Error al registrar la mascota.";
    }
    $stmt->close();
}

// Obtener las mascotas registradas por el usuario logueado
$stmt = $conn->prepare("SELECT nombre, especie, raza, edad, fecha_registro FROM mascotas WHERE idus = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$mascotas = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Mascotas</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            min-height: 100vh;
        }
        .header {
            width: 100%;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .header a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        .form-container {
            background: #ffffff;
            padding: 20px 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            max-width: 400px;
            width: 100%;
            text-align: center;
            margin-bottom: 20px;
        }
        .form-container h1 {
            margin-bottom: 20px;
            color: #333;
        }
        .form-container label {
            display: block;
            margin: 10px 0 5px;
            text-align: left;
            font-weight: bold;
            color: #555;
        }
        .form-container input, .form-container select {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .form-container button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }
        .form-container button:hover {
            background-color: #0056b3;
        }
        .mascotas-container {
            width: 100%;
            max-width: 600px;
        }
        .mascotas-container table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .mascotas-container th, .mascotas-container td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .mascotas-container th {
            background-color: #f4f4f4;
        }
        .mensaje {
            margin-bottom: 15px;
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Registro de Mascotas</h1>
        <a href="logout.php">Cerrar Sesión</a>
    </div>

    <div class="form-container">
        <h1>Registrar Mascota</h1>
        <?php if ($mensaje): ?>
            <div class="mensaje">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>
        <form method="post">
            <label for="nombre_mascota">Nombre de la Mascota:</label>
            <input type="text" id="nombre_mascota" name="nombre_mascota" placeholder="Nombre" required>

            <label for="especie">Especie:</label>
            <select id="especie" name="especie" required>
                <option value="Perro">Perro</option>
                <option value="Gato">Gato</option>
                <option value="Ave">Ave</option>
                <option value="Otro">Otro</option>
            </select>

            <label for="raza">Raza:</label>
            <input type="text" id="raza" name="raza" placeholder="Raza" required>

            <label for="edad">Edad (en años):</label>
            <input type="number" id="edad" name="edad" placeholder="Edad" min="0" required>

            <button type="submit">Registrar Mascota</button>
        </form>
    </div>

    <div class="mascotas-container">
        <h2>Mis Mascotas Registradas</h2>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Especie</th>
                    <th>Raza</th>
                    <th>Edad</th>
                    <th>Fecha de Registro</th>
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
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No has registrado ninguna mascota.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
