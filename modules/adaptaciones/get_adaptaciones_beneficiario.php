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

// Consulta: Selecciona adaptacion y une la tabla de profesionales para obtener el nombre
$query = "SELECT 
            d.id_adaptacion,
            d.tipo_adaptacion,
            d.fecha_implementacion,
            CONCAT(p.nombre, ' ', p.apellido_paterno) AS nombre_profesional
        FROM adaptaciones d
        LEFT JOIN profesionales p ON d.profesional_id = p.id_profesional
        WHERE d.beneficiario_id = ?
        ORDER BY d.fecha_implementacion DESC";

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
        
        // Añadir la columna de opciones con botones Editar y Ver
        $row['opciones'] = '
            <a href="editar_adaptaciones.php?id=' . $row['id_adaptacion'] . '" class="btn-action2 btn-edit" title="Editar">
                <i class="fas fa-pencil-alt"></i>
            </a>
            <a href="ver_adaptaciones.php?id=' . $row['id_adaptacion'] . '" class="btn-action2 btn-view" title="Ver">
                <i class="fas fa-eye"></i>
            </a>
        ';
        $data[] = $row;
    }

    mysqli_stmt_close($stmt);
}

// Devuelve el JSON que DataTables espera
echo json_encode(["data" => $data]);
mysqli_close($conex);
