<?php

/***************************
  Sample using a PHP array
****************************/

require('fpdf/fpdm.php');

$fields = array(
    'Contact Name'    => 'My name',
    'Others' => 'My address',
    'Beverage 2'    => 'My city',
    'Beverage 1'   => 'My phone number'
);

$pdf = new FPDM('resources/comprobante1.pdf');
$pdf->Load($fields, false); // second parameter: false if field values are in ISO-8859-1, true if UTF-8
$pdf->Merge();
$pdf->Output();
?>