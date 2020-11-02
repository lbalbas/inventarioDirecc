<?php 
  $conec = mysqli_connect('localhost', 'root', '','inventario');
	if(! $conec) {
		die ('No se pudo conectar con la base de datos: '. mysqli_connect_errno());
	}
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$serial = $_POST["serial"];
		$query = 'SELECT * FROM articulos_en_inventario WHERE serial = "'.$serial.'"';
		$resultado = mysqli_query($conec, $query);
		$articulo = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
		if($articulo == false OR $articulo == ""){
				echo '<script language="javascript">alert("El artículo especificado no existe en inventario");</script>';
		}else{
			mysqli_close();
			$articulo = $articulo[0];
			if(strpos($articulo['ubicacion'], "En préstamo a") !== false){
				if($_POST['operacion'] != "Retorno"){
					echo '<script language="javascript">alert("El artículo especificado se encuentra en préstamo");</script>';
				}else{
					session_start();
					$_SESSION['articulo'] = $articulo;
					$_SESSION['destino'] = $_POST['destino'];
					$_SESSION['fregreso'] = "Ninguna";
					$_SESSION['operacion'] = $_POST['operacion'];
					header('Location: confirmar.php');
				}
			}else{
			    if($_POST['operacion'] == 'Préstamo'){
						session_start();
						$_SESSION['articulo'] = $articulo;
						$_SESSION['destino'] = $_POST['destino'];
						$_SESSION['fregreso'] = $_POST['fregreso'];
						$_SESSION['operacion'] = $_POST['operacion'];
						header('Location: confirmar.php');
				}else if($_POST['operacion'] == 'Asignación'){
						session_start();
						$_SESSION['articulo'] = $articulo;
						$_SESSION['destino'] = $_POST['destino'];
						$_SESSION['fregreso'] = "Ninguna";
						$_SESSION['operacion'] = $_POST['operacion'];
						header('Location: confirmar.php');
				}
			}
		}
	}
	
echo '
<html>
<head>
	<meta charset="UTF-8">
	<title>Asignación/Prestámo</title>
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
	    </div>
	</nav>
	<br>
	<div class="box">
		<form action="" method="POST">
			<div class="control">
				<label class="label "for="serial">Serial</label>
				<input id="serial" required maxlength="50" class="input" name="serial" type="text">
			</div>
			<br>
			<div class="control">
				<label class="label" for="destino">Solicitante</label>
				<input required maxlength="255" class="input" name="destino" type="text">
				<p class="help">En caso de retorno insertar la nueva ubicación del artículo</p>
			</div>
			<br>
			<div class="control">
				<label class="radio" for="prestamo">
				<input required class="radio" id="prestamo" value="Préstamo" name="operacion" type="radio" onclick="mostrarInputFecha()">
				Prestámo
				</label>
				<label class="radio" for="asignacion">
				<input required class="radio" id="asignacion" value="Asignación" name="operacion" type="radio" onclick="mostrarInputFecha()">
				Asignación
				</label>
				<label class="radio" for="retorno">	
				<input required class="radio" id="retorno" value="Retorno" name="operacion" type="radio" onclick="mostrarInputFecha()">
				Retorno
				</label>
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
	<script>
		function mostrarInputFecha(){
			var radioPrestamo, inputFecha;
			inputFecha = document.getElementById("inputFecha");
			radioPrestamo = document.getElementById("prestamo");
			
			if(radioPrestamo.checked == true){
				inputFecha.disabled = false;
				inputFecha.required = true;
			}
			else{
				inputFecha.disabled = true;
				inputFecha.required = false;
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
		var serialInput = document.getElementById("serial");
		var radioPrestamo = document.getElementById("prestamo");
		var radioRetorno = document.getElementById("retorno");
		var radioAsignacion = document.getElementById("asignacion");
		serialInput.value = obtenerVariableQuery("serial");
		
		switch (obtenerVariableQuery("operacion")){
			case "r" :
				radioRetorno.checked = true;
				break;
			case "a" :
				radioAsignacion.checked = true;
				break;
			case "p" :
				radioPrestamo.checked = true;
				break;
		}
		mostrarInputFecha();
	</script>
</body>
</html>'
?>