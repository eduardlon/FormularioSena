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

// Obtener datos del cuerpo de la solicitud
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (!$data) {
    die(json_encode(['success' => false, 'error' => 'Datos no válidos']));
}

// Extraer datos
$id_aprendiz = $data['id_aprendiz'] ?? '';
$competencia = $data['competencia'] ?? '';
$nota1 = $data['nota1'] ?? '';
$nota2 = $data['nota2'] ?? '';
$nota3 = $data['nota3'] ?? '';
$promedio = $data['promedio'] ?? '';
$fecha = $data['fecha'] ?? '';

// Validar datos
if (empty($id_aprendiz) || empty($competencia) || 
    !is_numeric($nota1) || !is_numeric($nota2) || !is_numeric($nota3) || 
    empty($fecha)) {
    die(json_encode(['success' => false, 'error' => 'Todos los campos son obligatorios']));
}

// Validar notas (entre 0 y 5)
if ($nota1 < 0 || $nota1 > 5 || $nota2 < 0 || $nota2 > 5 || $nota3 < 0 || $nota3 > 5) {
    die(json_encode(['success' => false, 'error' => 'Las notas deben ser números entre 0 y 5']));
}

try {
    // Iniciar transacción
    $conn->begin_transaction();
    
    // Verificar si ya existen notas para esta competencia
    $stmt = $conn->prepare("SELECT id_nota FROM notas_multiples WHERE id_aprendiz = ? AND competencia = ?");
    $stmt->bind_param("ss", $id_aprendiz, $competencia);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Ya existe, actualizar
        $stmt = $conn->prepare("UPDATE notas_multiples SET nota1 = ?, nota2 = ?, nota3 = ?, promedio = ?, fecha = ? WHERE id_aprendiz = ? AND competencia = ?");
        $stmt->bind_param("ddddsss", $nota1, $nota2, $nota3, $promedio, $fecha, $id_aprendiz, $competencia);
    } else {
        // No existe, insertar
        $stmt = $conn->prepare("INSERT INTO notas_multiples (id_aprendiz, competencia, nota1, nota2, nota3, promedio, fecha) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdddss", $id_aprendiz, $competencia, $nota1, $nota2, $nota3, $promedio, $fecha);
    }
    
    // Ejecutar la consulta
    $stmt->execute();
    
    // Confirmar transacción
    $conn->commit();
    
    echo json_encode(['success' => true, 'message' => 'Notas guardadas correctamente']);
    
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conn->rollback();
    error_log('Save notes error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Error del servidor: ' . $e->getMessage()]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}
?>