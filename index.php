<?php
    $conec = mysqli_connect('localhost', 'root', '','inventario');
	if(! $conec) {
		die ('No se pudo conectar con la base de datos: '. mysqli_connect_errno());
	}
	$query = "SELECT * from articulos_en_inventario";
	$queryPrestamos = "SELECT * from articulos_en_inventario INNER JOIN articulos_en_prestamo ON articulos_en_inventario.serial = articulos_en_prestamo.serial";
	$resultado = mysqli_query($conec, $query);
	$articulos = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
	$filas = iterarArticulos($articulos);
	$resultado = mysqli_query($conec, $queryPrestamos);
	$prestamos = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
	$alerta = alertaPrestamo($prestamos);

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
                <a href="prestamos.php" class="navbar-item">Préstamos</a> 
            </div>
            '.$alerta.'
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
			  <option value="1">Articulo</option>
			  <option value="2">Descripción</option>
			  <option value="3">Marca</option>
			  <option value="4">Serial</option>
			</select>
		</div>
      </div>
      <div class="column"></div>
 </div>
 <div class="columns is-centered">
      <table id="tablaInventario" class="table">
            <thead>
                <tr>
                	<th></th>
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
			$a = '<tr>
				  <td>
				  	<a title="Eliminar" class="icon is-small">
				  	<img src="css/trash-outline.svg">
				  	</a>
				  	<a title="Préstamo" class="icon is-small" href="transferencia.php?serial='.$articulos[$x]["serial"].'&operacion=p">
				  	<img src="css/swap-vertical-outline.svg">
				  	</a>
				  	<a title="Retorno" class="icon is-small" href="transferencia.php?serial='.$articulos[$x]["serial"].'&operacion=r">
				  	<img src="css/arrow-down-circle-outline.svg">
				  	</a>
				  	<a title="Asignación" class="icon is-small" href="transferencia.php?serial='.$articulos[$x]["serial"].'&operacion=a">
				  	<img src="css/arrow-up-circle-outline.svg">
				  	</a>	  	
				  	</td>
				  <td>'.$articulos[$x]["articulo"].'
				  </td><td>'.$articulos[$x]["descripcion"].'
				  </td><td>'.$articulos[$x]["marca"].'
				  </td><td>'.$articulos[$x]["serial"].'
				  </td><td>'.$articulos[$x]["ubicacion"].'
				  </td></tr>';
			$temp .= $a;
		}
		return $temp;
	}
  function alertaPrestamo($prestamos){
  	$prestamosCercanos = "";
  	for($x = 0; $x < count($prestamos); $x++){
  		$interval = date_diff(date_create(),date_create($prestamos[$x]["fecha_de_retorno"]));
	    if($interval->format('%R%a') <= 5){
	    	if($interval->format('%R%a') == 0){
	    		$prestamosCercanos .= "<a class='navbar-item'>Hoy es la fecha límite del préstamo de ".$prestamos[$x]['articulo']." a ".$prestamos[$x]['destino']."</a>";
			}else if($interval->format('%R%a') < 0){
				$prestamosCercanos .= "<a class='navbar-item is-danger'>La fecha límite del préstamo de ".$prestamos[$x]['articulo']." a ".$prestamos[$x]['destino']." ha pasado</a>";
			}else{
				$prestamosCercanos .= "<a class='navbar-item'>La fecha límite del préstamo de ".$prestamos[$x]['articulo']." a ".$prestamos[$x]['destino']." es en ".$interval->format('%a')." días</a>";
			}
		}
  	}
  	if ($prestamosCercanos != ""){
  		$temp = '
  			<div class="navbar-end">
            	<div class="navbar-item has-dropdown is-hoverable">
                	<div class="navbar-item">
	                	<div>
	                		<img class="icon is-large" src="css/alert.svg">
	                	</div>
                	</div>
						
				    <div class="navbar-dropdown is-right">
				 	'.$prestamosCercanos.'
				    </div>
			    </div>
			</div>
  		';
  		$prestamosCercanos = $temp;
  	}
  	return $prestamosCercanos;
  }

?>