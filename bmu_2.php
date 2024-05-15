<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Cargar el archivo .ods
$spreadsheet = IOFactory::load('./resources/bm-2.xls');

// Obtener la hoja activa para trabajar con ella
$sheet = $spreadsheet->getActiveSheet();





if(isset($_GET['operacion'])){
  $conec = mysqli_connect('localhost', 'root', '','inventario');
  $resultadoOperacion = mysqli_query($conec, "SELECT historial_operaciones.*, divisiones.nombre_division, divisiones.direccion, divisiones.municipio from historial_operaciones LEFT JOIN divisiones ON divisiones.id = historial_operaciones.destino WHERE historial_operaciones.id = '".$_GET['operacion']."'");
  $resultadoArticulos = mysqli_query($conec, "SELECT * from historial_operaciones_articulos LEFT JOIN articulos ON articulos.id = historial_operaciones_articulos.id_articulo LEFT JOIN nro_identificacion_articulo ON nro_identificacion_articulo.id_articulo = articulos.id WHERE historial_operaciones_articulos.id_operacion = '".$_GET['operacion']."'");
  $operacionAssoc = mysqli_fetch_all($resultadoOperacion, MYSQLI_ASSOC)[0];
  $articulos = mysqli_fetch_all($resultadoArticulos, MYSQLI_ASSOC);
  $destino = $operacionAssoc["nombre_division"];
  $direccion = $operacionAssoc["direccion"];
  $municipio = $operacionAssoc["municipio"];
  $operacion = $operacionAssoc["tipo_operacion"];
  $observaciones = $operacionAssoc["observaciones"];
}else{
    exit;
}
$fecha = date('d-m-Y');
// Crear un archivo ZIP temporal
$zip = new ZipArchive();
$zipName = "BMU-2 ({$fecha}).zip";
if ($zip->open($zipName, ZipArchive::CREATE) !== TRUE) {
    exit("No se puede abrir el archivo ZIP <$zipName>\n");
}

$maxArticulosPorArchivo = 16;
$articulosPorPagina = 16;
$totalArticulos = count($articulos);
$numeroDeArchivos = ceil($totalArticulos / $articulosPorPagina);

// Crear y llenar archivos de Excel
for ($i = 0; $i < $numeroDeArchivos; $i++) {
    $spreadsheet = IOFactory::load('./resources/bm-2.xls');
    $sheet = $spreadsheet->getActiveSheet();

    // Configurar datos generales
    $sheet->setCellValue('G1', "Codigo: " . "42");
    $sheet->setCellValue('G2', "Concepto: " . strtoupper($operacion));
    $sheet->setCellValue('G4', "Fecha: " . date('d/m/Y'));
    $sheet->setCellValue('F8', "Destino: " . $destino);
    $sheet->setCellValue('F9', "Dirección: " . $direccion);
    $sheet->setCellValue('F10', "Municipio: " . $municipio);
    $sheet->setCellValue('E14', $observaciones);
    if($operacion == "Retiro"){
        $sheet->setCellValue('C14', "x");
    }else{
        $sheet->setCellValue('C13', "x");
    }
    // Configurar número de página
    $paginaActual = $i + 1;
    $sheet->setCellValue('G5', "Página Nº {$paginaActual}/{$numeroDeArchivos}");

    // Llenar artículos
    $startRow = 18;
    $articulosParaEsteArchivo = array_slice($articulos, $i * $articulosPorPagina, $articulosPorPagina);
    foreach ($articulosParaEsteArchivo as $index => $articulo) {
        $row = $startRow + $index;
        $sheet->setCellValue('D' . $row, !empty($articulo['n_identificacion']) ? $articulo['n_identificacion'] : $articulo['serial_fabrica']);
        $sheet->setCellValue('E' . $row, $articulo['descripcion']);
        $sheet->setCellValue('G' . $row, $articulo['monto_valor']);
        $sheet->setCellValue('H' . $row, "00.00");
    }

    $tempFile = tempnam(sys_get_temp_dir(), 'xlsx');
    $writer = new Xlsx($spreadsheet);
    $writer->save($tempFile);

    // Agregar el archivo de Excel al archivo ZIP
    $n = $i + 1;
    $zip->addFile($tempFile, "BMU-2_({$n} de {$numeroDeArchivos})_{$fecha}.xlsx");
}

// Cerrar el archivo ZIP
$zip->close();

// Enviar el archivo ZIP al usuario para descarga
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . basename($zipName) . '"');
header('Content-Length: ' . filesize($zipName));
readfile($zipName);

// Eliminar el archivo ZIP temporal
unlink($zipName);

session_destroy();
exit;
?>