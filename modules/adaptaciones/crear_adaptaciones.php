<?php
session_start();
if (!isset($_SESSION['usuarioingresando'])) {
    header("Location: ../auth/index.php");
    exit();
}
$user = $_SESSION['usuarioingresando'];

// 1. Incluir la conexión a la base de datos
include '../../config/db.php';

// Si la conexión falla, las consultas más abajo usarán $conex = false o null, lo cual se maneja
// en el if de las consultas o en el script de guardado.

// Obtiene el nombre del archivo de la URL
$currentPage = basename($_SERVER['REQUEST_URI']);

// --- 2. Obtener ID del Beneficiario y sus datos para el título ---
$beneficiario = null;
$beneficiario_id = null;

if (isset($_GET['beneficiario_id']) && is_numeric($_GET['beneficiario_id'])) {
    $beneficiario_id = intval($_GET['beneficiario_id']);

    // Consulta para obtener el nombre completo del beneficiario
    $query = "SELECT nombre, apellido_paterno, apellido_materno FROM beneficiarios WHERE id_beneficiario = ?";

    if (isset($conex) && $stmt = mysqli_prepare($conex, $query)) {
        mysqli_stmt_bind_param($stmt, "i", $beneficiario_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $beneficiario = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    }
}

// Si no se encuentra el beneficiario o el ID es inválido, redirigir al buscador
if (!$beneficiario) {
    header("Location: index_adaptaciones.php");
    exit();
}

// Generar el nombre completo para el título
$nombre_completo_beneficiario = trim(
    $beneficiario['nombre'] . ' ' .
        $beneficiario['apellido_paterno'] . ' ' .
        ($beneficiario['apellido_materno'] ?? '')
);
$titulo_seccion = "Nuevo Seguimiento para: " . htmlspecialchars($nombre_completo_beneficiario);

// --- 3. Consulta para obtener los profesionales activos ---
$profesionales = [];
$query = "SELECT id_profesional, nombre, apellido_paterno, apellido_materno FROM profesionales WHERE estado = 'Activo'";
if (isset($conex) && $conex) { // Añadida verificación de $conex
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

// --- 4. CALCULAR EL PRÓXIMO NÚMERO DE ADAPTACIÓN (Para mostrarlo al usuario) ---
// El número de adaptación es el conteo de registros existentes para ESTE beneficiario + 1.
$next_numero_adaptacion = '';
$numero_de_registros = 0;

// Solo ejecuta la consulta si el ID del beneficiario es válido y la conexión existe
if ($beneficiario_id !== null && isset($conex) && $conex) {
    // Consulta: Cuenta cuántas adaptaciones existen para ESTE beneficiario
    $query_count = "SELECT COUNT(id_adaptacion) AS total FROM adaptaciones WHERE beneficiario_id = ?";

    if ($stmt_count = mysqli_prepare($conex, $query_count)) {
        mysqli_stmt_bind_param($stmt_count, "i", $beneficiario_id);
        mysqli_stmt_execute($stmt_count);
        $result_count = mysqli_stmt_get_result($stmt_count);

        if ($row_count = mysqli_fetch_assoc($result_count)) {
            $numero_de_registros = intval($row_count['total']);
        }
        mysqli_stmt_close($stmt_count);
    }
}

// El próximo número de adaptación es (Registros existentes) + 1
$next_numero_adaptacion = $numero_de_registros + 1;
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
        .diagnosticos-container {
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


        <div class="diagnosticos-container" style="min-height: 85vh;">
            <div class="header-section">
                <!-- Título dinámico -->
                <h2 class="section-title"><?php echo $titulo_seccion; ?></h2>
                <a href="historico_adaptaciones.php?id=<?php echo $beneficiario_id; ?>" class="btn-regresar">
                    <ion-icon name="caret-back-circle-outline"></ion-icon> Regresar
                </a>
            </div>

            <!-- Contenedor del formulario (usaremos la lógica de paginación en el main.js) -->
            <div class="form-pagination-container" style="min-height: 500px;">
                <!-- NOTA: El formulario se envía a guardar_adaptaciones.php (debe ser creado) -->
                <form id="beneficiaryForm" action="guardar_adaptaciones.php" method="POST" onsubmit="return false;">

                    <!-- Campo oculto: ID del beneficiario para el backend -->
                    <input type="hidden" name="beneficiario_id" value="<?php echo htmlspecialchars($beneficiario_id); ?>">

                    <!-- Única Página: Datos de la Adaptación -->
                    <div class="form-page is-active" data-page="1">
                        <h3>Detalles de la Adaptación</h3>

                        <!-- NUEVOS CAMPOS DE VISUALIZACIÓN DE CONTEXTO -->
                        <div class="readonly-fields-group" style="display: flex; gap: 15px; margin-bottom: 10px;">
                            <label style="flex: 1;"><span>N° Adaptación:</span><input type="text" value="<?php echo $next_numero_adaptacion; ?>" readonly name="numero_adaptacion" style="background-color: #f0f0f0; font-weight: 700;">
                            </label>
                            <label style="flex: 2;">
                                <span>Beneficiario:</span>
                                <input type="text" value="<?php echo htmlspecialchars($nombre_completo_beneficiario); ?>" readonly style="background-color: #f0f0f0;">
                            </label>
                        </div>
                        <!-- FIN NUEVOS CAMPOS -->

                        <label><span>Fecha de Implementación:</span><input type="date" name="fecha_implementacion" required></label>

                        <label><span>Tipo de Adaptación:</span>
                            <select name="tipo_adaptacion" required>¿
                                <option value="">Selecciona...</option>
                                <option value="Curricular">Curricular</option>
                                <option value="Evaluativa">Evaluativa</option>
                                <option value="Infraestructura">Infraestructura</option>
                                <option value="Tecnológica">Tecnológica</option>
                                <option value="Material didáctico">Material didáctico</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </label>

                        <!-- Profesional Asignado (Mismas reglas de validación de Beneficiarios) -->
                        <label><span>Profesional Responsable:</span>
                            <input type="text" name="profesional_asignado_nombre" list="profesionales-list" id="profesionalAsignadoNombre" required>
                            <input type="hidden" name="profesional_asignado_id" id="profesionalAsignadoId">
                            <datalist id="profesionales-list">
                                <?php foreach ($profesionales as $profesional): ?>
                                    <option data-id="<?php echo $profesional['id']; ?>" value="<?php echo htmlspecialchars($profesional['nombre']); ?>">
                                    <?php endforeach; ?>
                            </datalist>
                        </label>
                        <div id="profesionalError" class="validation-message-small"></div>

                        <label><span>Estado:</span>
                            <select name="estado" required>
                                <option value="">Selecciona...</option>
                                <option value="Pendiente">Pendiente</option>
                                <option value="En progreso">En progreso</option>
                                <option value="Finalizada">Finalizada</option>
                            </select>
                        </label>
                        <label><span>Descripción:</span><textarea name="descripcion" placeholder="Descripción acerca de la adaptación a realizar."></textarea></label>

                        <label><span>Observaciones:</span><input type="text" name="observaciones" placeholder="Detalles acerca de la adaptación a realizar"></label>

                    </div>

                </form>
            </div>

            <!-- Controles de Paginación (Simulados como si fuera una sola página) -->
            <div class="pagination-buttons">
                <!-- El botón Siguiente actúa como Guardar -->
                <button type="button" class="btn-pagination btn-next" id="submitAdaptacionBtn">
                    Guardar Adaptación <ion-icon name="checkmark-circle-outline"></ion-icon>
                </button>
            </div>
            <div id="formValidationMessage" class="validation-message-global"></div>

        </div>
    </div>
    </div>
    <div id="successModal" class="modal">
        <div class="modal-content success">
            <div class="modal-body">
                <ion-icon name="checkmark-circle-outline" class="success-icon"></ion-icon>
                <h2 class="success-title">¡Registro Exitoso!</h2>
                <p>La adaptación ha sido guardada correctamente.</p>
                <p>Serás redirigido a la lista de adaptaciones en 3 segundos.</p>
            </div>
        </div>
    </div>
    <!-- MODALES Y SCRIPTS -->
    <script src="../../assets/js/main_adaptaciones.js"></script>
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