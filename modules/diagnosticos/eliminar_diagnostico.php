<?php
session_start();
require_once '../../config/db.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuarioingresando'])) {
    header("Location: index_diagnosticos.php");
    exit();
}

// Obtener ID del diagnóstico y del beneficiario
$id_diagnostico = isset($_GET['id']) ? intval($_GET['id']) : 0;
$beneficiario_id = isset($_GET['beneficiario_id']) ? intval($_GET['beneficiario_id']) : 0;

if ($id_diagnostico <= 0) {
    $_SESSION['error'] = "ID de seguimiento inválido";
    if ($beneficiario_id > 0) {
        header("Location: historico_diagnosticos.php?id=" . $beneficiario_id);
    } else {
        header("Location: index_diagnosticos.php");
    }
    exit();
}

// Eliminar el registro de diagnóstico
$sql = "DELETE FROM diagnosticos WHERE id_diagnostico = $id_diagnostico";

if (mysqli_query($conex, $sql)) {
    $_SESSION['success_delete'] = true;
    mysqli_close($conex);
    
    // Redirigir al histórico del beneficiario
    if ($beneficiario_id > 0) {
        header("Location: historico_diagnosticos.php?id=" . $beneficiario_id);
    } else {
        header("Location: index_diagnosticos.php");
    }
    exit();
} else {
    $_SESSION['error'] = "Error al eliminar el seguimiento: " . mysqli_error($conex);
    mysqli_close($conex);
    
    if ($beneficiario_id > 0) {
        header("Location: historico_diagnosticos.php?id=" . $beneficiario_id);
    } else {
        header("Location: index_diagnosticos.php");
    }
    exit();
}
