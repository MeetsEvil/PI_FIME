<?php
session_start();
if (!isset($_SESSION['usuarioingresando'])) {
    header("Location: ../auth/index.php");
    exit();
}
$user = $_SESSION['usuarioingresando'];

// Incluir la conexión a la base de datos
include '../../config/db.php';

// Obtiene el nombre del archivo de la URL para la navegación
$currentPage = basename($_SERVER['REQUEST_URI']);

// --- Lógica para obtener los datos del beneficiario ---
$beneficiario = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $beneficiario_id = intval($_GET['id']);

    $query = "SELECT * FROM beneficiarios WHERE id_beneficiario = ?";

    // Usar prepared statements para prevenir inyección SQL
    if ($stmt = mysqli_prepare($conex, $query)) {
        mysqli_stmt_bind_param($stmt, "i", $beneficiario_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $beneficiario = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    }
}

// Si no se encuentra el beneficiario, puedes redirigir o mostrar un mensaje de error
if (!$beneficiario) {
    header("Location: index_beneficiarios.php");
    exit();
}

// Lista de carreras (reutilizada del formulario de creación)
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

// Consulta para obtener los profesionales (necesario para la datalist, aunque sea de solo lectura)
$profesionales = [];
$query = "SELECT id_profesional, nombre, apellido_paterno, apellido_materno FROM profesionales WHERE estado = 'Activo'";
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
    <title>Ver Beneficiario</title>
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
                        <span class="title">Diagnósticos</span>
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
                // Profesionales
                $profesionalesPages = ['index_profesionales.php', 'crear_profesionales.php', 'editar_profesionales.php', 'ver_profesionales.php'];
                ?>
                <li class="<?php echo in_array($currentPage, $profesionalesPages) ? 'active' : ''; ?>">
                    <a href="../../modules/profesionales/index_profesionales.php" data-tooltip="Profesionales">
                        <span class="icon"><ion-icon name="briefcase-outline"></ion-icon></span>
                        <span class="title">Profesionales</span>
                    </a>
                </li>

                <?php
                // Reportes
                $reportesPages = ['index_reportes.php', 'generar_reportes.php'];
                ?>
                <li class="<?php echo in_array($currentPage, $reportesPages) ? 'active' : ''; ?>">
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

        <div class="beneficiary-container">
            <div class="header-section">
                <?php
                // --- CÓDIGO AÑADIDO/MODIFICADO AQUÍ ---
                $nombre_completo_beneficiario = trim(
                    $beneficiario['nombre'] . ' ' .
                        $beneficiario['apellido_paterno'] . ' ' .
                        ($beneficiario['apellido_materno'] ?? '')
                );
                ?>
                <h2 class="section-title">Editar Beneficiario:
                    <?php echo htmlspecialchars($beneficiario['id_beneficiario'] . ' - ' . $nombre_completo_beneficiario); ?>
                </h2>
                <a href="../../modules/beneficiarios/index_beneficiarios.php" class="btn-regresar">
                    <ion-icon name="caret-back-circle-outline"></ion-icon> Regresar
                </a>
            </div>

            <div class="form-pagination-container">
                <form id="beneficiaryForm" onsubmit="return false;">

                    <div class="form-page is-active" data-page="1">
                        <h3>Datos Personales</h3>
                        <label><span>Nombre:</span><input type="text" name="nombre" value="<?php echo htmlspecialchars($beneficiario['nombre'] ?? ''); ?>" readonly></label>
                        <label><span>Apellido Paterno:</span><input type="text" name="apellido_paterno" value="<?php echo htmlspecialchars($beneficiario['apellido_paterno'] ?? ''); ?>" readonly></label>
                        <label><span>Apellido Materno:</span><input type="text" name="apellido_materno" value="<?php echo htmlspecialchars($beneficiario['apellido_materno'] ?? ''); ?>" readonly></label>
                        <label><span>CURP:</span><input type="text" name="curp" value="<?php echo htmlspecialchars($beneficiario['curp'] ?? ''); ?>" readonly></label>
                        <label><span>Fecha de nacimiento:</span><input type="date" name="fecha_nacimiento" value="<?php echo htmlspecialchars($beneficiario['fecha_nacimiento'] ?? ''); ?>" readonly></label>
                        <label><span>Género:</span>
                            <select name="genero" readonly disabled>
                                <option value="">Selecciona...</option>
                                <option value="Masculino" <?php echo ($beneficiario['genero'] ?? '') == 'Masculino' ? 'selected' : ''; ?>>Masculino</option>
                                <option value="Femenino" <?php echo ($beneficiario['genero'] ?? '') == 'Femenino' ? 'selected' : ''; ?>>Femenino</option>
                                <option value="Otro" <?php echo ($beneficiario['genero'] ?? '') == 'Otro' ? 'selected' : ''; ?>>Otro</option>
                            </select>
                        </label>
                        <label><span>Teléfono:</span><input type="tel" name="telefono" value="<?php echo htmlspecialchars($beneficiario['telefono'] ?? ''); ?>" readonly></label>
                        <label><span>Correo Institucional:</span><input type="email" name="correo_institucional" value="<?php echo htmlspecialchars($beneficiario['correo_institucional'] ?? ''); ?>" readonly></label>
                    </div>

                    <div class="form-page" data-page="2">
                        <h3>Datos Académicos</h3>
                        <label><span>Matrícula:</span><input type="text" name="matricula" value="<?php echo htmlspecialchars($beneficiario['matricula'] ?? ''); ?>" readonly></label>
                        <label><span>Carrera:</span>
                            <select name="carrera" readonly disabled>
                                <option value="">Selecciona una carrera...</option>
                                <?php foreach ($carreras as $carrera): ?>
                                    <option value="<?php echo htmlspecialchars($carrera); ?>" <?php echo ($beneficiario['carrera'] ?? '') == $carrera ? 'selected' : ''; ?>><?php echo htmlspecialchars($carrera); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label><span>Semestre:</span><input type="number" name="semestre" value="<?php echo htmlspecialchars($beneficiario['semestre'] ?? ''); ?>" readonly></label>
                        <label><span>Estatus Académico:</span>
                            <select name="estatus_academico" readonly disabled>
                                <option value="">Selecciona...</option>
                                <option value="Activo" <?php echo ($beneficiario['estatus_academico'] ?? '') == 'Activo' ? 'selected' : ''; ?>>Activo</option>
                                <option value="Baja temporal" <?php echo ($beneficiario['estatus_academico'] ?? '') == 'Baja temporal' ? 'selected' : ''; ?>>Baja temporal</option>
                                <option value="Egresado" <?php echo ($beneficiario['estatus_academico'] ?? '') == 'Egresado' ? 'selected' : ''; ?>>Egresado</option>
                                <option value="Baja definitiva" <?php echo ($beneficiario['estatus_academico'] ?? '') == 'Baja definitiva' ? 'selected' : ''; ?>>Baja definitiva</option>
                            </select>
                        </label>
                    </div>

                    <div class="form-page" data-page="3">
                        <h3>Inclusión y Apoyos</h3>
                        <label><span>Tipo de Discapacidad:</span><input type="text" name="tipo_discapacidad" value="<?php echo htmlspecialchars($beneficiario['tipo_discapacidad'] ?? ''); ?>" readonly></label>
                        <label><span>Diagnóstico:</span><textarea name="diagnostico" readonly><?php echo htmlspecialchars($beneficiario['diagnostico'] ?? ''); ?></textarea></label>
                        <label><span>Adaptaciones:</span><textarea name="adaptaciones" readonly><?php echo htmlspecialchars($beneficiario['adaptaciones'] ?? ''); ?></textarea></label>
                        <label><span>Recursos Asignados:</span><textarea name="recursos_asignados" readonly><?php echo htmlspecialchars($beneficiario['recursos_asignados'] ?? ''); ?></textarea></label>

                        <label><span>Profesional Asignado:</span>
                            <input type="text" name="profesional_asignado_nombre" value="<?php
                                                                                            // Busca el nombre del profesional usando el ID
                                                                                            $profesional_encontrado = array_search($beneficiario['profesional_asignado'], array_column($profesionales, 'id'));
                                                                                            echo $profesional_encontrado !== false ? htmlspecialchars($profesionales[$profesional_encontrado]['nombre']) : '';
                                                                                            ?>" readonly>
                        </label>
                    </div>

                    <div class="form-page" data-page="4">
                        <h3>Seguimiento Inicial</h3>
                        <label><span>Fecha de Ingreso:</span><input type="date" name="fecha_ingreso" value="<?php echo htmlspecialchars($beneficiario['fecha_ingreso'] ?? ''); ?>" readonly></label>
                        <label><span>Estado Inicial:</span><input type="text" name="estado_inicial" value="<?php echo htmlspecialchars($beneficiario['estado_inicial'] ?? ''); ?>" readonly></label>
                        <label><span>Observaciones Iniciales:</span><textarea name="observaciones_iniciales" readonly><?php echo htmlspecialchars($beneficiario['observaciones_iniciales'] ?? ''); ?></textarea></label>
                    </div>

                </form>
            </div>

            <div class="pagination-info">
                Hoja <span id="currentPageNumber">1</span> de 4
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

    <script src="../../assets/js/main.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>