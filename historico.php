<?php
  header('Content-Type: text/html; charset=UTF-8');
  $conec = mysqli_connect('localhost', 'root', '','inventario');
  if(! $conec) {
    die ('No se pudo conectar con la base de datos: '. mysqli_connect_errno());
  }
  include "./helpers.php";
  $query = "SELECT * from historial_operaciones_articulos LEFT JOIN articulos ON articulos.id = historial_operaciones_articulos.id_articulo INNER JOIN historial_operaciones ON historial_operaciones_articulos.id_operacion = historial_operaciones.id LEFT JOIN divisiones ON divisiones.id = historial_operaciones.destino";
  $resultado = mysqli_query($conec, $query);
  $operaciones = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
  $filas = iterarOperaciones($operaciones);
    include './alerta.php';

echo '
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Operaciones</title>
    <link rel="stylesheet" href="css/output.css"></head>
<body>
  '.$header.'
  <h1 class="ml-6 mt-28 text-6xl font-rubik text-sky-900 font-bold">Historial de Operaciones</h1>

  <div class="w-full flex justify-center py-10">
    <input placeholder="Filtrar Histórico" id="filtroInventario" type="text" class="bg-white shadow-md w-72 border-gray-300 border-solid rounded-l-xl border-2 border-r-0 px-6 py-3" onkeyup="filtrar()">
    <select class="bg-white shadow-md rounded-r-xl border-gray-300 border-2 px-3 py-3" name="filtroCampos" id="selectFiltro">
        <option value="1">Operación</option>
        <option value="2">Fecha</option>
        <option value="3">Destino</option>
      </select>
 </div>
 <div class="grid grid-cols-1 text-sm bg-blue-100 bg-opacity-60 rounded-xl m-4 px-4">
 <div class="grid grid-cols-12 text-blue-900 rounded-xl bg-white shadow-xl py-4 my-5 font-bold tracking-wider font-rubik rounded-lg">
    <div class="col-start-2 col-end-4 text-lg">Operación</div>
    <div class="col-start-4 col-end-7 text-lg">Fecha Realizada</div>
    <div class="col-start-8 col-end-11 text-lg">Destino</div>
    <div class="col-start-11 col-start-12 text-lg"></div>
 </div>
 '.$filas.'
</div>
  <script>
 function filtrar() {
    var input, filtro, gridContainer, gridItems, i, txtValue, filtroAtrib;
    input = document.getElementById("filtroInventario");
    filtro = input.value.toUpperCase();
    gridContainer = document.querySelector(\'.grid.grid-cols-1\'); // Adjust the selector as needed
    gridItems = gridContainer.querySelectorAll(\'.grid-cols-12\'); // Adjust the selector to target grid items
    filtroAtrib = document.getElementById("selectFiltro");
    
    for (i = 1; i < gridItems.length; i++) {
        var targetColumn = gridItems[i].querySelectorAll(\'.col-span-1, .col-start-2, .col-end-3, .col-start-4, .col-end-7, .col-start-7, .col-end-9, .col-start-9, .col-end-10, .col-start-10, .col-end-12\')[filtroAtrib.value - 1]; // Adjust the index based on your column selection
        if (targetColumn) {
            txtValue = targetColumn.textContent || targetColumn.innerText;
            if (txtValue.toUpperCase().indexOf(filtro) > -1) {
                gridItems[i].style.display = "";
            } else {
                gridItems[i].style.display = "none";
            }
        }
    }
}
  </script>
  '.$scriptRespaldo.'
</body>
</html>';

function iterarOperaciones($operaciones){
 $temp = "";
 $operaciones = array_reverse($operaciones);
 for($x = 0; $x < count($operaciones); $x++){
    // Verificar el tipo de operación para determinar qué elementos mostrar
    $mostrarBmu2 = in_array($operaciones[$x]["tipo_operacion"], ["Traspaso", "Traspaso Temporal", "Retiro"]);
    $mostrarNota = $mostrarBmu2 && $operaciones[$x]["tipo_operacion"] != "Retiro";

    $a = '
           <div class="grid grid-cols-12 text-blue-950 border-blue-300 font-karla border-solid border-b-2 py-2 last:border-0 group">
    <div class="flex items-center justify-center col-start-1 col-end-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">';

    // Añadir el primer anchor si corresponde
    if ($mostrarBmu2) {
        $a .= '<a href="bmu_2.php?operacion=' . $operaciones[$x]['id_operacion'] . '">
                <img title="Generar BMU-2 de esta operación." class="w-6" src="./resources/list-circle-outline.svg">
            </a>';
    }

    // Añadir el segundo anchor si corresponde
    if ($mostrarNota) {
        $a .= '<a href="nota.php?operacion=' . $operaciones[$x]['id_operacion'] . '">
                <img title="Generar Nota de esta operación." class="w-6" src="./resources/documents-outline.svg">
            </a>';
    }

    $a .= '</div>
    <div class="flex items-center col-start-2 col-end-4">'.$operaciones[$x]["tipo_operacion"].'</div>
    <div class="flex items-center col-start-4 col-end-7">'.$operaciones[$x]["fecha_operacion"].'</div>
    <div class="flex items-center col-start-8 col-end-12">'.$operaciones[$x]["nombre_division"].'</div>
</div>';
    $temp .= $a;
 }
 return $temp;
}
?>
