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

// Si no se encuentra el beneficiario, redirigir o mostrar un mensaje de error
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

// Consulta para obtener los profesionales
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
    <title>Adaptaciones</title>
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
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

                <li class="<?php echo ($currentPage == 'index_beneficiarios.php' or $currentPage == 'crear_beneficiarios.php') ? 'active' : ''; ?>">
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
            <h2 class="page-title">Programa de Inclusión</h2>
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

            <!-- Incluye el modal de éxito para mostrar la confirmación después de la actualización -->
            <div id="successModal" class="modal">
                <div class="modal-content success">
                    <div class="modal-body">
                        <ion-icon name="checkmark-circle-outline" class="success-icon"></ion-icon>
                        <h2 class="success-title">¡Actualización Exitosa!</h2>
                        <p>Los cambios del beneficiario se han guardado correctamente.</p>
                        <p>Serás redirigido a la lista de beneficiarios en 3 segundos.</p>
                    </div>
                </div>
            </div>



            <script src="../../assets/js/main.js"></script>
            <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
            <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>