<?php
include '../../config/db.php'; 

// Verificar si se solicitan usuarios inactivos
$inactivos = isset($_GET['inactivos']) && $_GET['inactivos'] == '1';
$condicion_estado = $inactivos ? "ul.estado = 'Inactivo'" : "ul.estado = 'Activo'";

// Consulta que combina datos de usuarios_login y profesionales
$query = "SELECT 
            ul.id AS id_usuario,
            CONCAT(COALESCE(p.nombre, ''), ' ', COALESCE(p.apellido_paterno, ''), ' ', COALESCE(p.apellido_materno, '')) AS nombre_usuario,
            ul.rol,
            COALESCE(p.correo_institucional, 'N/A') AS correo,
            COALESCE(p.especialidad, 'N/A') AS especialidad,
            ul.estado
        FROM usuarios_login ul
        LEFT JOIN profesionales p ON ul.usuario = p.usuario
        WHERE $condicion_estado
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
        <div style="display: flex; gap: 8px; justify-content: center;">
            <a href="editar_usuarios.php?id='.$row['id_usuario'].'" class="btn-action2 btn-edit" title="Editar" style="background: #ffc107; color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.3s ease;">
                <i class="fas fa-pencil-alt"></i>
            </a>
            <a href="ver_usuarios.php?id='.$row['id_usuario'].'" class="btn-action2 btn-view" title="Ver" style="background: #28a745; color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.3s ease;">
                <i class="fas fa-eye"></i>
            </a>
            <a href="javascript:void(0);" onclick="confirmarEliminar('.$row['id_usuario'].')" class="btn-action2 btn-delete" title="Desactivar" style="background: #dc3545; color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.3s ease;">
                <i class="fas fa-times"></i>
            </a>
        </div>
    ';
    $data[] = $row;
}

// Devolver array directo sin envolver en "data"
echo json_encode($data);
?>
