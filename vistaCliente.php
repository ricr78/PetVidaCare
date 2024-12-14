<?php
// Conexión a la base de datos
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'petvida';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}

// Operaciones CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['crear'])) {
        $nombre = $_POST['nombre'];
        $usuario = $_POST['usuario'];
        $contraseña = password_hash($_POST['contraseña'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO usuario (nombre, usuario, contraseña, id_cargo, ultimo_inicio) VALUES (?, ?, ?, 3, NOW())");
        $stmt->bind_param("sss", $nombre, $usuario, $contraseña);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['editar'])) {
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $usuario = $_POST['usuario'];
        $stmt = $conn->prepare("UPDATE usuario SET nombre = ?, usuario = ? WHERE id = ? AND id_cargo = 3");
        $stmt->bind_param("ssi", $nombre, $usuario, $id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['eliminar'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM usuario WHERE id = ? AND id_cargo = 3");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['volver'])){

        header('Location: usuario.php');
        exit;

    }

}

// Obtener los datos del cliente con ID 3
$stmt = $conn->prepare("SELECT * FROM usuario WHERE id_cargo = 3");
$stmt->execute();
$result = $stmt->get_result();
$clientes = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Cliente ID 3</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input, button {
            margin: 5px 0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestión del Cliente (ID: 3)</h1>

        <!-- Tabla de clientes -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td><?= htmlspecialchars($cliente['id']) ?></td>
                        <td><?= htmlspecialchars($cliente['nombre']) ?></td>
                        <td><?= htmlspecialchars($cliente['usuario']) ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($cliente['id']) ?>">
                                <button type="submit" name="eliminar">Eliminar</button>
                            </form>
                            <form method="get" action="editar.php" style="display:inline;">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($cliente['id']) ?>">
                                <button type="submit">Editar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Formulario para agregar cliente -->
        <h2>Agregar Cliente</h2>
        <form method="post">
            <input type="text" name="nombre" placeholder="Nombre" required>
            <input type="text" name="usuario" placeholder="Usuario" required>
            
            <input type="password" name="contraseña" placeholder="Contraseña" required>
            
            <button type="submit" name="crear">Crear Cliente</button>
            
        </form>
        <form method="post">
        <button type="submit" name="volver">Volver</button>
        </form>
    </div>
    
</body>
</html>
