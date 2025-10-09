<?php
include '../../config/db.php';
header('Content-Type: application/json');

// Permite ver errores durante pruebas (desactiva en producción)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "error" => "Método no permitido."]);
    exit();
}

try {
    // --- 1. Recibir y validar los datos ---
    $id_diagnostico = isset($_POST['id_diagnostico']) ? intval($_POST['id_diagnostico']) : 0;
    $beneficiario_id = isset($_POST['beneficiario_id']) ? intval($_POST['beneficiario_id']) : 0;

    $fecha_diagnostico = $_POST['fecha_diagnostico'] ?? null;
    $tipo_diagnostico = $_POST['tipo_diagnostico'] ?? null;
    $resultado = $_POST['resultado'] ?? null;
    $observaciones = $_POST['observaciones'] ?? null;
    $archivo_adjunto = $_POST['archivo_adjunto'] ?? null;

    // --- 2. Profesional (puede ser NULL) ---
    $profesional_raw = $_POST['profesional_asignado_id'] ?? '';
    if ($profesional_raw === '' || $profesional_raw === '0') {
        $profesional_id = null;
    } else {
        $profesional_id = intval($profesional_raw);
    }

    // --- 3. Validaciones mínimas ---
    if ($id_diagnostico <= 0) {
        echo json_encode(["success" => false, "error" => "ID de diagnóstico inválido."]);
        exit();
    }

    if (!$fecha_diagnostico || !$tipo_diagnostico || !$resultado) {
        echo json_encode(["success" => false, "error" => "Faltan campos obligatorios."]);
        exit();
    }

    // --- 4. Armar consulta dinámica (maneja NULL en profesional_id) ---
    if ($profesional_id === null) {
        $query = "UPDATE diagnosticos 
                SET fecha_diagnostico = ?, 
                    tipo_diagnostico = ?, 
                    resultado = ?, 
                    observaciones = ?, 
                    archivo_adjunto = ?, 
                    profesional_id = NULL
                WHERE id_diagnostico = ?";
    } else {
        $query = "UPDATE diagnosticos 
                    SET fecha_diagnostico = ?, 
                        tipo_diagnostico = ?, 
                        resultado = ?, 
                        observaciones = ?, 
                        archivo_adjunto = ?, 
                        profesional_id = ?
                    WHERE id_diagnostico = ?";
    }

    // --- 5. Preparar y ejecutar ---
    $stmt = mysqli_prepare($conex, $query);

    if ($profesional_id === null) {
        mysqli_stmt_bind_param(
            $stmt,
            "sssssi",
            $fecha_diagnostico,
            $tipo_diagnostico,
            $resultado,
            $observaciones,
            $archivo_adjunto,
            $id_diagnostico
        );
    } else {
        mysqli_stmt_bind_param(
            $stmt,
            "ssssssi",
            $fecha_diagnostico,
            $tipo_diagnostico,
            $resultado,
            $observaciones,
            $archivo_adjunto,
            $profesional_id,
            $id_diagnostico
        );
    }

    mysqli_stmt_execute($stmt);
    $affected = mysqli_stmt_affected_rows($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conex);

    // --- 6. Respuesta final ---
    if ($affected >= 0) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "No se realizaron cambios."]);
    }
} catch (mysqli_sql_exception $e) {
    echo json_encode([
        "success" => false,
        "error" => "Error SQL: " . $e->getMessage()
    ]);
} catch (Exception $ex) {
    echo json_encode([
        "success" => false,
        "error" => "Error general: " . $ex->getMessage()
    ]);
}
