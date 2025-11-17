<?php
// Asegura que solo se envíen datos, sin HTML extra
header('Content-Type: application/json');

include '../../config/db.php'; // Incluye la conexión a la base de datos

// VERIFICACIÓN DE CONEXIÓN: Si la conexión falló, salimos inmediatamente.
if (!$conex) {
    echo json_encode(["data" => [], "error" => "Fallo en la conexión a la base de datos."]);
    exit();
}

// Verifica que el ID del beneficiario haya sido pasado
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(["data" => []]);
    exit();
}

$beneficiario_id = intval($_GET['id']);

// Consulta: Selecciona diagnósticos y une la tabla de profesionales para obtener el nombre
$query = "SELECT 
            d.numero_diagnostico,  -- Usar la columna ALMACENADA
            d.id_diagnostico,
            d.tipo_diagnostico,
            d.fecha_diagnostico,
            CONCAT(p.nombre, ' ', p.apellido_paterno) AS nombre_profesional
        FROM diagnosticos d
        LEFT JOIN profesionales p ON d.profesional_id = p.id_profesional
        WHERE d.beneficiario_id = ?
        ORDER BY d.numero_diagnostico ASC";


$data = array();

// Usar prepared statements
if ($stmt = mysqli_prepare($conex, $query)) {
    mysqli_stmt_bind_param($stmt, "i", $beneficiario_id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($resultado)) {
        
        // --- CORRECCIÓN CLAVE: Asegurar que el nombre del profesional no sea nulo ---
        if (empty($row['nombre_profesional'])) {
            $row['nombre_profesional'] = 'No Asignado';
        }
        // --------------------------------------------------------------------------
        
        // Añadir la columna de opciones con botones Editar, Ver y Eliminar
        $row['opciones'] = '
            <div style="display: flex; gap: 8px; justify-content: center;">
                <a href="editar_diagnosticos.php?id=' . $row['id_diagnostico'] . '" class="btn-action2 btn-edit" title="Editar" style="background: #ffc107; color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.3s ease;">
                    <i class="fas fa-pencil-alt"></i>
                </a>
                <a href="ver_diagnosticos.php?id=' . $row['id_diagnostico'] . '" class="btn-action2 btn-view" title="Ver" style="background: #28a745; color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.3s ease;">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="javascript:void(0);" onclick="confirmarEliminar(' . $row['id_diagnostico'] . ')" class="btn-action2 btn-delete" title="Eliminar" style="background: #dc3545; color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.3s ease;">
                    <i class="fas fa-trash-alt"></i>
                </a>
            </div>
        ';
        $data[] = $row;
    }

    mysqli_stmt_close($stmt);
}

// Devuelve el JSON que DataTables espera
echo json_encode(["data" => $data]);
mysqli_close($conex);
