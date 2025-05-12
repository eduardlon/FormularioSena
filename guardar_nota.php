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

// Obtener datos del formulario
$id_aprendiz = $_POST['id_aprendiz'] ?? '';
$competencia = $_POST['competencia'] ?? '';
$nota = $_POST['nota'] ?? '';
$fecha_nota = $_POST['fecha_nota'] ?? '';

// Validar datos
if (empty($id_aprendiz) || empty($competencia) || empty($nota) || empty($fecha_nota)) {
    die(json_encode(['success' => false, 'error' => 'Todos los campos son obligatorios']));
}

// Validar nota (entre 0 y 5)
if (!is_numeric($nota) || $nota < 0 || $nota > 5) {
    die(json_encode(['success' => false, 'error' => 'La nota debe ser un número entre 0 y 5']));
}

try {
    // Preparar la consulta SQL para insertar la nota
    $stmt = $conn->prepare("INSERT INTO notas (id_aprendiz, competencia, nota, fecha) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssds", $id_aprendiz, $competencia, $nota, $fecha_nota);
    
    // Ejecutar la consulta
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Nota guardada correctamente']);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se pudo guardar la nota']);
    }
    
    $stmt->close();
} catch (Exception $e) {
    error_log('Save note error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Error del servidor: ' . $e->getMessage()]);
} finally {
    $conn->close();
}
?>