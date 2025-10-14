<?php
/**
 * Template Name: Landing Oftalmi
 */
get_header(); ?>

<div class="container-fluid bg-oftalmi">
    <div class="container ">
        <img src="<?php echo get_template_directory_uri(); ?>/img/landing.gif" alt="Logo de la empresa" class="img-fluid mar"  >
    </div>
    </div>
    <!-- Barra de color azul -->
    <div class="container-flud bg-titulo">
        <div class="container">
            <img src="<?php echo get_template_directory_uri(); ?>/img/UCV.jpg" class=" mary" alt="congreso" >
        </div>
    </div>
    <!-- Contenedor principal con scroll -->
    <div class="content-wrapper">
        <!-- Imagen de fondo -->
        <img src="<?php echo get_template_directory_uri(); ?>/img/PORTADA.jpg" alt="Imagen de la empresa" class="hero-image mi-imagen-css">
        
        <!-- Formulario -->
        <div class="form-container">
            <h2 class="form-title letras_oftalmi">Formulario de Contacto</h2>
            <form id="myForm">
                <div class="mb-3">
                    <label for="nombre" class="form-label fw-bold letras_oftalmi required-field">Nombre Completo</label>
                    <input type="text" class="form-control" id="nombre" placeholder="Nombres y Apellidos" required>
                    <div class="error" id="nombreError">Por favor ingrese solo letras y espacios en el nombre</div>
                </div>
                
                <div class="mb-3">
                    <label for="cedula" class="form-label fw-bold letras_oftalmi required-field">Cédula o pasaporte</label>
                    <input type="text" class="form-control" id="cedula" placeholder="Número de documento" required>
                    <div class="error" id="cedulaError">Por favor ingrese solo el número de cédula</div>
                </div>

                <div class="mb-3">
                    <label for="mpps" class="form-label fw-bold letras_oftalmi required-field">MPPS</label>
                    <input type="text" class="form-control" id="mpps" placeholder="Número de Medico" required>
                    <div class="error" id="mppsError">Por favor ingrese solo el número de MPPS</div>
                </div>
                
                <div class="mb-3">
                    <label for="telefono" class="form-label fw-bold letras_oftalmi required-field">Teléfono</label>
                    <input type="tel" class="form-control" id="telefono" placeholder="(0123)-456-7890" required>
                    <div class="error" id="telefonoError">El formato debe ser (0123)-456-7890</div>
                </div>
                
                <div class="mb-3">
                    <label for="correo" class="form-label fw-bold letras_oftalmi required-field">Correo electrónico</label>
                    <input type="email" class="form-control" id="correo" placeholder="ejemplo@correo.com" required>
                    <div class="error" id="correoError">Por favor ingrese un correo válido con @</div>
                </div>
                
                <!-- Selector de Especialidad -->
                <div class="mb-3">
                    <label for="especialidad" class="form-label fw-bold letras_oftalmi required-field">Especialidad</label>
                    <select class="form-select" id="especialidad" required>
                        <option value="" selected disabled>Seleccione una especialidad</option>
                        <option value="medicina_general">Medicina General</option>
                        <option value="estudiante">Estudiante</option>
                        <option value="profesor">Profesor</option>
                        <option value="farmaceutico">Farmaceutico</option>
                        <option value="residente">Residente</option>
                        <option value="oftalmologia">Oftalmología</option>
                        <option value="otorrinolaringologia">Otorrinolaringología</option>
                        <option value="medicina_interna">Medicina Interna</option>
                        <option value="medicina_familiar">Medicina Familiar</option>
                        <option value="pediatria">Pediatría</option>
                        <option value="neumonologia">Neumonología</option>
                    </select>
                    <div class="error" id="especialidadError">Por favor seleccione una especialidad</div>
                </div>
                
                <!-- Selector de Subespecialidad -->
                <div class="mb-3" id="subespecialidad-container">
                    <label for="subespecialidad" class="form-label fw-bold letras_oftalmi">Subespecialidad</label>
                    <select class="form-select" id="subespecialidad">
                        <option value="" selected disabled>Primero seleccione una especialidad</option>
                    </select>
                    <div class="error" id="subespecialidadError">Por favor seleccione una subespecialidad</div>
                </div>
                
                <!-- Checkbox Group para Residentes -->
                <div class="checkbox-group" id="resident-checkbox-group">
                    <div class="checkbox-title required-field">Nivel de Residencia</div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" value="R1" id="r1" name="residentLevel">
                        <label class="form-check-label" for="r1">R1 - Primer año</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" value="R2" id="r2" name="residentLevel">
                        <label class="form-check-label" for="r2">R2 - Segundo año</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" value="R3" id="r3" name="residentLevel">
                        <label class="form-check-label" for="r3">R3 - Tercer año</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" value="R4" id="r4" name="residentLevel">
                        <label class="form-check-label" for="r4">R4 - Cuarto año</label>
                    </div>
                    <div class="error" id="residentLevelError">Por favor seleccione un nivel de residencia</div>
                </div>
                
                <!-- Checkbox Group Opcional para otras especialidades -->
                <div class="checkbox-group" id="optional-checkbox-group">
                    <div class="checkbox-title">Nivel de Residencia (Opcional)</div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" value="En proceso" id="opt-na" name="optionalResidentLevel" checked>
                        <label class="form-check-label" for="opt-na">En proceso</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" value="Graduado" id="opt-na" name="optionalResidentLevel" checked>
                        <label class="form-check-label" for="opt-na">Graduado</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" value="R1" id="opt-r1" name="optionalResidentLevel">
                        <label class="form-check-label" for="opt-r1">R1 - Primer año</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" value="R2" id="opt-r2" name="optionalResidentLevel">
                        <label class="form-check-label" for="opt-r2">R2 - Segundo año</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" value="R3" id="opt-r3" name="optionalResidentLevel">
                        <label class="form-check-label" for="opt-r3">R3 - Tercer año</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" value="R4" id="opt-r4" name="optionalResidentLevel">
                        <label class="form-check-label" for="opt-r4">R4 - Cuarto año</label>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="lugarVive" class=" form-label fw-bold letras_oftalmi required-field">Dirección de Consultorio / Consulta Hospitalaria</label>
                    <input type="text" class="form-control" id="lugarVive" placeholder="Dirección" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Enviar Información</button>
                
                <p class="form-legend mt-3">
                    <i class="fas fa-info-circle"></i> Todos sus datos están protegidos y serán utilizados únicamente para fines de contacto.
                </p>
            </form>
            
            <div class="success-message" id="successMessage"></div>
            <div class="error-message" id="errorMessage"></div>
            
            <div id="jsonOutput"></div>
        </div>
    </div>

<?php get_footer(); ?>