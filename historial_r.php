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
</head>
<body class="w-11/12 mx-auto">
  '.$header.'
  <h1 class="ml-6 mt-28 text-6xl font-rubik text-sky-900 font-bold">Historial de Respaldos</h1>
  <div class="grid grid-cols-1 text-sm bg-blue-100 bg-opacity-60 rounded-xl my-4 px-4">
   <div class="grid grid-cols-12 text-blue-900 rounded-xl bg-white shadow-xl py-4 my-5 font-bold tracking-wider font-rubik rounded-lg">
      <div class="col-start-2 col-end-7 text-lg">Fecha del Respaldo</div>
      <div class="col-start-7 col-end-12 text-lg">Realizado por</div>
   </div>
   '.$filas.'
  </div>
  '.$scriptRespaldo.'
</body>
</html>';

function iterarRespaldos($respaldos){
  $temp = "";
  $respaldos = array_reverse($respaldos);
  for($x = 0; $x < count($respaldos); $x++){
    $a = '
    <div class="grid grid-cols-12 text-blue-950 last:border-0 border-blue-200 font-karla border-solid border-b-2 py-2">
       <div class="col-start-2 col-end-7">'.$respaldos[$x]["fecha_realizado"].'</div>
        <div class="col-start-7 col-end-12">'.$respaldos[$x]["nombre_usuario"].'</div>
    </div>';
    $temp .= $a;
  }
  return $temp;
}
?>