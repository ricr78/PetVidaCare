<?php
// Verificar si se recibieron los datos del formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!empty($_POST['usuario']) && !empty($_POST['contraseña'])) {
        $usuario = $_POST['usuario'];
        $contraseña = $_POST['contraseña'];

        // Conexión a la base de datos
        $conexion = new mysqli("localhost", "root", "", "petvida");

        // Verificar conexión
        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }

        // Consulta para verificar usuario
        $consulta = "SELECT * FROM usuario WHERE usuario = ?";
        $stmt = $conexion->prepare($consulta);
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($fila = $resultado->fetch_assoc()) {
            // Verificar contraseña
            if ($contraseña === $fila['contraseña']) { // Aquí usa hashing para mayor seguridad
                session_start();
                $_SESSION['id'] = $fila['id'];
                $_SESSION['usuario'] = $fila['usuario'];
                $_SESSION['id_cargo'] = $fila['id_cargo'];

                // Actualizar la columna `ultimo_inicio`
                $updateQuery = "UPDATE usuario SET ultimo_inicio = NOW() WHERE id = ?";
                $stmtUpdate = $conexion->prepare($updateQuery);
                $stmtUpdate->bind_param("i", $fila['id']);
                $stmtUpdate->execute();
                $stmtUpdate->close();

                // Redirigir según el rol
                switch ($fila['id_cargo']) {
                    case 1:
                        header("Location: admin.php");
                        exit();
                    case 2:
                        header("Location: usuario.php");
                        exit();
                    case 3:
                        header("Location: cliente.php");
                        exit();
                    default:
                        echo "<div style='color: red; text-align: center; margin-top: 20px;'>Rol no reconocido.</div>";
                        break;
                }
            } else {
                echo "<div style='color: red; text-align: center; margin-top: 20px;'>Contraseña incorrecta.</div>";
            }
        } else {
            echo "<div style='color: red; text-align: center; margin-top: 20px;'>Usuario no encontrado.</div>";
        }

        // Cerrar conexión
        $stmt->close();
        $conexion->close();
    } else {
        echo "<div style='color: red; text-align: center; margin-top: 20px;'>Los campos usuario y contraseña son obligatorios.</div>";
    }
} else {
    echo "<div style='color: red; text-align: center; margin-top: 20px;'>Método de solicitud no permitido.</div>";
}
?>
