<?php 
  $conec = mysqli_connect('localhost', 'root', '','inventario');
	if(! $conec) {
		die ('No se pudo conectar con la base de datos: '. mysqli_connect_errno());
	}
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$serial = $_POST["serial"];
		$query = 'SELECT * FROM articulos_en_inventario WHERE serial = "'.$serial.'"';
		$resultado = mysqli_query($conec, $query);
		if($resultado != false AND $resultado != ""){
			$articulo = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
			mysqli_close();
			$articulo = $articulo[0];
			if(strpos($articulo['ubicacion'], "En préstamo a") !== false){
				echo '<script language="javascript">alert("El artículo especificado se encuentra en préstamo");</script>';
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
						$_SESSION['fregreso'] = "Indefinida";
						$_SESSION['operacion'] = $_POST['operacion'];
						header('Location: confirmar.php');
				}
			}
		}else{
				echo '<script language="javascript">alert("El artículo especificado no existe en inventario");</script>';
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
	            <a href="transferencia.php" class="navbar-item">Transferir</a>
	            <a href="historico.php" class="navbar-item">Historico</a>
	        </div>
	    </div>
	</nav>
	<br>
	<div class="box">
		<form action="" method="POST">
			<div class="control">
				<label class="label "for="serial">Serial</label>
				<input required maxlength="50" class="input" name="serial" type="text">
			</div>
			<br>
			<div class="control">
				<label class="label" for="destino">Solicitante</label>
				<input required maxlength="255" class="input" name="destino" type="text">
			</div>
			<div class="control">
				<label class="radio" for="prestamo">
				<input required class="radio" id="prestamo" value="Préstamo" name="operacion" type="radio" onclick="mostrarInputFecha()">
				Prestámo
				</label>
				<label class="radio" for="asignacion">
				<input required class="radio" id="asignacion" value="Asignación" name="operacion" type="radio" onclick="mostrarInputFecha()">
				Asignación
				</label>
			</div>
			<div class="control">
				<label class="label" for="fregreso">Fecha de Regreso</label>
				<input min="'.date("Y-m-d").'" disabled="true" id="inputFecha" class="input" name="fregreso" type="date">
			</div>

			<br>
			<input class="button" type="submit">
		</form>
	</div>
	<script>
		function mostrarInputFecha(){
			var radioPrestamo, inputFecha;
			inputFecha = document.getElementById("inputFecha");
			radioPrestamo = document.getElementById("prestamo");
			
			if(radioPrestamo.checked == true){
				inputFecha.disabled = false;
			}
			else{
				inputFecha.disabled = true;
			}
		}
	</script>
</body>
</html>'
?>