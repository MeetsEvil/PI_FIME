<?php
session_start();
if (isset($_SESSION['usuarioingresando'])) {
    header('Location: ../auth/dashboard.php'); // Redirige a dashboard si ya está logueado
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

        <div class="botondeintercambiar">
            <div id="btnvai"></div>
            <button type="button" class="botoncambiarcaja" onclick="loginvai()" id="vaibtnlogin">Login</button>
            <button type="button" class="botoncambiarcaja" onclick="registrarvai()" id="vaibtnregistrar">Registrar</button>
        </div>

        <!-- Formulario Login -->
        <div class="FormularioLogin">
        <form method="POST" id="frmlogin" class="grupo-entradas" action="login.php">
            <h1 class="TextoS">Iniciar sesión</h1>

            <div class="TextoCajas">Usuario</div>
            <input type="text" name="txtusuario" class="CajaTexto" autocomplete="off" required>

            <div class="TextoCajas">Contraseña</div>
            <input type="password" name="txtpassword" class="CajaTexto" autocomplete="off" required>

            <div>
                <input type="submit" value="Iniciar sesión" class="BtnLogin" name="btningresar">
            </div>
        </form>
        </div>

        <!-- Formulario Registrar -->
        <form method="POST" id="frmregistrar" class="grupo-entradas" action="registrar.php">
            <h1>Crear nueva cuenta</h1>

            <div class="TextoCajas">Ingresar usuario</div>
            <input type="text" name="txtusuario1" class="CajaTexto" autocomplete="off" required>

            <div class="TextoCajas">Ingresar contraseña</div>
            <input type="password" name="txtpassword1" class="CajaTexto" autocomplete="off" required>

            <div>
                <label for="rol">Selecciona rol</label>
                <select name="txtrol1" required class="CajaTexto">
                    <option value="Administrador">Administrador</option>
                    <option value="Profesional">Profesional</option>
                    <option value="Academico">Académico</option>
                </select>
            </div>

            <div>
                <input type="submit" value="Crear cuenta" class="BtnRegistrar" name="btnregistrar">
            </div>
        </form>

    </div>
</div>

<script src="../../assets/js/boton_formulario.js"></script>
</body>
</html>
