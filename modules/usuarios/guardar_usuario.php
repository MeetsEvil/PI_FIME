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
$nombre = mysqli_real_escape_string($conex, trim($_POST['nombre']));
$apellido_paterno = mysqli_real_escape_string($conex, trim($_POST['apellido_paterno']));
$apellido_materno = mysqli_real_escape_string($conex, trim($_POST['apellido_materno'] ?? ''));
$correo_institucional = mysqli_real_escape_string($conex, trim($_POST['correo_institucional']));
$telefono = mysqli_real_escape_string($conex, trim($_POST['telefono'] ?? ''));
$especialidad = mysqli_real_escape_string($conex, trim($_POST['especialidad']));

$usuario = mysqli_real_escape_string($conex, trim($_POST['usuario']));
$contrasena = mysqli_real_escape_string($conex, trim($_POST['contrasena']));
$rol = mysqli_real_escape_string($conex, trim($_POST['rol']));
$estado = mysqli_real_escape_string($conex, trim($_POST['estado']));

// Permisos (checkboxes)
$permiso_beneficiario = isset($_POST['permiso_beneficiario']) ? 1 : 0;
$permiso_diagnostico = isset($_POST['permiso_diagnostico']) ? 1 : 0;
$permiso_adaptacion = isset($_POST['permiso_adaptacion']) ? 1 : 0;
$permiso_intervencion = isset($_POST['permiso_intervencion']) ? 1 : 0;

// Validaciones básicas
if (empty($nombre) || empty($apellido_paterno) || empty($correo_institucional) || 
    empty($usuario) || empty($contrasena) || empty($rol) || empty($especialidad)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos requeridos deben estar completos']);
    exit();
}

// Verificar que el usuario no exista ya
$check_usuario = "SELECT id FROM usuarios_login WHERE usuario = '$usuario'";
$result_check = mysqli_query($conex, $check_usuario);

if (mysqli_num_rows($result_check) > 0) {
    echo json_encode(['success' => false, 'message' => 'El nombre de usuario ya existe']);
    exit();
}

// Verificar que el correo no exista ya
$check_correo = "SELECT id_profesional FROM profesionales WHERE correo_institucional = '$correo_institucional'";
$result_check_correo = mysqli_query($conex, $check_correo);

if (mysqli_num_rows($result_check_correo) > 0) {
    echo json_encode(['success' => false, 'message' => 'El correo institucional ya está registrado']);
    exit();
}

// Iniciar transacción
mysqli_begin_transaction($conex);

try {
    // 1. Insertar en tabla profesionales
    $query_profesional = "INSERT INTO profesionales 
        (nombre, apellido_paterno, apellido_materno, correo_institucional, telefono, 
        especialidad, estado, usuario, contrasena, 
        permiso_beneficiario, permiso_diagnostico, permiso_adaptacion, permiso_intervencion) 
        VALUES 
        ('$nombre', '$apellido_paterno', '$apellido_materno', '$correo_institucional', '$telefono', 
        '$especialidad', '$estado', '$usuario', '$contrasena', 
        $permiso_beneficiario, $permiso_diagnostico, $permiso_adaptacion, $permiso_intervencion)";
    
    if (!mysqli_query($conex, $query_profesional)) {
        throw new Exception('Error al insertar en profesionales: ' . mysqli_error($conex));
    }

    // 2. Insertar en tabla usuarios_login
    $query_usuario = "INSERT INTO usuarios_login (usuario, contrasena, rol, estado) 
        VALUES ('$usuario', '$contrasena', '$rol', '$estado')";
    
    if (!mysqli_query($conex, $query_usuario)) {
        throw new Exception('Error al insertar en usuarios_login: ' . mysqli_error($conex));
    }

    // Confirmar transacción
    mysqli_commit($conex);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Usuario creado exitosamente'
    ]);

} catch (Exception $e) {
    // Revertir transacción en caso de error
    mysqli_rollback($conex);
    
    echo json_encode([
        'success' => false, 
        'message' => 'Error al crear el usuario: ' . $e->getMessage()
    ]);
}

mysqli_close($conex);
?>
