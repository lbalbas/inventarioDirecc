<?php
  header('Content-Type: text/html; charset=UTF-8');
  $conec = mysqli_connect('localhost', 'root', '','inventario');
  if(! $conec) {
    die ('No se pudo conectar con la base de datos: '. mysqli_connect_errno());
  }
  include './alerta.php';
  $query = "SELECT * from historial_operaciones";
  $resultado = mysqli_query($conec, $query);
  $operaciones = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
  $filas = iterarOperaciones($operaciones);


echo '
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Operaciones</title>
    <link rel="stylesheet" href="css/bulma.css">
    <link rel="stylesheet" href="css/estilo1.css"></head>
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
  <nav class="navbar is-link">
            <div class="navbar-brand">
          <a role="button" class="navbar-burger burger" onclick="document.querySelector(`.navbar-menu`).classList.toggle(`is-active`);" aria-label="menu" aria-expanded="false">
          <span aria-hidden="true"></span>
          <span aria-hidden="true"></span>
          <span aria-hidden="true"></span>
        </a>
        </div>
        <div class="navbar-menu">
            <div class="navbar-start">
                <a href="index.php" class="navbar-item">Inicio</a>
                <a href="insertar.php" class="navbar-item">Registro</a>
                <a href="transferencia.php" class="navbar-item">Transferencias</a>
                <a href="historico.php" class="navbar-item">Histórico</a>
                <a href="prestamos.php" class="navbar-item">Préstamos</a>
                '.$opcionesUsuario.'
            </div>
            '.$alerta.'
        </div>
  </nav>
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
        <option value="1">Serial</option>
        <option value="2">Fecha</option>
        <option value="3">Ubicación</option>
      </select>
    </div>
      </div>
      <div class="column is-hidden-touch"></div>
 </div>
 <table id="tablaHistorial" class="table is-fullwidth">
            <thead>
                <tr>
                    <th>Tipo de Operación</th>
                    <th>Serial del Artículo</th>
                    <th>Fecha de Operación</th>
                    <th>Ubicación</th>
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
        </td><td>'.$operaciones[$x]["serial"].'
        </td><td>'.$operaciones[$x]["fecha_operacion"].'
        </td><td>'.$operaciones[$x]["destino"].'
        </td></tr>';
    $temp .= $a;
  }
  return $temp;
}
?>