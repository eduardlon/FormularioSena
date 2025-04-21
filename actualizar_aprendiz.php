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
    die(json_encode(['success' => false, 'error' => "Error de conexión: " . $conn->connect_error]));
}

// Establecer el conjunto de caracteres
$conn->set_charset("utf8");

header('Content-Type: application/json');
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'error' => 'Método no permitido']));
}

$accion = $_POST['accion'] ?? '';

try {
    if ($accion === 'actualizar') {
        $id = $_POST['id_aprendiz'];
        $nombre = $_POST['nombre'];
        $direccion = $_POST['direccion'];
        $telefono = $_POST['telefono'];
        $programa = $_POST['programa'];
        $ficha = $_POST['ficha'];

        $stmt = $conn->prepare("UPDATE aprendices SET 
            nombre = ?, 
            direccion = ?, 
            telefono = ?, 
            programa = ?, 
            ficha = ? 
            WHERE id_aprendiz = ?");
        
        $stmt->bind_param("ssssss", $nombre, $direccion, $telefono, $programa, $ficha, $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Aprendiz actualizado correctamente']);
        } else {
            echo json_encode(['success' => true, 'notice' => 'No se realizaron cambios']);
        }
        $stmt->close();
    }
} catch (Exception $e) {
    error_log('Update error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Error del servidor: ' . $e->getMessage()]);
} finally {
    $conn->close();
}
?>