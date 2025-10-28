<?php
require '../../vendor/autoload.php';
include '../../config/db.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Obtener ID de beneficiario opcional desde GET
$beneficiario_id = isset($_GET['beneficiario_id']) ? intval($_GET['beneficiario_id']) : 0;

// Consulta base
$query = "
SELECT a.id_adaptacion,
       CONCAT(b.nombre, ' ', b.apellido_paterno, ' ', b.apellido_materno) AS beneficiario,
       CONCAT(COALESCE(p.nombre,''), ' ', COALESCE(p.apellido_paterno,''), ' ', COALESCE(p.apellido_materno,'')) AS profesional,
       a.fecha_implementacion,
       a.estado,
       a.tipo_adaptacion,
       a.numero_adaptacion,
       a.descripcion,
       a.observaciones
FROM adaptaciones a
JOIN beneficiarios b ON a.beneficiario_id = b.id_beneficiario
LEFT JOIN profesionales p ON a.profesional_id = p.id_profesional
";

// Filtro por beneficiario si aplica
if($beneficiario_id > 0){
    $query .= " WHERE a.beneficiario_id = $beneficiario_id";
}

// Orden
$query .= " ORDER BY a.id_adaptacion ASC";

$resultado = mysqli_query($conex, $query);

if(!$resultado){
    die("Error en la consulta: " . mysqli_error($conex));
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Estilo encabezado
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
        'size' => 11,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '239358'], // Verde
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_LEFT,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
];

// Estilo filas alternadas
$evenRowStyle = [
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'F9F9F9'],
    ],
];

// Encabezados
$fields = mysqli_fetch_fields($resultado);
$col = 'A';
foreach ($fields as $field) {
    $sheet->setCellValue($col.'1', $field->name);
    $sheet->getStyle($col.'1')->applyFromArray($headerStyle);
    $col++;
}

// Datos
$rowNum = 2;
while ($row = mysqli_fetch_assoc($resultado)) {
    $col = 'A';
    foreach ($row as $value) {
        $sheet->setCellValue($col.$rowNum, $value);
        $col++;
    }
    // Estilo alternado de filas
    if ($rowNum % 2 == 0) {
        $sheet->getStyle("A$rowNum:".chr(64 + count($fields))."$rowNum")->applyFromArray($evenRowStyle);
    }
    $rowNum++;
}

// Ajustar ancho de columnas automáticamente
foreach (range('A', chr(64 + count($fields))) as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Enviar al navegador como .xlsx
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="adaptaciones_completo_'.date('Y-m-d').'.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
