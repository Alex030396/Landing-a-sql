<?php
// Función para cargar estilos y scripts
function oftalmi_theme_scripts() {
    // Cargar Bootstrap CSS
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css');
    
    // Cargar Font Awesome
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
    
    // Cargar estilos del tema
    wp_enqueue_style('oftalmi-style', get_stylesheet_uri());
    
    // Cargar Bootstrap JS
    wp_enqueue_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', array('jquery'), '', true);
    
    // Cargar scripts personalizados
    wp_enqueue_script('oftalmi-script', get_template_directory_uri() . '/js/script.js', array('jquery'), '', true);
}
add_action('wp_enqueue_scripts', 'oftalmi_theme_scripts');

// 1. Pasar variables PHP a JavaScript
function oftalmi_localize_scripts() {
    wp_localize_script('oftalmi-script', 'oftalmi_ajax', array(
        'url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('oftalmi_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'oftalmi_localize_scripts');

// 2. Función para guardar los datos (reemplaza guardar.php)
function oftalmi_guardar_datos() {
    // Verificar nonce para seguridad
    check_ajax_referer('oftalmi_nonce', 'nonce');
    
    // Obtener los datos enviados
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    // Verificar si json_decode tuvo errores
    if (json_last_error() !== JSON_ERROR_NONE) {
        wp_send_json_error('Error en el formato JSON: ' . json_last_error_msg());
    }

    // Validar que todos los campos requeridos estén presentes
    $required_fields = ['nombre', 'cedula', 'mpps', 'telefono', 'correo', 'especialidad', 'subespecialidad', 'nivelResidencia', 'direccion','evento'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            wp_send_json_error('Faltan campos requeridos: ' . $field);
        }
    }

    // Configuración de la base de datos (usa las credenciales de WordPress)
    global $wpdb;
    
    // PRIMERO VERIFICAR SI YA EXISTE LA CÉDULA + EVENTO
    $existe = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}doctores WHERE cedula = %s AND evento = %s",
        $data['cedula'],
        $data['evento']
    ));

    if ($existe > 0) {
        wp_send_json_error('Error: Ya existe un registro con la cédula ' . $data['cedula'] . ' para el evento ' . $data['evento']);
    }

    // Preparar datos para inserción
    $nombre = sanitize_text_field(trim($data['nombre']));
    $cedula = sanitize_text_field(trim($data['cedula']));
    $mpps = sanitize_text_field(trim($data['mpps']));
    $telefono = sanitize_text_field(trim($data['telefono']));
    $correo = sanitize_email(trim($data['correo']));
    $especialidad = sanitize_text_field(trim($data['especialidad']));
    $subespecialidad = sanitize_text_field(trim($data['subespecialidad']));
    $nivelResidencia = sanitize_text_field(trim($data['nivelResidencia']));
    $direccion = sanitize_text_field(trim($data['direccion']));
    $evento = sanitize_text_field(trim($data['evento']));

    // Insertar en la base de datos
    $resultado = $wpdb->insert(
        $wpdb->prefix . 'doctores',
        array(
            'nombre' => $nombre,
            'cedula' => $cedula,
            'mpps' => $mpps,
            'telefono' => $telefono,
            'correo' => $correo,
            'especialidad' => $especialidad,
            'subespecialidad' => $subespecialidad,
            'nivel-residencia' => $nivelResidencia,
            'direccion' => $direccion,
            'evento' => $evento,
            'fecha_registro' => current_time('mysql')
        ),
        array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
    );

    if ($resultado !== false) {
        wp_send_json_success('¡Formulario enviado correctamente! Los datos han sido guardados en la base de datos.');
    } else {
        wp_send_json_error('Error al guardar los datos: ' . $wpdb->last_error);
    }
}

// 3. Registrar los hooks para AJAX
add_action('wp_ajax_oftalmi_guardar', 'oftalmi_guardar_datos');
add_action('wp_ajax_nopriv_oftalmi_guardar', 'oftalmi_guardar_datos');

// 4. Crear la tabla en la base de datos al activar el tema
function oftalmi_crear_tabla() {
    global $wpdb;
    
    $tabla_doctores = $wpdb->prefix . 'doctores';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $tabla_doctores (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nombre varchar(255) NOT NULL,
        cedula varchar(50) NOT NULL,
        mpps varchar(50) NOT NULL,
        telefono varchar(50) NOT NULL,
        correo varchar(255) NOT NULL,
        especialidad varchar(255) NOT NULL,
        subespecialidad varchar(255) NOT NULL,
        `nivel-residencia` varchar(255) NOT NULL,
        direccion text NOT NULL,
        evento varchar(255) NOT NULL,
        fecha_registro datetime NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY cedula_evento (cedula, evento)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
add_action('after_switch_theme', 'oftalmi_crear_tabla');

?>
