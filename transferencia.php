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
		$query = "SELECT articulos.*, divisiones.nombre_division FROM articulos LEFT JOIN divisiones ON articulos.ubicacion = divisiones.id WHERE articulos.id IN ({$idArray})";
		$resultado = mysqli_query($conec, $query);
		$articulos = mysqli_fetch_all($resultado, MYSQLI_ASSOC);

		switch ($_POST['operacion']) {
			case "Retorno":
				session_start();
				$_SESSION['articulos'] = $articulos;
				$_SESSION['destino'] = $_POST['destino'];
				$_SESSION['observaciones'] = $_POST['observaciones'];
				$_SESSION['fregreso'] = "Ninguna";
				$_SESSION['operacion'] = $_POST['operacion'];
				header('Location: confirmar.php');
				break;
			case "Extensión":
				session_start();
				$_SESSION['articulos'] = $articulos;
				$_SESSION['destino'] = "";
				$_SESSION['observaciones'] = $_POST['observaciones'];
				$_SESSION['fregreso'] = $_POST['fregreso'];
				$_SESSION['operacion'] = $_POST['operacion'];
				header('Location: confirmar.php');
				break;
			case 'Traspaso Temporal':
				session_start();
				$_SESSION['articulos'] = $articulos;
				$_SESSION['destino'] = $_POST['destino'];
				$_SESSION['observaciones'] = $_POST['observaciones'];
				$_SESSION['fregreso'] = $_POST['fregreso'];
				$_SESSION['operacion'] = $_POST['operacion'];
				header('Location: confirmar.php');
				break;
			case'Traspaso':
				session_start();
				$_SESSION['articulos'] = $articulos;
				$_SESSION['destino'] = $_POST['destino'];
				$_SESSION['observaciones'] = $_POST['observaciones'];
				$_SESSION['fregreso'] = "Ninguna";
				$_SESSION['operacion'] = $_POST['operacion'];
				header('Location: confirmar.php');
				break;
			case 'Retiro':
				session_start();
				$_SESSION['articulos'] = $articulos;
				$_SESSION['operacion'] = "Retiro";
				$_SESSION['destino'] = $_POST["destino"];
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
		$_SESSION['destino'] = 1;
		$_SESSION['fregreso'] = "Ninguna";
		header('Location: confirmar.php');
	}


	$destinos = mysqli_fetch_all(mysqli_query($conec,"SELECT * FROM divisiones WHERE es_destino_retiro = 0"),MYSQLI_ASSOC);

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
	<link rel="stylesheet" href="css/estilo.css">
	<link rel="stylesheet" href="css/bulma.css">
</head>
<body>
	'.$header.'
	<br>
	<div id="box" class="box">
		<form action="" method="POST">
			<div class="control">
						<div class="control">
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
			<br>
			<div class="control">
				<label class="label" for="observaciones">Observaciones</label>
				<input required id="observaciones" class="input" name="observaciones" type="text">
			</div>
			<br>
        <div class="control" id="destinoDestino">
            <label class="label" for="destino">Destino</label>
            <select required id="inputDestino" class="input" name="destino">
                ' . $destinoOptions . '
            </select>
        </div>

        <div class="control" id="retiroDestino" style="display: none;">
            <label class="label" for="retiroDestino">Destino de Retiro</label>
            <select required id="inputRetiroDestino" class="input" name="retiroDestino">
                ' . $retiroOptions . '
            </select>
        </div>

			<br>

			<div class="control">
				<label class="label" for="fregreso">Fecha de Regreso</label>
				<input required min="'.date("Y-m-d").'" disabled="true" id="inputFecha" class="input" name="fregreso" type="date">
			</div>

			<br>
			<div class="control has-text-right">
				<input class="button" type="submit">
			</div>
		</form>
	</div>
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
      retiroDestino.style.display = "block";
      inputDestino.style.display = "none";
    } else {
      retiroDestino.style.display = "none";
      inputDestino.style.display = "block";
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