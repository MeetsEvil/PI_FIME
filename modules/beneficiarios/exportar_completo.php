<?php
require '../../vendor/autoload.php';
include '../../config/db.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Consulta
$query = "SELECT * FROM beneficiarios ORDER BY id_beneficiario";
$resultado = mysqli_query($conex, $query);

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
        'startColor' => ['rgb' => '239358'], // Verde similar a tu CSS
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
        'startColor' => ['rgb' => 'F9F9F9'], // gris claro
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
header('Content-Disposition: attachment;filename="beneficiarios_completo_'.date('Y-m-d').'.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
