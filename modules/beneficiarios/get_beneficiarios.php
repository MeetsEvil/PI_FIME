<?php
include '../../config/db.php'; // ajusta la ruta según tu proyecto

$query = "SELECT 
            id_beneficiario,
            CONCAT(nombre, ' ', apellido_paterno, ' ', IFNULL(apellido_materno, '')) AS nombre_completo,
            TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS edad,
            genero,
            tipo_discapacidad AS tipo_apoyo,
            fecha_ingreso,
            observaciones_iniciales AS ultima_actualizacion
        FROM beneficiarios";

$resultado = mysqli_query($conex, $query);

$data = array();
while ($row = mysqli_fetch_assoc($resultado)) {
    $row['opciones'] = '
    <a href="editar_beneficiarios.php?id='.$row['id_beneficiario'].'" class="btn-action2 btn-edit" title="Editar">
        <i class="fas fa-pencil-alt"></i>
    </a>
    <a href="ver_beneficiarios.php?id='.$row['id_beneficiario'].'" class="btn-action2 btn-view" title="Ver">
        <i class="fas fa-eye"></i>
    </a>
';
    $data[] = $row;
}

echo json_encode(["data" => $data]);
?>
