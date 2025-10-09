<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejar preflight
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Configuración de la base de datos
$servername = "127.0.0.1";
$username = "root";
$password = "OftalmiBD12.";
$dbname = "congreso";
$port = 3306;

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Verificar conexión
if ($conn->connect_error) {
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error de conexión: ' . $conn->connect_error
    ]);
    exit;
}

// Obtener datos del POST
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validar que todos los campos requeridos estén presentes
$required_fields = ['nombre', 'cedula', 'mpps', 'telefono', 'correo', 'especialidad', 'subespecialidad', 'nivelResidencia', 'direccion','evento'];
foreach ($required_fields as $field) {
    if (!isset($data[$field]) || empty($data[$field])) {
        echo json_encode([
            'exito' => false,
            'mensaje' => 'Faltan campos requeridos: ' . $field
        ]);
        exit;
    }
}

// PRIMERO VERIFICAR SI YA EXISTE LA CÉDULA + EVENTO
$checkStmt = $conn->prepare("SELECT id FROM doctores WHERE cedula = ? AND evento = ?");
$checkStmt->bind_param("ss", $data['cedula'], $data['evento']);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    // Ya existe un registro con esta cédula y evento
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
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error al preparar la consulta: ' . $conn->error
    ]);
    exit;
}

$stmt->bind_param("ssssssssss", 
    $data['nombre'],
    $data['cedula'],
    $data['mpps'],
    $data['telefono'],
    $data['correo'],
    $data['especialidad'],
    $data['subespecialidad'],
    $data['nivelResidencia'],
    $data['direccion'],
    $data['evento']
);

if ($stmt->execute()) {
    echo json_encode([
        'exito' => true,
        'mensaje' => '¡Formulario enviado correctamente! Los datos han sido guardados en la base de datos.'
    ]);
} else {
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error al guardar los datos: ' . $conn->error
    ]);
}

$stmt->close();
$conn->close();
?>