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
    <link rel="stylesheet" href="css/bulma.css">
    <link rel="stylesheet" href="css/estilo.css"></head>
<body>
  <div id="logo" class="columns is-gapless">
    <div id="logo" class="column is-one-fifth">
      <figure class="column image is-3by1">
        <img src="./resources/goblogo.jpg">
      </figure>
    </div>
    <div class="column is-three-fifths"></div>
    <div id="logo" class="column is-one-fifth">
      <figure class="column image is-3by1">
        <img src="./resources/dirlogo.jpg">
      </figure>
    </div>
  </div>
  '.$header.'
  <div class="column is-fullwidth"></div>
  <div id="columns" class="columns is-mobile is-centered">
      <div class="column is-hidden-touch"></div>
      <div class="column is-three-quarters-mobile is-5 control">
        <input placeholder="Filtrar Histórico" id="filtroInventario" type="text" class="input" onkeyup="filtrar()">
      </div>
      <div class="column is-one-quarters-mobile is-3 control">
        <div class="select">
          <select name="filtroCampos" id="selectFiltro">
        <option value="0">Operación</option>
        <option value="1">N° de Identificación</option>
        <option value="2">Fecha</option>
        <option value="3">Destino</option>
      </select>
    </div>
      </div>
      <div class="column is-hidden-touch"></div>
 </div>
 <table id="tablaHistorial" class="table is-fullwidth">
            <thead>
                <tr>
                    <th>Tipo de Operación</th>
                    <th>N° de Identificación</th>
                    <th>Fecha de Operación</th>
                    <th>Destino</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                '.$filas.'
            </tbody>
  </table>
  <script>
  function filtrar() {
    var input, filtro, tabla, tr, td, i, txtValor, filtroAtrib;
    input = document.getElementById("filtroInventario");
    filtro = input.value.toUpperCase();
    tabla = document.getElementById("tablaHistorial");
    tr = tabla.getElementsByTagName("tr");
    filtroAtrib = document.getElementById("selectFiltro");
    
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[filtroAtrib.value];
      if (td) {
        txtValue = td.textContent || td.innerText;
        if (txtValue.toUpperCase().indexOf(filtro) > -1) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
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
    $a = '<tr><td>'.$operaciones[$x]["tipo_operacion"].'
        </td><td>'.$operaciones[$x]["serial_fabrica"].'
        </td><td>'.$operaciones[$x]["fecha_operacion"].'
        </td><td>'.$operaciones[$x]["nombre_division"].'
        </td>
        <td><a href="excel.php?operacion=' . $operaciones[$x]['id_operacion'] . '"><img title="Generar Excel de esta operación." class="excel-icon nav-icon" src="./resources/documents-outline.svg"></a></td></tr>';
    $temp .= $a;
  }
  return $temp;
}
?>