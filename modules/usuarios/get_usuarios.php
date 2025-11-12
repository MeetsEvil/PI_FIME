<?php
include '../../config/db.php'; 

// Consulta que combina datos de usuarios_login y profesionales
$query = "SELECT 
            ul.id AS id_usuario,
            CONCAT(COALESCE(p.nombre, ''), ' ', COALESCE(p.apellido_paterno, ''), ' ', COALESCE(p.apellido_materno, '')) AS nombre_usuario,
            ul.rol,
            COALESCE(p.correo_institucional, 'N/A') AS correo,
            COALESCE(p.especialidad, 'N/A') AS especialidad
        FROM usuarios_login ul
        LEFT JOIN profesionales p ON ul.usuario = p.usuario
        ORDER BY ul.id ASC";

$resultado = mysqli_query($conex, $query);

$data = array();
while ($row = mysqli_fetch_assoc($resultado)) {
    // Limpiar espacios en blanco del nombre
    $row['nombre_usuario'] = trim($row['nombre_usuario']);
    if (empty($row['nombre_usuario'])) {
        $row['nombre_usuario'] = 'Sin nombre';
    }
    
    $row['opciones'] = '
    <a href="editar_usuarios.php?id='.$row['id_usuario'].'" class="btn-action2 btn-edit" title="Editar">
        <i class="fas fa-pencil-alt"></i>
    </a>
    <a href="ver_usuarios.php?id='.$row['id_usuario'].'" class="btn-action2 btn-view" title="Ver">
        <i class="fas fa-eye"></i>
    </a>
';
    $data[] = $row;
}

echo json_encode(["data" => $data]);
?>
