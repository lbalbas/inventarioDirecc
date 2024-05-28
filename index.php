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
	$query = "SELECT articulos.*, divisiones.nombre_division, modelo_articulo.*, nro_identificacion_articulo.n_identificacion FROM `articulos`
LEFT JOIN modelo_articulo ON modelo_articulo.id_articulo = articulos.id LEFT JOIN `divisiones` ON `articulos`.`ubicacion` = `divisiones`.`id` LEFT JOIN nro_identificacion_articulo ON articulos.id = nro_identificacion_articulo.id_articulo WHERE `articulos`.`esta_retirado` = 0";

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
		<link href="./datatables.min.css" rel="stylesheet">
	</head>
	<body class="w-11/12 mx-auto">
	'.$header.'
		<div id="selectOperation" style="align-items: center !important; border-radius: 25px; position:fixed; padding: 5px 10px;" class="bg-blue-600 shadow-xl hidden">
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

	<h1 class="ml-6 mt-28 mb-10 text-6xl font-rubik text-sky-900 font-bold">Inventario General</h1>

<table id="inventarioGeneral" class="font-karla display text-sky-900 bg-blue-200 bg-opacity-30 rounded-xl m-4 px-4">
    <thead>
        <tr>
        	<th></th>
            <th>Serial</th>
            <th>Descripción</th>
            <th>Marca</th>
            <th>Ubicación</th>
			<th></th>
        </tr>
    </thead>
    <tbody>
    '.$filas.'
    </tbody>
    <tfoot>
            <tr>
                <th></th>
	            <th>Serial</th>
	            <th>Descripción</th>
	            <th>Marca</th>
	            <th>Ubicación</th>
	            <th></th>
            </tr>
        </tfoot>
</table>

	<script language="javascript">
	function mostrarTodosArticulos(mostrarTodos) {
	    var arts = document.querySelectorAll(".fuera");
	    arts.forEach(e=>{
	    	e.classList.toggle("articulo-fuera-oficina")
	    })

}
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

		var checkboxes = document.querySelectorAll(\'input[type="checkbox"]:not(#mostrarTodos)\');
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
	 
<script src="./datatables.min.js"></script>
<script language="javascript">
$(document).ready(function () {
  var showAllFlag = false;

  // Custom filtering function
  $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
    if (showAllFlag) {
      return true;
    }
    var value = "OFICINA DE ADMINISTRACION Y GESTION INTERNA";
    var location = data[4]; // Assuming "Ubicación" is in the 6th column (index 5)
    if (location == value) {
      return true;
    }

    return false;
  });

  var table = $("#inventarioGeneral").DataTable({
    language: {
      url: "./resources/lng-es.json",
    },
    responsive: true,
        layout: {
        topStart: "buttons"
    },
    "columnDefs": [ {
"targets": 0,
"orderable": false
} ],
    buttons: [
      {
        text: "Mostrar artículos fuera de la oficina",
        className: "filter-cols bg-blue-400 my-2 border-0 bg cursor-pointer text-white rounded-xl px-5 py-3",
        action: function (e, dt, node, config) {
          showAllFlag = !showAllFlag;
          dt.draw();
          e.target.innerHTML = showAllFlag ? "<span>Ocultar artículos fuera de la oficina</span>" : "<span>Mostrar artículos fuera de la oficina</span>";;
        },
      },
    ],
  });

  table.on("click", "td.dt-control", function (e) {
    let tr = e.target.closest("tr");
    let row = table.row(tr);
 
    if (row.child.isShown()) {
        // This row is already open - close it
        row.child.hide();
    }
    else {
        // Open this row
       row.child(format(tr.dataset.modelo, tr.dataset.nid)).show();

    }
});
});
function format (modelo, nid) {
    return `<div class="flex flex-col items-start">
    <div class="py-2 border-b border-solid border-gray-300">Modelo:  ` + modelo +  `</div><div class="py-2">Nro. Id.:  ` + nid +  `</div></div> `;
}
</script>
	'.$scriptRespaldo.'
	</body>
	</html>';

function iterarArticulos($articulos, $conec) {
$rows = ""; // Initialize rows string

for ($x = 0; $x < count($articulos); $x++) {
  $enPrestamo = estaEnPrestamo($articulos[$x], $conec);
  $claseFueraOficina = ($articulos[$x]["ubicacion"] != 2) ? 'fuera articulo-fuera-oficina' : '';
  $deshabilitado = ($enPrestamo || $articulos[$x]["ubicacion"] != 2) ? 'disabled' : '';
  $modelo = !empty($articulos[$x]['nombre_modelo']) ? $articulos[$x]['nombre_modelo'] : "Modelo no especificado";
  $n_id = !empty($articulos[$x]['n_identificacion']) ? $articulos[$x]['n_identificacion'] : "S.C";
  $asterisco = ($enPrestamo) ? '**' : '';

  $row = '<tr class="font-karla" data-modelo="'.$modelo.'" data-nid="'.$n_id.'">
  		<td class="dt-control"></td>
          <td>' . $articulos[$x]["serial_fabrica"] . '</td>
          <td>' . $articulos[$x]["descripcion"] . '</td>
          <td>' . $articulos[$x]["fabricante"] . '</td>
          <td>' . $articulos[$x]["nombre_division"] . '</td>
          <td>
                    <input type="checkbox" value="'.$articulos[$x]["id"].'" '.$deshabilitado.' onClick="handleCheckboxChange(\''.$articulos[$x]["id"].'\')" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"></td>
        </tr>';

  $rows .= $row;
}

    return $rows;
}


function estaEnPrestamo($articulo, $conec) {
	$query = "SELECT * FROM traspasos_temporales WHERE articulo_id = " .$articulo['id'];
	$exec = mysqli_query($conec, $query);
	$resultado = mysqli_fetch_all($exec, MYSQLI_ASSOC);
	return !empty($resultado);
};
	mysqli_close($conec);

/*function iterarArticulos($articulos, $conec) {
    $temp = "";

    for($x = 0; $x < count($articulos); $x++) {
        // Verifica si el artículo está en préstamo o traspaso
        $enPrestamo = estaEnPrestamo($articulos[$x], $conec);
        $claseFueraOficina = ($articulos[$x]["ubicacion"] != 2) ? 'fuera articulo-fuera-oficina' : '';

        // Si el artículo está en préstamo o traspaso, deshabilita la casilla de verificación y agrega un asterisco
        $deshabilitado = ($enPrestamo || $articulos[$x]["ubicacion"] != 2) ? 'disabled' : '';
        // Corrección aquí: Cambiado $articulo[$x]['nombre_modelo'] a $articulos[$x]['nombre_modelo']
        $modelo = !empty($articulos[$x]['nombre_modelo']) ? $articulos[$x]['nombre_modelo'] : "Modelo no especificado";
        $asterisco = ($enPrestamo) ? '**' : '';
        $a = '
            <div class="grid grid-cols-12 text-blue-950 border-blue-300 font-karla border-solid border-b-2 py-2 last:border-0 group '.$claseFueraOficina.'">
                <div class="col-span-1 gap-2 items-center justify-center flex">
                    <a class="w-4 group-hover:opacity-100 opacity-0 text-blue-950" href="/modificar.php?id='.$articulos[$x]["id"].'"><svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512"><title>Modificar articulo</title><path d="M262.29 192.31a64 64 0 1057.4 57.4 64.13 64.13 0 00-57.4-57.4zM416.39 256a154.34 154.34 0 01-1.53 20.79l45.21 35.46a10.81 10.81 0 012.45 13.75l-42.77 74a10.81 10.81 0 01-13.14 4.59l-44.9-18.08a16.11 16.11 0 00-15.17 1.75A164.48 164.48 0 01325 400.8a15.94 15.94 0 00-8.82 12.14l-6.73 47.89a11.08 11.08 0 01-10.68 9.17h-85.54a11.11 11.11 0 01-10.69-8.87l-6.72-47.82a16.07 16.07 0 00-9-12.22 155.3 155.3 0 01-21.46-12.57 16 16 0 00-15.11-1.71l-44.89 18.07a10.81 10.81 0 01-13.14-4.58l-42.77-74a10.8 10.8 0 012.45-13.75l38.21-30a16.05 16.05 0 006-14.08c-.36-4.17-.58-8.33-.58-12.5s.21-8.27.58-12.35a16 16 0 00-6.07-13.94l-38.19-30A10.81 10.81 0 0149.48 186l42.77-74a10.81 10.81 0 0113.14-4.59l44.9 18.08a16.11 16.11 0 0015.17-1.75A164.48 164.48 0 01187 111.2a15.94 15.94 0 008.82-12.14l6.73-47.89A11.08 11.08 0 01213.23 42h85.54a11.11 11.11 0 0110.69 8.87l6.72 47.82a16.07 16.07 0 009 12.22 155.3 155.3 0 0121.46 12.57 16 16 0 0015.11 1.71l44.89-18.07a10.81 10.81 0 0113.14 4.58l42.77 74a10.8 10.8 0 01-2.45 13.75l-38.21 30a16.05 16.05 0 00-6.05 14.08c.33 4.14.55 8.3.55 12.47z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/></svg></a>    
                    <input type="checkbox" value="'.$articulos[$x]["id"].'" '.$deshabilitado.' onClick="handleCheckboxChange(\''.$articulos[$x]["id"].'\')" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                </div>
                <div class="flex items-center col-start-2 col-end-3">'.$articulos[$x]["serial_fabrica"].'</div>
                <div class="flex items-center col-start-4 col-end-7">'.$articulos[$x]["descripcion"].'</div>
                <div class="flex items-center col-start-7 col-end-8">'.$articulos[$x]["fabricante"].'</div>
                <div class="flex items-center col-start-8 col-end-10">'.$modelo.'</div>
                <div class="flex items-center col-start-10 col-end-12">'.$articulos[$x]["nombre_division"].'</div>
            </div>';
        $temp .= $a;
    }
    return $temp;
}*/

?>

