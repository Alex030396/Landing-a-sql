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
    if (!check_ajax_referer('oftalmi_nonce', 'nonce', false)) {
        wp_send_json_error('Error de seguridad: nonce inválido');
    }
    
    // Obtener y procesar los datos
    if (!isset($_POST['data'])) {
        wp_send_json_error('No se recibieron datos');
    }
    
    $data = json_decode(stripslashes($_POST['data']), true);

    // Verificar si json_decode tuvo errores
    if (json_last_error() !== JSON_ERROR_NONE) {
        wp_send_json_error('Error en el formato JSON: ' . json_last_error_msg());
    }

    // Validar campos requeridos
    $required_fields = ['nombre', 'cedula', 'mpps', 'telefono', 'correo', 'especialidad', 'subespecialidad', 'nivelResidencia', 'direccion','evento'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            wp_send_json_error('Faltan campos requeridos: ' . $field);
        }
    }

    // Configuración de la base de datos
    global $wpdb;
    
    $tabla_doctores = $wpdb->prefix . 'doctores';
    
    // Verificar si ya existe la cédula + evento
    $existe = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $tabla_doctores WHERE cedula = %s AND evento = %s",
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
        $tabla_doctores,
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

// Función para debug - verificar base de datos
function oftalmi_debug_info() {
    global $wpdb;
    
    $tabla_doctores = $wpdb->prefix . 'doctores';
    
    echo '<div style="background: #f8f9fa; border: 2px solid #0073aa; padding: 15px; margin: 20px 0; border-radius: 5px; font-size: 14px;">';
    echo '<h4 style="color: #0073aa; margin-top: 0;">🔍 Estado de la Base de Datos</h4>';
    echo '<p><strong>Base de datos:</strong> ' . DB_NAME . '</p>';
    echo '<p><strong>Tabla:</strong> ' . $tabla_doctores . '</p>';
    
    if($wpdb->get_var("SHOW TABLES LIKE '$tabla_doctores'") == $tabla_doctores) {
        echo '<p style="color: green;"><strong>✅ Tabla EXISTE</strong></p>';
        
        $total_registros = $wpdb->get_var("SELECT COUNT(*) FROM $tabla_doctores");
        echo '<p><strong>Total de registros:</strong> ' . $total_registros . '</p>';
        
        if($total_registros > 0) {
            $ultimos_registros = $wpdb->get_results("SELECT * FROM $tabla_doctores ORDER BY id DESC LIMIT 3");
            echo '<p><strong>Últimos registros:</strong></p>';
            foreach($ultimos_registros as $registro) {
                echo '<div style="background: white; padding: 10px; margin: 5px 0; border-radius: 3px;">';
                echo 'ID: ' . $registro->id . ' | Nombre: ' . $registro->nombre . ' | Cédula: ' . $registro->cedula;
                echo '</div>';
            }
        }
    } else {
        echo '<p style="color: red;"><strong>❌ La tabla NO EXISTE</strong></p>';
    }
    
    echo '</div>';
}
add_action('wp_footer', 'oftalmi_debug_info');
?>
