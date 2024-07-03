<?php 
 header('Content-Type: text/html; charset=UTF-8');
 $conec = mysqli_connect('localhost', 'root', '','inventario');
	if(! $conec) {
		die ('No se pudo conectar con la base de datos: '. mysqli_connect_errno());
	}
	include "./helpers.php";
	include "./alerta.php";
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$idArray = $_GET["ids"];
		$query = "SELECT articulos.*, divisiones.nombre_division, nro_identificacion_articulo.n_identificacion FROM articulos LEFT JOIN divisiones ON articulos.ubicacion = divisiones.id LEFT JOIN nro_identificacion_articulo ON articulos.id = nro_identificacion_articulo.id_articulo WHERE articulos.id IN ({$idArray})";
		$resultado = mysqli_query($conec, $query);
		$articulos = mysqli_fetch_all($resultado, MYSQLI_ASSOC);

	if (isset($_POST['destinoNoRegistrado']) && $_POST['destinoNoRegistrado'] == 'on') {
        $nombre_division = $_POST['nombre_division'];
        $direccion = $_POST['direccion'];
        $municipio = $_POST['municipio'];
        $es_destino_retiro = ($_POST['operacion'] == 'Retiro') ? 1 : 0;

        mysqli_query($conec,"INSERT INTO divisiones (nombre_division, direccion, municipio, es_destino_retiro) VALUES ('".$nombre_division."', '".$direccion."', '".$municipio."', '".$es_destino_retiro."')");
        // Obtener el ID del nuevo destino
        $id_nuevo_destino = mysqli_insert_id($conec);

        // Inicializar la sesión con el nuevo destino
        session_start();
        $_SESSION['destino'] = $id_nuevo_destino;
    } else {
        // Inicializar la sesión con el destino existente
        session_start();
        $_SESSION['destino'] = $_POST['destino'];
    }
		switch ($_POST['operacion']) {
			case "Retorno":
				session_start();
				$_SESSION['articulos'] = $articulos;
				$_SESSION['observaciones'] = $_POST['observaciones'];
				$_SESSION['fregreso'] = "Ninguna";
				$_SESSION['operacion'] = $_POST['operacion'];
				header('Location: confirmar.php');
				break;
			case 'Traspaso Temporal':
				session_start();
				$_SESSION['articulos'] = $articulos;
				$_SESSION['observaciones'] = $_POST['observaciones'];
				$_SESSION['fregreso'] = $_POST['fregreso'];
				$_SESSION['operacion'] = $_POST['operacion'];
				header('Location: confirmar.php');
				break;
			case'Traspaso':
				session_start();
				$_SESSION['articulos'] = $articulos;
				$_SESSION['observaciones'] = $_POST['observaciones'];
				$_SESSION['fregreso'] = "Ninguna";
				$_SESSION['operacion'] = $_POST['operacion'];
				header('Location: confirmar.php');
				break;
			case 'Retiro':
				session_start();
				$_SESSION['articulos'] = $articulos;
				$_SESSION['operacion'] = "Retiro";
				$_SESSION['destino'] = "2";
				$_SESSION['observaciones'] = $_POST['observaciones'];
				$_SESSION['fregreso'] = "Ninguna";
				header('Location: confirmar.php');
		}
		mysqli_close($conec);
	}else if(isset($_GET['operacion']) AND $_GET['operacion'] === 'r'){
		$idArray = $_GET["ids"];
		$query = "SELECT articulos.*, historial_operaciones_articulos.origen, traspasos_temporales.*, divisiones.nombre_division   
          FROM articulos   
          INNER JOIN traspasos_temporales ON articulos.id = traspasos_temporales.articulo_id   
          LEFT JOIN divisiones ON articulos.ubicacion = divisiones.id
          LEFT JOIN historial_operaciones_articulos ON traspasos_temporales.id_operacion = historial_operaciones_articulos.id_operacion AND traspasos_temporales.articulo_id = historial_operaciones_articulos.id_articulo   
          WHERE articulos.id IN ($idArray)";
		$resultado = mysqli_query($conec, $query);
		$articulos = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
		session_start();
		$_SESSION['articulos'] = $articulos;
		$_SESSION['operacion'] = "Retorno";
		$_SESSION['destino'] = 2;
		$_SESSION['fregreso'] = "Ninguna";
		header('Location: confirmar.php');
	}else if(isset($_GET['operacion']) AND $_GET['operacion'] === 're'){
		$idArray = $_GET["ids"];
		$query = "SELECT articulos.*, divisiones.nombre_division FROM articulos LEFT JOIN divisiones ON articulos.ubicacion = divisiones.id WHERE articulos.id IN ($idArray)";
		$resultado = mysqli_query($conec, $query);
		$articulos = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
		session_start();
		$_SESSION['articulos'] = $articulos;
		$_SESSION['operacion'] = "Reincorporación";
		$_SESSION['destino'] = 2;
		$_SESSION['fregreso'] = "Ninguna";
		header('Location: confirmar.php');
	}

	$destinos = mysqli_fetch_all(mysqli_query($conec,"SELECT * FROM divisiones WHERE es_destino_retiro = 0 AND id != 2"),MYSQLI_ASSOC);

	$destinoOptions = "";

	for($x = 0; $x < count($destinos); $x++){
		$destinoOptions .= '<option value="'.$destinos[$x]["id"].'">'.$destinos[$x]["nombre_division"].'</option>';
	};

	$destinosRetiro = mysqli_fetch_all(mysqli_query($conec,"SELECT * FROM divisiones WHERE es_destino_retiro = 1"),MYSQLI_ASSOC);

	$retiroOptions = "";

	for($x = 0; $x < count($destinosRetiro); $x++){
		$retiroOptions .= '<option value="'.$destinosRetiro[$x]["id"].'">'.$destinosRetiro[$x]["nombre_division"].'</option>';
	};
	
echo '
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Transferencia de Artículos</title>
	<link rel="stylesheet" href="css/output.css">
	
</head>
<body class="w-11/12 mx-auto">
	'.$header.'
	<h1 class="mt-28 text-6xl font-rubik text-sky-900 font-bold">Realizar una Operación</h1>
		<form class="flex flex-col gap-6 mt-12 mx-auto w-6/12 bg-gray-100 bg-opacity-80 rounded-xl p-10 font-karla text-gray-400" action="" method="POST">
			<div class="flex justify-between w-96 items-center">
				<label class="radio" for="asignacion">
				<input required class="radio" id="asignacion" value="Traspaso" name="operacion" type="radio" onclick="mostrarInputFecha()">
				Traspaso
				</label>
				<label class="radio" for="prestamo">
				<input required class="radio" id="prestamo" value="Traspaso Temporal" name="operacion" type="radio" onclick="mostrarInputFecha()">
				Traspaso Temporal
				</label>
				<label class="radio" for="retiro">
				<input required class="radio" id="retiro" value="Retiro" name="operacion" type="radio" onclick="mostrarInputFecha()">
				Retiro
				</label>
			</div>
			<div class="flex flex-col gap-1">
				<label class="font-bold" for="observaciones">Observaciones</label>
				<input required id="observaciones" class="w-96 bg-gray-50 shadow-inner px-4 py-2" name="observaciones" type="text">
			</div>
        <div class="flex flex-col gap-1" id="destinoDestino">
            <label class="font-bold" for="destino">Destino</label>
            <select required id="inputDestino" class="w-96 bg-gray-50 shadow-inner px-4 py-2" name="destino">
                ' . $destinoOptions . '
            </select>
        </div>

        <div class="flex flex-col gap-1" id="retiroDestino" style="display: none;">
            <label class="font-bold w-full" for="retiroDestino">Destino de Retiro</label>
            <select required id="inputRetiroDestino" class="w-96 bg-gray-50 shadow-inner px-4 py-2" name="retiroDestino">
                ' . $retiroOptions . '
            </select>
        </div>

		<div class="-mt-2 flex items-center gap-1">
		<input type="checkbox" id="destinoNoRegistrado" name="destinoNoRegistrado" onclick="toggleDestinoNoRegistrado()">
    <label for="destinoNoRegistrado">Destino no registrado</label>
    
</div>

<div id="nuevoDestino" style="display: none;">
    <div class="flex flex-col gap-1">
        <label class="font-bold" for="nombre_division">Nombre de la División</label>
        <input  id="nombre_division" class="w-96 bg-gray-50 shadow-inner px-4 py-2" name="nombre_division" type="text">
    </div>
    <div class="flex flex-col gap-1">
        <label class="font-bold" for="direccion">Dirección</label>
        <input  id="direccion" class="w-96 bg-gray-50 shadow-inner px-4 py-2" name="direccion" type="text">
    </div>
    <div class="flex flex-col gap-1">
        <label class="font-bold" for="municipio">Municipio</label>
        <input  id="municipio" class="w-96 bg-gray-50 shadow-inner px-4 py-2" name="municipio" type="text">
    </div>
</div>

			<div class="flex flex-col gap-1">
				<label class="font-bold" for="fregreso">Fecha de Regreso</label>
				<input required min="'.date("Y-m-d").'" disabled="true" id="inputFecha" class="w-96 bg-gray-50 shadow-inner px-4 py-2" name="fregreso" type="date">
			</div>

		
			<div class="flex justify-end">
				<input class="bg-blue-500 cursor-pointer text-white hover:text-blue-950 rounded-xl hover:bg-white px-4 py-2" value="Siguiente" type="submit">
			</div>
		</form>
	<script language="javascript">
		function mostrarInputFecha(){
			var radioPrestamo, inputFecha, inputDestino;
			inputFecha = document.getElementById("inputFecha");
			inputDestino = document.getElementById("inputDestino");
			radioPrestamo = document.getElementById("prestamo");
			
			if(radioPrestamo.checked == true){
				inputFecha.disabled = false;
				inputFecha.required = true;
			}
			else{
				inputFecha.disabled = true;
				inputFecha.required = false;
			}

			mostrarRetiroDestino();
		}

		function toggleDestinoNoRegistrado() {
    const checkbox = document.getElementById("destinoNoRegistrado");
    const nuevoDestinoDiv = document.getElementById("nuevoDestino");
    const inputDestino = document.getElementById("inputDestino");
    const inputRetiroDestino = document.getElementById("inputRetiroDestino");
    // Selecciona los tres inputs dentro de #nuevoDestino
    const inputsNuevoDestino = nuevoDestinoDiv.querySelectorAll("input");

    if (checkbox.checked) {
        // Mostrar inputs y ocultar dropdowns
        nuevoDestinoDiv.style.display = "block";
        inputDestino.required = false;
        inputRetiroDestino.required = false;
        inputDestino.disabled = true;
        inputRetiroDestino.disabled = true;
        // Añade el atributo required a los inputs dentro de #nuevoDestino
        inputsNuevoDestino.forEach(input => input.required = true);
    } else {
        // Ocultar inputs y mostrar dropdowns
        nuevoDestinoDiv.style.display = "none";
        inputDestino.required = true;
        inputRetiroDestino.required = true;
        inputDestino.disabled = false;
        inputRetiroDestino.disabled = false;
        // Quita el atributo required de los inputs dentro de #nuevoDestino
        inputsNuevoDestino.forEach(input => input.required = false);
    }
}

		function obtenerVariableQuery(variable)
		{
		       var query = window.location.search.substring(1);
		       var vars = query.split("&");
		       for (var i=0;i<vars.length;i++) {
		               var pair = vars[i].split("=");
		               if(pair[0] == variable){return pair[1];}
		       }
		       return("");
		}
		var radioPrestamo = document.getElementById("prestamo");
		var radioRetorno = document.getElementById("retorno");
		var radioExtension = document.getElementById("extension");
		var radioAsignacion = document.getElementById("asignacion");
		var radioRetiro = document.getElementById("retiro");

		switch (obtenerVariableQuery("operacion")){
			case "r" :
				radioRetorno.disabled = false;
				radioExtension.disabled = false;
				radioRetorno.checked = true;
				mostrarRetiroDestino();
				break;
			case "p" :
				radioAsignacion.checked = true;
				break;
			case "t" :
				radioPrestamo.checked = true;
				break;
			case "e" :
				radioExtension.disabled = false;
				radioRetorno.disabled = false;	
				radioExtension.checked = true;
				break;
			case "ret":
				radioRetiro.checked = true;
				break;
		}
function mostrarRetiroDestino() {
  const retiroRadio = document.querySelector("#retiro"); // Use querySelector
  const retiroDestino = document.getElementById("retiroDestino");
  const inputDestino = document.getElementById("destinoDestino");
  if (retiroRadio) { // Check if retiroRadio exists before accessing checked
    if (retiroRadio.checked) {
      retiroDestino.style.display = "flex";
      inputDestino.style.display = "none";
    } else {
      retiroDestino.style.display = "none";
      inputDestino.style.display = "flex";
    }
  }
}
	window.onload = function() {
	  mostrarInputFecha();
	};
        
	</script>
	'.$scriptRespaldo.'
</body>
</html>';

function estaEnPrestamo($articulo, $conec) {
	$query = "SELECT * FROM traspasos_temporales WHERE articulo_id = " .$articulo['id'];
	$exec = mysqli_query($conec, $query);
	$resultado = mysqli_fetch_all($exec, MYSQLI_ASSOC);
	return !empty($resultado);
};
?>