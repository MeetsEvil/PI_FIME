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
    $id_adaptacion = isset($_POST['id_adaptacion']) ? intval($_POST['id_adaptacion']) : 0;
    $beneficiario_id = isset($_POST['beneficiario_id']) ? intval($_POST['beneficiario_id']) : 0;
    
        // AÑADIDO: Capturamos el N° de Adaptación (NO se usará en el UPDATE)
        $numero_adaptacion = isset($_POST['numero_adaptacion']) ? intval($_POST['numero_adaptacion']) : null; 
    
    $fecha_implementacion = $_POST['fecha_implementacion'] ?? null;
    $tipo_adaptacion = $_POST['tipo_adaptacion'] ?? null;
    $estado = $_POST['estado'] ?? null;
    $descripcion = $_POST['descripcion'] ?? null;
    $observaciones = $_POST['observaciones'] ?? null;

    // --- 2. Profesional (puede ser NULL) ---
    $profesional_raw = $_POST['profesional_asignado_id'] ?? '';
    if ($profesional_raw === '' || $profesional_raw === '0') {
        $profesional_id = null;
    } else {
        $profesional_id = intval($profesional_raw);
    }

    // --- 3. Validaciones mínimas ---
    if ($id_adaptacion <= 0) {
        echo json_encode(["success" => false, "error" => "ID de adaptación inválido."]);
        exit();
    }

    if (!$fecha_implementacion || !$tipo_adaptacion || !$estado || !$descripcion) {
        echo json_encode(["success" => false, "error" => "Faltan campos obligatorios."]);
        exit();
    }

    // --- 4. Armar consulta dinámica (maneja NULL en profesional_id) ---
    if ($profesional_id === null) {
        $query = "UPDATE adaptaciones 
                SET fecha_implementacion = ?, 
                    tipo_adaptacion = ?, 
                    estado = ?, 
                    descripcion = ?, 
                    observaciones = ?, 
                    profesional_id = NULL
                WHERE id_adaptacion = ?";
    } else {
        $query = "UPDATE adaptaciones 
                    SET fecha_implementacion = ?, 
                        tipo_adaptacion = ?, 
                        estado = ?, 
                        descripcion = ?, 
                        observaciones = ?, 
                        profesional_id = ?
                    WHERE id_adaptacion = ?";
    }

    // --- 5. Preparar y ejecutar ---
    $stmt = mysqli_prepare($conex, $query);

    if ($profesional_id === null) {
        mysqli_stmt_bind_param(
            $stmt,
            "sssssi",
            $fecha_implementacion,
            $tipo_adaptacion,
            $estado,
            $descripcion,
            $observaciones,
            $id_adaptacion
        );
    } else {
        mysqli_stmt_bind_param(
            $stmt,
            "sssssii",
            $fecha_implementacion,
            $tipo_adaptacion,
            $estado,
            $descripcion,
            $observaciones,
            $profesional_id,
            $id_adaptacion
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