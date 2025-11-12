<?php
session_start();
header('Content-Type: application/json');

// Verificar que el usuario esté autenticado y sea administrador
if (!isset($_SESSION['usuarioingresando']) || $_SESSION['rol'] !== 'Administrador') {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

include '../../config/db.php';

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

// Obtener y sanitizar datos del formulario
$id_usuario = mysqli_real_escape_string($conex, trim($_POST['id_usuario']));
$usuario_original = mysqli_real_escape_string($conex, trim($_POST['usuario_original']));

$nombre = mysqli_real_escape_string($conex, trim($_POST['nombre']));
$apellido_paterno = mysqli_real_escape_string($conex, trim($_POST['apellido_paterno']));
$apellido_materno = mysqli_real_escape_string($conex, trim($_POST['apellido_materno'] ?? ''));
$correo_institucional = mysqli_real_escape_string($conex, trim($_POST['correo_institucional']));
$telefono = mysqli_real_escape_string($conex, trim($_POST['telefono'] ?? ''));
$especialidad = mysqli_real_escape_string($conex, trim($_POST['especialidad']));

$usuario = mysqli_real_escape_string($conex, trim($_POST['usuario']));
$contrasena = mysqli_real_escape_string($conex, trim($_POST['contrasena'] ?? ''));
$rol = mysqli_real_escape_string($conex, trim($_POST['rol']));
$estado = mysqli_real_escape_string($conex, trim($_POST['estado']));

// Permisos (checkboxes)
$permiso_beneficiario = isset($_POST['permiso_beneficiario']) ? 1 : 0;
$permiso_diagnostico = isset($_POST['permiso_diagnostico']) ? 1 : 0;
$permiso_adaptacion = isset($_POST['permiso_adaptacion']) ? 1 : 0;
$permiso_intervencion = isset($_POST['permiso_intervencion']) ? 1 : 0;

// Validaciones básicas
if (empty($nombre) || empty($apellido_paterno) || empty($correo_institucional) || 
    empty($usuario) || empty($rol) || empty($especialidad)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos requeridos deben estar completos']);
    exit();
}

// Verificar que el usuario no exista ya (solo si se cambió el nombre de usuario)
if ($usuario !== $usuario_original) {
    $check_usuario = "SELECT id FROM usuarios_login WHERE usuario = ? AND id != ?";
    if ($stmt_check = mysqli_prepare($conex, $check_usuario)) {
        mysqli_stmt_bind_param($stmt_check, "si", $usuario, $id_usuario);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
        
        if (mysqli_num_rows($result_check) > 0) {
            echo json_encode(['success' => false, 'message' => 'El nombre de usuario ya existe']);
            mysqli_stmt_close($stmt_check);
            exit();
        }
        mysqli_stmt_close($stmt_check);
    }
}

// Verificar que el correo no exista ya (solo si se cambió el correo)
$check_correo = "SELECT id_profesional FROM profesionales WHERE correo_institucional = ? AND usuario != ?";
if ($stmt_check_correo = mysqli_prepare($conex, $check_correo)) {
    mysqli_stmt_bind_param($stmt_check_correo, "ss", $correo_institucional, $usuario_original);
    mysqli_stmt_execute($stmt_check_correo);
    $result_check_correo = mysqli_stmt_get_result($stmt_check_correo);
    
    if (mysqli_num_rows($result_check_correo) > 0) {
        echo json_encode(['success' => false, 'message' => 'El correo institucional ya está registrado']);
        mysqli_stmt_close($stmt_check_correo);
        exit();
    }
    mysqli_stmt_close($stmt_check_correo);
}

// Iniciar transacción
mysqli_begin_transaction($conex);

try {
    // 1. Actualizar tabla profesionales
    $query_profesional = "UPDATE profesionales SET 
        nombre = '$nombre',
        apellido_paterno = '$apellido_paterno',
        apellido_materno = '$apellido_materno',
        correo_institucional = '$correo_institucional',
        telefono = '$telefono',
        especialidad = '$especialidad',
        estado = '$estado',
        usuario = '$usuario',
        permiso_beneficiario = $permiso_beneficiario,
        permiso_diagnostico = $permiso_diagnostico,
        permiso_adaptacion = $permiso_adaptacion,
        permiso_intervencion = $permiso_intervencion";
    
    // Si se proporcionó una nueva contraseña, actualizarla
    if (!empty($contrasena)) {
        $query_profesional .= ", contrasena = '$contrasena'";
    }
    
    $query_profesional .= " WHERE usuario = '$usuario_original'";
    
    if (!mysqli_query($conex, $query_profesional)) {
        throw new Exception('Error al actualizar profesionales: ' . mysqli_error($conex));
    }

    // 2. Actualizar tabla usuarios_login
    $query_usuario = "UPDATE usuarios_login SET 
        usuario = '$usuario',
        rol = '$rol'";
    
    // Si se proporcionó una nueva contraseña, actualizarla
    if (!empty($contrasena)) {
        $query_usuario .= ", contrasena = '$contrasena'";
    }
    
    $query_usuario .= " WHERE id = $id_usuario";
    
    if (!mysqli_query($conex, $query_usuario)) {
        throw new Exception('Error al actualizar usuarios_login: ' . mysqli_error($conex));
    }

    // Confirmar transacción
    mysqli_commit($conex);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Usuario actualizado exitosamente'
    ]);

} catch (Exception $e) {
    // Revertir transacción en caso de error
    mysqli_rollback($conex);
    
    echo json_encode([
        'success' => false, 
        'message' => 'Error al actualizar el usuario: ' . $e->getMessage()
    ]);
}

mysqli_close($conex);
?>
