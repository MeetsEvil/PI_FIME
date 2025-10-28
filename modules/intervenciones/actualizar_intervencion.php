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
    $id_intervencion = isset($_POST['id_intervencion']) ? intval($_POST['id_intervencion']) : 0;
    $beneficiario_id = isset($_POST['beneficiario_id']) ? intval($_POST['beneficiario_id']) : 0;
    
        // AÑADIDO: Capturamos el N° de Intervención (NO se usará en el UPDATE)
        $numero_intervencion = isset($_POST['numero_intervencion']) ? intval($_POST['numero_intervencion']) : null; 
    
    $fecha_implementacion = $_POST['fecha_implementacion'] ?? null;
    $tipo_intervencion = $_POST['tipo_intervencion'] ?? null;
    $estado = $_POST['estado'] ?? null;
    $resultados_esperados = $_POST['resultados_esperados'] ?? null;
    $observaciones = $_POST['observaciones'] ?? null;

    // --- 2. Profesional (puede ser NULL) ---
    $profesional_raw = $_POST['profesional_asignado_id'] ?? '';
    if ($profesional_raw === '' || $profesional_raw === '0') {
        $profesional_id = null;
    } else {
        $profesional_id = intval($profesional_raw);
    }

    // --- 3. Validaciones mínimas ---
    if ($id_intervencion <= 0) {
        echo json_encode(["success" => false, "error" => "ID de intervención inválido."]);
        exit();
    }

    if (!$fecha_implementacion || !$tipo_intervencion || !$estado || !$resultados_esperados) {
        echo json_encode(["success" => false, "error" => "Faltan campos obligatorios."]);
        exit();
    }

    // --- 4. Armar consulta dinámica (maneja NULL en profesional_id) ---
    if ($profesional_id === null) {
        $query = "UPDATE intervenciones 
                SET fecha_implementacion = ?, 
                    tipo_intervencion = ?, 
                    estado = ?, 
                    resultados_esperados = ?, 
                    observaciones = ?, 
                    profesional_id = NULL
                WHERE id_intervencion = ?";
    } else {
        $query = "UPDATE intervenciones 
                    SET fecha_implementacion = ?, 
                        tipo_intervencion = ?, 
                        estado = ?, 
                        resultados_esperados = ?, 
                        observaciones = ?, 
                        profesional_id = ?
                    WHERE id_intervencion = ?";
    }

    // --- 5. Preparar y ejecutar ---
    $stmt = mysqli_prepare($conex, $query);

    if ($profesional_id === null) {
        mysqli_stmt_bind_param(
            $stmt,
            "sssssi",
            $fecha_implementacion,
            $tipo_intervencion,
            $estado,
            $resultados_esperados,
            $observaciones,
            $id_intervencion
        );
    } else {
        mysqli_stmt_bind_param(
            $stmt,
            "sssssii",
            $fecha_implementacion,
            $tipo_intervencion,
            $estado,
            $resultados_esperados,
            $observaciones,
            $profesional_id,
            $id_intervencion
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