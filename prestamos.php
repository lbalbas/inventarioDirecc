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
    <link rel="stylesheet" href="css/estilo2.css">
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
					<a class="has-text-white icon is-medium" title="Retorno" id="r" href="#">
				  		<svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M176 262.62L256 342l80-79.38M256 330.97V170"/><path d="M256 64C150 64 64 150 64 256s86 192 192 192 192-86 192-192S362 64 256 64z" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32"/></svg>
				  	</a>
	</div>
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
					<th>Codificación</th>
					<th>Descripción</th>
					<th>Fabricante</th>
					<th>Valor</th>
					<th>Ubicación</th>
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

function iterarPrestamos($prestamos){
	$temp = "";
	for($x = 0; $x < count($prestamos); $x++){
		$a = '<tr>
            <td>
                <input type="checkbox" value="'.$prestamos[$x]["articulo_id"].'" onClick="handleCheckboxChange(\''.$prestamos[$x]["articulo_id"].'\')"/>
            </td>
			  <td>'.$prestamos[$x]["codigo_unidad"].'
			  </td><td>'.$prestamos[$x]["descripcion"].'
			  </td><td>'.$prestamos[$x]["fabricante"].'
			  </td><td>'.$prestamos[$x]["monto_valor"].'
			  </td><td>'.$prestamos[$x]["nombre_division"].'
			  </td><td>'.date("d-m-Y",strtotime($prestamos[$x]["fecha_de_retorno"])).'
			  </td></tr>';
		$temp .= $a;
	}
	return $temp;
}
?>