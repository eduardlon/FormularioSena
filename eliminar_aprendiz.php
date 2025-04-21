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

// Obtener ID del aprendiz
$id_aprendiz = $_POST['id_aprendiz'] ?? '';

if (empty($id_aprendiz)) {
    die(json_encode(['success' => false, 'error' => 'ID de aprendiz no proporcionado']));
}

try {
    // Preparar la consulta SQL para eliminar
    $stmt = $conn->prepare("DELETE FROM aprendices WHERE id_aprendiz = ?");
    $stmt->bind_param("s", $id_aprendiz);
    
    // Ejecutar la consulta
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Aprendiz eliminado correctamente']);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se pudo eliminar el aprendiz']);
    }
    
    $stmt->close();
} catch (Exception $e) {
    error_log('Delete error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Error del servidor: ' . $e->getMessage()]);
} finally {
    $conn->close();
}
?>