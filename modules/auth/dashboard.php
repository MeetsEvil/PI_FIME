<?php
session_start();
if (!isset($_SESSION['usuarioingresando'])) {
    header("Location: ../auth/index.php");
    exit();
}
$user = $_SESSION['usuarioingresando'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FIME Inclusivo</title>
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
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

                <li class="hovered">
                    <a href="#">
                        <span class="icon"><ion-icon name="home-outline"></ion-icon></span>
                        <span class="title">Inicio</span>
                    </a>
                </li>

                <li>
                    <a href="../../modules/beneficiarios/index_beneficiarios.php">
                        <span class="icon"><ion-icon name="people-outline"></ion-icon></span>
                        <span class="title">Beneficiarios</span>
                    </a>
                </li>

                <li>
                    <a href="../../modules/diagnosticos/index_diagnosticos.php">
                        <span class="icon"><ion-icon name="medkit-outline"></ion-icon></span>
                        <span class="title">Diagnósticos</span>
                    </a>
                </li>

                <li>
                    <a href="../../modules/adaptaciones/index_adaptaciones.php">
                        <span class="icon"><ion-icon name="construct-outline"></ion-icon></span>
                        <span class="title">Adaptaciones</span>
                    </a>
                </li>

                <li>
                    <a href="../../modules/intervenciones/index_intervenciones.php">
                        <span class="icon"><ion-icon name="clipboard-outline"></ion-icon></span>
                        <span class="title">Intervenciones</span>
                    </a>
                </li>

                <li>
                    <a href="../../modules/profesionales/index_profesionales.php">
                        <span class="icon"><ion-icon name="briefcase-outline"></ion-icon></span>
                        <span class="title">Profesionales</span>
                    </a>
                </li>

                <li>
                    <a href="../../modules/reportes/index_reportes.php">
                        <span class="icon"><ion-icon name="bar-chart-outline"></ion-icon></span>
                        <span class="title">Reportes</span>
                    </a>
                </li>

                <li>
                    <a href="#" onclick="showLogoutModal()">
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
            <div class="module-box" id="beneficiarios-box">
                <div class="content-wrapper">
                    <div class="text-content">
                        <h2>N° de Beneficiarios<br><span>Activos: 210</span></h2>
                        <p>El Programa de Coordinación de Inclusión apoya a beneficiarios mediante estrategias sociales, fomentando igualdad de oportunidades y desarrollo comunitario.</p>
                        <a href="#" class="btn-consultar">Consultar información</a>
                    </div>
                    <div class="image-content">
                        <img src="../../assets/images/Dibujo_Mujer.png" alt="Dibujo">
                    </div>
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
        // Función para mostrar la información del usuario
        function mostrarInfo() {
            alert("Información del usuario:\nNombre: <?php echo htmlspecialchars($user); ?>\nRol: <?php echo htmlspecialchars($_SESSION['rol']); ?>\nAquí podrías mostrar más detalles en un modal bonito.");
        }

        // Script para el modal de cerrar sesión
        var modal = document.getElementById("logoutModal");
        var span = document.getElementsByClassName("close-btn")[0];
        var cancelBtn = document.getElementById("cancelBtn");

        function showLogoutModal() {
            modal.style.display = "flex";
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        cancelBtn.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>

</html>