<?php
session_start();
require_once '../../config/db.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuarioingresando'])) {
    header("Location: index_beneficiarios.php");
    exit();
}

// Obtener ID del beneficiario
$id_beneficiario = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_beneficiario <= 0) {
    $_SESSION['error'] = "ID de beneficiario inválido";
    header("Location: index_beneficiarios.php");
    exit();
}

// Cambiar estado a 'Baja temporal' en lugar de eliminar
$sql = "UPDATE beneficiarios SET estatus_academico = 'Baja temporal' WHERE id_beneficiario = $id_beneficiario";

if (mysqli_query($conex, $sql)) {
    $_SESSION['success_delete'] = true;
    mysqli_close($conex);
    header("Location: index_beneficiarios.php");
    exit();
} else {
    $_SESSION['error'] = "Error al desactivar el beneficiario: " . mysqli_error($conex);
    mysqli_close($conex);
    header("Location: index_beneficiarios.php");
    exit();
}
