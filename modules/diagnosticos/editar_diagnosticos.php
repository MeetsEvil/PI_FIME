<?php
session_start();
if (!isset($_SESSION['usuarioingresando'])) {
    header("Location: ../auth/index.php");
    exit();
}
$user = $_SESSION['usuarioingresando'];
include '../../config/db.php';

// --- 1. Validar que se reciba el ID del diagnóstico ---
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index_diagnosticos.php");
    exit();
}

$id_diagnostico = intval($_GET['id']);

// --- 2. Obtener los datos del diagnóstico ---
$query = "SELECT d.*, 
                CONCAT(b.nombre, ' ', b.apellido_paterno, ' ', IFNULL(b.apellido_materno, '')) AS beneficiario_nombre
        FROM diagnosticos d
        INNER JOIN beneficiarios b ON b.id_beneficiario = d.beneficiario_id
        WHERE d.id_diagnostico = ?";
$stmt = mysqli_prepare($conex, $query);
mysqli_stmt_bind_param($stmt, "i", $id_diagnostico);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$diagnostico = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$diagnostico) {
    header("Location: index_diagnosticos.php");
    exit();
}

$beneficiario_id = $diagnostico['beneficiario_id'];
$beneficiario_nombre = $diagnostico['beneficiario_nombre'];

// --- 3. Obtener lista de profesionales activos ---
$profesionales = [];
$query = "SELECT id_profesional, nombre, apellido_paterno, apellido_materno 
          FROM profesionales WHERE estado = 'Activo'";
$result = mysqli_query($conex, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $nombre_completo = trim($row['nombre'] . ' ' . $row['apellido_paterno'] . ' ' . $row['apellido_materno']);
    $profesionales[] = [
        'id' => $row['id_profesional'],
        'nombre' => $nombre_completo
    ];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Seguimiento #<?php echo htmlspecialchars($id_diagnostico); ?></title>
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    <div class="container">
        <?php
        // Obtiene solo el archivo actual sin parámetros
        $currentPage = basename($_SERVER['PHP_SELF']);
        ?>
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


        <div class="main">
            <div class="topbar">
                <div class="toggle"><ion-icon name="menu-outline"></ion-icon></div>
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
                    <h2 class="section-title">Editar Seguimiento de <?php echo htmlspecialchars($beneficiario_nombre); ?></h2>
                    <a href="historico_diagnosticos.php?id=<?php echo $beneficiario_id; ?>" class="btn-regresar">
                        <ion-icon name="caret-back-circle-outline"></ion-icon> Regresar
                    </a>
                </div>

                <div class="form-pagination-container">
                    <form id="editDiagnosisForm" action="actualizar_diagnostico.php" method="POST">
                        <input type="hidden" name="id_diagnostico" value="<?php echo $id_diagnostico; ?>">
                        <input type="hidden" name="beneficiario_id" value="<?php echo htmlspecialchars($beneficiario_id); ?>">

                        <div class="form-page is-active" data-page="1">
                            <h3>Detalles de Seguimiento</h3>

                            <div class="readonly-fields-group" style="display: flex; gap: 15px; margin-bottom: 10px;">
                                <label style="flex:1;"><span>ID de Registro:</span>
                                    <input type="text" value="<?php echo htmlspecialchars($id_diagnostico); ?>" readonly style="background-color:#f0f0f0;">
                                </label>
                                <label style="flex:2;"><span>Beneficiario Asignado:</span>
                                    <input type="text" value="<?php echo htmlspecialchars($beneficiario_nombre); ?>" readonly style="background-color:#f0f0f0;">
                                </label>
                            </div>

                            <label><span>Fecha de Seguimiento:</span>
                                <input type="date" name="fecha_diagnostico" required value="<?php echo htmlspecialchars($diagnostico['fecha_diagnostico']); ?>">
                            </label>

                            <label><span>Tipo de Seguimiento:</span>
                                <select name="tipo_diagnostico" required>
                                    <?php
                                    $tipos = ["Médico General", "Psicológico", "Psicopedagógico", "Oftalmológico", "Auditivo", "Otro"];
                                    foreach ($tipos as $tipo) {
                                        $sel = ($diagnostico['tipo_diagnostico'] == $tipo) ? "selected" : "";
                                        echo "<option value='$tipo' $sel>$tipo</option>";
                                    }
                                    ?>
                                </select>
                            </label>

                            <label><span>Profesional Asignado:</span>
                                <input type="text"
                                    name="profesional_asignado_nombre"
                                    list="profesionales-list"
                                    id="profesionalAsignadoNombre"
                                    value="<?php
                                            foreach ($profesionales as $p) {
                                                if ($p['id'] == $diagnostico['profesional_id']) {
                                                    echo htmlspecialchars($p['nombre']);
                                                    break;
                                                }
                                            }
                                            ?>"
                                    required>

                                <input type="hidden" name="profesional_asignado_id" id="profesionalAsignadoId" value="<?php echo htmlspecialchars($diagnostico['profesional_id']); ?>">
                                <datalist id="profesionales-list">
                                    <?php foreach ($profesionales as $p): ?>
                                        <option data-id="<?php echo $p['id']; ?>" value="<?php echo htmlspecialchars($p['nombre']); ?>"></option>
                                    <?php endforeach; ?>
                                </datalist>
                            </label>
                            <div id="profesionalError" class="validation-message-small"></div>

                            <label><span>Resultado:</span>
                                <textarea name="resultado" required><?php echo htmlspecialchars($diagnostico['resultado']); ?></textarea>
                            </label>

                            <label><span>Observaciones:</span>
                                <textarea name="observaciones"><?php echo htmlspecialchars($diagnostico['observaciones']); ?></textarea>
                            </label>

                            <label><span>Archivo Adjunto:</span>
                                <input type="text" name="archivo_adjunto" value="<?php echo htmlspecialchars($diagnostico['archivo_adjunto']); ?>">
                            </label>
                        </div>
                    </form>
                    <div class="pagination-buttons">
                        <button type="button" class="btn-pagination btn-next" id="updateDiagnosisBtn" style="margin-top: 20px;">
                            Guardar Cambios <ion-icon name="checkmark-circle-outline"></ion-icon>
                        </button>
                    </div>
                    <div id="formValidationMessage" class="validation-message-global"></div>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal de éxito -->
    <div id="successModal" class="modal">
        <div class="modal-content success">
            <div class="modal-body">
                <ion-icon name="checkmark-circle-outline" class="success-icon"></ion-icon>
                <h2 class="success-title">¡Actualización Exitosa!</h2>
                <p>Los cambios del diagnóstico se han guardado correctamente.</p>
                <p>Serás redirigido al historial de diagnósticos en 3 segundos.</p>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../assets/js/main_editar_diagnosticos.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>