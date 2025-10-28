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

// --- Lógica para obtener los datos del beneficiario (Necesaria para el título) ---
$beneficiario = null;
$beneficiario_id = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $beneficiario_id = intval($_GET['id']);

    $query = "SELECT nombre, apellido_paterno, apellido_materno FROM beneficiarios WHERE id_beneficiario = ?";

    if ($stmt = mysqli_prepare($conex, $query)) {
        mysqli_stmt_bind_param($stmt, "i", $beneficiario_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $beneficiario = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    }
}

// Si no se encuentra el beneficiario, redirigir
if (!$beneficiario) {
    header("Location: index_diagnosticos.php"); // Redirigir al buscador de diagnósticos
    exit();
}

// CONCATENAR EL NOMBRE COMPLETO PARA EL TÍTULO
$nombre_completo_beneficiario = trim(
    $beneficiario['nombre'] . ' ' .
        $beneficiario['apellido_paterno'] . ' ' .
        ($beneficiario['apellido_materno'] ?? '')
);
$titulo_seccion = "Histórico de Seguimiento: " . htmlspecialchars($nombre_completo_beneficiario);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo_seccion; ?></title>
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- Extensión Botones CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- Dependencias de Botones y Exportación -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <style>
            div.dt-buttons .dt-button {
            margin-bottom: 10px;
        }
    </style>

</head>

<body>
    <div class="navigation">
        <?php
        // Obtiene solo el archivo actual sin parámetros
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
            // Profesionales - Solo visible para Administradores
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
        <div class="diagnosticos-container">
            <div class="header-section-tabla">
                <h2 class="section-title"><?php echo $titulo_seccion; ?></h2>
                <div class="action-buttons-container" style="display: flex; gap: 10px;">
                <a href="exportar_completo2.php"
                    style="background: linear-gradient(90deg,rgb(200, 224, 90),rgb(143, 177, 20)); border: none; color: white; font-weight: 600; cursor: pointer; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 5px; padding: 10px 20px; border-radius: 50px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);">                    
                    <ion-icon name="download-outline"></ion-icon> Exportar Completo
                    </a>
                    <button id="newRecordBtn" class="btn-action-nuevo btn-new" style="font-size: 16px !important; font-weight: 700 !important;"
                        onclick="window.location.href='crear_diagnosticos.php?beneficiario_id=' + $('#beneficiarioId').val();">
                        <ion-icon name="add-circle-outline"></ion-icon> Nuevo
                    </button>
                </div>
            </div>
            <!-- TABLA DE HISTÓRICO DE DIAGNÓSTICOS -->
            <table id="tablaDiagnosticos" class="tabla-beneficiarios" style="width:100%">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Tipo Seguimiento</th>
                        <th>Fecha Consulta</th>
                        <th>Profesional Asignado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Los datos se cargarán aquí vía AJAX/DataTables -->
                </tbody>
            </table>

            <!-- Campo oculto para DataTables sepa qué beneficiario buscar -->
            <input type="hidden" id="beneficiarioId" value="<?php echo $beneficiario_id; ?>">
        </div>
    </div>

    <!-- Modales y Scripts Externos omitidos por brevedad -->
    <script src="../../assets/js/main.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script>
        $(document).ready(function() {
            const beneficiarioId = $('#beneficiarioId').val();

            $('#tablaDiagnosticos').DataTable({
                "ajax": "get_diagnosticos_beneficiario.php?id=" + beneficiarioId,
                "columns": [{
                        "data": "numero_diagnostico"
                    },
                    {
                        "data": "tipo_diagnostico"
                    },
                    {
                        "data": "fecha_diagnostico"
                    },
                    {
                        "data": "nombre_profesional"
                    },
                    {
                        "data": "opciones",
                        "orderable": false,
                        "searchable": false
                    }
                ],
                // ==============================================================
                // === CAMBIOS CLAVE AÑADIDOS: columnDefs y order inicial ===
                // ==============================================================

                // 1. Define la columna 0 (numero_adaptacion) como NUMÉRICA
                "columnDefs": [{
                    "type": "num",
                    "targets": 0
                }],

                // 2. Ordena la tabla inicialmente por la columna 0 (N°) ascendente (asc)
                "order": [
                    [0, "asc"]
                ],

                // ==============================================================

                // --- CAMBIOS REALIZADOS AQUÍ ---
                "pageLength": 7, // <-- Ahora muestra 7 registros por defecto
                "lengthMenu": [
                    [7, 14, 28, 50, -1],
                    [7, 14, 28, 50, "Todos"]
                ], // <-- Opciones de 7 en 7
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
                },
                // --- CONFIGURACIÓN DE BOTONES DE EXPORTACIÓN ---
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
                        pageSize: 'A4',
                        title: 'Histórico de Seguimiento - ID: <?php echo $beneficiario_id; ?>'
                    }
                ]
            });
        });
    </script>
</body>

</html>