<?php
session_start();
if (!isset($_SESSION['usuarioingresando'])) {
    header("Location: ../auth/index.php");
    exit();
}
$user = $_SESSION['usuarioingresando'];

// 1. Incluir la conexión a la base de datos
include '../../config/db.php';

// Obtiene el nombre del archivo de la URL
$currentPage = basename($_SERVER['REQUEST_URI']);

// Lista de carreras
$carreras = [
    "Ingeniería Aeronáutica",
    "Ingeniería Biomédica",
    "Ingeniería Mecánica y Administración",
    "Ingeniería Mecánica y Administración Empresarial (modalidad dual)",
    "Ingeniería Mecánica y Eléctrica",
    "Ingeniería Mecatrónica",
    "Ingeniería de Manufactura",
    "Ingeniería de Materiales",
    "Ingeniería en Electrónica y Comunicaciones",
    "Ingeniería en Electrónica y Automatización",
    "Ingeniería en Electromovilidad",
    "Ingeniero Administrador de Sistemas"
];

// 2. Consulta para obtener los profesionales
$profesionales = [];
$query = "SELECT id_profesional, nombre, apellido_paterno, apellido_materno FROM profesionales WHERE estado = 'Activo'";
// Se asume que $conex está disponible desde db.php
if (isset($conex)) {
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
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Beneficiario</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    
    <!-- ESTILOS TEMPORALES PARA DEBUGGING -->
    <style>
        .btn-regresar {
            background: linear-gradient(90deg, #ce2828, #720202) !important;
            border: none !important;
            color: white !important;
            font-weight: 600 !important;
            cursor: pointer !important;
            text-decoration: none !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 5px !important;
            padding: 10px 20px !important;
            border-radius: 50px !important;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2) !important;
            transition: all 0.3s !important;
        }

        .btn-regresar:hover {
            background: linear-gradient(90deg, #b52323, #5a0101) !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3) !important;
            transform: translateY(-2px) !important;
        }

        .btn-regresar ion-icon {
            color: white !important;
            font-size: 1.4em !important;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="navigation">
            <?php
            // Obtiene solo el archivo actual sin parámetros
            $currentPage = basename($_SERVER['PHP_SELF']);
            ?>
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

                <?php
                // Beneficiarios
                $beneficiariosPages = ['index_beneficiarios.php', 'crear_beneficiarios.php', 'editar_beneficiarios.php', 'ver_beneficiarios.php'];
                ?>
                <li class="<?php echo in_array($currentPage, $beneficiariosPages) ? 'active' : ''; ?>">
                    <a href="../../modules/beneficiarios/index_beneficiarios.php" data-tooltip="Beneficiarios">
                        <span class="icon"><ion-icon name="people-outline"></ion-icon></span>
                        <span class="title">Beneficiarios</span>
                    </a>
                </li>

                <?php
                // Diagnosticos
                $diagnosticosPages = ['index_diagnosticos.php', 'crear_diagnosticos.php', 'editar_diagnosticos.php', 'historico_diagnosticos.php', 'ver_diagnosticos.php'];
                ?>
                <li class="<?php echo in_array($currentPage, $diagnosticosPages) ? 'active' : ''; ?>">
                    <a href="../../modules/diagnosticos/index_diagnosticos.php" data-tooltip="Diagnósticos">
                        <span class="icon"><ion-icon name="medkit-outline"></ion-icon></span>
                        <span class="title">Seguimiento</span>
                    </a>
                </li>

                <?php
                // Adaptaciones
                $adaptacionesPages = ['index_adaptaciones.php', 'crear_adaptaciones.php', 'editar_adaptaciones.php', 'historico_adaptacione.php', 'ver_adaptaciones.php'];
                ?>
                <li class="<?php echo in_array($currentPage, $adaptacionesPages) ? 'active' : ''; ?>">
                    <a href="../../modules/adaptaciones/index_adaptaciones.php" data-tooltip="Adaptaciones">
                        <span class="icon"><ion-icon name="construct-outline"></ion-icon></span>
                        <span class="title">Adaptaciones</span>
                    </a>
                </li>

                <?php
                // Intervenciones
                $intervencionesPages = ['index_intervenciones.php', 'crear_intervenciones.php', 'editar_intervenciones.php', 'historico_intervenciones.php', 'ver_intervenciones.php'];
                ?>
                <li class="<?php echo in_array($currentPage, $intervencionesPages) ? 'active' : ''; ?>">
                    <a href="../../modules/intervenciones/index_intervenciones.php" data-tooltip="Intervenciones">
                        <span class="icon"><ion-icon name="clipboard-outline"></ion-icon></span>
                        <span class="title">Intervenciones</span>
                    </a>
                </li>

                <?php
                // Profesionales - Solo visible para Administradores
                if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador') {
                    $profesionalesPages = ['index_profesionales.php', 'crear_profesionales.php', 'editar_profesionales.php', 'ver_profesionales.php'];
                ?>
                    <li class="<?php echo in_array($currentPage, $profesionalesPages) ? 'active' : ''; ?>">
                        <a href="../../modules/profesionales/index_profesionales.php" data-tooltip="Profesionales">
                            <span class="icon"><ion-icon name="briefcase-outline"></ion-icon></span>
                            <span class="title">Profesionales</span>
                        </a>
                    </li>
                <?php
                }
                ?>


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

        <div class="beneficiary-container">
            <div class="header-section">
                <h2 class="section-title">Registrar beneficiario</h2>
                <a href="../../modules/beneficiarios/index_beneficiarios.php" class="btn-regresar">
                    <ion-icon name="caret-back-circle-outline"></ion-icon> Regresar
                </a>
            </div>
            <div class="form-pagination-container">
                <form id="beneficiaryForm" action="guardar_beneficiarios.php" method="POST">

                    <div class="form-page is-active" data-page="1">
                        <h3>Datos Personales</h3>
                        <label><span>Nombre:</span><input type="text" name="nombre" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" title="Solo se permiten letras y espacios." maxlength="100" required></label>
                        <label><span>Apellido Paterno:</span><input type="text" name="apellido_paterno" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" title="Solo se permiten letras y espacios." maxlength="100" required></label>
                        <label><span>Apellido Materno:</span><input type="text" name="apellido_materno" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" title="Solo se permiten letras y espacios." maxlength="100" required></label>
                        <label><span>CURP:</span><input type="text" name="curp" minlength="18" maxlength="18" required pattern="[A-Z0-9]{18}" title="El CURP debe tener exactamente 18 caracteres alfanuméricos y estar en mayúsculas."></label>
                        <label><span>Fecha de nacimiento:</span><input type="date" name="fecha_nacimiento" required></label>
                        <label><span>Género:</span>
                            <select name="genero" required>
                                <option value="">Selecciona...</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </label>
                        <label><span>Teléfono:</span><input type="text" name="telefono" minlength="10" maxlength="10" required pattern="[0-9]{10}" title="El teléfono debe tener 10 dígitos."></label>
                        <label>
                            <span>Correo Institucional:</span>
                            <input type="email" name="correo_institucional" required title="Ingresa un correo electrónico válido.">
                        </label>
                    </div>

                    <div class="form-page" data-page="2">
                        <h3>Datos Académicos</h3>
                        <label><span>Matrícula:</span><input type="text" name="matricula" minlength="7" maxlength="7" required pattern="[0-9]{7}" title="La matrícula debe tener exactamente 7 dígitos."></label>
                        <label><span>Carrera:</span>
                            <select name="carrera" required>
                                <option value="">Selecciona una carrera...</option>
                                <?php foreach ($carreras as $carrera): ?>
                                    <option value="<?php echo htmlspecialchars($carrera); ?>"><?php echo htmlspecialchars($carrera); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label><span>Plan de Estudio:</span><input type="text" name="plan_de_estudio" value="<?php echo htmlspecialchars($beneficiario['plan_de_estudio'] ?? ''); ?>" required pattern="[0-9]{3}" maxlength="3" title="Debe tener exactamente 3 dígitos."></label>
                        <label>
                            <span>Semestre:</span>
                            <input type="number" name="semestre" min="1" max="12" required title="El semestre debe ser un número entre 1 y 12.">
                        </label>
                        <label><span>Estatus Académico:</span>
                            <select name="estatus_academico" required>
                                <option value="">Selecciona...</option>
                                <option value="Activo">Activo</option>
                                <option value="Baja temporal">Baja temporal</option>
                                <option value="Egresado">Egresado</option>
                                <option value="Baja definitiva">Baja definitiva</option>
                            </select>
                        </label>
                    </div>

                    <div class="form-page" data-page="3">
                        <h3>Inclusión y Apoyos</h3>
                        <label><span>Tipo de Discapacidad:</span><input type="text" name="tipo_discapacidad"></label>
                        <label><span>Diagnóstico:</span><input name="diagnostico"></label>
                        <label><span>Adaptaciones:</span><input name="adaptaciones"></label>
                        <label><span>Recursos Asignados:</span><input name="recursos_asignados"></label>

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
                    </div>

                    <div class="form-page" data-page="4">
                        <h3>Seguimiento Inicial</h3>
                        <label><span>Fecha de Ingreso:</span><input type="date" name="fecha_ingreso"></label>
                        <label><span>Estado Inicial:</span><input type="text" name="estado_inicial"></label>
                        <label><span>Observaciones Iniciales:</span><input name="observaciones_iniciales"></label>
                    </div>
                    <div class="form-page" data-page="5">
                        <h3>Contacto de Emergencia</h3>

                        <label><span>Nombre del Contacto:</span>
                            <input type="text" name="nombre_emergencia" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" title="Solo se permiten letras y espacios." maxlength="100" required>
                        </label>

                        <label><span>Teléfono del Contacto:</span>
                            <input type="tel" name="telefono_emergencia" minlength="10" maxlength="10" required pattern="[0-9]{10}" title="El teléfono debe tener 10 dígitos.">
                        </label>
                        <label><span>Parentesco:</span>
                            <select name="parentesco_emergencia" required>
                                <option value="">Selecciona...</option>
                                <option value="Madre">Madre</option>
                                <option value="Padre">Padre</option>
                                <option value="Hermano(a)">Hermano(a)</option>
                                <option value="Tío(a)">Tío(a)</option>
                                <option value="Abuelo(a)">Abuelo(a)</option>
                                <option value="Amigo(a)">Amigo(a)</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </label>
                    </div>


                </form>
            </div>

            <div class="pagination-info">
                Hoja <span id="currentPageNumber">1</span> de 5
            </div>
            <div class="pagination-buttons">
                <button type="button" class="btn-pagination btn-prev" style="display: none;">
                    <ion-icon name="arrow-back-outline"></ion-icon> Anterior
                </button>
                <button type="button" class="btn-pagination btn-next">
                    Siguiente <ion-icon name="arrow-forward-outline"></ion-icon>
                </button>
            </div>
            <div id="formValidationMessage" class="validation-message-global"></div>
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

    <script src="../../assets/js/main.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

</body>

</html>