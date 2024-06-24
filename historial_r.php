<?php
header('Content-Type: text/html; charset=UTF-8');
$conec = mysqli_connect('localhost', 'root', '', 'inventario');
if (! $conec) {
  die('No se pudo conectar con la base de datos: '. mysqli_connect_errno());
}

include "./helpers.php";

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_FILES['sql_file'])) {
    $file = $_FILES['sql_file'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];

    // Validate file type (only accept.sql files)
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
    if ($fileExtension === 'sql') {

      // Disable autocommit for manual transactions
      $conec->autocommit(FALSE);

      // Drop all tables before restoring
      dropAllTables($conec);

      // Check if the file was uploaded
      if (is_uploaded_file($fileTmpName)) {
          // Move the uploaded file to the desired directory
          move_uploaded_file($fileTmpName, 'uploads/'.$fileName);

          // Call the restoreMysqlDB function to handle the restoration process
          $response = restoreMysqlDB('uploads/'.$fileName, $conec);
          
          // Handle the response based on success or failure
          if ($response['type'] === 'success') {
              echo $response['message'];
          } else {
              echo "Error restoring database: ". $response['message'];
              $conec->rollback(); // Rollback on error
          }
      } else {
          echo "File upload failed.";
      }

      // Re-enable autocommit after restore
      $conec->autocommit(TRUE);

    } else {
      echo "Invalid file type. Only.sql files are accepted.";
    }

  }
}

// Function to drop all tables
function dropAllTables($conn) {
    // Select the database explicitly
    $dbName = "inventario"; // Replace with your actual database name
    $conn->select_db($dbName);

    // Temporarily disable foreign key checks
    $disableForeignKeyCheck = "SET FOREIGN_KEY_CHECKS=0;";
    if (!$conn->query($disableForeignKeyCheck)) {
        echo "Failed to disable foreign key checks: ". mysqli_error($conn);
        return;
    }

    $tablesResult = $conn->query("SHOW TABLES");
    if ($tablesResult && $tablesResult->num_rows > 0) {
        while ($row = $tablesResult->fetch_assoc()) {
            // Correctly extract the table name
            $tableName = isset($row['Tables_in_'. $dbName])? $row['Tables_in_'. $dbName] : '';
            if (!empty($tableName)) {
                $dropTableQuery = "DROP TABLE IF EXISTS `$tableName`;";
                if (!$conn->query($dropTableQuery)) {
                    echo "Failed to drop table: $tableName, Error: ". mysqli_error($conn);
                }
            }
        }
    } else {
        echo "No tables found.";
    }

    // Re-enable foreign key checks
    $enableForeignKeyCheck = "SET FOREIGN_KEY_CHECKS=1;";
    if (!$conn->query($enableForeignKeyCheck)) {
        echo "Failed to enable foreign key checks: ". mysqli_error($conn);
    }
}



// Include the restoreMysqlDB function here or ensure it's accessible globally
function restoreMysqlDB($filePath, $conn)
{
    $sql = '';
    $error = '';
    
    if (file_exists($filePath)) {
        $lines = file($filePath);
        
        foreach ($lines as $line) {
            if (substr($line, 0, 2) == '--' || $line == '') {
                continue;
            }
            $sql.= $line;
            
            if (substr(trim($line), - 1, 1) == ';') {
                $result = mysqli_query($conn, $sql);
                if (!$result) {
                    $error.= mysqli_error($conn). "\n";
                }
                $sql = '';
            }
        }
        
        if ($error) {
            return [
                "type" => "error",
                "message" => $error
            ];
        } else {
            return [
                "type" => "success",
                "message" => ""
            ];
        }
    }
    return null; // Return null if file does not exist
}


$query = "SELECT * FROM historial_respaldos LEFT JOIN usuarios ON historial_respaldos.realizado_por = usuarios.id";
$resultado = mysqli_query($conec, $query);
$respaldos = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
$filas = iterarRespaldos($respaldos);

include './alerta.php';

$form = $_COOKIE['login'] >= 2 ? '<form action="" method="post" class="flex flex-col" enctype="multipart/form-data">
    <input type="file" name="sql_file" required accept=".sql">
    <button class="underline hover:font-bold hover:text-red-900 hover:bg-red-400 w-52 py-2 px-1 mt-2 rounded-xl" type="submit">Restaurar base de datos</button>
</form>' : '';
echo '
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Base de Datos</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link href="./css/output.css" rel="stylesheet">
    <link href="./datatables.min.css" rel="stylesheet">
</head>
<body class="w-11/12 mx-auto">
  '.$header.'
  <h1 class="mt-12 mb-4 md:mt-28 md:mb-10 text-4xl md:text-6xl font-rubik text-sky-900 font-bold">Base de Datos</h1>
<div class="mx-auto rounded-xl bg-gray-100 shadow-4xl bg-opacity-70 flex flex-col gap-6 p-10 font-karla text-gray-400">
'.$form.'
<button class="bg-green-400 p-2 w-44 rounded-xl text-white" onclick="respaldoVentana()">Generar Respaldo</button>
</div>
<h3 class="mt-10 mb-4 text-xl font-rubik font-bold text-sky-900">Historial de Respaldos</h3>

<table id="historialRespaldo" class="font-karla display text-sky-900 bg-blue-200 bg-opacity-30 rounded-xl m-4 px-4" style="width=100%">
    <thead>
        <tr>
            <th>Fecha del Respaldo</th>
            <th>Realizado por</th>
        </tr>
    </thead>
    <tbody>
    '.$filas.'
    </tbody>
    <tfoot>
            <tr>
            <th>Fecha del Respaldo</th>
            <th>Realizado por</th>
            </tr>
        </tfoot>
</table>
  <script src="./datatables.min.js"></script>

<script language="javascript">
$(document).ready(function () {
  var table = $("#historialRespaldo").DataTable({
    language: {
      url: "./resources/lng-es.json",
    },
     "columnDefs": [ {
"targets": 0,
"className": "dt-left dt-head-left dt-body-left",
} ],
  });

});

    function respaldoVentana(){
        if(window.confirm("¿Desea crear un respaldo de la base de datos?")){
            window.open("/respaldo.php","_blank");
        }
    }
</script>
  '.$scriptRespaldo.'
</body>
</html>';

function iterarRespaldos($respaldos){
  $temp = "";
  $respaldos = array_reverse($respaldos);
  for($x = 0; $x < count($respaldos); $x++){
    $a = '
    <tr>
       <td>'.$respaldos[$x]["fecha_realizado"].'</td>
        <td>'.$respaldos[$x]["nombre_usuario"].'</td>
    </tr>';
    $temp .= $a;
  }
  return $temp;
}
?>
