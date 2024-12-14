<?php
// Función para obtener la conexión a la base de datos
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


// Conexión a la base de datos
$conexion = obtenerConexion();

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id']; // El ID se usa para identificar si es creación o edición
    $nombre = $_POST['nombre'];
    $usuario = $_POST['usuario'];
    $contraseña = $_POST['contraseña'];
    $id_cargo = $_POST['id_cargo'];

    if (empty($id)) {
        // Crear usuario
        $consulta = "INSERT INTO usuario (nombre, usuario, contraseña, id_cargo) VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($consulta);
        $stmt->bind_param("sssi", $nombre, $usuario, $contraseña, $id_cargo);
    } else {
        // Actualizar usuario
        $consulta = "UPDATE usuario SET nombre = ?, usuario = ?, contraseña = ?, id_cargo = ? WHERE id = ?";
        $stmt = $conexion->prepare($consulta);
        $stmt->bind_param("sssii", $nombre, $usuario, $contraseña, $id_cargo, $id);
    }

    $stmt->execute();
    $stmt->close();
}

// Eliminar usuario
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];

    $consulta = "DELETE FROM usuario WHERE id = ?";
    $stmt = $conexion->prepare($consulta);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Obtener todos los usuarios
$consulta = "SELECT * FROM usuario";
$resultado = $conexion->query($consulta);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - CRUD Usuarios</title>
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
        .form-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background-color: #28a745;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .delete-button {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .delete-button:hover {
            background-color: #c82333;
        }
        .guardar-btn {
            background-color: #007bff;
            color: white;
        }
        .guardar-btn:hover {
            background-color: #0056b3;
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
    <br>
    <br>
    <div class="container">
        <h1>Gestión de Usuarios</h1>

        <!-- Formulario para Crear/Actualizar Usuario -->
        <div class="form-container">
            <form method="POST" action="">
                <input type="hidden" name="id" id="id">
                <input type="text" name="nombre" id="nombre" placeholder="Nombre" required>
                <input type="text" name="usuario" id="usuario" placeholder="Usuario" required>
                <input type="password" name="contraseña" id="contraseña" placeholder="Contraseña" required>
                <select name="id_cargo" id="id_cargo" required>
                    <option value="1">Administrador</option>
                    <option value="2">Usuario</option>
                    <option value="3">Cliente</option>
                </select>
                <button type="submit" name="crear" id="crear-btn">Crear Usuario</button>
                <button type="submit" name="actualizar" id="guardar-btn" class="guardar-btn" style="display: none;">Guardar Cambios</button>
            </form>
        </div>

        <!-- Tabla de Usuarios -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Contraseña</th>
                    <th>ID Cargo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $fila['id']; ?></td>
                        <td><?php echo $fila['nombre']; ?></td>
                        <td><?php echo $fila['usuario']; ?></td>
                        <td><?php echo $fila['contraseña']; ?></td>
                        <td><?php echo $fila['id_cargo']; ?></td>
                        <td>
                            <button onclick="editarUsuario(<?php echo htmlspecialchars(json_encode($fila)); ?>)">Editar</button>
                            <a href="?eliminar=<?php echo $fila['id']; ?>" class="delete-button">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        function editarUsuario(usuario) {
            document.getElementById('id').value = usuario.id;
            document.getElementById('nombre').value = usuario.nombre;
            document.getElementById('usuario').value = usuario.usuario;
            document.getElementById('contraseña').value = usuario.contraseña;
            document.getElementById('id_cargo').value = usuario.id_cargo;

           
            document.getElementById('crear-btn').style.display = 'none';
            document.getElementById('guardar-btn').style.display = 'block';
        }
    </script>
</body>
</html>
