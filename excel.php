<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Cargar el archivo .ods
$spreadsheet = IOFactory::load('./resources/incorporacion-desincorporacion.xlsx');

// Obtener la hoja activa para trabajar con ella
$sheet = $spreadsheet->getActiveSheet();

session_start();

$articulos = $_SESSION['articulos'];
$operacion = $_SESSION['operacion'];
$observaciones = $_SESSION['observaciones'];
$destino = $_SESSION["destino"];

// Modificar las celdas según los requerimientos
$sheet->setCellValue('G2', "Concepto: " . strtoupper($operacion));
$sheet->setCellValue('G2', "Concepto: " . strtoupper($operacion));
$sheet->setCellValue('G3', "Fecha: " . date('d/m/Y'));
$sheet->setCellValue('F5', "Destino: " . $destino);
$sheet->setCellValue('E12', $observaciones);

if($operacion !== 'Retiro'){
   $sheet->setCellValue('C11', 'x'); 
}else{
    $sheet->setCellValue('C12', 'x');
    $sheet->setCellValue('F5', "Dirección: ZONA INDUSTRIAL LA SABANITA");
}


// Iterar sobre la array $articulos para colocar los datos en las celdas correspondientes
$startRow =  17; // Comenzar desde la fila  17
foreach ($articulos as $index => $articulo) {
    $row = $startRow + $index;
    $sheet->setCellValue('D' . $row, $articulo['codigo_unidad']);
    $sheet->setCellValue('E' . $row, $articulo['descripcion']);
    $sheet->setCellValue('G' . $row, $articulo['monto_valor']);
    $sheet->setCellValue('H' . $row,  "0.00");
}

// Crear un escritor para guardar el archivo .ods
$writer = new Xlsx($spreadsheet);
// Después de la descarga del archivo, establece una cookie para indicar que se debe redirigir

// Opcionalmente, puedes enviar el archivo al navegador para que el usuario lo descargue:
header('Content-Type: application/vnd.oasis.opendocument.spreadsheet');
header('Content-Disposition: attachment; filename="operacion.xlsx"');
$writer->save('php://output');
session_destroy();
exit;
?>