<?php
session_start();
if (!isset($_SESSION['usuarioingresando'])) {
    header("Location: ../auth/index.php");
    exit();
}

include '../../config/db.php';

// Configuración para descarga de Excel
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="beneficiarios_completo_'.date('Y-m-d').'.xls"');
header('Pragma: no-cache');
header('Expires: 0');

// Consulta con TODOS los campos
$query = "SELECT 
            id_beneficiario AS 'ID',
            matricula AS 'Matrícula',
            nombre AS 'Nombre',
            apellido_paterno AS 'Apellido Paterno',
            apellido_materno AS 'Apellido Materno',
            curp AS 'CURP',
            fecha_nacimiento AS 'Fecha de Nacimiento',
            TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS 'Edad',
            genero AS 'Género',
            telefono AS 'Teléfono',
            correo_institucional AS 'Correo Institucional',
            carrera AS 'Carrera',
            semestre AS 'Semestre',
            plan_de_estudio AS 'Plan de Estudio',
            estatus_academico AS 'Estatus Académico',
            tipo_discapacidad AS 'Tipo de Discapacidad',
            diagnostico AS 'Diagnóstico',
            adaptaciones AS 'Adaptaciones',
            recursos_asignados AS 'Recursos Asignados',
            profesional_asignado AS 'ID Profesional',
            fecha_ingreso AS 'Fecha de Ingreso',
            estado_inicial AS 'Estado Inicial',
            observaciones_iniciales AS 'Observaciones Iniciales',
            nombre_emergencia AS 'Contacto de Emergencia',
            telefono_emergencia AS 'Teléfono de Emergencia',
            parentesco_emergencia AS 'Parentesco'
        FROM beneficiarios
        ORDER BY id_beneficiario";

$resultado = mysqli_query($conex, $query);

if (!$resultado) {
    die("Error en la consulta: " . mysqli_error($conex));
}

// Crear tabla HTML para Excel
echo '<table border="1">';
echo '<thead><tr>';

// Encabezados
$fields = mysqli_fetch_fields($resultado);
foreach ($fields as $field) {
    echo '<th style="background-color: #4CAF50; color: white; font-weight: bold;">' . utf8_decode($field->name) . '</th>';
}
echo '</tr></thead><tbody>';

// Datos
while ($row = mysqli_fetch_assoc($resultado)) {
    echo '<tr>';
    foreach ($row as $value) {
        echo '<td>' . utf8_decode($value ?? '') . '</td>';
    }
    echo '</tr>';
}

echo '</tbody></table>';

mysqli_close($conex);
?>