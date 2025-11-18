<?php
session_start();
if (!isset($_SESSION['usuarioingresando'])) {
    header("Location: ../auth/index.php");
    exit();
}
$user = $_SESSION['usuarioingresando'];

// Verificar permisos de beneficiarios
$tiene_permiso_beneficiario = ($_SESSION['rol'] === 'Administrador') || (isset($_SESSION['permiso_beneficiario']) && $_SESSION['permiso_beneficiario'] == 1);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beneficiarios</title>
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
        .beneficiary-container {
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

        .table-wrapper {
            overflow-x: auto;
            overflow-y: auto;
            max-height: 570px;
        }

        .beneficiary-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .header-section {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            margin-bottom: 17px !important;
            padding-bottom: 15px !important;
            border-bottom: 2px solid #f0f0f0 !important;
        }

        .section-title {
            font-size: 2.3em;
            font-weight: 700;
            color: #000000;
            margin: 0;
        }

        .btn-new {
            background: #239358 !important;
            border: none !important;
            color: white !important;
            font-weight: 600 !important;
            cursor: pointer !important;
            text-decoration: none !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 5px !important;
            padding: 10px 20px !important;
            border-radius: 50px !important;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2) !important;
            transition: all 0.3s ease !important;
        }

        .btn-new:hover {
            background: #1a7043 !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3) !important;
            transform: translateY(-2px) !important;
        }

        .dataTables_wrapper {
            padding: 20px 0 !important;
            width: 100% !important;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 2px solid #239358 !important;
            border-radius: 8px !important;
            padding: 8px 15px !important;
            margin-left: 10px !important;
            font-size: 0.95em !important;
        }

        .dataTables_wrapper .dataTables_length select {
            border: 2px solid #239358 !important;
            border-radius: 8px !important;
            padding: 5px 10px !important;
            margin: 0 10px !important;
        }

        .dt-buttons {
            margin-bottom: 10px !important;
            display: flex !important;
            gap: 8px !important;
        }

        .btn-dt {
            background: #239358 !important;
            border: none !important;
            color: white !important;
            padding: 8px 15px !important;
            border-radius: 8px !important;
            cursor: pointer !important;
            font-size: 0.9em !important;
            transition: all 0.3s ease !important;
        }

        .btn-dt:hover {
            background: #1a7043 !important;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.3) !important;
            transform: translateY(-2px) !important;
        }

        #tablaBeneficiarios {
            width: 100% !important;
            border-collapse: collapse !important;
            background: white !important;
        }

        #tablaBeneficiarios thead {
            background: #239358 !important;
        }

        #tablaBeneficiarios th {
            background: transparent !important;
            color: white !important;
            font-weight: 600 !important;
            padding: 15px 10px !important;
            text-align: center !important;
            font-size: 0.95em !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
        }

        #tablaBeneficiarios td {
            padding: 12px 10px !important;
            text-align: center !important;
            vertical-align: middle !important;
            border-bottom: 1px solid #f0f0f0 !important;
            font-size: 0.9em !important;
        }

        #tablaBeneficiarios tbody tr {
            transition: all 0.2s ease !important;
        }

        #tablaBeneficiarios tbody tr:hover {
            background-color: #f0f9f4 !important;
            transform: scale(1.01) !important;
            box-shadow: 0 2px 5px rgba(35, 147, 88, 0.1) !important;
        }

        #tablaBeneficiarios tbody tr:nth-child(even) {
            background-color: #fafafa !important;
        }

        #tablaBeneficiarios tbody tr:nth-child(even):hover {
            background-color: #f0f9f4 !important;
        }

        .dataTables_wrapper .dataTables_paginate {
            padding-top: 20px !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 8px 12px !important;
            margin: 0 3px !important;
            border-radius: 8px !important;
            border: 2px solid #239358 !important;
            background: white !important;
            color: #239358 !important;
            font-weight: 500 !important;
            transition: all 0.3s ease !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: linear-gradient(90deg, #2db36a, #239358) !important;
            color: white !important;
            border: 2px solid #239358 !important;
            transform: translateY(-2px) !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(90deg, #2db36a, #239358) !important;
            color: white !important;
            border: 2px solid #239358 !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5 !important;
            cursor: not-allowed !important;
        }

        .dataTables_wrapper .dataTables_info {
            padding-top: 20px !important;
            color: #666 !important;
            font-size: 0.9em !important;
        }

        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_filter label {
            font-weight: 500 !important;
            color: #333 !important;
        }

        .btn-action2 {
            transition: all 0.3s ease !important;
        }

        .btn-action2:hover {
            transform: scale(1.1) !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3) !important;
        }

        .btn-edit:hover {
            background: #e0a800 !important;
        }

        .btn-view:hover {
            background: #218838 !important;
        }

        /* Forzar que el thead no tenga hover blanco */
        #tablaBeneficiarios thead tr:hover {
            background-color: #239358 !important;
            transform: none !important;
            box-shadow: none !important;
        }

        #tablaBeneficiarios thead th:hover {
            background-color: transparent !important;
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
                <h2 class="section-title">Beneficiarios</h2>
                <div style="display: flex; gap: 10px;">
                    <?php if ($tiene_permiso_beneficiario): ?>
                        <a href="crear_beneficiarios.php" class="btn-new">
                            <ion-icon name="add-circle-outline"></ion-icon> Nuevo Beneficiario
                        </a>
                    <?php else: ?>
                        <button onclick="mostrarModalPermisos()" class="btn-new" style="opacity: 0.6; cursor: not-allowed;">
                            <ion-icon name="lock-closed-outline"></ion-icon> Nuevo Beneficiario
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Tabla HTML con wrapper para scroll -->
            <div class="table-wrapper">
                <table id="tablaBeneficiarios" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Matrícula</th>
                            <th>Nombre</th>
                            <th>Edad</th>
                            <th>Género</th>
                            <th>Tipo de Apoyo</th>
                            <th>Estatus</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Los datos se cargarán dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Script de DataTable -->
        <script>
            let tablaBeneficiarios;
            let mostrandoInactivos = false;

            $(document).ready(function() {
                cargarTabla(false);

                // Verificar si hay éxito al eliminar
                <?php if (isset($_SESSION['success_delete']) && $_SESSION['success_delete'] === true): ?>
                    document.getElementById('successDeleteModal').style.display = 'flex';
                    setTimeout(function() {
                        document.getElementById('successDeleteModal').style.display = 'none';
                    }, 2000);
                    <?php unset($_SESSION['success_delete']); ?>
                <?php endif; ?>
            });

            function cargarTabla(inactivos) {
                if (tablaBeneficiarios) {
                    tablaBeneficiarios.destroy();
                }

                const url = inactivos ? 'get_beneficiarios.php?inactivos=1' : 'get_beneficiarios.php';

                tablaBeneficiarios = $('#tablaBeneficiarios').DataTable({
                    "ajax": {
                        "url": url,
                        "dataSrc": "",
                        "error": function(xhr, error, code) {
                            console.error('Error al cargar datos:', error);
                            console.error('Código:', code);
                            console.error('Respuesta:', xhr.responseText);
                        }
                    },
                    "columns": [{
                            "data": "id_beneficiario"
                        },
                        {
                            "data": "matricula"
                        },
                        {
                            "data": "nombre_completo"
                        },
                        {
                            "data": "edad"
                        },
                        {
                            "data": "genero"
                        },
                        {
                            "data": "tipo_apoyo"
                        },
                        {
                            "data": "estatus_academico"
                        },
                        {
                            "data": "opciones",
                            "orderable": false
                        }
                    ],
                    "pageLength": 10,
                    "lengthMenu": [10, 25, 50, 100],
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
                        "loadingRecords": "Cargando...",
                        "processing": "Procesando...",
                        "emptyTable": "No hay datos disponibles en la tabla",
                        "zeroRecords": "No se encontraron registros coincidentes"
                    },
                    "dom": 'Bfrtip',
                    "buttons": [{
                            extend: 'copyHtml5',
                            text: '<i class="fas fa-copy"></i> Copiar',
                            className: 'btn-dt',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6]
                            }
                        },
                        {
                            extend: 'excelHtml5',
                            text: '<i class="fas fa-file-excel"></i> Excel',
                            className: 'btn-dt',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6]
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            text: '<i class="fas fa-file-pdf"></i> PDF',
                            className: 'btn-dt',
                            orientation: 'landscape',
                            pageSize: 'A4',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6]
                            }
                        },
                        {
                            text: inactivos ? '<i class="fas fa-eye"></i> Ver Activos' : '<i class="fas fa-eye-slash"></i> Ver Inactivos',
                            className: 'btn-dt',
                            action: function() {
                                mostrandoInactivos = !mostrandoInactivos;
                                cargarTabla(mostrandoInactivos);
                            }
                        }
                    ],
                    "order": [
                        [0, 'asc']
                    ]
                });
            }

            function confirmarEliminar(id) {
                <?php if (!$tiene_permiso_beneficiario): ?>
                    mostrarModalPermisos();
                    return false;
                <?php endif; ?>
                document.getElementById('deleteModal').style.display = 'flex';
                document.getElementById('btnConfirmarEliminar').href = 'eliminar_beneficiario.php?id=' + id;
            }

            function cerrarModalEliminar() {
                document.getElementById('deleteModal').style.display = 'none';
            }

            function mostrarModalPermisos() {
                document.getElementById('permisosModal').style.display = 'flex';
            }

            function cerrarModalPermisos() {
                document.getElementById('permisosModal').style.display = 'none';
            }
        </script>
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

    <!-- Modal de confirmación de eliminación -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close-btn" onclick="cerrarModalEliminar()">&times;</span>
                <h2>Desactivar Beneficiario</h2>
            </div>
            <div class="modal-body">
                <p style="color: #000000; font-size: 1em;">¿Estás seguro de que deseas desactivar este beneficiario?</p>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="cerrarModalEliminar()">Cancelar</button>
                <a href="#" id="btnConfirmarEliminar" class="btn-confirm" style="background: #dc3545;">Desactivar</a>
            </div>
        </div>
    </div>

    <!-- Modal de éxito al eliminar -->
    <div id="successDeleteModal" class="modal">
        <div class="modal-content success">
            <div class="modal-body">
                <h2 class="success-title">¡Beneficiario Desactivado!</h2>
                <p style="margin-top: 8px;">El beneficiario ha sido desactivado correctamente.</p>
            </div>
        </div>
    </div>

    <!-- Modal de permisos denegados -->
    <div id="permisosModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close-btn" onclick="cerrarModalPermisos()">&times;</span>
                <h2 style="color: #ffffff; font-weight: 700;"><ion-icon name="lock-closed-outline" style="color: #ffffff;"></ion-icon> Acceso Denegado</h2>
            </div>
            <div class="modal-body">
                <p style="color: #000000; font-size: 1.1em; text-align: center;">No tienes los permisos necesarios para realizar esta acción.</p>
                <p style="color: #666; font-size: 0.95em; text-align: center; margin-top: 10px;">Por favor, contacta a un administrador para solicitar acceso.</p>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="cerrarModalPermisos()">Entendido</button>
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
                const deleteModal = document.getElementById('deleteModal');
                if (event.target == deleteModal) {
                    cerrarModalEliminar();
                }
            }
        });
    </script>

</body>

</html>