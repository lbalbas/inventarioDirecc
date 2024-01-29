<?php 
	header('Content-Type: text/html; charset=UTF-8');
	$conec = mysqli_connect('localhost', 'root', '','inventario');
	if(! $conec) {
		die ('No se pudo conectar con la base de datos: '. mysqli_connect_errno());
	}
	$getUsuarios = "SELECT * FROM usuarios";
	$resultado = mysqli_query($conec,$getUsuarios);
	$usuariosEnRegistro = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
	
	if($_SERVER['REQUEST_METHOD'] == 'GET'){
		if(isset($_GET['cerrarsesion'])){
			setcookie("login", "", time() - 86400);
			header('Location: login.php');
		}else if(count($usuariosEnRegistro) == 0){
			registroInicial();
		}else if(isset($_GET['registro']) AND isset($_COOKIE['login']) AND $_COOKIE['login'] == "admin"){
			muestraRegistro();
		}else if(isset($_COOKIE['login'])){
			header('Location: index.php');
		}else{
			muestraLogin();
		}
	}else if($_SERVER['REQUEST_METHOD'] == 'POST'){
		if($_POST['formEnviada'] == 'login'){
			$nombre_usuario = mysqli_real_escape_string($conec, $_POST['nombre_usuario']);
			$contrasena = md5($_POST['contrasena']);
			$queryLogin = "SELECT * FROM usuarios WHERE nombre_usuario = '".$nombre_usuario."'";
			$checkLogin = mysqli_query($conec,$queryLogin);
			$usuarioLogin = mysqli_fetch_all($checkLogin, MYSQLI_ASSOC);
			if($usuarioLogin == false OR $usuarioLogin == ""){
				echo '<script language="javascript">alert("Nombre de usuario incorrecto, intentelo nuevamente");</script>';
				muestraLogin();
			}else{
				$usuarioLogin = $usuarioLogin[0];
				if($usuarioLogin['contrasena'] == $contrasena){
					setcookie('login', $usuarioLogin['rol'],time()+86400);
					header('Location: index.php');
				}else{
					echo '<script language="javascript">alert("Contrasena incorrecta, intentelo nuevamente");</script>';
					muestraLogin();
				}
			}
		}else if($_POST['formEnviada'] == 'registro'){
			if($_COOKIE['login'] == 'admin'){
				$nombre_usuario = mysqli_real_escape_string($conec,$_POST['nombre_usuario']);
				$contrasena = md5($_POST['contrasena']);
				$rol = $_POST['rol'];
				$checkRegistro = "SELECT * FROM usuarios WHERE nombre_usuario = '".$nombre_usuario."'";
				$queryCheckRegistro = mysqli_query($conec, $checkRegistro);
				$resultadoCheckRegistro = mysqli_fetch_all($queryCheckRegistro, MYSQLI_ASSOC);
				if(count($resultadoCheckRegistro) > 0){
					echo '<script language="javascript">alert("Nombre de usuario ingresado ya se encuentra en uso");</script>';
					muestraRegistro();
				}else{
					$registrarUsuario = "INSERT INTO usuarios(nombre_usuario, contrasena, rol) VALUES('".$nombre_usuario."','".$contrasena."','".$rol."')";
					mysqli_query($conec,$registrarUsuario);
					echo '<script language="javascript">alert("Registro existoso");</script>';
					muestraRegistro();
				}
			}else{
			      echo '<script language="javascript">
			              alert("No tiene el permiso para realizar esta acción");
			              document.location="/"</script>';
			}
		}else if($_POST['formEnviada'] == 'registroInicial'){
			$nombre_usuario = mysqli_real_escape_string($conec,$_POST['nombre_usuario']);
			$contrasena = md5($_POST['contrasena']);
			$rol = $_POST['rol'];
			$registrarUsuario = "INSERT INTO usuarios(nombre_usuario, contrasena, rol) VALUES('".$nombre_usuario."','".$contrasena."','".$rol."')";
			mysqli_query($conec,$registrarUsuario);
			echo '<script language="javascript">alert("Registro existoso");</script>';
			muestraLogin();
		}

	}

	function muestraLogin(){
		echo'
				<html lang="en">
					<head>
					    <meta charset="UTF-8">
					    <meta name="viewport" content="width=device-width, initial-scale=1.0">
					    <title>Inicio de Sesión</title>
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
					  <div class="column">					  <h1 class="has-text-centered has-text-weight-bold">Iniciar Sesión</h1></div>

					 <div class="columns is-fullwidth is-centered">
					      <form id="box" class="box" action="" method="POST">
					      <div class="control">
					        <label class="label "for="nombre_usuario">Nombre de Usuario</label>
					        <input required class="input" name="nombre_usuario" type="text">
					      </div>
					      <br>
					      <div class="control">
					        <label class="label" for="contrasena">Contraseña</label>
					        <input required class="input" name="contrasena" type="password">
					      </div>
					      <br>
					        <input  value="login" name="formEnviada" type="hidden">
					      <input class="button" type="submit">
					    </form>
					  </div>
					</body>
				</html>
		';
	}


	function muestraRegistro(){
		echo'
				<html lang="en">
					<head>
					    <meta charset="UTF-8">
					    <meta name="viewport" content="width=device-width, initial-scale=1.0">
					    <title>Registro de Usuario</title>
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
			                <a href="traspasos_temporales.php" class="navbar-item">Traspasos Temporales</a>
			                '.$opcionesUsuario.'

			            </div>
			        </div></nav>
					  <div class="column">					  <h1 class="has-text-centered has-text-weight-bold">Registro de Usuario</h1></div>

					 <div class="columns is-fullwidth is-centered">
					      <form id="box" class="box" action="" method="POST">
					      <div class="control">
					        <label class="label "for="nombre_usuario">Nombre de Usuario</label>
					        <input required class="input" name="nombre_usuario" type="text">
					      </div>
					      <br>
					      <div class="control">
					        <label class="label" for="clave">Contraseña</label>
					        <input required class="input" name="contrasena" type="password">
					      </div>
					      <br>
							<div class="select">
							  <select name="rol">
							    <option value="admin">Administrador</option>
							    <option value="usuario">Usuario</option>
							  </select>
							</div>
					      <br>
					        <input  value="registro" name="formEnviada" type="hidden">
					       <br>
					      <input class="button" type="submit">
					    </form>
					  </div>
					  	<script language="javascript">
					    function respaldoVentana(){
					      if(window.confirm("¿Desea crear un respaldo de la base de datos?")){
					        window.open("/respaldo.php","_blank");
					      }
					    }
					  </script>
					</body>
				</html>
		';
	}
	function registroInicial(){
		echo'
				<html lang="en">
					<head>
					    <meta charset="UTF-8">
					    <meta name="viewport" content="width=device-width, initial-scale=1.0">
					    <title>Registro Inicial</title>
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
					  <div class="column">					  <h1 class="has-text-centered has-text-weight-bold">Registro de Administrador Inicial</h1></div>

					 <div class="columns is-fullwidth is-centered">
					      <form id="box" class="box" action="" method="POST">
					      <div class="control">
					        <label class="label "for="nombre_usuario">Nombre de Usuario</label>
					        <input required class="input" name="nombre_usuario" type="text">
					      </div>
					      <br>
					      <div class="control">
					        <label class="label" for="contrasena">Contraseña</label>
					        <input required class="input" name="contrasena" type="password">
					      </div>
					      <br>
					      	<input  value="admin" name="rol" type="hidden">
					        <input  value="registroInicial" name="formEnviada" type="hidden">
					       <br>
					      <input class="button" type="submit">
					    </form>
					  </div>
					</body>
				</html>
		';
	}

 ?>