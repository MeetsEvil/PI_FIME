<?php
session_start();
// La ruta asume que db.php está en PI_FIME/config/db.php
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
// SE RESTAURÓ LA CONSULTA QUE FALTABA
$result_max = mysqli_query($conex, "SELECT MAX(id_adaptacion) AS max_id FROM adaptaciones"); 

if (!$result_max) {
    return_error($conex, 'Error al obtener el ID máximo: ' . mysqli_error($conex));
}

$row_max = mysqli_fetch_assoc($result_max);
$next_id = ($row_max['max_id'] === null) ? 1 : $row_max['max_id'] + 1;
$id_adaptacion = $next_id; 
// ------------------------------------------

// Datos Requeridos (Vienen del formulario)
$beneficiario_id_str = get_post_value($conex, 'beneficiario_id'); 
$fecha_implementacion = get_post_value($conex, 'fecha_implementacion');
$tipo_adaptacion = get_post_value($conex, 'tipo_adaptacion');
$descripcion = get_post_value($conex, 'descripcion');
$estado = get_post_value($conex, 'estado');

// AÑADIDO: Capturar el NÚMERO DE ADAPTACIÓN
$numero_adaptacion = get_post_value($conex, 'numero_adaptacion');

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
if ($beneficiario_id_str === 'NULL' || $fecha_implementacion === 'NULL' || 
    $tipo_adaptacion === 'NULL' || $estado === 'NULL' || $descripcion === 'NULL' || $numero_adaptacion === 'NULL') {
    
    // Debug: mostrar qué campos faltan
    $missing_fields = [];
    if ($beneficiario_id_str === 'NULL') $missing_fields[] = 'beneficiario_id';
    if ($fecha_implementacion === 'NULL') $missing_fields[] = 'fecha_implementacion';
    if ($tipo_adaptacion === 'NULL') $missing_fields[] = 'tipo_adaptacion';
    if ($estado === 'NULL') $missing_fields[] = 'estado';
    if ($descripcion === 'NULL') $missing_fields[] = 'descripcion';
    if ($numero_adaptacion === 'NULL') $missing_fields[] = 'numero_adaptacion'; // Validación del nuevo campo
    
    return_error($conex, 'Faltan campos obligatorios: ' . implode(', ', $missing_fields));
}


// --- 4. CONSTRUCCIÓN Y EJECUCIÓN DE LA CONSULTA SQL (INSERT) ---

$query = "INSERT INTO adaptaciones (
    id_adaptacion, beneficiario_id, profesional_id, fecha_implementacion, tipo_adaptacion, estado,
    descripcion, observaciones, numero_adaptacion 
    -- CORREGIDO: SE AÑADE la columna numero_adaptacion
) VALUES (
    $id_adaptacion, $beneficiario_id_str, $profesional_sql_value, $fecha_implementacion, $tipo_adaptacion, $estado,
    $descripcion, $observaciones, $numero_adaptacion 
    -- CORREGIDO: SE AÑADE el valor del número de adaptación
)";

$resultado_db = mysqli_query($conex, $query);

// --- DEBUG TEMPORAL (Eliminar después) ---
error_log("POST Data: " . print_r($_POST, true));
error_log("Query: " . $query);
// --- FIN DEBUG ---

// --- 5. RESPUESTA AL USUARIO ---

if ($resultado_db) {
    // Éxito: devuelve JSON
    echo json_encode(['success' => true, 'message' => 'Adaptación registrada con éxito.']);
} else {
    // Error: devuelve JSON con el mensaje específico
    $error_msg = mysqli_error($conex);
    return_error($conex, 'Error al registrar la adaptación: ' . $error_msg);
}

mysqli_close($conex);