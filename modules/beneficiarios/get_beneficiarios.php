<?php
include '../../config/db.php'; 

// Verificar si se solicitan beneficiarios inactivos
$inactivos = isset($_GET['inactivos']) && $_GET['inactivos'] == '1';
$condicion_estado = $inactivos ? "estatus_academico IN ('Baja temporal', 'Egresado', 'Baja definitiva')" : "estatus_academico = 'Activo'";

$query = "SELECT 
            id_beneficiario,
            matricula,
            CONCAT(nombre, ' ', apellido_paterno, ' ', IFNULL(apellido_materno, '')) AS nombre_completo,
            TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS edad,
            genero,
            tipo_discapacidad AS tipo_apoyo,
            estatus_academico
        FROM beneficiarios
        WHERE $condicion_estado
        ORDER BY id_beneficiario ASC";

$resultado = mysqli_query($conex, $query);

$data = array();
while ($row = mysqli_fetch_assoc($resultado)) {
    $row['opciones'] = '
        <div style="display: flex; gap: 8px; justify-content: center;">
            <a href="editar_beneficiarios.php?id='.$row['id_beneficiario'].'" class="btn-action2 btn-edit" title="Editar" style="background: #ffc107; color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.3s ease;">
                <i class="fas fa-pencil-alt"></i>
            </a>
            <a href="ver_beneficiarios.php?id='.$row['id_beneficiario'].'" class="btn-action2 btn-view" title="Ver" style="background: #28a745; color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.3s ease;">
                <i class="fas fa-eye"></i>
            </a>
            <a href="javascript:void(0);" onclick="confirmarEliminar('.$row['id_beneficiario'].')" class="btn-action2 btn-delete" title="Desactivar" style="background: #dc3545; color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.3s ease;">
                <i class="fas fa-times"></i>
            </a>
        </div>
    ';
    $data[] = $row;
}

// Devolver array directo sin envolver en "data"
echo json_encode($data);
