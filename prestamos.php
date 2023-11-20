<?php
	header('Content-Type: text/html; charset=UTF-8');
    $conec = mysqli_connect('localhost', 'root', '','inventario');
	if(! $conec) {
		die ('No se pudo conectar con la base de datos: '. mysqli_connect_errno());
	}
	include './alerta.php';
	$query = "SELECT * from articulos_en_inventario INNER JOIN articulos_en_prestamo ON articulos_en_inventario.serial = articulos_en_prestamo.serial";
	$resultado = mysqli_query($conec, $query);
	$prestamos = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
	$filas = iterarPrestamos($prestamos);
	
echo '
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Préstamos</title>
    <link rel="stylesheet" href="css/estilo2.css">
    <link rel="stylesheet" href="css/bulma.css">
</head>
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
  	<div id="columns"class="columns is-fullwidth is-mobile is-centered">
      <div class="column is-hidden-touch"></div>
      <div class="column is-three-quarters-mobile is-5 control">
        <input placeholder="Filtrar Préstamos"id="filtroInventario" type="text" class="input" onkeyup="filtrar()">
      </div>
      <div class="column is-one-quarters-mobile is-3 control">
      	<div class="select">
	        <select name="filtroCampos" id="selectFiltro">
			  <option value="1">Articulo</option>
			  <option value="2">Descripción</option>
			  <option value="3">Marca</option>
			  <option value="4">Serial</option>
			  <option value="5">Propiedad</option>
			  <option value="6">Fecha de Regreso</option>
			</select>
		</div>
      </div>
      <div class="column is-hidden-touch"></div>
 </div>
    <table id="tablaInventario" class="table">
            <thead>
                <tr>
                	<th></th>
                    <th>Articulo</th>
                    <th>Descripción</th>
                    <th>Marca</th>
                    <th>Serial</th>
                    <th>En Propiedad</th>
                    <th>Fecha de Regreso</th>
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
  '.$scriptRespaldo.'
</body>
</html>';

function iterarPrestamos($prestamos){
	$temp = "";
	for($x = 0; $x < count($prestamos); $x++){
		$a = '<tr>
			  <td>
			  	<a class="icon is-small" href="transferencia.php?serial='.$prestamos[$x]["serial"].'&operacion=r">
			  	<img src="resources/arrow-down-circle-outline.svg">
			  	</a>	  	
			  		<a class="icon is-small" href="transferencia.php?serial='.$prestamos[$x]["serial"].'&operacion=e">
			  	<img src="resources/arrow-forward-circle-outline.svg">
			  	</a>	  	
			  	</td>
			  <td>'.$prestamos[$x]["articulo"].'
			  </td><td>'.$prestamos[$x]["descripcion"].'
			  </td><td>'.$prestamos[$x]["marca"].'
			  </td><td>'.$prestamos[$x]["serial"].'
			  </td><td>'.$prestamos[$x]["destino"].'
			  </td><td>'.date("d-m-Y",strtotime($prestamos[$x]["fecha_de_retorno"])).'
			  </td></tr>';
		$temp .= $a;
	}
	return $temp;
}
?>