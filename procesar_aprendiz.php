<?php
// Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sena_aprendices";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Establecer el conjunto de caracteres
$conn->set_charset("utf8");

// Determinar la acción basada en el botón presionado
$accion = isset($_POST['accion']) ? $_POST['accion'] : '';

switch ($accion) {
    case 'insertar':
        // Obtener datos del formulario
        $id_aprendiz = $_POST['id_aprendiz'];
        $nombre = $_POST['nombre'];
        $direccion = $_POST['direccion'];
        $telefono = $_POST['telefono'];
        $programa = $_POST['programa'];
        $ficha = $_POST['ficha'];
        
        // Preparar la consulta SQL
        $stmt = $conn->prepare("INSERT INTO aprendices (id_aprendiz, nombre, direccion, telefono, programa, ficha) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $id_aprendiz, $nombre, $direccion, $telefono, $programa, $ficha);
        
        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo "<script>
                alert('Aprendiz registrado correctamente');
                window.location.href = 'index.html';
            </script>";
        } else {
            echo "<script>
                alert('Error al registrar el aprendiz: " . $stmt->error . "');
                window.location.href = 'index.html';
            </script>";
        }
        
        $stmt->close();
        break;
        
    case 'consultar':
        // Obtener ID del aprendiz
        $id_aprendiz = $_POST['id_aprendiz'];
        
        // Preparar la consulta SQL
        $stmt = $conn->prepare("SELECT * FROM aprendices WHERE id_aprendiz = ?");
        $stmt->bind_param("s", $id_aprendiz);
        
        // Ejecutar la consulta
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            // Devolver los datos como JSON y establecer modo consulta
            echo "<script>
                var aprendizData = " . json_encode($row) . ";
                localStorage.setItem('aprendizData', JSON.stringify(aprendizData));
                localStorage.setItem('modoConsulta', 'true');
                alert('Aprendiz encontrado');
                window.location.href = 'index.html';
            </script>";
        } else {
            echo "<script>
                alert('No se encontró ningún aprendiz con ese ID');
                window.location.href = 'index.html';
            </script>";
        }
        
        $stmt->close();
        break;
        
    case 'actualizar':
        // Obtener datos del formulario
        $id_aprendiz = $_POST['id_aprendiz'];
        $nombre = $_POST['nombre'];
        $direccion = $_POST['direccion'];
        $telefono = $_POST['telefono'];
        $programa = $_POST['programa'];
        $ficha = $_POST['ficha'];
        
        // Preparar la consulta SQL
        $stmt = $conn->prepare("UPDATE aprendices SET nombre = ?, direccion = ?, telefono = ?, programa = ?, ficha = ? WHERE id_aprendiz = ?");
        $stmt->bind_param("ssssss", $nombre, $direccion, $telefono, $programa, $ficha, $id_aprendiz);
        
        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo "<script>
                alert('Datos del aprendiz actualizados correctamente');
                window.location.href = 'index.html';
            </script>";
        } else {
            echo "<script>
                alert('Error al actualizar los datos: " . $stmt->error . "');
                window.location.href = 'index.html';
            </script>";
        }
        
        $stmt->close();
        break;
        
    case 'eliminar':
        // Obtener ID del aprendiz
        $id_aprendiz = $_POST['id_aprendiz'];
        
        // Preparar la consulta SQL
        $stmt = $conn->prepare("DELETE FROM aprendices WHERE id_aprendiz = ?");
        $stmt->bind_param("s", $id_aprendiz);
        
        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo "<script>
                alert('Aprendiz eliminado correctamente');
                window.location.href = 'index.html';
            </script>";
        } else {
            echo "<script>
                alert('Error al eliminar el aprendiz: " . $stmt->error . "');
                window.location.href = 'index.html';
            </script>";
        }
        
        $stmt->close();
        break;
        
    default:
        echo "<script>
            alert('Acción no válida');
            window.location.href = 'index.html';
        </script>";
}

// Cerrar la conexión
$conn->close();
?>