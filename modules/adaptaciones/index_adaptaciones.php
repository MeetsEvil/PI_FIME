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
    <title>Adaptaciones</title>
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
                <h2 class="section-title">Adaptaciones</h2>
            </div>

            <!-- Estructura de Búsqueda -->
            <div class="search-container">
                <label class="search-label">Buscar beneficiario:</label>

                <div class="input-group-custom">
                    <input type="text" id="searchInput" name="buscar1" placeholder="Nombre completo o Matrícula"
                        onkeyup="if(event.key === 'Enter' || this.value.length >= 3) buscar_ahora(this.value)">
                    <button class="btn-search-icon" onclick="buscar_ahora($('#searchInput').val());">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                <div id="datos_buscador" class="results-card" style="display:none;">
                    <p class="result-placeholder">Comience a escribir para buscar beneficiarios...</p>
                </div>
                <div id="actionButtonsContainer" class="action-buttons-container" style="display:none;">
                    <button id="cancelSelectionBtn" class="btn-action-main btn-cancel-action" onclick="clearSelection();">
                        <i class="fas fa-times-circle"></i> Cancelar
                    </button>
                    <button id="viewHistoryBtn" class="btn-action-main" onclick="window.location.href='historico_diagnosticos.php?id=' + $('#selectedBeneficiaryId').val();">
                        <i class="fas fa-eye"></i> Visualizar Histórico
                    </button>
                </div>
            </div>
        </div>


        <!-- Campo oculto para almacenar el ID del beneficiario seleccionado -->
        <input type="hidden" id="selectedBeneficiaryId" value="">
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

    <script type="text/javascript">
        // Función para limpiar la selección y restablecer el buscador
        function clearSelection() {
            // 1. Limpiar el ID seleccionado
            $('#selectedBeneficiaryId').val('');

            // 2. Limpiar y habilitar el input
            $('#searchInput').val('');
            $('#searchInput').attr('disabled', false);

            // 3. Ocultar botones y resultados
            $('#actionButtonsContainer').hide();
            $('#datos_buscador').hide();

            // 4. Ocultar el botón Limpiar de la barra de búsqueda
            $('#clearSearchBtn').hide();
        }

        // Función global para iniciar la búsqueda AJAX
        function buscar_ahora(query) {

            // Si el input está bloqueado (porque ya se seleccionó un ID), llamar a la limpieza
            if ($('#searchInput').attr('disabled')) {
                // Si el usuario intenta buscar de nuevo, lo forzamos a limpiar primero
                clearSelection();
                // Luego permitimos que continúe la búsqueda si hay texto
                if (query.length >= 3) {
                    buscar_ahora(query);
                }
                return;
            }

            // Si el query es vacío o muy corto, limpiamos y salimos
            if (query.length < 3) {
                $('#datos_buscador').html('<p class="result-placeholder">Ingrese al menos 3 caracteres.</p>');
                $('#actionButtonsContainer').hide();
                $('#datos_buscador').show();
                return;
            }

            // Muestra indicador de carga
            $('#datos_buscador').html('<p class="result-placeholder"><i class="fas fa-spinner fa-spin"></i> Buscando...</p>');

            var parametros = {
                "buscar": query
            };

            $.ajax({
                data: parametros,
                type: 'POST',
                url: 'buscador.php', // Apunta al script PHP
                success: function(data) {
                    $('#datos_buscador').html(data);

                    // Si la búsqueda devuelve resultados, adjuntamos el evento de clic
                    if ($('#datos_buscador').find('.result-item').length > 0) {

                        // Mostramos el botón Limpiar en la barra de búsqueda
                        $('#clearSearchBtn').show();

                        $('.result-item').off('click').on('click', function() {
                            // Al seleccionar un resultado:
                            const selectedId = $(this).data('id');
                            const selectedText = $(this).text();

                            // 1. Almacenar el ID
                            $('#selectedBeneficiaryId').val(selectedId);

                            // 2. Ocultar resultados y poner el valor seleccionado en el input (efecto visual)
                            $('#datos_buscador').hide();
                            $('#searchInput').val(selectedText);

                            // 3. Mostrar los botones de acción
                            $('#actionButtonsContainer').show();

                            // 4. Bloquear el input para que no se pueda modificar la búsqueda sin limpiar
                            $('#searchInput').attr('disabled', true);
                        });
                    } else {
                        // Si no hay resultados, ocultar botones y Limpiar
                        $('#actionButtonsContainer').hide();
                        $('#clearSearchBtn').hide();
                    }
                },
                error: function() {
                    $("#datos_buscador").html('<p class="result-placeholder" style="color: red;">Error al contactar con el servidor de búsqueda.</p>');
                    $('#clearSearchBtn').hide();
                }
            });
            // Mostrar resultados después de la búsqueda AJAX
            $('#datos_buscador').show();
            $('#searchInput').attr('disabled', false); // Aseguramos que esté habilitado al buscar
        }
    </script>
</body>

</html>