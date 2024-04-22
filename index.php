<?php
	header('Content-Type: text/html; charset=UTF-8');
	include "./helpers.php";

	if (isset($_COOKIE['excel'])) {
    // Si la cookie está presente, usa JavaScript para redirigir al usuario
    echo '<script language="javascript">
            window.open("/bmu_2.php/?operacion='.$_COOKIE['excel'].'");
        </script>';
    // Asegúrate de limpiar la cookie después de usarla
    setcookie('excel', '', time() -  3600, '/');
}
		if (isset($_COOKIE['nota'])) {
    // Si la cookie está presente, usa JavaScript para redirigir al usuario
    echo '<script language="javascript">
            window.open("/nota.php/?operacion='.$_COOKIE['nota'].'");
        </script>';
    // Asegúrate de limpiar la cookie después de usarla
    setcookie('nota', '', time() -  3600, '/');
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
		<link href="./css/output.css" rel="stylesheet">
	</head>
	<body>
	'.$header.'
		<div id="selectOperation"box-shadow: 0px 3px 15px 5px rgba(0, 0, 0, 0.4); style="align-items: center !important; top: 100; left: 20; border-radius: 25px; position:fixed; padding: 5px 10px;" class="bg-blue-600 hidden">
					<a class="text-white h-10 w-10" title="Traspaso Temporal" id="trsp-t" href="#">
				  		<svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M464 208L352 96 240 208M352 113.13V416M48 304l112 112 112-112M160 398V96"/></svg>
				  	</a>
					<a id="trsp-p" class="text-white h-10 w-10" title="Traspaso Permanente" href="#">
		<svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M176 249.38L256 170l80 79.38M256 181.03V342"/><path d="M448 256c0-106-86-192-192-192S64 150 64 256s86 192 192 192 192-86 192-192z" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32"/></svg>
						  	</a>
						  	<a class="text-white h-10 w-10" title="Retiro" id="retiro" href="#">
						  	<svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512"><path d="M112 112l20 320c.95 18.49 14.4 32 32 32h184c17.67 0 30.87-13.51 32-32l20-320" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><path stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="32" d="M80 112h352"/><path d="M192 112V72h0a23.93 23.93 0 0124-24h80a23.93 23.93 0 0124 24h0v40M256 176v224M184 176l8 224M328 176l-8 224" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/></svg>
						  	</a>
				<a id="bmu-1" class="text-white h-10 w-10" title="Generar BMU-1" href="#">
		<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M336,264.13V436c0,24.3-19.05,44-42.95,44H107C83.05,480,64,460.3,64,436V172a44.26,44.26,0,0,1,44-44h94.12a24.55,24.55,0,0,1,17.49,7.36l109.15,111A25.4,25.4,0,0,1,336,264.13Z" style="fill:none;stroke:#ffff;stroke-linejoin:round;stroke-width:32px"/><path d="M200,128V236a28.34,28.34,0,0,0,28,28H336" style="fill:none;stroke:#ffff;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/><path d="M176,128V76a44.26,44.26,0,0,1,44-44h94a24.83,24.83,0,0,1,17.61,7.36l109.15,111A25.09,25.09,0,0,1,448,168V340c0,24.3-19.05,44-42.95,44H344" style="fill:none;stroke:#ffff;stroke-linejoin:round;stroke-width:32px"/><path d="M312,32V140a28.34,28.34,0,0,0,28,28H448" style="fill:none;stroke:#ffff;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/></svg>
						  	</a>
	</div>

	<h1 class="ml-12 mt-28 text-6xl font-rubik text-sky-900 font-bold">Inventario General</h1>

	<div class="w-full flex justify-center py-10">
			<input placeholder="Filtrar Inventario" id="filtroInventario" type="text" class="bg-white shadow-md w-72 border-gray-300 border-solid rounded-l-xl border-2 border-r-0 px-6 py-3" onkeyup="filtrar()">
			<select class="bg-white shadow-md rounded-r-xl border-gray-300 border-2 pl-3 py-3" name="filtroCampos" id="selectFiltro">
				<option value="2">Serial</option>
				<option value="3">Descripción</option>
				<option value="4">Marca</option>
				<option value="6">Ubicación</option>
			</select>
		<div class="column is-hidden-touch"></div>
	</div>

<div class="grid grid-cols-1 text-sm bg-blue-100 bg-opacity-60 rounded-xl m-4 px-4">
 <div class="grid grid-cols-12 text-blue-900 rounded-xl bg-white shadow-xl py-4 my-5 font-bold tracking-wider font-rubik rounded-lg">
    <div class="col-span-1 text-lg"></div>
    <div class=" col-start-2 col-end-3 text-lg">Serial</div>
    <div class="col-start-4 col-end-7 text-lg">Descripción</div>
    <div class="col-start-7 col-end-9 text-lg">Marca</div>
    <div class="col-start-9 col-end-10 text-lg">Valor</div>
    <div class="col-start-10 col-end-12 text-lg">Ubicación</div>
 </div>
 '.$filas.'
</div>
	<script language="javascript">
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
			var linkBMU = document.getElementById("bmu-1")
			var linkRetiro = document.getElementById("retiro")
			linkTraspasoP.href = "/transferencia.php?operacion=p&ids=" + selectedArticles.join(",")
			linkTraspasoT.href = "/transferencia.php?operacion=t&ids=" + selectedArticles.join(",")
			linkRetiro.href = "/transferencia.php?operacion=ret&ids=" + selectedArticles.join(",")
			linkBMU.href = "/bmu_1.php?ids=" + selectedArticles.join(",");
		}

		var checkboxes = document.querySelectorAll(\'input[type="checkbox"]\');
		checkboxes.forEach(function(checkbox) {
		    checkbox.addEventListener("change", function() {
		        var isAnyChecked = Array.prototype.slice.call(checkboxes).some(function(cb) {
		            return cb.checked;
		        });

		        var selectOperation = document.getElementById("selectOperation");

		        if (isAnyChecked) {
		            selectOperation.classList.remove(\'hidden\');
		            selectOperation.classList.add(\'flex\');
		        } else {
		        	selectOperation.classList.remove(\'flex\');
		            selectOperation.classList.add(\'hidden\');
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
        $a = '
        	 <div class="grid grid-cols-12 text-blue-950 border-blue-300 font-karla border-solid border-b-2 py-2 last:border-0">
			    <div class="col-span-1 items-center justify-center flex">
			          <input type="checkbox" value="'.$articulos[$x]["id"].'" '.$deshabilitado.' onClick="handleCheckboxChange(\''.$articulos[$x]["id"].'\')" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
			          '.$asterisco.'
			    </div>
			    <div class="flex items-center col-start-2 col-end-3">'.$articulos[$x]["serial_fabrica"].'</div>
			    <div class="flex items-center col-start-4 col-end-7">'.$articulos[$x]["descripcion"].'</div>
			    <div class="flex items-center col-start-7 col-end-9">'.$articulos[$x]["fabricante"].'</div>
			    <div class="flex items-center col-start-9 col-end-10">'.$articulos[$x]["monto_valor"].'</div>
			    <div class="flex items-center col-start-10 col-end-12">'.$articulos[$x]["nombre_division"].'</div>
			 </div>';
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