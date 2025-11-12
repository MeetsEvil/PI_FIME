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
    <title>Usuarios</title>
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- Extensión Botones -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <!-- Dependencias para exportar -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">

    <style>
        #tablaUsuarios {
            padding-top: 20px;
            margin-top: 70px;
            width: 100%;
            border-collapse: collapse;
            /* quita espacios entre bordes */
            margin-top: 30px;
            font-family: Arial, sans-serif;
            font-size: 0.95em;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        #tablaUsuarios th,
        #tablaUsuarios td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        #tablaUsuarios th {
            background-color: #239358;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
        }

        #tablaUsuarios tr:hover {
            background-color: #f1f1f1;
            cursor: pointer;
        }

        #tablaUsuarios tr:nth-child(even) {
            background-color: #f9f9f9;
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

        <div class="beneficiary-container">
            <div class="header-section">
                <h2 class="section-title">Usuarios</h2>
                <div style="display: flex; gap: 10px;">
                    <a href="../../modules/usuarios/crear_usuarios.php" class="btn-new">
                        <ion-icon name="add-circle-outline"></ion-icon> Nuevo
                    </a>
                </div>
            </div>
            <!-- Tabla HTML -->
            <table id="tablaUsuarios" class="tabla-beneficiarios" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre Usuario</th>
                        <th>Rol</th>
                        <th>Correo</th>
                        <th>Especialidad</th>
                        <th>Opciones</th>
                    </tr>
                </thead>
            </table>

            <!-- Script SOLO una vez -->
            <script>
                $('#tablaUsuarios').DataTable({
                    "ajax": "get_usuarios.php",
                    "columns": [{
                            "data": "id_usuario"
                        },
                        {
                            "data": "nombre_usuario"
                        },
                        {
                            "data": "rol"
                        },
                        {
                            "data": "correo"
                        },
                        {
                            "data": "especialidad"
                        },
                        {
                            "data": "opciones"
                        }
                    ],
                    "pageLength": 8,
                    "lengthMenu": [8, 16, 32, 50],
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
                    },
                    dom: 'Bfrtip',
                    buttons: [{
                            extend: 'copyHtml5',
                            text: 'Copiar',
                            className: 'btn btn-sm btn-secondary'
                        },
                        {
                            extend: 'excelHtml5',
                            text: 'Excel',
                            className: 'btn btn-sm btn-success'
                        },
                        {
                            extend: 'pdfHtml5',
                            text: 'PDF',
                            className: 'btn btn-sm btn-danger',
                            orientation: 'landscape',
                            pageSize: 'A4'
                        }
                    ]
                });
            </script>
        </div>
    </div>
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
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <script>
        // Variables globales (deben existir fuera de la función de inicialización)
        var contactModal = document.getElementById("contactModal");
        var logoutModal = document.getElementById("logoutModal");

        // Funciones llamadas por el atributo onclick
        function mostrarInfo() {
            contactModal.style.display = "flex";
        }

        function showLogoutModal() {
            logoutModal.style.display = "flex";
        }

        // --- Lógica de inicialización de eventos para cierre ---
        document.addEventListener('DOMContentLoaded', function() {
            var closeContact = document.getElementById("closeContact");
            var cancelContactBtn = document.getElementById("cancelContactBtn");
            var closeLogoutBtn = document.querySelector("#logoutModal .close-btn");
            var cancelBtn = document.getElementById("cancelBtn");

            // Eventos para el Modal de Contacto
            if (closeContact) closeContact.onclick = function() {
                contactModal.style.display = "none";
            }
            if (cancelContactBtn) cancelContactBtn.onclick = function() {
                contactModal.style.display = "none";
            }

            // Eventos para el Modal de Cerrar Sesión
            if (closeLogoutBtn) closeLogoutBtn.onclick = function() {
                logoutModal.style.display = "none";
            }
            if (cancelBtn) cancelBtn.onclick = function() {
                logoutModal.style.display = "none";
            }

            // Cierre al hacer clic fuera de los modales
            window.onclick = function(event) {
                if (event.target == contactModal) {
                    contactModal.style.display = "none";
                }
                if (event.target == logoutModal) {
                    logoutModal.style.display = "none";
                }
            }
        });
    </script>

</body>

</html>