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


// Asumiendo que $articulos ya contiene todos los artículos obtenidos de la base de datos
$articulosPorPagina = 10;
$totalArticulos = count($articulos);
$numeroDeArchivos = ceil($totalArticulos / $articulosPorPagina);

// Crear un archivo ZIP temporal
$zip = new ZipArchive();
$fecha = date('d-m-Y');
$zipName = "Notas de Salida_{$fecha}.zip";
if ($zip->open($zipName, ZipArchive::CREATE) !== TRUE) {
    exit("No se puede abrir el archivo ZIP <$zipName>\n");
}

$input        = './resources/nota.docx';
$output       = './resources/modified.docx';

for ($i = 0; $i < $numeroDeArchivos; $i++) {
    $articulosParaEsteArchivo = array_slice($articulos, $i * $articulosPorPagina, $articulosPorPagina);
$replacements = [];

// Construir las claves de reemplazo dinámicamente con valores por defecto de cadena vacía
for ($j = 0; $j < 10; $j++) { // Ajusta el 10 al número máximo de artículos que esperas manejar
    $replacements['{s' . ($j + 1) . '}'] = "";
    $replacements['{desc-' . ($j + 1) . '}'] = "";
    $replacements['{c' . ($j + 1) . '}'] = "";
}

// Sobrescribir los valores por defecto con los datos reales de los artículos si están disponibles
foreach ($articulosParaEsteArchivo as $index => $articulo) {
    $replacements['{s' . ($index + 1) . '}'] = !empty($articulo['n_identificacion']) ? $articulo['n_identificacion'] : $articulo['serial_fabrica'];
    $replacements['{desc-' . ($index + 1) . '}'] = isset($articulo['descripcion']) ? $articulo['descripcion'] : "";
    $replacements['{c' . ($index + 1) . '}'] = isset($articulo) ? "1" : "";
}

// Añadir las claves de reemplazo fijas que no cambian entre archivos
$replacements['{n}'] = $operacion["id"];
$replacements['{d}'] = date('d');
$replacements['{m}'] = date('m');
$replacements['{Y}'] = date('Y');
$replacements['{{destino}}'] = $operacion["nombre_division"];

    // Generar el archivo de Word para este grupo de artículos
    $input = '.\resources\nota.docx';
    $output = '.\resources\modified_'.$i.'.docx';
    $successful = searchReplaceWordDocument($input, $output, $replacements);

    if ($successful) {
        // Agregar el archivo de Word al archivo ZIP
        $pdfOutputPath = "./resources/pdf/modified_{$i}.pdf"; // Change the extension to.pdf

        // Convert.docx to PDF using LibreOffice
$command = '"C:\Program Files\LibreOffice\program\soffice.exe" --headless --convert-to pdf:writer_pdf_Export --outdir "C:\wampp\www\resources\pdf" "'.$output.'"';

// Execute the command and capture the output
$cmdResult = shell_exec($command);

// Check if the command returned an error
if (strpos($cmdResult, 'ERROR')!== false) {
    die("{$cmdResult}");
}
        // Now, add the PDF file to the ZIP archive
        $zip->addFile($pdfOutputPath, "Nota_de_Salida_(".$fecha.")_".$i.".pdf");
        //$zip->addFile($output, "Nota_de_Salida_($fecha)_{$i}.docx");
    }
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

// Eliminar los archivos de Word generados
foreach ($articulos as $index => $articulo) {
    $output = "./resources/modified_{$index}.docx";
    $pdfOutput = "./resources/pdf/modified_{$index}.pdf";
    if (file_exists($output)) {
        unlink($output);
    }
    if (file_exists($pdfOutput)) {
        unlink($pdfOutput);
    }
}

exit;
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