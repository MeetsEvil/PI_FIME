<?php
session_start();
include '../../config/db.php'; // Ajusta la ruta según tu proyecto

if (!isset($_SESSION['usuarioingresando'])) {
    header("Location: ../auth/index.php");
    exit();
}
$user = $_SESSION['usuarioingresando'];
$currentPage = basename($_SERVER['REQUEST_URI']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes</title>
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- jQuery es necesario para el manejo de AJAX -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

                <li class="<?php echo ($currentPage == 'index_beneficiarios.php') ? 'active' : ''; ?>">
                    <a href="../../modules/beneficiarios/index_beneficiarios.php" data-tooltip="Beneficiarios">
                        <span class="icon"><ion-icon name="people-outline"></ion-icon></span>
                        <span class="title">Beneficiarios</span>
                    </a>
                </li>

                <li class="<?php echo ($currentPage == 'index_diagnosticos.php') ? 'active' : ''; ?>">
                    <a href="../../modules/diagnosticos/index_diagnosticos.php" data-tooltip="Diagnósticos">
                        <span class="icon"><ion-icon name="medkit-outline"></ion-icon></span>
                        <span class="title">Seguimiento</span>
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

                <?php
                // Profesionales - Solo visible para Administradores
                if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador') {
                    ?>
                    <li class="<?php echo ($currentPage == 'index_profesionales.php') ? 'active' : ''; ?>">
                        <a href="../../modules/profesionales/index_profesionales.php" data-tooltip="Profesionales">
                            <span class="icon"><ion-icon name="briefcase-outline"></ion-icon></span>
                            <span class="title">Profesionales</span>
                        </a>
                    </li>
                    <?php
                }
                ?>

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

        <div class="diagnosticos-container" style="height: auto; min-height: 740px;">
            <div class="header-section">
                <h2 class="section-title">Reportes</h2>
            </div>
        </div>
    </div>

    <!-- Modal de Contacto -->
    <div id="contactModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close-btn" id="closeContact">&times;</span>
                <h2>Información de Contacto</h2>
            </div>
            <div class="modal-body">
                <h3>Orlando Jair - Ingeniero en Sistemas</h3>
                <p></p>
                <div class="socialMedia">
                    <a class="socialIcon" href="https://github.com/MeetsEvil" target="_blank"><i class="fab fa-github"></i></a>
                    <a class="socialIcon" href="https://www.linkedin.com/in/orlandojgarciap-17a612289/" target="_blank"><i class="fab fa-linkedin"></i></a>
                    <a class="socialIcon" href="mailto:orlandojgarciap@gmail.com" target="_blank"><i class="fas fa-envelope"></i></a>
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

    </script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <script>
        // Modal de Contacto
        var contactModal = document.getElementById("contactModal");
        var closeContact = document.getElementById("closeContact");
        // Seleccionamos el nuevo botón de cancelar
        var cancelContactBtn = document.getElementById("cancelContactBtn");

        function mostrarInfo() {
            contactModal.style.display = "flex";
        }

        closeContact.onclick = function() {
            contactModal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == contactModal) {
                contactModal.style.display = "none";
            }
        }

        // Modal de Cerrar Sesión
        var logoutModal = document.getElementById("logoutModal");
        var closeLogoutBtn = document.querySelector("#logoutModal .close-btn"); // Selecciona el botón de cerrar del modal de logout
        var cancelBtn = document.getElementById("cancelBtn");

        function showLogoutModal() {
            logoutModal.style.display = "flex";
        }

        // Asegúrate de que los eventos de clic usen las variables correctas
        closeLogoutBtn.onclick = function() {
            logoutModal.style.display = "none";
        }

        cancelBtn.onclick = function() {
            logoutModal.style.display = "none";
        }

        // Lógica para cerrar el modal al hacer clic fuera de él
        window.onclick = function(event) {
            if (event.target == logoutModal) {
                logoutModal.style.display = "none";
            }
        }
    </script>

</body>

</html>