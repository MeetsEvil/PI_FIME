<?php
session_start();
// La ruta asume que db.php está en PI_FIME/config/db.php
// Usar el @ aquí es crucial si tu db.php lanza warnings o errores en la conexión
include '../../config/db.php'; 

// Establecer el encabezado para devolver una respuesta JSON
header('Content-Type: application/json');

// Función para devolver una respuesta JSON de error y cerrar la conexión
function return_error($conex, $message)
{
    // Cierre seguro: si $conex es un objeto válido, lo cerramos.
    if (isset($conex) && $conex instanceof mysqli && @$conex->ping()) {
        mysqli_close($conex);
    }
    // Aseguramos que siempre se envíe un JSON válido
    echo json_encode(['success' => false, 'message' => $message]);
    exit();
}

// --- VERIFICACIÓN INICIAL DE CONEXIÓN ---
if (!$conex) {
    return_error(null, 'Error de conexión a la base de datos. Verifique sus credenciales o el servicio de MySQL.');
}


// --- 1. VERIFICACIÓN DE ACCESO Y MÉTODO ---

if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'Administrador' && $_SESSION['rol'] !== 'Profesional')) {
    return_error($conex, 'Acceso denegado. Permisos insuficientes.');
}

// Permitimos POST y revisamos si hay datos enviados
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST)) {
    return_error($conex, 'Método de solicitud no válido o datos de formulario faltantes.');
}

// --- 2. RECUPERACIÓN Y LIMPIEZA DE DATOS ---

// Función auxiliar para obtener un valor del POST o NULL si está vacío, y escapar
function get_post_value($conex, $key)
{
    if (isset($_POST[$key]) && $_POST[$key] !== '') { 
        // Si el valor existe, lo limpiamos y lo envolvemos en comillas simples.
        return "'" . mysqli_real_escape_string($conex, $_POST[$key]) . "'";
    }
    // Devolvemos la cadena literal NULL para SQL
    return 'NULL';
}

// --- LÓGICA PARA CALCULAR EL PRÓXIMO ID (Simulación de AUTO_INCREMENT) ---
$result_max = mysqli_query($conex, "SELECT MAX(id_diagnostico) AS max_id FROM diagnosticos");

if (!$result_max) {
    return_error($conex, 'Error al obtener el ID máximo: ' . mysqli_error($conex));
}

$row_max = mysqli_fetch_assoc($result_max);
$next_id = ($row_max['max_id'] === null) ? 1 : $row_max['max_id'] + 1;
$id_diagnostico = $next_id; 
// ------------------------------------------

// Datos Requeridos (Vienen del formulario)
$beneficiario_id_str = get_post_value($conex, 'beneficiario_id'); 
$fecha_diagnostico = get_post_value($conex, 'fecha_diagnostico');
$tipo_diagnostico = get_post_value($conex, 'tipo_diagnostico');
$resultado = get_post_value($conex, 'resultado');

// Datos Opcionales
$profesional_asignado_id_str = get_post_value($conex, 'profesional_asignado_id'); 
$observaciones = get_post_value($conex, 'observaciones');
$archivo_adjunto = get_post_value($conex, 'archivo_adjunto');

// Manejo de profesional_id (INT o NULL)
if ($profesional_asignado_id_str !== 'NULL') {
    // Si hay valor, quitamos las comillas para usar el INT puro
    $profesional_sql_value = (int) trim($profesional_asignado_id_str, "'"); 
} else {
    $profesional_sql_value = 'NULL'; 
}

// --- 3. VALIDACIÓN FINAL DE CAMPOS REQUERIDOS ---
if (trim($beneficiario_id_str, "'") === '' || $fecha_diagnostico == 'NULL' || $tipo_diagnostico == 'NULL' || $resultado == 'NULL') {
    return_error($conex, 'Faltan campos obligatorios para registrar el diagnóstico.');
}


// --- 4. CONSTRUCCIÓN Y EJECUCIÓN DE LA CONSULTA SQL (INSERT) ---

$query = "INSERT INTO diagnosticos (
    id_diagnostico, beneficiario_id, profesional_id, fecha_diagnostico, tipo_diagnostico, 
    resultado, observaciones, archivo_adjunto
) VALUES (
    $id_diagnostico, $beneficiario_id_str, $profesional_sql_value, $fecha_diagnostico, $tipo_diagnostico, 
    $resultado, $observaciones, $archivo_adjunto
)";

$resultado_db = mysqli_query($conex, $query);


// --- 5. RESPUESTA AL USUARIO ---

if ($resultado_db) {
    // Éxito: devuelve JSON
    echo json_encode(['success' => true, 'message' => 'Diagnóstico registrado con éxito.']);
} else {
    // Error: devuelve JSON con el mensaje específico
    $error_msg = mysqli_error($conex);
    return_error($conex, 'Error al registrar el diagnóstico: ' . $error_msg);
}

mysqli_close($conex);