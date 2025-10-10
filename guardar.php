<?php
// EVITA CUALQUIER SALIDA ANTES DE LOS HEADERS
ob_start(); // Capturar cualquier output accidental

// Headers primero
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejar preflight
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    ob_end_clean(); // Limpiar buffer
    exit(0);
}

// Configuración de la base de datos
$servername = "localhost";
$username = "oftalmi_Landingpage";
$password = "Landinpage*202510";
$dbname = "oftalmi_landingPG";

// Crear conexión con manejo de errores mejorado
try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Error de conexión MySQL: " . $conn->connect_error);
    }
    
    // Verificar codificación
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    ob_end_clean(); // Limpiar buffer
    echo json_encode([
        'exito' => false,
        'mensaje' => $e->getMessage()
    ]);
    exit;
}

// Obtener datos del POST
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Verificar si json_decode tuvo errores
if (json_last_error() !== JSON_ERROR_NONE) {
    ob_end_clean();
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error en el formato JSON: ' . json_last_error_msg()
    ]);
    exit;
}

// Validar que todos los campos requeridos estén presentes
$required_fields = ['nombre', 'cedula', 'mpps', 'telefono', 'correo', 'especialidad', 'subespecialidad', 'nivelResidencia', 'direccion','evento'];
foreach ($required_fields as $field) {
    if (!isset($data[$field]) || empty(trim($data[$field]))) {
        ob_end_clean();
        echo json_encode([
            'exito' => false,
            'mensaje' => 'Faltan campos requeridos: ' . $field
        ]);
        exit;
    }
}

// PRIMERO VERIFICAR SI YA EXISTE LA CÉDULA + EVENTO
$checkStmt = $conn->prepare("SELECT id FROM doctores WHERE cedula = ? AND evento = ?");
if (!$checkStmt) {
    ob_end_clean();
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error al preparar consulta de verificación: ' . $conn->error
    ]);
    $conn->close();
    exit;
}

$checkStmt->bind_param("ss", $data['cedula'], $data['evento']);
if (!$checkStmt->execute()) {
    ob_end_clean();
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error al ejecutar verificación: ' . $checkStmt->error
    ]);
    $checkStmt->close();
    $conn->close();
    exit;
}

$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    ob_end_clean();
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error: Ya existe un registro con la cédula ' . $data['cedula'] . ' para el evento ' . $data['evento']
    ]);
    $checkStmt->close();
    $conn->close();
    exit;
}
$checkStmt->close();

// Preparar y ejecutar la consulta de INSERCIÓN
$stmt = $conn->prepare("INSERT INTO doctores (nombre, cedula, mpps, telefono, correo, especialidad, subespecialidad, `nivel-residencia`, direccion, evento, fecha_registro) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

if ($stmt === false) {
    ob_end_clean();
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error al preparar la consulta: ' . $conn->error
    ]);
    $conn->close();
    exit;
}

// Limpiar y preparar datos
$nombre = trim($data['nombre']);
$cedula = trim($data['cedula']);
$mpps = trim($data['mpps']);
$telefono = trim($data['telefono']);
$correo = trim($data['correo']);
$especialidad = trim($data['especialidad']);
$subespecialidad = trim($data['subespecialidad']);
$nivelResidencia = trim($data['nivelResidencia']);
$direccion = trim($data['direccion']);
$evento = trim($data['evento']);

$stmt->bind_param("ssssssssss", 
    $nombre,
    $cedula,
    $mpps,
    $telefono,
    $correo,
    $especialidad,
    $subespecialidad,
    $nivelResidencia,
    $direccion,
    $evento
);

if ($stmt->execute()) {
    ob_end_clean();
    echo json_encode([
        'exito' => true,
        'mensaje' => '¡Formulario enviado correctamente! Los datos han sido guardados en la base de datos.'
    ]);
} else {
    ob_end_clean();
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error al guardar los datos: ' . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>