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

try {
    // Preparar la consulta SQL para obtener todos los aprendices
    $stmt = $conn->prepare("SELECT id_aprendiz, nombre, programa, ficha FROM aprendices ORDER BY nombre");
    
    // Ejecutar la consulta
    $stmt->execute();
    $result = $stmt->get_result();
    
    $aprendices = [];
    
    // Obtener los resultados
    while ($row = $result->fetch_assoc()) {
        $aprendices[] = [
            'id_aprendiz' => htmlspecialchars($row['id_aprendiz']),
            'nombre' => htmlspecialchars($row['nombre']),
            'programa' => htmlspecialchars($row['programa']),
            'ficha' => htmlspecialchars($row['ficha'])
        ];
    }
    
    echo json_encode(['success' => true, 'aprendices' => $aprendices]);
    
    $stmt->close();
} catch (Exception $e) {
    error_log('List error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Error del servidor: ' . $e->getMessage()]);
} finally {
    $conn->close();
}
?>