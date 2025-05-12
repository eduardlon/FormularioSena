<?php
// Add error reporting
ini_set('display_errors', 0);
ini_set('log_errors', 1);

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

// Obtener ID del aprendiz
$id_aprendiz = $_POST['id_aprendiz'];

// Preparar la consulta SQL
$stmt = $conn->prepare("SELECT * FROM aprendices WHERE id_aprendiz = ?");
$stmt->bind_param("s", $id_aprendiz);

// Ejecutar la consulta
try {
    $stmt->execute();
    $result = $stmt->get_result();
    
    $response = ['success' => false];
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $response = [
            'success' => true,
            'aprendiz' => [
                'id_aprendiz' => htmlspecialchars($row['id_aprendiz']),
                'nombre' => htmlspecialchars($row['nombre']),
                'direccion' => htmlspecialchars($row['direccion']),
                'telefono' => htmlspecialchars($row['telefono']),
                'programa' => htmlspecialchars($row['programa']),
                'ficha' => htmlspecialchars($row['ficha'])
            ]
        ];
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'error' => 'Database error'
    ];
}

$stmt->close();
$conn->close();

// Devolver la respuesta como JSON
header('Content-Type: application/json');
echo json_encode($response);
?>