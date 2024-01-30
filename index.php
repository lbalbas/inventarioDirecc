<?php
	header('Content-Type: text/html; charset=UTF-8');

    $conec = mysqli_connect('localhost', 'root', '','inventario');
	if(! $conec) {
		die ('No se pudo conectar con la base de datos: '. mysqli_connect_errno());
	}
	$query = "SELECT * FROM `articulos` WHERE `esta_retirado` = 0;
	";
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
	<nav id="navbar" class="is-hidden navbar is-link">
			<div class="navbar-menu">
				<div class="navbar-end">
					<a class="icon" title="Traspaso Temporal" id="trsp-t" href="#">
				  		<img src="resources/swap-vertical-outline.svg">
				  	</a>
					<a id="trsp-p" class="icon" title="Traspaso Permanente" href="#">
				  	<img src="resources/arrow-up-circle-outline.svg"/>
				  	</a>
				</div>
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
	<script language="javascript">
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
		var selectedArticles = [];

		function handleCheckboxChange(articleUnitCode) {
			var checkbox = document.querySelector("input[type=\"checkbox\"][value=\""+ articleUnitCode + "\"]");
			if (checkbox.checked) {
				// The checkbox was just checked, add the article unit code to the array
				selectedArticles.push(articleUnitCode);
			} else {
				// The checkbox was just unchecked, remove the article unit code from the array
				var index = selectedArticles.indexOf(articleUnitCode);
				if (index !== -1) {
					selectedArticles.splice(index, 1);
				}
			}
			actualizarLinksDeTraspaso();
		}

		function actualizarLinksDeTraspaso(){
			var linkTraspasoT = document.getElementById("trsp-t")
			var linkTraspasoP = document.getElementById("trsp-p")

			linkTraspasoP.href = "/transferencia.php?operacion=p&cod=" + selectedArticles.join(",")
			linkTraspasoT.href = "/transferencia.php?operacion=t&cod=" + selectedArticles.join(",")
		}

		var checkboxes = document.querySelectorAll(\'input[type="checkbox"]\');
		checkboxes.forEach(function(checkbox) {
		    checkbox.addEventListener("change", function() {
		        var isAnyChecked = Array.prototype.slice.call(checkboxes).some(function(cb) {
		            return cb.checked;
		        });

		        var navbar = document.getElementById("navbar");

		        if (isAnyChecked) {
		            navbar.classList.remove(\'is-hidden\');
		        } else {
		            navbar.classList.add(\'is-hidden\');
		        }
		    });
		});

	</script>
	'.$scriptRespaldo.'
	</body>
	</html>';

function iterarArticulos($articulos){
	$temp = "";

	for($x = 0; $x < count($articulos); $x++){
		$a = '<tr>
			 <td>
			  	 <input type="checkbox" value="'.$articulos[$x]["codigo_unidad"].'" onClick="handleCheckboxChange(\''.$articulos[$x]["codigo_unidad"].'\')"/>
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