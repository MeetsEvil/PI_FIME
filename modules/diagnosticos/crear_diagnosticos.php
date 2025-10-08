<?php
session_start();
if (!isset($_SESSION['usuarioingresando'])) {
    header("Location: ../auth/index.php");
    exit();
}
$user = $_SESSION['usuarioingresando'];

// 1. Incluir la conexión a la base de datos
include '../../config/db.php';

// Si la conexión falla, las consultas más abajo usarán $conex = false o null, lo cual se maneja
// en el if de las consultas o en el script de guardado.

// Obtiene el nombre del archivo de la URL
$currentPage = basename($_SERVER['REQUEST_URI']);

// --- 2. Obtener ID del Beneficiario y sus datos para el título ---
$beneficiario = null;
$beneficiario_id = null;

if (isset($_GET['beneficiario_id']) && is_numeric($_GET['beneficiario_id'])) {
    $beneficiario_id = intval($_GET['beneficiario_id']);

    // Consulta para obtener el nombre completo del beneficiario
    $query = "SELECT nombre, apellido_paterno, apellido_materno FROM beneficiarios WHERE id_beneficiario = ?";

    if (isset($conex) && $stmt = mysqli_prepare($conex, $query)) {
        mysqli_stmt_bind_param($stmt, "i", $beneficiario_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $beneficiario = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    }
}

// Si no se encuentra el beneficiario o el ID es inválido, redirigir al buscador
if (!$beneficiario) {
    header("Location: index_diagnosticos.php");
    exit();
}

// Generar el nombre completo para el título
$nombre_completo_beneficiario = trim(
    $beneficiario['nombre'] . ' ' .
    $beneficiario['apellido_paterno'] . ' ' .
    ($beneficiario['apellido_materno'] ?? '')
);
$titulo_seccion = "Nuevo Diagnóstico para: " . htmlspecialchars($nombre_completo_beneficiario);

// --- 3. Consulta para obtener los profesionales activos ---
$profesionales = [];
$query = "SELECT id_profesional, nombre, apellido_paterno, apellido_materno FROM profesionales WHERE estado = 'Activo'";
if (isset($conex) && $conex) { // Añadida verificación de $conex
    $result = mysqli_query($conex, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $nombre_completo = trim($row['nombre'] . ' ' . $row['apellido_paterno'] . ' ' . $row['apellido_materno']);
            $profesionales[] = [
                'id' => $row['id_profesional'],
                'nombre' => $nombre_completo
            ];
        }
        mysqli_free_result($result);
    }
}

// --- 4. CALCULAR EL PRÓXIMO ID DEL DIAGNÓSTICO (Para mostrarlo al usuario) ---
$next_diagnostico_id = '';
if (isset($conex) && $conex) {
    $result_max = mysqli_query($conex, "SELECT MAX(id_diagnostico) AS max_id FROM diagnosticos");
    if ($result_max) {
        $row_max = mysqli_fetch_assoc($result_max);
        $next_diagnostico_id = ($row_max['max_id'] === null) ? 1 : $row_max['max_id'] + 1;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo_seccion; ?></title>
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Librerías opcionales para DataPicker y validación -->
</head>

<body>
    <div class="container">
        <!-- Sidebar Navigation -->
        <div class="navigation">
            <ul>
                <li>
                    <a href="#">
                        <span class="icon">
                            <ion-icon name="school-outline"></ion-icon>
                        </span>
                        <span class="title">FIME Inclusivo</span>
                    </a>
                </li>

                <li class="<?php echo ($currentPage == 'dashboard.php') ? 'active' : ''; ?>">
                    <a href="../../modules/auth/dashboard.php" data-tooltip="Inicio">
                        <span class="icon"><ion-icon name="home-outline"></ion-icon></span>
                        <span class="title">Inicio</span>
                    </a>
                </li>

                <li class="<?php echo ($currentPage == 'index_beneficiarios.php') ? 'active' : ''; ?>">
                    <a href="../../modules/beneficiarios/index_beneficiarios.php" data-tooltip="Beneficiarios">
                        <span class="icon"><ion-icon name="people-outline"></ion-icon></span>
                        <span class="title">Beneficiarios</span>
                    </a>
                </li>

                <li class="<?php echo ($currentPage == 'index_diagnosticos.php') ? 'active' : ''; ?>">
                    <a href="../../modules/diagnosticos/index_diagnosticos.php" data-tooltip="Diagnósticos">
                        <span class="icon"><ion-icon name="medkit-outline"></ion-icon></span>
                        <span class="title">Diagnósticos</span>
                    </a>
                </li>

                <li class="<?php echo ($currentPage == 'index_adaptaciones.php') ? 'active' : ''; ?>">
                    <a href="../../modules/adaptaciones/index_adaptaciones.php" data-tooltip="Adaptaciones">
                        <span class="icon"><ion-icon name="construct-outline"></ion-icon></span>
                        <span class="title">Adaptaciones</span>
                    </a>
                </li>

                <li class="<?php echo ($currentPage == 'index_intervenciones.php') ? 'active' : ''; ?>">
                    <a href="../../modules/intervenciones/index_intervenciones.php" data-tooltip="Intervenciones">
                        <span class="icon"><ion-icon name="clipboard-outline"></ion-icon></span>
                        <span class="title">Intervenciones</span>
                    </a>
                </li>

                <li class="<?php echo ($currentPage == 'index_profesionales.php') ? 'active' : ''; ?>">
                    <a href="../../modules/profesionales/index_profesionales.php" data-tooltip="Profesionales">
                        <span class="icon"><ion-icon name="briefcase-outline"></ion-icon></span>
                        <span class="title">Profesionales</span>
                    </a>
                </li>

                <li class="<?php echo ($currentPage == 'index_reportes.php') ? 'active' : ''; ?>">
                    <a href="../../modules/reportes/index_reportes.php" data-tooltip="Reportes">
                        <span class="icon"><ion-icon name="bar-chart-outline"></ion-icon></span>
                        <span class="title">Reportes</span>
                    </a>
                </li>

                <li>
                    <a href="#" onclick="showLogoutModal()" data-tooltip="Cerrar Sesión">
                        <span class="icon">
                            <ion-icon name="log-out-outline"></ion-icon>
                        </span>
                        <span class="title">Cerrar Sesión</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="main">
        <div class="topbar">
            <div class="toggle">
                <ion-icon name="menu-outline"></ion-icon>
            </div>
            <h2 class="page-title">PROGRAMA DE INCLUSIÓN</h2>
            <div class="user-box">
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($user); ?></div>
                    <div class="user-role"><?php echo htmlspecialchars($_SESSION['rol']); ?></div>
                </div>
                <button class="info-btn" onclick="mostrarInfo()">
                    <ion-icon name="information-outline"></ion-icon>
                </button>


            </div>
        </div>


        <div class="diagnosticos-container" style="min-height: 85vh;">
            <div class="header-section">
                <!-- Título dinámico -->
                <h2 class="section-title"><?php echo $titulo_seccion; ?></h2>
                <a href="historico_diagnosticos.php?id=<?php echo $beneficiario_id; ?>" class="btn-regresar">
                    <ion-icon name="caret-back-circle-outline"></ion-icon> Regresar
                </a>
            </div>

            <!-- Contenedor del formulario (usaremos la lógica de paginación en el main.js) -->
            <div class="form-pagination-container" style="min-height: 500px;">
                <!-- NOTA: El formulario se envía a guardar_diagnostico.php (debe ser creado) -->
                <form id="beneficiaryForm" action="guardar_diagnostico.php" method="POST" onsubmit="return false;">

                    <!-- Campo oculto: ID del beneficiario para el backend -->
                    <input type="hidden" name="beneficiario_id" value="<?php echo htmlspecialchars($beneficiario_id); ?>">

                    <!-- Única Página: Datos del Diagnóstico -->
                    <div class="form-page is-active" data-page="1">
                        <h3>Detalles del Diagnóstico</h3>

                        <!-- NUEVOS CAMPOS DE VISUALIZACIÓN DE CONTEXTO -->
                        <div class="readonly-fields-group" style="display: flex; gap: 15px; margin-bottom: 10px;">
                            <!-- ID del Diagnóstico (Calculado) -->
                            <label style="flex: 1;">
                                <span>ID de Registro:</span>
                                <input type="text" value="<?php echo $next_diagnostico_id; ?>" readonly style="background-color: #f0f0f0;">
                            </label>

                            <!-- Beneficiario (Nombre Completo) -->
                            <label style="flex: 2;">
                                <span>Beneficiario Asignado:</span>
                                <input type="text" value="<?php echo htmlspecialchars($nombre_completo_beneficiario); ?>" readonly style="background-color: #f0f0f0;">
                            </label>
                        </div>
                        <!-- FIN NUEVOS CAMPOS -->

                        <label><span>Fecha de Diagnóstico:</span><input type="date" name="fecha_diagnostico" required></label>

                        <label><span>Tipo de Diagnóstico:</span>
                            <select name="tipo_diagnostico" required>
                                <option value="">Selecciona...</option>
                                <option value="Médico General">Médico General</option>
                                <option value="Psicológico">Psicológico</option>
                                <option value="Psicopedagógico">Psicopedagógico</option>
                                <option value="Oftalmológico">Oftalmológico</option>
                                <option value="Auditivo">Auditivo</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </label>

                        <!-- Profesional Asignado (Mismas reglas de validación de Beneficiarios) -->
                        <label><span>Profesional Asignado:</span>
                            <input type="text" name="profesional_asignado_nombre" list="profesionales-list" id="profesionalAsignadoNombre" required>
                            <input type="hidden" name="profesional_asignado_id" id="profesionalAsignadoId">
                            <datalist id="profesionales-list">
                                <?php foreach ($profesionales as $profesional): ?>
                                    <option data-id="<?php echo $profesional['id']; ?>" value="<?php echo htmlspecialchars($profesional['nombre']); ?>">
                                    <?php endforeach; ?>
                            </datalist>
                        </label>
                        <div id="profesionalError" class="validation-message-small"></div>

                        <label><span>Resultado (Detalles):</span><textarea name="resultado" required placeholder="Descripción detallada del diagnóstico y hallazgos."></textarea></label>

                        <label><span>Observaciones:</span><textarea name="observaciones" placeholder="Recomendaciones, próximos pasos o notas adicionales."></textarea></label>

                        <label><span>Archivo Adjunto (Ruta):</span><input type="text" name="archivo_adjunto" placeholder="Ruta o nombre del archivo adjunto (opcional)"></label>

                    </div>

                </form>
            </div>

            <!-- Controles de Paginación (Simulados como si fuera una sola página) -->
            <div class="pagination-buttons">
                <!-- El botón Siguiente actúa como Guardar -->
                <button type="button" class="btn-pagination btn-next" id="submitDiagnosisBtn">
                    Guardar Diagnóstico <ion-icon name="checkmark-circle-outline"></ion-icon>
                </button>
            </div>
            <div id="formValidationMessage" class="validation-message-global"></div>

        </div>
    </div>
    </div>
    <div id="successModal" class="modal">
        <div class="modal-content success">
            <div class="modal-body">
                <ion-icon name="checkmark-circle-outline" class="success-icon"></ion-icon>
                <h2 class="success-title">¡Registro Exitoso!</h2>
                <p>El nuevo beneficiario ha sido guardado correctamente.</p>
                <p>Serás redirigido a la lista de beneficiarios en 3 segundos.</p>
            </div>
        </div>
    </div>
    <!-- MODALES Y SCRIPTS -->
    <script src="../../assets/js/main.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

<script>
    // Usamos jQuery para adjuntar el evento al botón por ID
    $(document).ready(function() {
        $('#submitDiagnosisBtn').on('click', function(e) {
            e.preventDefault(); // Prevenimos la acción por defecto
            
            const form = document.getElementById('beneficiaryForm');
            const profesionalInput = document.getElementById('profesionalAsignadoNombre');
            const profesionalList = document.getElementById('profesionales-list');
            const profesionalIdField = document.getElementById('profesionalAsignadoId');
            const profesionalErrorDiv = document.getElementById('profesionalError');
            
            let isFormValid = true;

            // 1. VALIDACIÓN ESPECÍFICA DEL PROFESIONAL (llama a la función en main.js)
            // Asumiendo que validateProfesional() está en main.js y accesible globalmente
            if (profesionalInput && typeof validateProfesional === 'function') {
                if (!validateProfesional(profesionalInput, profesionalList, profesionalIdField, profesionalErrorDiv)) {
                    isFormValid = false;
                    profesionalInput.reportValidity();
                }
            }
            
            // 2. VALIDACIÓN HTML GENERAL
            const isValid = Array.from(form.querySelectorAll('[required]')).every(field => field.checkValidity());

            if (!isValid) {
                // Si la validación HTML falla, encontramos el primer campo inválido y lo reportamos
                form.querySelector('[required]:invalid').reportValidity();
                isFormValid = false;
            }
            
            // 3. SI AMBAS VALIDACIONES SON OK, PROCEDEMOS CON AJAX
            if (isFormValid) {
                submitDiagnosticFormAjax(); // Llamamos a la función AJAX de guardado
            }
        });
    });

    // Función que contiene el proceso AJAX de guardado
    function submitDiagnosticFormAjax() {
        const form = document.getElementById('beneficiaryForm');
        // El resto del código es idéntico a su función submitDiagnosticForm anterior:
        (async function() {
            const nextBtn = form.closest('.diagnosticos-container').querySelector('#submitDiagnosisBtn'); // Usamos el ID
            const globalValidationMessage = form.closest('.diagnosticos-container').querySelector('#formValidationMessage');
            const successModal = document.getElementById('successModal');
            const formData = new FormData(form);

            nextBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
            nextBtn.disabled = true;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    // Muestra el modal de éxito y redirige al histórico
                    if (successModal) {
                        const redirectId = form.querySelector('input[name="beneficiario_id"]').value;
                        const title = successModal.querySelector('.success-title');
                        const message = successModal.querySelector('.modal-body p');
                        
                        if (title) title.textContent = '¡Diagnóstico Registrado!';
                        if (message) message.textContent = 'El nuevo diagnóstico ha sido guardado correctamente.';

                        successModal.style.display = 'flex';
                        setTimeout(() => {
                            window.location.href = 'historico_diagnosticos.php?id=' + redirectId;
                        }, 3000);
                    }
                } else {
                    globalValidationMessage.textContent = result.message || 'Error desconocido al guardar el diagnóstico.';
                }
            } catch (error) {
                globalValidationMessage.textContent = 'Inténtalo de nuevo.';
                console.error('Error AJAX:', error);
            } finally {
                nextBtn.innerHTML = 'Guardar Diagnóstico <ion-icon name="checkmark-circle-outline"></ion-icon>';
                nextBtn.disabled = false;
            }
        })();
    }
</script>


</body>

</html>