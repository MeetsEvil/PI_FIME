<?php
session_start();
require_once '../../config/db.php';

// Verificar que el usuario esté logueado y sea administrador
if (!isset($_SESSION['usuarioingresando']) || $_SESSION['rol'] !== 'Administrador') {
    header("Location: index_usuarios.php");
    exit();
}

// Obtener ID del usuario
$id_usuario = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_usuario <= 0) {
    $_SESSION['error'] = "ID de usuario inválido";
    header("Location: index_usuarios.php");
    exit();
}

// Cambiar estado a Inactivo en lugar de eliminar
$sql = "UPDATE usuarios_login SET estado = 'Inactivo' WHERE id = $id_usuario";

if (mysqli_query($conex, $sql)) {
    $_SESSION['success_delete'] = true;
    mysqli_close($conex);
    header("Location: index_usuarios.php");
    exit();
} else {
    $_SESSION['error'] = "Error al desactivar el usuario: " . mysqli_error($conex);
    mysqli_close($conex);
    header("Location: index_usuarios.php");
    exit();
}
