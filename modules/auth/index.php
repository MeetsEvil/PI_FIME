<?php
session_start();
if (isset($_SESSION['usuarioingresando'])) {
    // Redirige a dashboard si ya está logueado
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Login | FIME Inclusivo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

    <div class="FormCajaLogin">
        <div class="FormLogin">

            <form method="POST" id="frmlogin" class="grupo-entradas" action="login.php">
                <div class="Titulo"><div class="Titulo11">
                    <h1>BIENVENIDO!</h1></div>

                    <div class="input-grupo">
                        <i class="fas fa-user icono"></i>
                        <input type="text" name="txtusuario" class="CajaTexto" placeholder="Usuario" autocomplete="off" required>
                    </div>

                    <div class="input-grupo">
                        <i class="fas fa-lock icono"></i>
                        <input type="password" id="password" name="txtpassword" class="CajaTexto" placeholder="Contraseña" autocomplete="off" required>
                        <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                    </div>

                    <div class="input-grupo select-container">
                        <i class="fas fa-user-tag icono"></i>
                        <select name="txtrol" required class="CajaTexto" id="selectRol">
                            <option value="Administrador">Administrador</option>
                            <option value="Profesional">Profesional</option>
                            <option value="Académico">Académico</option>
                        </select>
                        <span class="custom-arrow">&#9662;</span> <!-- Flecha ▼ -->
                    </div>


                    <!-- Nuevo script para deseleccionar al cargar -->
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const selectElement = document.getElementById('selectRol');
                            // Quita la selección de cualquier opción al cargar la página
                            selectElement.selectedIndex = -1;
                        });
                    </script>
                    <script>
                        document.getElementById('togglePassword').addEventListener('click', function() {
                            const passwordField = document.getElementById('password');
                            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                            passwordField.setAttribute('type', type);

                            this.classList.toggle('fa-eye');
                            this.classList.toggle('fa-eye-slash');
                        });
                    </script>

                </div>

                <div>
                    <input type="submit" value="Ingresar" class="BtnLogin" name="btningresar">
                </div>
            </form>
        </div>
    </div>

</body>

</html>