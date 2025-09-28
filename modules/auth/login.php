<?php
session_start();
include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = mysqli_real_escape_string($conex, $_POST["txtusuario"]);
    $pass    = mysqli_real_escape_string($conex, $_POST["txtpassword"]);
    $rol     = mysqli_real_escape_string($conex, $_POST["txtrol"]); // Se obtiene el rol del formulario

    // Consulta que ahora verifica los tres campos: usuario, contraseña y rol
    $query = "SELECT * FROM usuarios_login WHERE usuario = '$usuario' AND contrasena = '$pass' AND rol = '$rol'";
    $resultado = mysqli_query($conex, $query);

    if ($resultado && mysqli_num_rows($resultado) === 1) {
        $fila = mysqli_fetch_assoc($resultado);
        $_SESSION['usuarioingresando'] = $fila['usuario'];
        $_SESSION['rol'] = $fila['rol']; // Se guarda el rol en la sesión
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<script>alert('Usuario, contraseña o rol incorrectos'); window.location='index.php';</script>";
        exit();
    }
} else {
    // Si se intenta acceder a login.php directamente, se redirige a index
    header("Location: index.php");
    exit();
}
?>