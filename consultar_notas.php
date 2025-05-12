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

// Configurar cabeceras
header('Content-Type: application/json');
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Obtener ID del aprendiz
$id_aprendiz = $_POST['id_aprendiz'] ?? '';

if (empty($id_aprendiz)) {
    die(json_encode(['success' => false, 'error' => 'ID de aprendiz no proporcionado']));
}

try {
    // Consultar todas las notas del aprendiz
    $stmt = $conn->prepare("SELECT competencia, nota1, nota2, nota3, promedio, DATE_FORMAT(fecha, '%d/%m/%Y') as fecha FROM notas_multiples WHERE id_aprendiz = ? ORDER BY competencia");
    $stmt->bind_param("s", $id_aprendiz);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $notas = [];
    while ($row = $result->fetch_assoc()) {
        $notas[] = $row;
    }
    
    echo json_encode(['success' => true, 'notas' => $notas]);
    
    $stmt->close();
} catch (Exception $e) {
    error_log('Query error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Error del servidor: ' . $e->getMessage()]);
} finally {
    $conn->close();
}
?>