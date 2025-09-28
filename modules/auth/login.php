<?php
session_start();
include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = mysqli_real_escape_string($conex, $_POST["txtusuario"]);
    $pass    = mysqli_real_escape_string($conex, $_POST["txtpassword"]);

    // Consulta con password plano (en producción usa password_hash y password_verify)
    $query = "SELECT * FROM usuarios_login WHERE usuario = '$usuario' AND contrasena = '$pass'";
    $resultado = mysqli_query($conex, $query);

    if ($resultado && mysqli_num_rows($resultado) === 1) {
        $fila = mysqli_fetch_assoc($resultado);
        $_SESSION['usuarioingresando'] = $fila['usuario'];
        $_SESSION['rol'] = $fila['rol'];
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<script>alert('Usuario o contraseña incorrectos'); window.location='index.php';</script>";
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>
