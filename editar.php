<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'petvida';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM usuario WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
    $stmt->close();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $usuario = $_POST['usuario'];
    $contraseña = $_POST['contraseña'];
    $stmt = $conn->prepare("UPDATE usuario SET nombre = ?, usuario = ?, contraseña =? WHERE id = ?");
    $stmt->bind_param("ssi", $nombre, $usuario, $id);
    $stmt->execute();
    $stmt->close();
    header('Location: vistaCliente.php'); // Redirigir al archivo cliente.php
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background: #ffffff;
            padding: 20px 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            max-width: 400px;
            width: 100%;
            text-align: center;
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
        .form-container input {
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
    <div class="form-container">
        <h1>Editar Usuario</h1>
        <form method="post">
            <input type="hidden" name="id" value="<?= htmlspecialchars($usuario['id']) ?>">
            <label>Nombre:</label>
            <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
            <label>Usuario:</label>
            <input type="text" name="usuario" value="<?= htmlspecialchars($usuario['usuario']) ?>" required>
            <label>Contraseña:</label>
            <div style="position: relative;">
                <input type="password" id="password" name="contraseña" value="<?= htmlspecialchars($usuario['contraseña']) ?>" required>
                <span class="toggle-password" onclick="togglePassword()">👁️</span>
            </div>
            
            <button type="submit">Actualizar</button>
        </form>
    </div>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.toggle-password');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.textContent = '🙈'; // Cambiar ícono a ojo cerrado
            } else {
                passwordInput.type = 'password';
                toggleIcon.textContent = '👁️'; // Cambiar ícono a ojo abierto
            }
        }
    </script>
</body>
</html>
