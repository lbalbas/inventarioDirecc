<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xls; // Changed from Xlsx to Xls

// Cargar el archivo.ods
$spreadsheet = IOFactory::load('./resources/bm-1.xls');

// Obtener la hoja activa para trabajar con ella
$sheet = $spreadsheet->getActiveSheet();

if(isset($_GET['ids'])){
  $idArray = $_GET["ids"];
  $conec = mysqli_connect('localhost', 'root', '','inventario');
  $resultadoArticulos = mysqli_query($conec, "SELECT articulos.*, modelo_articulo.nombre_modelo, nro_identificacion_articulo.n_identificacion, divisiones.nombre_division from articulos LEFT JOIN divisiones ON divisiones.id = articulos.ubicacion LEFT JOIN modelo_articulo ON modelo_articulo.id_articulo = articulos.id LEFT JOIN nro_identificacion_articulo ON nro_identificacion_articulo.id_articulo = articulos.id WHERE articulos.id IN ({$idArray})");
  $articulos = mysqli_fetch_all($resultadoArticulos, MYSQLI_ASSOC);
}else{
    exit;
}
$fecha = date('d-m-Y');
// Crear un archivo ZIP temporal
$zip = new ZipArchive();
$zipName = "BMU-1 ({$fecha}).zip";
if ($zip->open($zipName, ZipArchive::CREATE)!== TRUE) {
    exit("No se puede abrir el archivo ZIP <$zipName>\n");
}

$maxArticulosPorArchivo = 20;
$articulosPorPagina = 20;
$totalArticulos = count($articulos);
$numeroDeArchivos = ceil($totalArticulos / $articulosPorPagina);

// Crear y llenar archivos de Excel
for ($i = 0; $i < $numeroDeArchivos; $i++) {
    $spreadsheet = IOFactory::load('./resources/bm-1.xls');
    $sheet = $spreadsheet->getActiveSheet();

    // Configurar datos generales
    $sheet->setCellValue('I9', "Fecha de Inventario: ". date('d/m/Y'));

    // Configurar número de página
    $paginaActual = $i + 1;
    $sheet->setCellValue('L6', "Página Nº {$paginaActual}/{$numeroDeArchivos}");

    // Llenar artículos
    $startRow = 12;
    $articulosParaEsteArchivo = array_slice($articulos, $i * $articulosPorPagina, $articulosPorPagina);
    foreach ($articulosParaEsteArchivo as $index => $articulo) {
        $row = $startRow + $index;
        $sheet->setCellValue('D'. $row,!empty($articulo['n_identificacion'])? $articulo['n_identificacion'] : $articulo['serial_fabrica']);
        $sheet->setCellValue('G'. $row, $articulo['descripcion']);
        $sheet->setCellValue('F'. $row, "1");
        $sheet->setCellValue('L'. $row, floatval($articulo['monto_valor']));
        $sheet->setCellValue('M'. $row, floatval($articulo['monto_valor']));
    }

    $tempFile = tempnam(sys_get_temp_dir(), 'xls'); // Adjusted to use 'xls'
    $writer = new Xls($spreadsheet); // Changed from Xlsx to Xls
    $writer->save($tempFile);

    // Agregar el archivo de Excel al archivo ZIP
    $n = $i + 1;
    $zip->addFile($tempFile, "BMU-1_({$n} de {$numeroDeArchivos})_{$fecha}.xls"); // Adjusted to use '.xls'
}

// Cerrar el archivo ZIP
$zip->close();

// Enviar el archivo ZIP al usuario para descarga
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="'. basename($zipName). '"');
header('Content-Length: '. filesize($zipName));
readfile($zipName);

// Eliminar el archivo ZIP temporal
unlink($zipName);

session_destroy();
exit;
?>
