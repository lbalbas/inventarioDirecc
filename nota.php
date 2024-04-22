<?php
  $conec = mysqli_connect('localhost', 'root', '','inventario');
if(isset($_GET['operacion'])){
$resultadoOperacion = mysqli_query($conec, "SELECT historial_operaciones.*, divisiones.nombre_division from historial_operaciones LEFT JOIN divisiones ON divisiones.id = historial_operaciones.destino WHERE historial_operaciones.id = '".$_GET['operacion']."'");
  $resultadoArticulos = mysqli_query($conec, "SELECT * from historial_operaciones_articulos LEFT JOIN articulos ON articulos.id = historial_operaciones_articulos.id_articulo WHERE historial_operaciones_articulos.id_operacion = '".$_GET['operacion']."'");
  $operacion = mysqli_fetch_all($resultadoOperacion, MYSQLI_ASSOC)[0];
  $articulos = mysqli_fetch_all($resultadoArticulos, MYSQLI_ASSOC);
}else{
    exit;
}

$input        = './resources/nota.docx';
$output       = './resources/modified.docx';
$replacements = [
    '{s1}' => isset($articulos[0]["serial_fabrica"]) ? $articulos[0]["serial_fabrica"] : "",
    '{s2}' => isset($articulos[1]["serial_fabrica"]) ? $articulos[1]["serial_fabrica"] : "",
    '{s3}' => isset($articulos[2]["serial_fabrica"]) ? $articulos[2]["serial_fabrica"] : "",
    '{s4}' => isset($articulos[3]["serial_fabrica"]) ? $articulos[3]["serial_fabrica"] : "",
    '{s5}' => isset($articulos[4]["serial_fabrica"]) ? $articulos[4]["serial_fabrica"] : "",
    '{s6}' => isset($articulos[5]["serial_fabrica"]) ? $articulos[5]["serial_fabrica"] : "",
    '{s7}' => isset($articulos[6]["serial_fabrica"]) ? $articulos[6]["serial_fabrica"] : "",
    '{s8}' => isset($articulos[7]["serial_fabrica"]) ? $articulos[7]["serial_fabrica"] : "",
    '{s9}' => isset($articulos[8]["serial_fabrica"]) ? $articulos[8]["serial_fabrica"] : "",
    '{s10}' => isset($articulos[9]["serial_fabrica"]) ? $articulos[9]["serial_fabrica"] : "",
    '{desc-1}' => isset($articulos[0]["descripcion"]) ? $articulos[0]["descripcion"] : "",
    '{desc-2}' => isset($articulos[1]["descripcion"]) ? $articulos[1]["descripcion"] : "",
    '{desc-3}' => isset($articulos[2]["descripcion"]) ? $articulos[2]["descripcion"] : "",
    '{desc-4}' => isset($articulos[3]["descripcion"]) ? $articulos[3]["descripcion"] : "",
    '{desc-5}' => isset($articulos[4]["descripcion"]) ? $articulos[4]["descripcion"] : "",
    '{desc-6}' => isset($articulos[5]["descripcion"]) ? $articulos[5]["descripcion"] : "",
    '{desc-7}' => isset($articulos[6]["descripcion"]) ? $articulos[6]["descripcion"] : "",
    '{desc-8}' => isset($articulos[7]["descripcion"]) ? $articulos[7]["descripcion"] : "",
    '{desc-9}' => isset($articulos[8]["descripcion"]) ? $articulos[8]["descripcion"] : "",
    '{desc-10}' => isset($articulos[9]["descripcion"]) ? $articulos[9]["descripcion"] : "",
    '{c1}' => isset($articulos[0]) ? "1" : "",
    '{c2}' => isset($articulos[1]) ? "1" : "",
    '{c3}' => isset($articulos[2]) ? "1" : "",
    '{c4}' => isset($articulos[3]) ? "1" : "",
    '{c5}' => isset($articulos[4]) ? "1" : "",
    '{c6}' => isset($articulos[5]) ? "1" : "",
    '{c7}' => isset($articulos[6]) ? "1" : "",
    '{c8}' => isset($articulos[7]) ? "1" : "",
    '{c9}' => isset($articulos[8]) ? "1" : "",
    '{c10}' => isset($articulos[9]) ? "1" : "",
    '{n}' => $operacion["id"],
    '{d}' => date('d'),
    '{m}' => date('m'),
    '{Y}' => date('Y'),
    '{{destino}}' => $operacion["nombre_division"],
];

$successful = searchReplaceWordDocument($input, $output, $replacements);

$currentDate = date('d-m-Y');
$newFileName = "Nota_de_Salida_($currentDate).docx";
rename($output, './resources/' . $newFileName);

// Send the file for download
if ($successful) {
    // Set headers for file download
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($newFileName) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize('./resources/' . $newFileName));
    // Read the file content and send it to the user
    readfile('./resources/' . $newFileName);
    
    // Delete the file after sending it to the user
    ignore_user_abort(true); // Ensure the script continues to run even if the user aborts the download
    if (connection_aborted()) {
        unlink('./resources/' . $newFileName); // Delete the file if the user cancels the download
    } else {
        unlink('./resources/' . $newFileName); // Delete the file after sending it to the user
    }
    exit;
} else {
    // Handle the failure case
    echo 'No se pudo crear la nota de salida.';
}

/**
 * Edit a Word 2007 and newer .docx file.
 * Utilizes the zip extension http://php.net/manual/en/book.zip.php
 * to access the document.xml file that holds the markup language for
 * contents and formatting of a Word document.
 *
 * In this example we're replacing some token strings.  Using
 * the Office Open XML standard ( https://en.wikipedia.org/wiki/Office_Open_XML )
 * you can add, modify, or remove content or structure of the document.
 *
 * @param string $input
 * @param string $output
 * @param array  $replacements
 *
 * @return bool
 */
function searchReplaceWordDocument(string $input, string $output, array $replacements): bool
{
    if (copy($input, $output)) {

        // Create the Object.
        $zip = new ZipArchive();

        // Open the Microsoft Word .docx file as if it were a zip file... because it is.
        if ($zip->open($output, ZipArchive::CREATE) !== true) {
            return false;
        }

        // Fetch the document.xml file from the word subdirectory in the archive.
        $xml = $zip->getFromName('word/document.xml');

        // Replace
        $xml = str_replace(array_keys($replacements), array_values($replacements), $xml);

        // Write back to the document and close the object
        if (false === $zip->addFromString('word/document.xml', $xml)) {
            return false;
        }
        $zip->close();

        return true;
    }

    return false;
}