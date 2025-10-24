<?php
session_start();
include '../../config/db.php'; // Incluye la conexión a la base de datos

// Establecer el encabezado para devolver una respuesta JSON
header('Content-Type: application/json');

// Función para devolver una respuesta JSON de error y cerrar la conexión
function return_error($conex, $message) {
    if (isset($conex) && $conex instanceof mysqli) { // <-- CAMBIO AQUÍ: Usamos instanceof mysqli
        mysqli_close($conex);
    }
    echo json_encode(['success' => false, 'message' => $message]);
    exit();
}

// --- 1. VERIFICACIÓN DE ACCESO Y MÉTODO ---

// Solo se permite la edición a Administradores (por seguridad)
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['Administrador', 'Profesional'])) {
    return_error($conex, 'Acceso denegado. Solo Administradores o Profesionales pueden actualizar beneficiarios.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return_error($conex, 'Método de solicitud no válido.');
}

// --- 2. RECUPERACIÓN Y LIMPIEZA DE DATOS ---

// Función auxiliar para obtener un valor del POST o NULL si está vacío, y escapar
function get_post_value($conex, $key) {
    // Si el valor existe y NO está vacío
    if (isset($_POST[$key]) && $_POST[$key] !== '') {
        return "'" . mysqli_real_escape_string($conex, $_POST[$key]) . "'";
    }
    // Si no existe o está vacío, devolvemos la palabra NULL (sin comillas) para SQL
    return 'NULL';
}

// ID del beneficiario (CAMPO REQUERIDO)
$id_beneficiario_raw = trim(get_post_value($conex, 'id_beneficiario'), "'");
if (!is_numeric($id_beneficiario_raw) || empty($id_beneficiario_raw)) {
    return_error($conex, 'ID del beneficiario no proporcionado o inválido.');
}
$id_beneficiario = $id_beneficiario_raw; // Usamos el valor INT sin comillas para el WHERE

// Datos Personales
$nombre = get_post_value($conex, 'nombre');
$apellido_paterno = get_post_value($conex, 'apellido_paterno');
$apellido_materno = get_post_value($conex, 'apellido_materno');
$curp = get_post_value($conex, 'curp');
$fecha_nacimiento = get_post_value($conex, 'fecha_nacimiento');
$genero = get_post_value($conex, 'genero');
$telefono = get_post_value($conex, 'telefono');
$correo_institucional = get_post_value($conex, 'correo_institucional');

// Datos Académicos
$matricula = get_post_value($conex, 'matricula');
$carrera = get_post_value($conex, 'carrera');
$plan_de_estudio = get_post_value($conex, 'plan_de_estudio');
$semestre = get_post_value($conex, 'semestre');
$estatus_academico = get_post_value($conex, 'estatus_academico');

// Datos de Inclusión/Diagnóstico
$tipo_discapacidad = get_post_value($conex, 'tipo_discapacidad');
$diagnostico = get_post_value($conex, 'diagnostico');
$adaptaciones = get_post_value($conex, 'adaptaciones');
$recursos_asignados = get_post_value($conex, 'recursos_asignados');

// El profesional asignado
$profesional_asignado = get_post_value($conex, 'profesional_asignado_id'); 
if ($profesional_asignado !== 'NULL') {
    $profesional_asignado = trim($profesional_asignado, "'"); // Lo preparamos como INT
}

$fecha_ingreso = get_post_value($conex, 'fecha_ingreso');
$estado_inicial = get_post_value($conex, 'estado_inicial');
$observaciones_iniciales = get_post_value($conex, 'observaciones_iniciales');

$nombre_emergencia = get_post_value($conex, 'nombre_emergencia');
$telefono_emergencia = get_post_value($conex, 'telefono_emergencia');
$parentesco_emergencia = get_post_value($conex, 'parentesco_emergencia');


// --- 3. VALIDACIÓN DE CAMPOS REQUERIDOS (la validación JS ya lo hace, pero el backend siempre debe validar) ---
if ($nombre == 'NULL' || $apellido_paterno == 'NULL' || $curp == 'NULL' || $fecha_nacimiento == 'NULL' || $genero == 'NULL' || $matricula == 'NULL' || $carrera == 'NULL' || $semestre == 'NULL' || $estatus_academico == 'NULL' || $profesional_asignado == 'NULL') {
    return_error($conex, 'Faltan campos obligatorios para la actualización.');
}


// --- 4. CONSTRUCCIÓN Y EJECUCIÓN DE LA CONSULTA SQL (UPDATE) ---

$query = "UPDATE beneficiarios SET
    nombre = $nombre,
    apellido_paterno = $apellido_paterno,
    apellido_materno = $apellido_materno,
    curp = $curp,
    fecha_nacimiento = $fecha_nacimiento,
    genero = $genero,
    telefono = $telefono,
    correo_institucional = $correo_institucional,
    matricula = $matricula,
    carrera = $carrera,
    plan_de_estudio = $plan_de_estudio,
    semestre = $semestre,
    estatus_academico = $estatus_academico,
    tipo_discapacidad = $tipo_discapacidad,
    diagnostico = $diagnostico,
    adaptaciones = $adaptaciones,
    recursos_asignados = $recursos_asignados,
    profesional_asignado = $profesional_asignado,
    fecha_ingreso = $fecha_ingreso,
    estado_inicial = $estado_inicial,
    observaciones_iniciales = $observaciones_iniciales,
    nombre_emergencia = $nombre_emergencia,
    telefono_emergencia = $telefono_emergencia,
    parentesco_emergencia = $parentesco_emergencia
WHERE id_beneficiario = $id_beneficiario";

$resultado = mysqli_query($conex, $query);


// --- 5. RESPUESTA AL USUARIO ---

if ($resultado) {
    // Si la consulta se ejecutó sin error SQL, verificamos si hubo cambios.
    if (mysqli_affected_rows($conex) > 0) {
        // Éxito: hubo cambios
        echo json_encode(['success' => true, 'message' => 'Beneficiario actualizado con éxito.']);
    } else {
        // Éxito: no hubo cambios (la consulta pasó pero no actualizó filas)
        echo json_encode(['success' => true, 'message' => 'Actualización completada. No se detectaron cambios en los datos.']);
    }
} else {
    // Error SQL: si la consulta falló por errores de sintaxis, conexión, etc.
    $error_msg = mysqli_error($conex);
    
    // Verificación de errores comunes (claves UNIQUE)
    if (strpos($error_msg, 'Duplicate entry') !== false) {
        if (strpos($error_msg, 'matricula') !== false) {
            return_error($conex, 'Error: La matrícula ingresada ya existe en otro registro.');
        } elseif (strpos($error_msg, 'curp') !== false) {
            return_error($conex, 'Error: La CURP ingresada ya existe en otro registro.');
        }
    }
    
    // Error general
    return_error($conex, 'Error al actualizar el beneficiario. Por favor, revisa los datos o contacta a soporte: ' . $error_msg);
}

mysqli_close($conex);
?>
