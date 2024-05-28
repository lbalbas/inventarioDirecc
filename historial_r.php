<?php
  header('Content-Type: text/html; charset=UTF-8');
  $conec = mysqli_connect('localhost', 'root', '','inventario');
  if(! $conec) {
    die ('No se pudo conectar con la base de datos: '. mysqli_connect_errno());
  }
  include "./helpers.php";
  $query = "SELECT * from historial_respaldos LEFT JOIN usuarios ON historial_respaldos.realizado_por = usuarios.id";
  $resultado = mysqli_query($conec, $query);
  $respaldos = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
  $filas = iterarRespaldos($respaldos);
  
  include './alerta.php';

echo '
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Operaciones</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link href="./css/output.css" rel="stylesheet">
    <link href="./datatables.min.css" rel="stylesheet">
</head>
<body class="w-11/12 mx-auto">
  '.$header.'
  <h1 class="ml-6 mt-28 mb-10 text-6xl font-rubik text-sky-900 font-bold">Historial de Respaldos</h1>
<table id="historialRespaldo" class="font-karla display text-sky-900 bg-blue-200 bg-opacity-30 rounded-xl m-4 px-4">
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