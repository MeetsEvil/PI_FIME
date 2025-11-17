<?php
session_start();
if (!isset($_SESSION['usuarioingresando'])) {
    header("Location: ../auth/index.php");
    exit();
}
$user = $_SESSION['usuarioingresando'];

// Incluir la conexión a la base de datos
include '../../config/db.php';

// Obtiene el nombre del archivo de la URL
$currentPage = basename($_SERVER['REQUEST_URI']);

// --- Lógica para obtener los datos del usuario ---
$usuario_data = null;
$profesional_data = null;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $usuario_id = intval($_GET['id']);

    // Obtener datos de usuarios_login
    $query = "SELECT * FROM usuarios_login WHERE id = ?";
    if ($stmt = mysqli_prepare($conex, $query)) {
        mysqli_stmt_bind_param($stmt, "i", $usuario_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $usuario_data = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    }

    // Obtener datos de profesionales usando el nombre de usuario
    if ($usuario_data) {
        $query_prof = "SELECT * FROM profesionales WHERE usuario = ?";
        if ($stmt_prof = mysqli_prepare($conex, $query_prof)) {
            mysqli_stmt_bind_param($stmt_prof, "s", $usuario_data['usuario']);
            mysqli_stmt_execute($stmt_prof);
            $result_prof = mysqli_stmt_get_result($stmt_prof);
            $profesional_data = mysqli_fetch_assoc($result_prof);
            mysqli_stmt_close($stmt_prof);
        }
    }
}

// Si no se encuentra el usuario, redirigir
if (!$usuario_data) {
    header("Location: index_usuarios.php");
    exit();
}

$titulo_seccion = "Ver Usuario";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo_seccion; ?></title>
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
            /* borde negro */
            /* Degradado y bordes */
            background: white;
            border: 2px solid #adabab;
            border-radius: 25px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);

            /* Dimensiones */
            width: calc(95% - 200px);
            min-height: 95px;
            height: 740px;

            /* Configuración del layout interno */
            display: flex;
            flex-direction: column;
            /* Cambiado para apilar los elementos verticalmente */
            /* Aquí se elimina justify-content: center y align-items: center */
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
            max-width: 1000px;
            margin: 0 auto;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 35px;
            margin-bottom: 30px;
        }

        .info-section {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 12px;
            border-left: 4px solid #239358;
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
            min-width: 150px;
            font-size: 0.95em;
            padding-right: 5px;
            margin-right: 10px;
        }

        .info-value {
            color: #333;
            font-size: 0.95em;
            flex: 1;
        }

        .info-value.email {
            color: #2196F3;
            text-decoration: none;
        }

        .info-value.email:hover {
            text-decoration: underline;
        }

        .badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9em;
        }

        .badge.activo {
            background: #d4edda;
            color: #155724;
        }

        .badge.inactivo {
            background: #f8d7da;
            color: #721c24;
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

        @media (max-width: 1200px) {
            .info-grid {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 1.2em !important;
            }

            .view-container {
                margin-left: 20px !important;
                margin-right: 20px !important;
                width: calc(100% - 40px) !important;
            }
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 1em !important;
            }

            .view-container {
                margin: 20px auto !important;
                margin-left: 10px !important;
                width: calc(100% - 20px) !important;
                padding: 20px !important;
            }

            .view-title {
                font-size: 1.5em !important;
            }

            .info-section {
                padding: 20px !important;
            }

            .action-buttons {
                flex-direction: column !important;
            }
        }

        @media (max-width: 480px) {
            .page-title {
                font-size: 0.9em !important;
            }

            .view-container {
                margin: 10px auto !important;
                margin-left: 5px !important;
                width: calc(100% - 10px) !important;
                padding: 15px !important;
            }

            .view-title {
                font-size: 1.3em !important;
            }

            .info-label {
                min-width: 120px !important;
                font-size: 0.85em !important;
            }

            .info-value {
                font-size: 0.85em !important;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="navigation">
            <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
            <ul>
                <li>
                    <a href="#">
                        <span class="icon"><ion-icon name="school-outline"></ion-icon></span>
                        <span class="title">FIME Inclusivo</span>
                    </a>
                </li>

                <li class="<?php echo ($currentPage == 'dashboard.php') ? 'active' : ''; ?>">
                    <a href="../../modules/auth/dashboard.php" data-tooltip="Inicio">
                        <span class="icon"><ion-icon name="home-outline"></ion-icon></span>
                        <span class="title">Inicio</span>
                    </a>
                </li>

                <?php $beneficiariosPages = ['index_beneficiarios.php', 'crear_beneficiarios.php', 'editar_beneficiarios.php', 'ver_beneficiarios.php']; ?>
                <li class="<?php echo in_array($currentPage, $beneficiariosPages) ? 'active' : ''; ?>">
                    <a href="../../modules/beneficiarios/index_beneficiarios.php" data-tooltip="Beneficiarios">
                        <span class="icon"><ion-icon name="people-outline"></ion-icon></span>
                        <span class="title">Beneficiarios</span>
                    </a>
                </li>

                <?php $diagnosticosPages = ['index_diagnosticos.php', 'crear_diagnosticos.php', 'editar_diagnosticos.php', 'historico_diagnosticos.php', 'ver_diagnosticos.php']; ?>
                <li class="<?php echo in_array($currentPage, $diagnosticosPages) ? 'active' : ''; ?>">
                    <a href="../../modules/diagnosticos/index_diagnosticos.php" data-tooltip="Diagnósticos">
                        <span class="icon"><ion-icon name="medkit-outline"></ion-icon></span>
                        <span class="title">Seguimiento</span>
                    </a>
                </li>

                <?php $adaptacionesPages = ['index_adaptaciones.php', 'crear_adaptaciones.php', 'editar_adaptaciones.php', 'historico_adaptacione.php', 'ver_adaptaciones.php']; ?>
                <li class="<?php echo in_array($currentPage, $adaptacionesPages) ? 'active' : ''; ?>">
                    <a href="../../modules/adaptaciones/index_adaptaciones.php" data-tooltip="Adaptaciones">
                        <span class="icon"><ion-icon name="construct-outline"></ion-icon></span>
                        <span class="title">Adaptaciones</span>
                    </a>
                </li>

                <?php $intervencionesPages = ['index_intervenciones.php', 'crear_intervenciones.php', 'editar_intervenciones.php', 'historico_intervenciones.php', 'ver_intervenciones.php']; ?>
                <li class="<?php echo in_array($currentPage, $intervencionesPages) ? 'active' : ''; ?>">
                    <a href="../../modules/intervenciones/index_intervenciones.php" data-tooltip="Intervenciones">
                        <span class="icon"><ion-icon name="clipboard-outline"></ion-icon></span>
                        <span class="title">Intervenciones</span>
                    </a>
                </li>

                <?php
                if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador') {
                    $usuariosPages = ['index_usuarios.php', 'crear_usuarios.php', 'editar_usuarios.php', 'ver_usuarios.php'];
                ?>
                    <li class="<?php echo in_array($currentPage, $usuariosPages) ? 'active' : ''; ?>">
                        <a href="../../modules/usuarios/index_usuarios.php" data-tooltip="Usuarios">
                            <span class="icon"><ion-icon name="people-circle-outline"></ion-icon></span>
                            <span class="title">Usuarios</span>
                        </a>
                    </li>
                <?php } ?>

                <li>
                    <a href="#" onclick="showLogoutModal()" data-tooltip="Cerrar Sesión">
                        <span class="icon"><ion-icon name="log-out-outline"></ion-icon></span>
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
                <h2 class="view-title">Información del Usuario</h2>
                <a href="index_usuarios.php" class="btn-regresar">
                    <ion-icon name="caret-back-circle-outline"></ion-icon> Regresar
                </a>
            </div>

            <div class="view-content">
                <div class="info-grid">
                    <!-- SECCIÓN IZQUIERDA: Información Personal -->
                    <div class="info-section">
                        <h3 style="color: #239358; margin-bottom: 20px; font-size: 1.3em;">
                            <ion-icon name="person-outline" style="vertical-align: middle;"></ion-icon>
                            Información Personal
                        </h3>

                        <div class="info-row">
                            <span class="info-label">ID:</span>
                            <span class="info-value"><?php echo $usuario_data['id']; ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Nombre Completo:</span>
                            <span class="info-value"><?php echo htmlspecialchars(($profesional_data['nombre'] ?? '') . ' ' . ($profesional_data['apellido_paterno'] ?? '') . ' ' . ($profesional_data['apellido_materno'] ?? '')); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Correo:</span>
                            <a href="mailto:<?php echo $profesional_data['correo_institucional'] ?? ''; ?>" class="info-value email">
                                <?php echo htmlspecialchars($profesional_data['correo_institucional'] ?? 'N/A'); ?>
                            </a>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Teléfono:</span>
                            <span class="info-value">
                                <?php echo ($profesional_data['telefono'] ?? false) ? htmlspecialchars($profesional_data['telefono']) : 'No especificado'; ?>
                            </span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Especialidad:</span>
                            <span class="info-value"><?php echo htmlspecialchars($profesional_data['especialidad'] ?? 'N/A'); ?></span>
                        </div>
                    </div>

                    <!-- SECCIÓN DERECHA: Información del Sistema -->
                    <div class="info-section">
                        <h3 style="color: #239358; margin-bottom: 20px; font-size: 1.3em;">
                            <ion-icon name="settings-outline" style="vertical-align: middle;"></ion-icon>
                            Información del Sistema
                        </h3>

                        <div class="info-row">
                            <span class="info-label">Usuario:</span>
                            <span class="info-value"><?php echo htmlspecialchars($usuario_data['usuario']); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Rol:</span>
                            <span class="info-value"><?php echo htmlspecialchars($usuario_data['rol']); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Estado:</span>
                            <span class="info-value">
                                <span class="badge <?php echo strtolower($usuario_data['estado'] ?? 'activo'); ?>">
                                    <?php echo $usuario_data['estado'] ?? 'Activo'; ?>
                                </span>
                            </span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Permisos:</span>
                            <span class="info-value">
                                <?php
                                $permisos = [];
                                if (($profesional_data['permiso_beneficiario'] ?? 0) == 1) $permisos[] = 'Beneficiarios';
                                if (($profesional_data['permiso_diagnostico'] ?? 0) == 1) $permisos[] = 'Seguimiento';
                                if (($profesional_data['permiso_adaptacion'] ?? 0) == 1) $permisos[] = 'Adaptaciones';
                                if (($profesional_data['permiso_intervencion'] ?? 0) == 1) $permisos[] = 'Intervenciones';
                                echo !empty($permisos) ? implode(', ', $permisos) : 'Sin permisos asignados';
                                ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="action-buttons">
                    <a href="editar_usuarios.php?id=<?php echo $usuario_data['id']; ?>" class="btn-action btn-edit">
                        <ion-icon name="create-outline"></ion-icon> Editar Usuario
                    </a>
                    <a href="index_usuarios.php" class="btn-action btn-cancel">
                        <ion-icon name="list-outline"></ion-icon> Ver Todos los Usuarios
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div id="logoutModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close-btn">&times;</span>
                <h2>Cierre de sesión</h2>
            </div>
            <div class="modal-body">
                <p>¿Confirmas que deseas cerrar sesión?</p>
            </div>
            <div class="modal-footer">
                <button id="cancelBtn" class="btn-cancel">Cancelar</button>
                <a href="../../modules/auth/logout.php" class="btn-confirm">Cerrar Sesión</a>
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

            var logoutModal = document.getElementById("logoutModal");
            var closeLogoutBtn = document.querySelector("#logoutModal .close-btn");
            var cancelBtn = document.getElementById("cancelBtn");

            window.showLogoutModal = function() {
                logoutModal.style.display = "flex";
            }

            if (closeLogoutBtn) closeLogoutBtn.onclick = function() {
                logoutModal.style.display = "none";
            }

            if (cancelBtn) cancelBtn.onclick = function() {
                logoutModal.style.display = "none";
            }

            window.onclick = function(event) {
                if (event.target == logoutModal) {
                    logoutModal.style.display = "none";
                }
            }
        });
    </script>
</body>

</html>