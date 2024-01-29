<?php 
 header('Content-Type: text/html; charset=UTF-8');
 $conec = mysqli_connect('localhost', 'root', '','inventario');
	if(! $conec) {
		die ('No se pudo conectar con la base de datos: '. mysqli_connect_errno());
	}
	include './alerta.php';
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$codigo_articulo = mysqli_real_escape_string($conec,$_POST["codigo_articulo"]);
		$query = 'SELECT * FROM articulos_en_inventario WHERE codigo_articulo = "'.$codigo_articulo.'"';
		$resultado = mysqli_query($conec, $query);
		$articulo = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
		if($articulo == false OR $articulo == ""){
				echo '<script language="javascript">alert("El artículo especificado no existe en inventario");</script>';
		}else{
			mysqli_close($conec);
			$articulo = $articulo[0];
			if(strpos($articulo['ubicacion'], "En préstamo a") !== false){
				if($_POST['operacion'] !== "Retorno" AND $_POST['operacion'] !== "Extensión"){
					echo '<script language="javascript">alert("El artículo especificado se encuentra en préstamo");</script>';
				}else if($_POST['operacion'] == "Retorno"){
					session_start();
					$_SESSION['articulo'] = $articulo;
					$_SESSION['destino'] = $_POST['destino'];
					$_SESSION['fregreso'] = "Ninguna";
					$_SESSION['operacion'] = $_POST['operacion'];
					header('Location: confirmar.php');
				}else if($_POST['operacion'] == "Extensión"){
						session_start();
						$_SESSION['articulo'] = $articulo;
						$_SESSION['destino'] = "";
						$_SESSION['fregreso'] = $_POST['fregreso'];
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
				}else{
					echo '<script language="javascript">alert("El artículo especificado no se encuentra en préstamo");</script>';
				}
			}
		}
	}
	
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
	<nav class="navbar is-link">
	<div class="navbar-brand">
                <a role="button" class="navbar-burger burger" onclick="document.querySelector(`.navbar-menu`).classList.toggle(`is-active`);" aria-label="menu" aria-expanded="false">
                  <span aria-hidden="true"></span>
                  <span aria-hidden="true"></span>
                  <span aria-hidden="true"></span>
                </a>
        </div>
        <div class="navbar-menu">
            <div class="navbar-start">
                <a href="index.php" class="navbar-item">Inicio</a>
                <a href="insertar.php" class="navbar-item">Registro</a>
                <a href="transferencia.php" class="navbar-item">Transferencias</a>
                <a href="historico.php" class="navbar-item">Histórico</a>
                <a href="prestamos.php" class="navbar-item">Préstamos</a>
                '.$opcionesUsuario.'
            </div>
            '.$alerta.'
        </div>
  	</nav>
	<br>
	<div id="box" class="box">
		<form action="" method="POST">
			<div class="control">
						<div class="control">
				<label class="radio" for="prestamo">
				<input required class="radio" id="prestamo" value="Préstamo" name="operacion" type="radio" onclick="mostrarInputFecha()">
				Prestámo
				</label>
				<label class="radio" for="asignacion">
				<input required class="radio" id="asignacion" value="Asignación" name="operacion" type="radio" onclick="mostrarInputFecha()">
				Asignación
				</label>
				<label class="radio" for="extension">	
				<input required disabled class="radio" id="extension" value="Extensión" name="operacion" type="radio" onclick="mostrarInputFecha()">
				Extensión
				</label>
				<label class="radio" for="retorno">	
				<input required disabled class="radio" id="retorno" value="Retorno" name="operacion" type="radio" onclick="mostrarInputFecha()">
				Retorno
				</label>
			</div>
			<br>
<label class="label "for="codigo_articulo">Código de Artículo</label>
<input id="codigo_articulo" required maxlength="50" class="input" name="codigo_articulo" type="text">

			</div>
			<br>
			<div class="control">
				<label class="label" for="destino">Solicitante</label>
				<input required id="inputDestino" maxlength="255" class="input" name="destino" type="text">
				<p class="help">En caso de retorno insertar la nueva ubicación del artículo</p>
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
			radioExtension = document.getElementById("extension");
			
			if(radioPrestamo.checked == true || radioExtension.checked == true){
				inputFecha.disabled = false;
				inputFecha.required = true;
			}
			else{
				inputFecha.disabled = true;
				inputFecha.required = false;
			}

			if(radioExtension.checked == true){
				inputDestino.disabled = true;
				inputDestino.required = false;
			}else{
				inputDestino.disabled = false;
				inputDestino.required = true;
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
		var codigoInput = document.getElementById("codigo_articulo");
		var radioPrestamo = document.getElementById("prestamo");
		var radioRetorno = document.getElementById("retorno");
		var radioExtension = document.getElementById("extension");
		var radioAsignacion = document.getElementById("asignacion");
		serialInput.value = obtenerVariableQuery("serial");
		
		switch (obtenerVariableQuery("operacion")){
			case "r" :
				radioRetorno.disabled = false;
				radioExtension.disabled = false;
				radioRetorno.checked = true;
				break;
			case "a" :
				radioAsignacion.checked = true;
				break;
			case "p" :
				radioPrestamo.checked = true;
				break;
			case "e" :
				radioExtension.disabled = false;
				radioRetorno.disabled = false;	
				radioExtension.checked = true;
				break;	
		}
		mostrarInputFecha();
	</script>
	'.$scriptRespaldo.'
</body>
</html>'
?>