<?php
	header('Content-Type: text/html; charset=UTF-8');
	include "./helpers.php";

	if (isset($_COOKIE['excel'])) {
    // Si la cookie está presente, usa JavaScript para redirigir al usuario
    echo '<script language="javascript">
            window.open("/excel.php");
        </script>';
    // Asegúrate de limpiar la cookie después de usarla
    setcookie('excel', '', time() -  3600, '/');
}
    $conec = mysqli_connect('localhost', 'root', '','inventario');
	if(! $conec) {
		die ('No se pudo conectar con la base de datos: '. mysqli_connect_errno());
	}
	$query = "SELECT articulos.*, divisiones.nombre_division FROM `articulos`
LEFT JOIN `divisiones` ON `articulos`.`ubicacion` = `divisiones`.`id`
WHERE `articulos`.`esta_retirado` = 0";

	$resultado = mysqli_query($conec, $query);
	$articulos = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
	$filas = iterarArticulos($articulos, $conec);
	include "./alerta.php";
	
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
	'.$header.'
	<div id="selectOperation"box-shadow: 0px 3px 15px 5px rgba(0, 0, 0, 0.4); style="align-items: center !important; top: 100; left: 20; border-radius: 25px; position:fixed; padding: 5px 10px;" class="flex is-hidden has-background-link">
					<a class="has-text-white icon is-medium" title="Traspaso Temporal" id="trsp-t" href="#">
				  		<svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M464 208L352 96 240 208M352 113.13V416M48 304l112 112 112-112M160 398V96"/></svg>
				  	</a>
					<a id="trsp-p" class="has-text-white icon is-medium" title="Traspaso Permanente" href="#">
		<svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M176 249.38L256 170l80 79.38M256 181.03V342"/><path d="M448 256c0-106-86-192-192-192S64 150 64 256s86 192 192 192 192-86 192-192z" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32"/></svg>
						  	</a>
						  	<a class="has-text-white icon is-medium" title="Retiro" id="retiro" href="#">
						  	<svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512"><path d="M112 112l20 320c.95 18.49 14.4 32 32 32h184c17.67 0 30.87-13.51 32-32l20-320" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><path stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="32" d="M80 112h352"/><path d="M192 112V72h0a23.93 23.93 0 0124-24h80a23.93 23.93 0 0124 24h0v40M256 176v224M184 176l8 224M328 176l-8 224" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/></svg>
						  	</a>
	</div>
	<div class="column is-fullwidth"></div>
	<div id="columns"class="columns is-fullwidth is-mobile is-centered">
		<div class="column is-hidden-touch"></div>
		<div class="column is-three-quarters-mobile is-5 control">
			<input placeholder="Filtrar Inventario" id="filtroInventario" type="text" class="input" onkeyup="filtrar()">
		</div>
		<div class="column is-one-quarters-mobile is-3 control">
			<div class="select">
				<select name="filtroCampos" id="selectFiltro">
				<option value="1">Codificación</option>
				<option value="2">Descripción</option>
				<option value="3">Fabricante</option>
				<option value="5">Ubicación</option>
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
			var linkRetiro = document.getElementById("retiro")
			linkTraspasoP.href = "/transferencia.php?operacion=p&ids=" + selectedArticles.join(",")
			linkTraspasoT.href = "/transferencia.php?operacion=t&ids=" + selectedArticles.join(",")
			linkRetiro.href = "/transferencia.php?operacion=ret&ids=" + selectedArticles.join(",")
		}

		var checkboxes = document.querySelectorAll(\'input[type="checkbox"]\');
		checkboxes.forEach(function(checkbox) {
		    checkbox.addEventListener("change", function() {
		        var isAnyChecked = Array.prototype.slice.call(checkboxes).some(function(cb) {
		            return cb.checked;
		        });

		        var selectOperation = document.getElementById("selectOperation");

		        if (isAnyChecked) {
		            selectOperation.classList.remove(\'is-hidden\');
		        } else {
		            selectOperation.classList.add(\'is-hidden\');
		        }
		    });
		});

	</script>
	'.$scriptRespaldo.'
	</body>
	</html>';

function iterarArticulos($articulos,$conec){
    $temp = "";

    for($x =  0; $x < count($articulos); $x++){
        // Verifica si el artículo está en préstamo o traspaso
        $enPrestamo = estaEnPrestamo($articulos[$x], $conec);

        // Si el artículo está en préstamo o traspaso, deshabilita la casilla de verificación y agrega un asterisco
        $deshabilitado = ($enPrestamo) ? 'disabled' : '';
        $asterisco = ($enPrestamo) ? '**' : '';
        $a = '<tr>
            <td>
                <input type="checkbox" value="'.$articulos[$x]["id"].'" '.$deshabilitado.' onClick="handleCheckboxChange(\''.$articulos[$x]["id"].'\')"/>
                '.$asterisco.'
            </td>
            <td>'.$articulos[$x]["codigo_unidad"].'
            </td><td>'.$articulos[$x]["descripcion"].'
            </td><td>'.$articulos[$x]["fabricante"].'
            </td><td>'.$articulos[$x]["monto_valor"].'
            </td><td>'.$articulos[$x]["nombre_division"].'
            </td></tr>';
        $temp .= $a;
    }
    return $temp;
}
function estaEnPrestamo($articulo, $conec) {
	$query = "SELECT * FROM traspasos_temporales WHERE articulo_id = " .$articulo['id'];
	$exec = mysqli_query($conec, $query);
	$resultado = mysqli_fetch_all($exec, MYSQLI_ASSOC);
	return !empty($resultado);
};
	mysqli_close($conec);
?>