<?php
	header('Content-Type: text/html; charset=UTF-8');

    $conec = mysqli_connect('localhost', 'root', '','inventario');
	if(! $conec) {
		die ('No se pudo conectar con la base de datos: '. mysqli_connect_errno());
	}
	$query = "SELECT * from articulos_en_inventario";
	$resultado = mysqli_query($conec, $query);
	$articulos = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
	$filas = iterarArticulos($articulos);
	include './alerta.php';
	mysqli_close($conec);

	echo '
	<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Inventario General</title>
		<link rel="stylesheet" href="css/estilo.css">
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
			</div>  </nav>
	<div class="column is-fullwidth"></div>
	<div id="columns"class="columns is-fullwidth is-mobile is-centered">
		<div class="column is-hidden-touch"></div>
		<div class="column is-three-quarters-mobile is-5 control">
			<input placeholder="Filtrar Inventario" id="filtroInventario" type="text" class="input" onkeyup="filtrar()">
		</div>
		<div class="column is-one-quarters-mobile is-3 control">
			<div class="select">
				<select name="filtroCampos" id="selectFiltro">
				<option value="1">Artículo</option>
				<option value="2">Descripción</option>
				<option value="3">Marca</option>
				<option value="4">Serial</option>
				</select>
			</div>
		</div>
		<div class="column is-hidden-touch"></div>
	</div>
	<div class="table-wrapper">
		<table id="tablaInventario" class="table is-fullwidth is-striped">
				<thead>
					<tr>
						<th></th>
						<th>Codificación</th>
						<th>Descripción</th>
						<th>Fabricante</th>
						<th>Valor</th>
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
	'.$scriptRespaldo.'
	</body>
	</html>';

	function iterarArticulos($articulos){
		$temp = "";

		for($x = 0; $x < count($articulos); $x++){
			$a = '<tr>
				  <td>
				  	 <a title="Préstamo" class="icon is-small" href="transferencia.php?id='.$articulos[$x]["id"].'&operacion=p">
				
				  	<img src="resources/swap-vertical-outline.svg">
				  	</a> 	
				  	<a title="Asignación" class="icon is-small" href="transferencia.php?id='.$articulos[$x]["id"].'&operacion=a">
				  	<img src="resources/arrow-up-circle-outline.svg">
				  	</a>	
				  	<a title="Préstamo" class="icon is-small" href="modificar.php?id='.$articulos[$x]["id"].'">
				
				  	<img src="resources/settings-outline.svg">
				  	</a>  	
				  	</td>
				  <td>'.$articulos[$x]["codigo_unidad"].'
				  </td><td>'.$articulos[$x]["descripcion"].'
				  </td><td>'.$articulos[$x]["fabricante"].'
				  </td><td>'.$articulos[$x]["monto_valor"].'
				  </td><td>'.$articulos[$x]["ubicacion"].'
				  </td></tr>';
			$temp .= $a;
		}
		return $temp;
	}
?>