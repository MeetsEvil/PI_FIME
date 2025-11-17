<?php
session_start();
require_once '../../config/db.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuarioingresando'])) {
    header("Location: index_adaptaciones.php");
    exit();
}

// Obtener ID de la adaptación y del beneficiario
$id_adaptacion = isset($_GET['id']) ? intval($_GET['id']) : 0;
$beneficiario_id = isset($_GET['beneficiario_id']) ? intval($_GET['beneficiario_id']) : 0;

if ($id_adaptacion <= 0) {
    $_SESSION['error'] = "ID de adaptación inválido";
    if ($beneficiario_id > 0) {
        header("Location: historico_adaptaciones.php?id=" . $beneficiario_id);
    } else {
        header("Location: index_adaptaciones.php");
    }
    exit();
}

// Eliminar el registro de adaptación
$sql = "DELETE FROM adaptaciones WHERE id_adaptacion = $id_adaptacion";

if (mysqli_query($conex, $sql)) {
    $_SESSION['success_delete'] = true;
    mysqli_close($conex);
    
    // Redirigir al histórico del beneficiario
    if ($beneficiario_id > 0) {
        header("Location: historico_adaptaciones.php?id=" . $beneficiario_id);
    } else {
        header("Location: index_adaptaciones.php");
    }
    exit();
} else {
    $_SESSION['error'] = "Error al eliminar la adaptación: " . mysqli_error($conex);
    mysqli_close($conex);
    
    if ($beneficiario_id > 0) {
        header("Location: historico_adaptaciones.php?id=" . $beneficiario_id);
    } else {
        header("Location: index_adaptaciones.php");
    }
    exit();
}
