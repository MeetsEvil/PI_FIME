<?php
session_start();
if (!isset($_SESSION['usuarioingresando'])) {
    header("Location: ../auth/index.php");
    exit();
}
$user = $_SESSION['usuarioingresando'];
include '../../config/db.php';

// --- 1. Validar que se reciba el ID de la adaptación ---
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index_adaptaciones.php");
    exit();
}

$id_adaptacion = intval($_GET['id']);

// --- 2. Obtener los datos de la adaptación con el nombre del profesional ---
$query = "SELECT a.*, 
CONCAT(b.nombre, ' ', b.apellido_paterno, ' ', IFNULL(b.apellido_materno, '')) AS beneficiario_nombre,
CONCAT(p.nombre, ' ', p.apellido_paterno, ' ', IFNULL(p.apellido_materno, '')) AS profesional_nombre
FROM adaptaciones a
INNER JOIN beneficiarios b ON b.id_beneficiario = a.beneficiario_id
LEFT JOIN profesionales p ON p.id_profesional = a.profesional_id
WHERE a.id_adaptacion = ?";
$stmt = mysqli_prepare($conex, $query);
mysqli_stmt_bind_param($stmt, "i", $id_adaptacion);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$adaptacion = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$adaptacion) {
    header("Location: index_adaptaciones.php");
    exit();
}

$beneficiario_id = $adaptacion['beneficiario_id'];
$beneficiario_nombre = $adaptacion['beneficiario_nombre'];
$profesional_nombre = $adaptacion['profesional_nombre'] ?: 'No asignado';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Adaptación #<?php echo htmlspecialchars($id_adaptacion); ?></title>
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .view-container {
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
            padding-bottom: 50px;
            display: flex;
            flex-direction: column;
        }

        .view-container:hover {
            box-shadow: 0 6px 20px rgba(35, 147, 88, 0.3);
            transition: all 0.3s ease;
        }

        .view-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }

        .view-title {
            font-size: 2em;
            font-weight: 700;
            color: #000000ff;
            margin: 0;
        }

        .view-content {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
        }

        .info-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 12px;
            border-left: 4px solid #239358;
        }

        .info-section h3 {
            color: #239358;
            margin-bottom: 20px;
            font-size: 1.3em;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-row {
            display: flex;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .info-row:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .info-label {
            font-weight: 700;
            color: #555;
            min-width: 180px;
            font-size: 0.95em;
            padding-right: 5px;
            margin-right: 10px;
        }

        .info-value {
            color: #333;
            font-size: 0.95em;
            flex: 1;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #f0f0f0;
        }

        .btn-action {
            padding: 12px 30px;
            border: none;
            border-radius: 50px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-edit {
            background: linear-gradient(90deg, #FF9800, #E65100);
            color: white;
        }

        .btn-edit:hover {
            background: linear-gradient(90deg, #FB8C00, #D84315);
            box-shadow: 0 4px 12px rgba(255, 152, 0, 0.3);
            transform: translateY(-2px);
        }

        .btn-cancel {
            background: linear-gradient(90deg, #6c757d, #495057);
            color: white;
        }

        .btn-cancel:hover {
            background: linear-gradient(90deg, #5a6268, #3d4349);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
    <?php
    // Obtiene el nombre del archivo sin parámetros de la URL
    $currentPage = basename($_SERVER['PHP_SELF']);
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
                $adaptacionesPages = ['index_adaptaciones.php', 'crear_adaptaciones.php', 'editar_adaptaciones.php', 'historico_adaptaciones.php', 'ver_adaptaciones.php'];
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

        <div class="view-container">
            <div class="view-header">
                <h2 class="view-title">Información de la Adaptación</h2>
                <a href="historico_adaptaciones.php?id=<?php echo $beneficiario_id; ?>" class="btn-regresar">
                    <ion-icon name="caret-back-circle-outline"></ion-icon> Regresar
                </a>
            </div>

            <div class="view-content">
                <div class="info-grid">
                    <div class="info-section">
                        <h3>
                            <ion-icon name="information-circle-outline"></ion-icon>
                            Información General
                        </h3>

                        <div class="info-row">
                            <span class="info-label">N° Adaptación:</span>
                            <span class="info-value" style="font-weight: 700;"><?php echo htmlspecialchars($adaptacion['numero_adaptacion']); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Beneficiario:</span>
                            <span class="info-value"><?php echo htmlspecialchars($beneficiario_nombre); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Fecha de Implementación:</span>
                            <span class="info-value"><?php echo htmlspecialchars($adaptacion['fecha_implementacion']); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Tipo de Adaptación:</span>
                            <span class="info-value"><?php echo htmlspecialchars($adaptacion['tipo_adaptacion']); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Profesional Responsable:</span>
                            <span class="info-value"><?php echo htmlspecialchars($profesional_nombre); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Estado:</span>
                            <span class="info-value"><?php echo htmlspecialchars($adaptacion['estado']); ?></span>
                        </div>
                    </div>

                    <div class="info-section">
                        <h3>
                            <ion-icon name="document-text-outline"></ion-icon>
                            Descripción y Observaciones
                        </h3>

                        <div class="info-row">
                            <span class="info-label">Descripción:</span>
                            <span class="info-value"><?php echo nl2br(htmlspecialchars($adaptacion['descripcion'])); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Observaciones:</span>
                            <span class="info-value"><?php echo nl2br(htmlspecialchars($adaptacion['observaciones'] ?: 'Sin observaciones')); ?></span>
                        </div>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="editar_adaptaciones.php?id=<?php echo $adaptacion['id_adaptacion']; ?>" class="btn-action btn-edit">
                        <ion-icon name="create-outline"></ion-icon> Editar Adaptación
                    </a>
                    <a href="historico_adaptaciones.php?id=<?php echo $beneficiario_id; ?>" class="btn-action btn-cancel">
                        <ion-icon name="list-outline"></ion-icon> Ver Histórico
                    </a>
                </div>
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