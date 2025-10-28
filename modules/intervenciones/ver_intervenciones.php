<?php
session_start();
if (!isset($_SESSION['usuarioingresando'])) {
    header("Location: ../auth/index.php");
    exit();
}
$user = $_SESSION['usuarioingresando'];
include '../../config/db.php';

// --- 1. Validar que se reciba el ID de la intervención ---
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index_intervenciones.php");
    exit();
}

$id_intervencion = intval($_GET['id']);

// --- 2. Obtener los datos de la intervención con el nombre del profesional ---
$query = "SELECT a.*, 
CONCAT(b.nombre, ' ', b.apellido_paterno, ' ', IFNULL(b.apellido_materno, '')) AS beneficiario_nombre,
CONCAT(p.nombre, ' ', p.apellido_paterno, ' ', IFNULL(p.apellido_materno, '')) AS profesional_nombre
FROM intervenciones a
INNER JOIN beneficiarios b ON b.id_beneficiario = a.beneficiario_id
LEFT JOIN profesionales p ON p.id_profesional = a.profesional_id
WHERE a.id_intervencion = ?";
$stmt = mysqli_prepare($conex, $query);
mysqli_stmt_bind_param($stmt, "i", $id_intervencion);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$intervencion = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$intervencion) {
    header("Location: index_intervenciones.php");
    exit();
}

$beneficiario_id = $intervencion['beneficiario_id'];
$beneficiario_nombre = $intervencion['beneficiario_nombre'];
$profesional_nombre = $intervencion['profesional_nombre'] ?: 'No asignado';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Intervención #<?php echo htmlspecialchars($id_intervencion); ?></title>
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- ESTILOS TEMPORALES PARA btn-regresar -->
    <style>
        .btn-regresar {
            background: linear-gradient(90deg, #ce2828, #720202) !important;
            border: none;
            color: white !important;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none !important;
            display: flex !important;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 10px 20px;
            border-radius: 50px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: all 0.3s;
        }

        .btn-regresar:hover {
            background: linear-gradient(90deg, #b52323, #5a0101) !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            transform: translateY(-2px);
        }

        .btn-regresar ion-icon {
            color: white !important;
            font-size: 1.4em;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="navigation">
            <?php
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
                $beneficiariosPages = ['index_beneficiarios.php', 'crear_beneficiarios.php', 'editar_beneficiarios.php', 'ver_beneficiarios.php'];
                ?>
                <li class="<?php echo in_array($currentPage, $beneficiariosPages) ? 'active' : ''; ?>">
                    <a href="../../modules/beneficiarios/index_beneficiarios.php" data-tooltip="Beneficiarios">
                        <span class="icon"><ion-icon name="people-outline"></ion-icon></span>
                        <span class="title">Beneficiarios</span>
                    </a>
                </li>

                <?php
                $diagnosticosPages = ['index_diagnosticos.php', 'crear_diagnosticos.php', 'editar_diagnosticos.php', 'historico_diagnosticos.php', 'ver_diagnosticos.php'];
                ?>
                <li class="<?php echo in_array($currentPage, $diagnosticosPages) ? 'active' : ''; ?>">
                    <a href="../../modules/diagnosticos/index_diagnosticos.php" data-tooltip="Diagnósticos">
                        <span class="icon"><ion-icon name="medkit-outline"></ion-icon></span>
                        <span class="title">Seguimiento</span>
                    </a>
                </li>

                <?php
                $adaptacionesPages = ['index_adaptaciones.php', 'crear_adaptaciones.php', 'editar_adaptaciones.php', 'historico_adaptaciones.php', 'ver_adaptaciones.php'];
                ?>
                <li class="<?php echo in_array($currentPage, $adaptacionesPages) ? 'active' : ''; ?>">
                    <a href="../../modules/adaptaciones/index_adaptaciones.php" data-tooltip="Adaptaciones">
                        <span class="icon"><ion-icon name="construct-outline"></ion-icon></span>
                        <span class="title">Adaptaciones</span>
                    </a>
                </li>

                <?php
                $intervencionesPages = ['index_intervenciones.php', 'crear_intervenciones.php', 'editar_intervenciones.php', 'historico_intervenciones.php', 'ver_intervenciones.php'];
                ?>
                <li class="<?php echo in_array($currentPage, $intervencionesPages) ? 'active' : ''; ?>">
                    <a href="../../modules/intervenciones/index_intervenciones.php" data-tooltip="Intervenciones">
                        <span class="icon"><ion-icon name="clipboard-outline"></ion-icon></span>
                        <span class="title">Intervenciones</span>
                    </a>
                </li>

                <?php
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
                <h2 class="section-title">Intervención de <?php echo htmlspecialchars($beneficiario_nombre); ?></h2>
                <a href="historico_intervenciones.php?id=<?php echo $beneficiario_id; ?>" class="btn-regresar">
                    <ion-icon name="caret-back-circle-outline"></ion-icon> Regresar
                </a>
            </div>

            <div class="form-pagination-container">
                <form>
                    <div class="form-page is-active" data-page="1">
                        <h3>Detalles de la Intervención</h3>

                        <div class="readonly-fields-group" style="display: flex; gap: 15px; margin-bottom: 10px;">

                            <label style="flex:1;"><span>N° Intervención:</span>
                                <input type="text"
                                    value="<?php echo htmlspecialchars($intervencion['numero_intervencion']); ?>"
                                    readonly
                                    style="background-color:#f0f0f0; font-weight: 700;">
                            </label>
                            <label style="flex:2;"><span>Beneficiario Asignado:</span>
                                <input type="text" value="<?php echo htmlspecialchars($beneficiario_nombre); ?>" readonly style="background-color:#f0f0f0;">
                            </label>
                        </div>

                        <label><span>Fecha de Implementación:</span>
                            <input type="date" value="<?php echo htmlspecialchars($intervencion['fecha_implementacion']); ?>" readonly style="background-color:#f0f0f0;">
                        </label>

                        <label><span>Tipo de Intervención:</span>
                            <input type="text" value="<?php echo htmlspecialchars($intervencion['tipo_intervencion']); ?>" readonly style="background-color:#f0f0f0;">
                        </label>

                        <label><span>Profesional Responsable:</span>
                            <input type="text" value="<?php echo htmlspecialchars($profesional_nombre); ?>" readonly style="background-color:#f0f0f0;">
                        </label>

                        <label><span>Estado:</span>
                            <input type="text" value="<?php echo htmlspecialchars($intervencion['estado']); ?>" readonly style="background-color:#f0f0f0;">
                        </label>

                        <label><span>Resultados Esperados:</span>
                            <textarea readonly style="background-color:#f0f0f0;"><?php echo htmlspecialchars($intervencion['resultados_esperados']); ?></textarea>
                        </label>

                        <label><span>Observaciones:</span>
                            <textarea readonly style="background-color:#f0f0f0;"><?php echo htmlspecialchars($intervencion['observaciones'] ?: 'Sin observaciones'); ?></textarea>
                        </label>
                    </div>
                </form>

                <!-- Sin botones de acción (solo vista) -->
                <div class="pagination-buttons"></div>
            </div>
        </div>
    </div>
    </div>

    <script src="../../assets/js/main.js"></script>
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