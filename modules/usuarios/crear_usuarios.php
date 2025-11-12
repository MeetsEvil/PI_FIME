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

$titulo_seccion = "Nuevo Usuario";
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
</head>

<body>
    <div class="container">
        <!-- Sidebar Navigation -->
        <div class="navigation">
            <?php
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
                <h2 class="section-title"><?php echo $titulo_seccion; ?></h2>
                <a href="index_usuarios.php" class="btn-regresar">
                    <ion-icon name="caret-back-circle-outline"></ion-icon> Regresar
                </a>
            </div>

            <!-- Contenedor del formulario con paginación -->
            <div class="form-pagination-container" style="min-height: 500px;">
                <form id="usuarioForm" action="guardar_usuario.php" method="POST" onsubmit="return false;">

                    <!-- PÁGINA 1: Datos Personales -->
                    <div class="form-page is-active" data-page="1">
                        <h3>Datos Personales</h3>

                        <label><span>Nombre: <span class="required-asterisk">*</span></span><input type="text" name="nombre" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" title="Solo se permiten letras y espacios." maxlength="100" required></label>

                        <label><span>Apellido Paterno: <span class="required-asterisk">*</span></span><input type="text" name="apellido_paterno" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" title="Solo se permiten letras y espacios." maxlength="100" required></label>

                        <label><span>Apellido Materno:</span><input type="text" name="apellido_materno" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" title="Solo se permiten letras y espacios." maxlength="100"></label>

                        <label><span>Correo: <span class="required-asterisk">*</span></span><input type="email" name="correo_institucional" required title="Ingresa un correo electrónico válido."></label>

                        <label><span>Teléfono:</span><input type="text" name="telefono" minlength="10" maxlength="10" pattern="[0-9]{10}" title="El teléfono debe tener exactamente 10 dígitos." placeholder="10 dígitos"></label>

                        <label><span>Especialidad: <span class="required-asterisk">*</span></span>
                            <select name="especialidad" required>
                                <option value="">Selecciona...</option>
                                <option value="Psicología">Psicología</option>
                                <option value="Educación Especial">Educación Especial</option>
                                <option value="Trabajo Social">Trabajo Social</option>
                                <option value="Medicina">Medicina</option>
                                <option value="Pedagogía">Pedagogía</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </label>
                    </div>

                    <!-- PÁGINA 2: Datos de Acceso -->
                    <div class="form-page" data-page="2">
                        <h3>Datos de Acceso</h3>

                        <label><span>Usuario: <span class="required-asterisk">*</span></span><input type="text" name="usuario" minlength="4" maxlength="50" required title="El usuario debe tener entre 4 y 50 caracteres."></label>

                        <label class="password-field">
                            <span>Contraseña: <span class="required-asterisk">*</span></span>
                            <div class="password-input-wrapper">
                                <input type="password" name="contrasena" id="contrasena" required minlength="4" title="La contraseña debe tener al menos 4 caracteres.">
                                <button type="button" class="toggle-password" onclick="togglePassword('contrasena')">
                                    <ion-icon name="eye-outline" class="eye-icon"></ion-icon>
                                </button>
                            </div>
                        </label>

                        <label class="password-field">
                            <span>Confirmar Contraseña: <span class="required-asterisk">*</span></span>
                            <div class="password-input-wrapper">
                                <input type="password" name="confirmar_contrasena" id="confirmar_contrasena" required minlength="4" title="Confirma tu contraseña.">
                                <button type="button" class="toggle-password" onclick="togglePassword('confirmar_contrasena')">
                                    <ion-icon name="eye-outline" class="eye-icon"></ion-icon>
                                </button>
                            </div>
                        </label>
                        <div id="passwordError" class="validation-message-small"></div>

                        <label><span>Rol: <span class="required-asterisk">*</span></span>
                            <select name="rol" required>
                                <option value="">Selecciona...</option>
                                <option value="Administrador">Administrador</option>
                                <option value="Profesional">Profesional</option>
                                <option value="Academico">Académico</option>
                            </select>
                        </label>

                        <label><span>Estado: <span class="required-asterisk">*</span></span>
                            <select name="estado" required>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </label>

                        <div class="permisos-section">
                            <h4 class="permisos-title" style="  font-size: 1em; color: #003366; text-align: center; margin-bottom: 15px; margin-top: 25px; font-weight: 700;">Permisos de Acceso</h4>

                            <div class="permisos-grid-two-columns">
                                <label class="checkbox-label">
                                    <span class="checkbox-text">Beneficiarios</span>
                                    <input type="checkbox" name="permiso_beneficiario" value="1">
                                </label>

                                <label class="checkbox-label">
                                    <span class="checkbox-text">Seguimiento</span>
                                    <input type="checkbox" name="permiso_diagnostico" value="1">
                                </label>

                                <label class="checkbox-label">
                                    <span class="checkbox-text">Adaptaciones</span>
                                    <input type="checkbox" name="permiso_adaptacion" value="1">
                                </label>

                                <label class="checkbox-label">
                                    <span class="checkbox-text">Intervenciones</span>
                                    <input type="checkbox" name="permiso_intervencion" value="1">
                                </label>
                            </div>
                        </div>
                    </div>

                </form>
            </div>

            <!-- Indicador de página -->
            <div class="pagination-info">
                Hoja <span id="currentPageNumber">1</span> de 2
            </div>

            <!-- Controles de Paginación -->
            <div class="pagination-buttons">
                <button type="button" class="btn-pagination btn-prev" id="prevBtn" style="display: none;">
                    <ion-icon name="arrow-back-outline"></ion-icon> Anterior
                </button>
                <button type="button" class="btn-pagination btn-next" id="nextBtn">
                    Siguiente <ion-icon name="arrow-forward-outline"></ion-icon>
                </button>
                <button type="button" class="btn-pagination btn-next" id="submitBtn" style="display: none;">
                    Guardar Usuario <ion-icon name="checkmark-circle-outline"></ion-icon>
                </button>
            </div>
            <div id="formValidationMessage" class="validation-message-global"></div>

        </div>
    </div>

    <!-- Modal de Éxito -->
    <div id="successModal" class="modal">
        <div class="modal-content success">
            <div class="modal-body">
                <ion-icon name="checkmark-circle-outline" class="success-icon"></ion-icon>
                <h2 class="success-title">¡Registro Exitoso!</h2>
                <p>El nuevo usuario ha sido creado correctamente.</p>
                <p>Serás redirigido a la lista de usuarios en 3 segundos.</p>
            </div>
        </div>
    </div>

    <!-- Modal de Logout -->
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

    <script src="../../assets/js/main_usuarios.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script>
        // Función para mostrar/ocultar contraseña
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const button = field.nextElementSibling;
            const icon = button.querySelector('.eye-icon');

            if (field.type === 'password') {
                field.type = 'text';
                icon.setAttribute('name', 'eye-off-outline');
            } else {
                field.type = 'password';
                icon.setAttribute('name', 'eye-outline');
            }
        }

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

            // Modal de Logout
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