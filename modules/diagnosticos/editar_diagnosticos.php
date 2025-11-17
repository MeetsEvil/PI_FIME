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
$query = "SELECT d.*, d.numero_diagnostico, -- Agregamos el campo de numeración
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
    <style>
        .diagnosticos-container {
            margin: 30px auto;
            margin-top: 50px;
            margin-left: 170px;
            margin-right: 10px;
            margin-bottom: 90px;
            padding: 30px;
            border: 1px solid #000;
            background: white;
            border: 2px solid #adabab;
            border-radius: 25px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            width: calc(95% - 200px);
            min-height: 95px;
            height: 740px;
            display: flex;
            flex-direction: column;
        }
    </style>
</head>

<body>
    <?php
    // Obtiene el nombre del archivo de la URL
    $currentPage = basename($_SERVER['REQUEST_URI']);
    ?>
    <div class="container">
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
                // Usuarios - Solo visible para Administradores
                if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador') {
                    $usuariosPages = ['index_usuarios.php', 'crear_usuarios.php', 'editar_usuarios.php', 'ver_usuarios.php'];
                ?>
                    <li class="<?php echo in_array($currentPage, $usuariosPages) ? 'active' : ''; ?>">
                        <a href="../../modules/usuarios/index_usuarios.php" data-tooltip="Usuarios">
                            <span class="icon"><ion-icon name="people-circle-outline"></ion-icon></span>
                            <span class="title">Usuarios</span>
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

        <div class="diagnosticos-container" style="min-height: 85vh;">
            <div class="header-section">
                <h2 class="section-title">Editar Seguimiento: <?php echo htmlspecialchars($beneficiario_nombre); ?></h2>
                <a href="historico_diagnosticos.php?id=<?php echo $beneficiario_id; ?>" class="btn-regresar">
                    <ion-icon name="caret-back-circle-outline"></ion-icon> Regresar
                </a>
            </div>

            <div class="form-pagination-container" style="min-height: 500px;">
                <form id="beneficiaryForm" action="actualizar_diagnostico.php" method="POST" onsubmit="return false;">
                    
                    <input type="hidden" name="id_diagnostico" value="<?php echo htmlspecialchars($id_diagnostico); ?>">
                    <input type="hidden" name="beneficiario_id" value="<?php echo htmlspecialchars($beneficiario_id); ?>">

                    <div class="form-page is-active" data-page="1">
                        <h3>Detalles de Seguimiento</h3>

                        <div class="readonly-fields-group" style="display: flex; gap: 15px; margin-bottom: 10px;">
                            <label style="flex: 1;">
                                <span>N° de Seguimiento:</span>
                                <input type="text" value="<?php echo htmlspecialchars($diagnostico['numero_diagnostico'] ?? 'N/A'); ?>" readonly style="background-color: #f0f0f0; font-weight: 700;">
                            </label>

                            <label style="flex: 2;">
                                <span>Beneficiario Asignado:</span>
                                <input type="text" value="<?php echo htmlspecialchars($beneficiario_nombre); ?>" readonly style="background-color: #f0f0f0;">
                            </label>
                        </div>

                        <label><span>Fecha de Seguimiento:</span><input type="date" name="fecha_diagnostico" value="<?php echo htmlspecialchars($diagnostico['fecha_diagnostico'] ?? ''); ?>" required></label>

                        <label><span>Tipo de Seguimiento:</span>
                            <select name="tipo_diagnostico" required>
                                <option value="">Selecciona...</option>
                                <option value="Médico General" <?php echo ($diagnostico['tipo_diagnostico'] ?? '') == 'Médico General' ? 'selected' : ''; ?>>Médico General</option>
                                <option value="Psicológico" <?php echo ($diagnostico['tipo_diagnostico'] ?? '') == 'Psicológico' ? 'selected' : ''; ?>>Psicológico</option>
                                <option value="Psicopedagógico" <?php echo ($diagnostico['tipo_diagnostico'] ?? '') == 'Psicopedagógico' ? 'selected' : ''; ?>>Psicopedagógico</option>
                                <option value="Oftalmológico" <?php echo ($diagnostico['tipo_diagnostico'] ?? '') == 'Oftalmológico' ? 'selected' : ''; ?>>Oftalmológico</option>
                                <option value="Auditivo" <?php echo ($diagnostico['tipo_diagnostico'] ?? '') == 'Auditivo' ? 'selected' : ''; ?>>Auditivo</option>
                                <option value="Otro" <?php echo ($diagnostico['tipo_diagnostico'] ?? '') == 'Otro' ? 'selected' : ''; ?>>Otro</option>
                            </select>
                        </label>

                        <label><span>Profesional Asignado:</span>
                            <input type="text" name="profesional_asignado_nombre" list="profesionales-list" id="profesionalAsignadoNombre" value="<?php
                                $profesional_nombre = '';
                                if (!empty($diagnostico['profesional_asignado'])) {
                                    $profesional_encontrado = array_search($diagnostico['profesional_asignado'], array_column($profesionales, 'id'));
                                    $profesional_nombre = $profesional_encontrado !== false ? $profesionales[$profesional_encontrado]['nombre'] : '';
                                }
                                echo htmlspecialchars($profesional_nombre);
                            ?>" required>
                            <input type="hidden" name="profesional_asignado_id" id="profesionalAsignadoId" value="<?php echo htmlspecialchars($diagnostico['profesional_asignado'] ?? ''); ?>">
                            <datalist id="profesionales-list">
                                <?php foreach ($profesionales as $profesional): ?>
                                    <option data-id="<?php echo $profesional['id']; ?>" value="<?php echo htmlspecialchars($profesional['nombre']); ?>">
                                <?php endforeach; ?>
                            </datalist>
                        </label>
                        <div id="profesionalError" class="validation-message-small"></div>

                        <label><span>Resultado (Detalles):</span><textarea name="resultado" required placeholder="Descripción detallada del seguimiento y hallazgos."><?php echo htmlspecialchars($diagnostico['resultado'] ?? ''); ?></textarea></label>

                        <label><span>Observaciones:</span><textarea name="observaciones" placeholder="Recomendaciones, próximos pasos o notas adicionales."><?php echo htmlspecialchars($diagnostico['observaciones'] ?? ''); ?></textarea></label>

                        <label><span>Archivo Adjunto (Ruta):</span><input type="text" name="archivo_adjunto" value="<?php echo htmlspecialchars($diagnostico['archivo_adjunto'] ?? ''); ?>" placeholder="Ruta o nombre del archivo adjunto (opcional)"></label>

                    </div>

                </form>
            </div>

            <div class="pagination-buttons">
                <button type="button" class="btn-pagination btn-next" id="submitDiagnosisBtn">
                    Actualizar Seguimiento <ion-icon name="checkmark-circle-outline"></ion-icon>
                </button>
            </div>
            <div id="formValidationMessage" class="validation-message-global"></div>

        </div>
    </div>

    <!-- Modal de éxito -->
    <div id="successModal" class="modal"></div>
        <div class="modal-content success">
            <div class="modal-body">
                <ion-icon name="checkmark-circle-outline" class="success-icon"></ion-icon>
                <h2 class="success-title">¡Actualización Exitosa!</h2>
                <p>Los cambios del seguimiento se han guardado correctamente.</p>
                <p>Serás redirigido al historial de seguimiento en 3 segundos.</p>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../assets/js/main_editar_diagnosticos.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const toggle = document.querySelector(".toggle");
            const navigation = document.querySelector(".navigation");
            const main = document.querySelector(".main");

            if (toggle && navigation && main) {
                toggle.onclick = () => {
                    navigation.classList.toggle("active");
                    main.classList.toggle("active");
                };
            }
        });
    </script>
</body>

</html>