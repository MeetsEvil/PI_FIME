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
            height: auto;
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

        .badge.inactivo,
        .badge.baja {
            background: #f8d7da;
            color: #721c24;
        }

        .badge.egresado {
            background: #d1ecf1;
            color: #0c5460;
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

        <div class="view-container">
            <div class="view-header">
                <?php
                $nombre_completo_beneficiario = trim(
                    $beneficiario['nombre'] . ' ' .
                        $beneficiario['apellido_paterno'] . ' ' .
                        ($beneficiario['apellido_materno'] ?? '')
                );
                ?>
                <h2 class="view-title">Información del Beneficiario</h2>
                <a href="../../modules/beneficiarios/index_beneficiarios.php" class="btn-regresar">
                    <ion-icon name="caret-back-circle-outline"></ion-icon> Regresar
                </a>
            </div>

            <div class="view-content">
                <div class="info-grid">
                    <!-- SECCIÓN 1: Datos Personales -->
                    <div class="info-section">
                        <h3>
                            <ion-icon name="person-outline"></ion-icon>
                            Datos Personales
                        </h3>

                        <div class="info-row">
                            <span class="info-label">ID:</span>
                            <span class="info-value"><?php echo $beneficiario['id_beneficiario']; ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Nombre Completo:</span>
                            <span class="info-value"><?php echo htmlspecialchars($nombre_completo_beneficiario); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">CURP:</span>
                            <span class="info-value"><?php echo htmlspecialchars($beneficiario['curp'] ?? 'N/A'); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Fecha de Nacimiento:</span>
                            <span class="info-value"><?php echo htmlspecialchars($beneficiario['fecha_nacimiento'] ?? 'N/A'); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Género:</span>
                            <span class="info-value"><?php echo htmlspecialchars($beneficiario['genero'] ?? 'N/A'); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Teléfono:</span>
                            <span class="info-value"><?php echo htmlspecialchars($beneficiario['telefono'] ?? 'N/A'); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Correo:</span>
                            <a href="mailto:<?php echo $beneficiario['correo_institucional'] ?? ''; ?>" class="info-value email">
                                <?php echo htmlspecialchars($beneficiario['correo_institucional'] ?? 'N/A'); ?>
                            </a>
                        </div>
                    </div>

                    <!-- SECCIÓN 2: Datos Académicos -->
                    <div class="info-section">
                        <h3>
                            <ion-icon name="school-outline"></ion-icon>
                            Datos Académicos
                        </h3>

                        <div class="info-row">
                            <span class="info-label">Matrícula:</span>
                            <span class="info-value"><?php echo htmlspecialchars($beneficiario['matricula'] ?? 'N/A'); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Carrera:</span>
                            <span class="info-value"><?php echo htmlspecialchars($beneficiario['carrera'] ?? 'N/A'); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Plan de Estudio:</span>
                            <span class="info-value"><?php echo htmlspecialchars($beneficiario['plan_de_estudio'] ?? 'N/A'); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Semestre:</span>
                            <span class="info-value"><?php echo htmlspecialchars($beneficiario['semestre'] ?? 'N/A'); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Estatus Académico:</span>
                            <span class="info-value">
                                <?php
                                $estatus = $beneficiario['estatus_academico'] ?? 'N/A';
                                $badge_class = 'activo';
                                if (in_array($estatus, ['Baja temporal', 'Baja definitiva'])) {
                                    $badge_class = 'baja';
                                } elseif ($estatus == 'Egresado') {
                                    $badge_class = 'egresado';
                                }
                                ?>
                                <span class="badge <?php echo $badge_class; ?>">
                                    <?php echo htmlspecialchars($estatus); ?>
                                </span>
                            </span>
                        </div>
                    </div>

                    <!-- SECCIÓN 3: Inclusión y Apoyos -->
                    <div class="info-section">
                        <h3>
                            <ion-icon name="accessibility-outline"></ion-icon>
                            Inclusión y Apoyos
                        </h3>

                        <div class="info-row">
                            <span class="info-label">Tipo de Discapacidad:</span>
                            <span class="info-value"><?php echo htmlspecialchars($beneficiario['tipo_discapacidad'] ?? 'N/A'); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Diagnóstico:</span>
                            <span class="info-value"><?php echo htmlspecialchars($beneficiario['diagnostico'] ?? 'N/A'); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Adaptaciones:</span>
                            <span class="info-value"><?php echo htmlspecialchars($beneficiario['adaptaciones'] ?? 'N/A'); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Recursos Asignados:</span>
                            <span class="info-value"><?php echo htmlspecialchars($beneficiario['recursos_asignados'] ?? 'N/A'); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Profesional Asignado:</span>
                            <span class="info-value">
                                <?php
                                $profesional_encontrado = array_search($beneficiario['profesional_asignado'], array_column($profesionales, 'id'));
                                echo $profesional_encontrado !== false ? htmlspecialchars($profesionales[$profesional_encontrado]['nombre']) : 'N/A';
                                ?>
                            </span>
                        </div>
                    </div>

                    <!-- SECCIÓN 4: Seguimiento y Contacto de Emergencia -->
                    <div class="info-section">
                        <h3>
                            <ion-icon name="medkit-outline"></ion-icon>
                            Seguimiento y Emergencia
                        </h3>

                        <div class="info-row">
                            <span class="info-label">Fecha de Ingreso:</span>
                            <span class="info-value"><?php echo htmlspecialchars($beneficiario['fecha_ingreso'] ?? 'N/A'); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Estado Inicial:</span>
                            <span class="info-value"><?php echo htmlspecialchars($beneficiario['estado_inicial'] ?? 'N/A'); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Observaciones:</span>
                            <span class="info-value"><?php echo htmlspecialchars($beneficiario['observaciones_iniciales'] ?? 'N/A'); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Contacto Emergencia:</span>
                            <span class="info-value"><?php echo htmlspecialchars($beneficiario['nombre_emergencia'] ?? 'N/A'); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Teléfono Emergencia:</span>
                            <span class="info-value"><?php echo htmlspecialchars($beneficiario['telefono_emergencia'] ?? 'N/A'); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Parentesco:</span>
                            <span class="info-value"><?php echo htmlspecialchars($beneficiario['parentesco_emergencia'] ?? 'N/A'); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="action-buttons">
                    <a href="editar_beneficiarios.php?id=<?php echo $beneficiario['id_beneficiario']; ?>" class="btn-action btn-edit">
                        <ion-icon name="create-outline"></ion-icon> Editar Beneficiario
                    </a>
                    <a href="index_beneficiarios.php" class="btn-action btn-cancel">
                        <ion-icon name="list-outline"></ion-icon> Ver Todos los Beneficiarios
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