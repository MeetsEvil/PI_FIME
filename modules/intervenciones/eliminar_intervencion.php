<?php
session_start();
require_once '../../config/db.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuarioingresando'])) {
    header("Location: index_intervenciones.php");
    exit();
}

// Obtener ID de la intervención y del beneficiario
$id_intervencion = isset($_GET['id']) ? intval($_GET['id']) : 0;
$beneficiario_id = isset($_GET['beneficiario_id']) ? intval($_GET['beneficiario_id']) : 0;

if ($id_intervencion <= 0) {
    $_SESSION['error'] = "ID de intervención inválido";
    if ($beneficiario_id > 0) {
        header("Location: historico_intervenciones.php?id=" . $beneficiario_id);
    } else {
        header("Location: index_intervenciones.php");
    }
    exit();
}

// Eliminar el registro de intervención
$sql = "DELETE FROM intervenciones WHERE id_intervencion = $id_intervencion";

if (mysqli_query($conex, $sql)) {
    $_SESSION['success_delete'] = true;
    mysqli_close($conex);
    
    // Redirigir al histórico del beneficiario
    if ($beneficiario_id > 0) {
        header("Location: historico_intervenciones.php?id=" . $beneficiario_id);
    } else {
        header("Location: index_intervenciones.php");
    }
    exit();
} else {
    $_SESSION['error'] = "Error al eliminar la intervención: " . mysqli_error($conex);
    mysqli_close($conex);
    
    if ($beneficiario_id > 0) {
        header("Location: historico_intervenciones.php?id=" . $beneficiario_id);
    } else {
        header("Location: index_intervenciones.php");
    }
    exit();
}
