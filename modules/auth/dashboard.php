<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <h1>Bienvenido, <?php echo $_SESSION['usuarioingresando']; ?></h1>

    <!-- Botón para cerrar sesión -->
    <form method="post" action="">
        <input type="submit" name="cerrar_sesion" value="Cerrar sesión / Regresar al login">
    </form>

<?php
// Si se presionó el botón de cerrar sesión
if(isset($_POST['cerrar_sesion'])){
    session_unset(); // Borra todas las variables de sesión
    session_destroy(); // Destruye la sesión
    header("Location: index.php"); // Redirige al login
    exit();
}
?>
</body>
</html>
