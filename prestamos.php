<?php
	header('Content-Type: text/html; charset=UTF-8');
    $conec = mysqli_connect('localhost', 'root', '','inventario');
	if(! $conec) {
		die ('No se pudo conectar con la base de datos: '. mysqli_connect_errno());
	}
	include './alerta.php';
	include "./helpers.php";
	$query = "SELECT articulos.*, traspasos_temporales.*,divisiones.nombre_division  
FROM articulos  
INNER JOIN traspasos_temporales ON articulos.id = traspasos_temporales.articulo_id  
LEFT JOIN divisiones ON articulos.ubicacion = divisiones.id  
WHERE articulos.esta_retirado =  0";
	$resultado = mysqli_query($conec, $query);
	$prestamos = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
	$filas = iterarPrestamos($prestamos);
	
echo '
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Préstamos</title>
    <link rel="stylesheet" href="css/output.css">
</head>
<body class="w-11/12 mx-auto">
	'.$header.'
	<h1 class="ml-12 mt-28 text-6xl font-rubik text-sky-900 font-bold">Traspasos Temporales</h1>

	<div id="selectOperation" style="align-items: center !important; border-radius: 25px; position:fixed; padding: 5px 10px;" class="bg-blue-600 shadow-xl hidden">
					<a class="text-white h-10 w-10" title="Retorno" id="r" href="#">
				  		<svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M176 262.62L256 342l80-79.38M256 330.97V170"/><path d="M256 64C150 64 64 150 64 256s86 192 192 192 192-86 192-192S362 64 256 64z" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32"/></svg>
				  	</a>
	</div>

<div class="grid grid-cols-1 text-sm bg-blue-100 bg-opacity-60 rounded-xl m-4 px-4">
 <div class="grid grid-cols-12 text-blue-900 rounded-xl bg-white shadow-xl py-4 my-5 font-bold tracking-wider font-rubik rounded-lg">
    <div class="col-span-1 text-lg"></div>
    <div class=" col-start-2 col-end-3 text-lg">Serial</div>
    <div class="col-start-4 col-end-7 text-lg">Descripción</div>
    <div class="col-start-7 col-end-8 text-lg">Marca</div>
    <div class="col-start-8 col-end-10 text-lg">Fecha de R.</div>
    <div class="col-start-10 col-end-12 text-lg">Ubicación</div>
 </div>
 '.$filas.'
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
			var linkRetorno = document.getElementById("r")

			linkRetorno.href = "/transferencia.php?operacion=r&ids=" + selectedArticles.join(",")
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

function iterarPrestamos($prestamos){
	$temp = "";
	for($x = 0; $x < count($prestamos); $x++){
		$a = '
        	 <div class="grid grid-cols-12 text-blue-950 border-blue-300 font-karla border-solid border-b-2 py-2 last:border-0">
			    <div class="col-span-1 items-center justify-center flex">
			          <input type="checkbox" value="'.$prestamos[$x]["articulo_id"].'"  onClick="handleCheckboxChange(\''.$prestamos[$x]["articulo_id"].'\')"  class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
			    </div>
			    <div class="flex items-center col-start-2 col-end-3">'.$prestamos[$x]["serial_fabrica"].'</div>
			    <div class="flex items-center col-start-4 col-end-7">'.$prestamos[$x]["descripcion"].'</div>
			    <div class="flex items-center col-start-7 col-end-8">'.$prestamos[$x]["fabricante"].'</div>
			    <div class="flex items-center col-start-8 col-end-10">'.date("d-m-Y",strtotime($prestamos[$x]["fecha_de_retorno"])).'</div>
			    <div class="flex items-center col-start-10 col-end-12">'.$prestamos[$x]["nombre_division"].'</div>
			 </div>';
		$temp .= $a;
	}
	return $temp;
}
?>