<?php
    $conec = mysqli_connect('localhost', 'root', '','inventario');
	if(! $conec) {
		die ('No se pudo conectar con la base de datos: '. mysqli_connect_errno());
	}
	$query = "SELECT * from articulos_en_inventario";
	$resultado = mysqli_query($conec, $query);
	$articulos = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
	$filas = iterarArticulos($articulos);


echo '
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario</title>
    <link rel="stylesheet" href="css/bulma.css">
</head>
<body>
  <nav class="navbar is-dark">
        <div class="navbar-menu">
            <div class="navbar-start">
                <a href="index.php" class="navbar-item">Inicio</a>
                <a href="insertar.php" class="navbar-item">Insertar</a>
                <a href="transferencia.php" class="navbar-item">Transferir</a>
                <a href="historico.php" class="navbar-item">Historico</a>
            </div>
        </div>
  </nav>
  <div class="column"></div>
  <div class="columns is-centered">
      <div class="column"></div>
      <div class="column is-5 control">
        <input id="filtroInventario" type="text" class="input" onkeyup="filtrar()">
      </div>
      <div class="column is-3 control">
      	<div class="select">
	        <select name="filtroCampos" id="selectFiltro">
			  <option value="0">Articulo</option>
			  <option value="1">Descripción</option>
			  <option value="2">Marca</option>
			  <option value="3">Serial</option>
			</select>
		</div>
      </div>
      <div class="column"></div>
 </div>
 <div class="columns is-centered">
      <table id="tablaInventario" class="table">
            <thead>
                <tr>
                    <th>Articulo</th>
                    <th>Descripción</th>
                    <th>Marca</th>
                    <th>Serial</th>
                    <th>Ubicación</th>
                </tr>
            </thead>
            <tbody>
                '.$filas.'
            </tbody>
      </table>
  </div>
  <script>
	function filtrar() {
	  var input, filtro, tabla, tr, td, i, txtValor, filtroAtrib;
	  input = document.getElementById("filtroInventario");
	  filtro = input.value.toUpperCase();
	  tabla = document.getElementById("tablaInventario");
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
</body>
</html>';

function iterarArticulos($articulos){
	$temp = "";
	for($x = 0; $x < count($articulos); $x++){
		$a = '<tr><td>'.$articulos[$x]["articulo"].'
			  </td><td>'.$articulos[$x]["descripcion"].'
			  </td><td>'.$articulos[$x]["marca"].'
			  </td><td>'.$articulos[$x]["serial"].'
			  </td><td>'.$articulos[$x]["ubicacion"].'
			  </td></tr>';
		$temp .= $a;
	}
	return $temp;
}
?>