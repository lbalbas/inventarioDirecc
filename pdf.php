<?php

date_default_timezone_set('America/Caracas');
session_start();
require('./fpdm/fpdm.php');
if(empty($_SESSION['articulo'])){
	session_destroy();
	header('Location: index.php');
}
$articulo = $_SESSION['articulo'];
$serial = $articulo["serial"];
$destino = $_SESSION['destino'];
$fregreso = $_SESSION['fregreso'];
$operacion = $_SESSION['operacion'];

$fields = [
	'operacion18' => $operacion,
    'articulo11'    => $articulo['articulo'],
    'serial12' => $serial,
    'fabricante14'    => $articulo['marca'],
    'descripcionDel13'   => $articulo['descripcion'],
    'destino16' => $destino,
    'fechaDe15' => date("Y-m-d"),
    'fechaDe17' => $fregreso
];

$pdf = new FPDM('resources/registro.pdf');
$nombrePDF = "Registro ".$operacion."_(".date("H-i-s")."_".date("d-m-Y").")";
$pdf->Load($fields, true);
$pdf->Merge();
session_destroy();
$pdf->Output('I',$nombrePDF);

?>