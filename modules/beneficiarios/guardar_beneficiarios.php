<?php
session_start();
// La ruta asume que db.php está en PI_FIME/config/db.php
include '../../config/db.php'; 

// Establecer el encabezado para devolver una respuesta JSON
header('Content-Type: application/json');

// Función para devolver una respuesta JSON de error y cerrar la conexión
function return_error($conex, $message) {
    if (isset($conex)) {
        mysqli_close($conex);
    }
    // Aseguramos que la respuesta sea un JSON válido
    echo json_encode(['success' => false, 'message' => $message]);
    exit();
}

// --- 1. VERIFICACIÓN DE ACCESO Y MÉTODO ---

// Solo se permite el registro a Administradores (por seguridad)
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador') {
    return_error($conex, 'Acceso denegado. Solo Administradores pueden registrar beneficiarios.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return_error($conex, 'Método de solicitud no válido.');
}

// --- 2. RECUPERACIÓN Y LIMPIEZA DE DATOS ---

// Función auxiliar para obtener un valor del POST o NULL si está vacío, y escapar
function get_post_value($conex, $key) {
    // Si el valor existe y NO está vacío (ni es 'null' como string, aunque no debería ser), lo limpiamos y lo envolvemos en comillas simples.
    if (isset($_POST[$key]) && $_POST[$key] !== '') {
        return "'" . mysqli_real_escape_string($conex, $_POST[$key]) . "'";
    }
    // Si no existe o está vacío, devolvemos la palabra NULL (sin comillas) para SQL
    return 'NULL';
}

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
$semestre = get_post_value($conex, 'semestre');
$estatus_academico = get_post_value($conex, 'estatus_academico');

// Datos de Inclusión/Diagnóstico
$tipo_discapacidad = get_post_value($conex, 'tipo_discapacidad');
$diagnostico = get_post_value($conex, 'diagnostico');
$adaptaciones = get_post_value($conex, 'adaptaciones');
$recursos_asignados = get_post_value($conex, 'recursos_asignados');

// El profesional asignado viene como ID del campo oculto (profesional_asignado_id)
$profesional_asignado = get_post_value($conex, 'profesional_asignado_id'); 
if ($profesional_asignado !== 'NULL') {
    // Si no es NULL, quitamos las comillas ya que es un INT en la base de datos
    $profesional_asignado = trim($profesional_asignado, "'");
}


$fecha_ingreso = get_post_value($conex, 'fecha_ingreso');
$estado_inicial = get_post_value($conex, 'estado_inicial');
$observaciones_iniciales = get_post_value($conex, 'observaciones_iniciales');


// --- 3. VALIDACIÓN DE CAMPOS REQUERIDOS ---

// Se verifica que los campos obligatorios NO sean 'NULL' (vacíos)
if ($nombre == 'NULL' || $apellido_paterno == 'NULL' || $curp == 'NULL' || $fecha_nacimiento == 'NULL' || $genero == 'NULL' || $matricula == 'NULL' || $carrera == 'NULL' || $semestre == 'NULL' || $estatus_academico == 'NULL') {
    return_error($conex, 'Faltan campos obligatorios. Por favor, complete la información marcada como requerida.');
}


// --- 4. CONSTRUCCIÓN Y EJECUCIÓN DE LA CONSULTA SQL ---

$query = "INSERT INTO beneficiarios (
    nombre, apellido_paterno, apellido_materno, curp, fecha_nacimiento, genero, telefono, correo_institucional, 
    matricula, carrera, semestre, estatus_academico, tipo_discapacidad, diagnostico, adaptaciones, 
    recursos_asignados, profesional_asignado, fecha_ingreso, estado_inicial, observaciones_iniciales
) VALUES (
    $nombre, $apellido_paterno, $apellido_materno, $curp, $fecha_nacimiento, $genero, $telefono, $correo_institucional, 
    $matricula, $carrera, $semestre, $estatus_academico, $tipo_discapacidad, $diagnostico, $adaptaciones, 
    $recursos_asignados, $profesional_asignado, $fecha_ingreso, $estado_inicial, $observaciones_iniciales
)";

$resultado = mysqli_query($conex, $query);


// --- 5. RESPUESTA AL USUARIO ---

if ($resultado) {
    // Éxito: devuelve JSON
    echo json_encode(['success' => true, 'message' => 'Beneficiario registrado con éxito.']);
} else {
    // Error: devuelve JSON con el mensaje específico
    $error_msg = mysqli_error($conex);
    
    // Verificación de errores comunes (claves UNIQUE)
    if (strpos($error_msg, 'Duplicate entry') !== false) {
        if (strpos($error_msg, 'matricula') !== false) {
            return_error($conex, 'Error: La matrícula ingresada ya existe en la base de datos.');
        } elseif (strpos($error_msg, 'curp') !== false) {
            return_error($conex, 'Error: La CURP ingresada ya existe en la base de datos.');
        }
    }
    
    // Si es otro tipo de error, devuelve un mensaje genérico
    return_error($conex, 'Error al registrar el beneficiario. Por favor, revisa los datos.');
}

// Cierre de la conexión (aunque return_error ya lo hace, lo mantenemos por si acaso)
mysqli_close($conex);
?>
