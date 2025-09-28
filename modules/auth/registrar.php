<?php
include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = mysqli_real_escape_string($conex, $_POST["txtusuario1"]);
    $pass    = mysqli_real_escape_string($conex, $_POST["txtpassword1"]);
    $rol     = mysqli_real_escape_string($conex, $_POST["txtrol1"]);

    // Insertar usuario
    $insertarusu = "INSERT INTO usuarios_login (usuario, contrasena, rol) VALUES ('$usuario', '$pass', '$rol')";
    $resultado = mysqli_query($conex, $insertarusu);

    if (!$resultado) {
        echo "<script>alert('Error al registrar usuario, puede que ya exista.'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Usuario registrado con éxito: $usuario'); window.location='index.php';</script>";
    }
} else {
    header("Location: index.php");
    exit();
}
?>
