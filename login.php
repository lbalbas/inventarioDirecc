<?php 
	header('Content-Type: text/html; charset=UTF-8');
	$conec = mysqli_connect('localhost', 'root', '','inventario');
	if(! $conec) {
		die ('No se pudo conectar con la base de datos: '. mysqli_connect_errno());
	}
	include "./helpers.php";
	$getUsuarios = "SELECT * FROM usuarios";
	$resultado = mysqli_query($conec,$getUsuarios);
	$usuariosEnRegistro = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
	
	if($_SERVER['REQUEST_METHOD'] == 'GET'){
		if(isset($_GET['cerrarsesion'])){
			setcookie("login", "", time() - 86400);
			header('Location: login.php');
		}else if(count($usuariosEnRegistro) == 0){
			registroInicial();
		}else if(isset($_GET['registro']) AND isset($_COOKIE['login']) AND $_COOKIE['login'] == 3){
			muestraRegistro($header);
		}else if(isset($_GET['registro']) AND isset($_COOKIE['login']) AND $_COOKIE['login'] != 3){
			echo '<script language="javascript">
			alert("No estás autorizado para acceder a esta función.")
			document.location="/login.php";
		</script>';
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
					setcookie('login', $usuarioLogin['n_acceso'],time()+86400);
					setcookie('userid', $usuarioLogin['id'],time()+86400);
					header('Location: index.php');
				}else{
					echo '<script language="javascript">alert("Contrasena incorrecta, intentelo nuevamente");</script>';
					muestraLogin();
				}
			}
		}else if($_POST['formEnviada'] == 'registro'){
			if($_COOKIE['login'] == 3){
				$nombre_usuario = mysqli_real_escape_string($conec,$_POST['nombre_usuario']);
				$contrasena = md5($_POST['contrasena']);
				$n_acceso = $_POST['n_acceso'];
				$pregunta = $_POST['pregunta'];
				$respuesta = md5($_POST['respuesta']);
				$checkRegistro = "SELECT * FROM usuarios WHERE nombre_usuario = '".$nombre_usuario."'";
				$queryCheckRegistro = mysqli_query($conec, $checkRegistro);
				$resultadoCheckRegistro = mysqli_fetch_all($queryCheckRegistro, MYSQLI_ASSOC);
				if(count($resultadoCheckRegistro) > 0){
					echo '<script language="javascript">alert("Nombre de usuario ingresado ya se encuentra en uso");</script>';
					muestraRegistro($header);
				}else{
					$registrarUsuario = "INSERT INTO usuarios(nombre_usuario, contrasena, n_acceso, pregunta_recup, respuesta_recup) VALUES('".$nombre_usuario."','".$contrasena."','".$n_acceso."','".$pregunta."','".$respuesta."')";
					mysqli_query($conec,$registrarUsuario);
					echo '<script language="javascript">alert("Registro existoso");</script>';
					muestraRegistro($header);
				}
			}else{
			      echo '<script language="javascript">
			              alert("No tiene el permiso para realizar esta acción");
			              document.location="/"</script>';
			}
		}else if($_POST['formEnviada'] == 'registroInicial'){
			$nombre_usuario = mysqli_real_escape_string($conec,$_POST['nombre_usuario']);
			$contrasena = md5($_POST['contrasena']);
			$n_acceso = $_POST['n_acceso'];
			$pregunta = $_POST['pregunta'];
			$respuesta = md5($_POST['respuesta']);
			$registrarUsuario = "INSERT INTO usuarios(nombre_usuario, contrasena, n_acceso, pregunta_recup, respuesta_recup) VALUES('".$nombre_usuario."','".$contrasena."','".$n_acceso."','".$pregunta."','".$respuesta."')";
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
					    <link href="./css/output.css" rel="stylesheet">
					</head>
					<body>
					<div class="flex md:h-full justify-end">
					<div class="hidden md:flex w-full md:h-full flex-col justify-end items-center">
					<svg class="w-7/12 mb-16" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 548.27 592.23" id="logistics"><defs><linearGradient id="f" x1="210.14" x2="771.37" y1="263.34" y2="830.29" gradientUnits="userSpaceOnUse"><stop offset="0" stop-opacity="0"></stop><stop offset=".2" stop-color="#09080c" stop-opacity=".14"></stop><stop offset=".57" stop-color="#201f2b" stop-opacity=".5"></stop><stop offset=".99" stop-color="#3f3d56"></stop></linearGradient><linearGradient id="e" x1="549.95" x2="548.92" y1="157.43" y2="155.02" gradientUnits="userSpaceOnUse"><stop offset=".09" stop-color="#68e1fd"></stop><stop offset=".2" stop-color="#65d5f1"></stop><stop offset=".4" stop-color="#5db7d2"></stop><stop offset=".66" stop-color="#51859f"></stop><stop offset=".97" stop-color="#404059"></stop><stop offset=".99" stop-color="#3f3d56"></stop></linearGradient><linearGradient id="a" x1="528.8" x2="567.69" y1="133.77" y2="133.77" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#ecc4d7"></stop><stop offset=".42" stop-color="#efd4d1"></stop><stop offset="1" stop-color="#f2eac9"></stop></linearGradient><linearGradient id="g" x1="-1217.56" x2="-1226.39" y1="2615.31" y2="2624.21" gradientTransform="rotate(52.4 1989.299 3144.169)" xlink:href="#a"></linearGradient><linearGradient id="b" x1="201.68" x2="214.56" y1="289.71" y2="289.71" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#183866"></stop><stop offset="1" stop-color="#1a7fc1"></stop></linearGradient><linearGradient id="h" x1="194.68" x2="207.56" y1="339.08" y2="339.08" xlink:href="#b"></linearGradient><linearGradient id="c" x1="223.94" x2="205.18" y1="310.91" y2="315.18" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#68e1fd"></stop><stop offset="1" stop-color="#69b9eb"></stop></linearGradient><linearGradient id="i" x1="217.01" x2="198.25" y1="360.28" y2="364.55" xlink:href="#c"></linearGradient><linearGradient id="d" x1="193.69" x2="271.42" y1="165.01" y2="250.51" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#fff" stop-opacity="0"></stop><stop offset=".09" stop-color="#fff" stop-opacity=".05"></stop><stop offset=".24" stop-color="#fff" stop-opacity=".17"></stop><stop offset=".45" stop-color="#fff" stop-opacity=".37"></stop><stop offset=".69" stop-color="#fff" stop-opacity=".65"></stop><stop offset=".98" stop-color="#fff"></stop><stop offset=".98" stop-color="#fff"></stop></linearGradient><linearGradient id="j" x1="173.29" x2="266.57" y1="208.68" y2="318.61" xlink:href="#d"></linearGradient><linearGradient id="k" x1="219.12" x2="211.4" y1="-200.44" y2="715.79" xlink:href="#e"></linearGradient><linearGradient id="l" x1="211.27" x2="203.55" y1="-200.5" y2="715.72" xlink:href="#e"></linearGradient><linearGradient id="m" x1="234.89" x2="212.53" y1="190.59" y2="195.68" xlink:href="#c"></linearGradient><linearGradient id="n" x1="226.62" x2="204.27" y1="249.43" y2="254.52" xlink:href="#c"></linearGradient><linearGradient id="o" x1="88.05" x2="162.09" y1="236.68" y2="323.71" xlink:href="#d"></linearGradient><linearGradient id="p" x1="152.74" x2="193.39" y1="241.97" y2="289.75" xlink:href="#d"></linearGradient><linearGradient id="q" x1="84.41" x2="167.15" y1="330.44" y2="415.94" xlink:href="#d"></linearGradient><linearGradient id="r" x1="132.41" x2="124.69" y1="-201.17" y2="715.06" xlink:href="#e"></linearGradient><linearGradient id="s" x1="104.79" x2="97.07" y1="-201.4" y2="714.82" xlink:href="#e"></linearGradient><linearGradient id="t" x1="150.1" x2="124.6" y1="279.82" y2="285.62" xlink:href="#c"></linearGradient><linearGradient id="u" x1="122" x2="96.5" y1="346.92" y2="352.73" xlink:href="#c"></linearGradient><linearGradient id="v" x1="73.36" x2="195.51" y1="100.24" y2="185.75" xlink:href="#d"></linearGradient><linearGradient id="w" x1="58.2" x2="185.89" y1="158.73" y2="263.11" xlink:href="#d"></linearGradient><linearGradient id="x" x1="126.58" x2="118.86" y1="-201.22" y2="715.01" xlink:href="#e"></linearGradient><linearGradient id="y" x1="117.63" x2="109.92" y1="-201.29" y2="714.93" xlink:href="#e"></linearGradient><linearGradient id="z" x1="145.48" x2="119.98" y1="137.12" y2="142.93" xlink:href="#c"></linearGradient><linearGradient id="A" x1="136.05" x2="110.55" y1="204.22" y2="210.03" xlink:href="#c"></linearGradient><linearGradient id="B" x1="290.11" x2="267.85" y1="217.89" y2="240.34" xlink:href="#b"></linearGradient><linearGradient id="C" x1="203.56" x2="156.16" y1="243.66" y2="291.46" xlink:href="#b"></linearGradient><linearGradient id="D" x1="317.25" x2="411.63" y1="232.15" y2="376.5" xlink:href="#d"></linearGradient><linearGradient id="E" x1="227.71" x2="384.2" y1="392.2" y2="392.2" xlink:href="#d"></linearGradient><linearGradient id="F" x1="351.76" x2="344.05" y1="-199.32" y2="716.9" xlink:href="#e"></linearGradient><linearGradient id="G" x1="307.28" x2="299.56" y1="-199.69" y2="716.53" xlink:href="#e"></linearGradient><linearGradient id="H" x1="382.75" x2="341.68" y1="280.67" y2="290.02" xlink:href="#c"></linearGradient><linearGradient id="I" x1="337.49" x2="296.42" y1="388.75" y2="398.1" xlink:href="#c"></linearGradient><linearGradient id="J" x1="319.4" x2="340.94" y1="348.19" y2="348.19" xlink:href="#d"></linearGradient><linearGradient id="K" x1="288.07" x2="339.23" y1="231.02" y2="231.02" xlink:href="#d"></linearGradient><linearGradient id="L" x1="319.83" x2="594.18" y1="466.45" y2="466.45" xlink:href="#d"></linearGradient><linearGradient id="M" x1="322.52" x2="594.18" y1="461.44" y2="461.44" xlink:href="#d"></linearGradient><linearGradient id="N" x1="359.62" x2="364.69" y1="451.83" y2="514.36" gradientTransform="rotate(45 363.49 499.622)" xlink:href="#e"></linearGradient><linearGradient id="O" x1="244.78" x2="246.32" y1="494.74" y2="513.67" xlink:href="#b"></linearGradient><linearGradient id="P" x1="238.3" x2="260.01" y1="483.48" y2="483.48" xlink:href="#d"></linearGradient><linearGradient id="Q" x1="3325.19" x2="3330.26" y1="451.83" y2="514.36" gradientTransform="matrix(-.38 -.92 -.92 .38 2281.89 3384.08)" xlink:href="#e"></linearGradient><linearGradient id="R" x1="3329.01" x2="3330.55" y1="498.51" y2="517.44" gradientTransform="matrix(-1 0 0 1 3875.38 0)" xlink:href="#b"></linearGradient><linearGradient id="S" x1="234.89" x2="256.5" y1="478.53" y2="486.03" xlink:href="#d"></linearGradient><linearGradient id="T" x1="412.48" x2="434.19" y1="483.48" y2="483.48" xlink:href="#d"></linearGradient><linearGradient id="U" x1="220.75" x2="156.85" y1="305.63" y2="370.06" xlink:href="#b"></linearGradient><linearGradient id="V" x1="305.4" x2="245.81" y1="248.65" y2="308.74" xlink:href="#b"></linearGradient><linearGradient id="W" x1="267.78" x2="202.44" y1="358.48" y2="424.37" xlink:href="#b"></linearGradient><linearGradient id="X" x1="487.83" x2="478.11" y1="275.77" y2="285.56" xlink:href="#a"></linearGradient><linearGradient id="Y" x1="547.79" x2="590.29" y1="317.81" y2="317.81" xlink:href="#f"></linearGradient><linearGradient id="Z" x1="600.33" x2="647.23" y1="290.79" y2="290.79" xlink:href="#f"></linearGradient><linearGradient id="aa" x1="526.29" x2="326.57" y1="117.86" y2="168.65" xlink:href="#f"></linearGradient><linearGradient id="ab" x1="869.38" x2="925.86" y1="-109.47" y2="-109.47" gradientTransform="rotate(-7 2034.654 3080.446)" xlink:href="#a"></linearGradient><linearGradient id="ac" x1="417.45" x2="402.02" y1="117.36" y2="145.49" xlink:href="#e"></linearGradient><linearGradient id="ad" x1="439.96" x2="446.39" y1="125.66" y2="136.59" xlink:href="#e"></linearGradient><linearGradient id="ae" x1="566.8" x2="535.15" y1="135.85" y2="177.35" xlink:href="#a"></linearGradient></defs><path fill="#68e1fd" d="M208 584.23c-117.3-17-188.48-100.1-204.52-199-9.86-60.76 1.08-127.51 34.92-188.59C127.34 36.08 351.99 32.35 351.99 32.35c129.28-6.41 103.82 211.93 96.34 259-4 24.93 21.18 58.21 47.3 93.89 23.15 31.64 47.06 65.18 52.13 96.48C558.55 548.28 397.34 611.74 208 584.23Z" opacity=".18"></path><path fill="#68e1fd" d="M208 585.65c-117.3-17-188.48-100.12-204.52-199h492.15c23.15 31.63 47.06 65.17 52.13 96.47C558.55 549.69 397.34 613.14 208 585.65Z"></path><path fill="url(#f)" d="M326.66 589.42c-117.3-17-188.48-100.12-204.52-199h492.15c23.15 31.63 47.06 65.17 52.13 96.47C677.21 553.46 516 616.91 326.66 589.42Z" transform="translate(-118.66 -3.77)" style="isolation:isolate"></path><path fill="#1d2741" d="M209.42 500.53c-5.93-.12-13-.92-15.73-6.22-1.34-2.63-1.17-5.74-1.62-8.66-4-26.3-50-25.15-57.55-50.65-.65-2.21-.86-4.85.67-6.56 1.37-1.51 3.62-1.76 5.65-1.9l66.85-4.69c2.7-9.88-7.86-18.31-17.62-21.44-43.71-14-93.33 14.77-136.26-1.52-2.29-.87-4.71-2-5.79-4.22-3.1-6.33 6.84-10.67 13.88-10.78l169.24-2.66c3.56-.06 7.2-.1 10.6 1 5.23 1.69 9.21 5.85 13 9.86 16.03 17.14 32.6 34.55 53.6 45.05 38.5 19.24 84.48 12.13 126.75 20.25 12.25 2.35 27.25 10.76 24.25 22.84-9.58 37.76-80.87 23.64-106.53 23.13Z" opacity=".18"></path><path fill="#68e1fd" d="m399.19 82.23.08 32.94L412.12 149l10.46-5a5.78 5.78 0 0 1 7.5 2.3l2.84 4.9a7.57 7.57 0 0 1-3 10.51c-7.56 4-23 13.58-25.89 13.58-4.43 0-17.17-19.83-17.17-19.83Z"></path><path fill="url(#e)" d="M545.42 147.4s-4.73 3.67-2.5 7.7 9.69 4.19 9.69 4.19 6.13-10.85-7.19-11.89Z" transform="translate(-118.66 -3.77)" style="isolation:isolate"></path><path fill="url(#a)" d="M548.8 125.08a44 44 0 0 1 6.91 1.28l10.42 2.52a2.21 2.21 0 0 1 1.38.72c.56.85-.3 2-1.24 2.35-3 1.22-6.8-1-9.48.75-1.5 1-2 3-3.2 4.34-1.52 1.84-4 2.58-6.38 2.84s-4.8.15-7.12.69c-3.49.81-7.23 3.06-10.36 1.31a1.94 1.94 0 0 1-.78-.72c-.39-.74.08-1.63.46-2.37a13.08 13.08 0 0 0 1.4-6.79c-.15-3.29-.24-3.05 3.15-4.24 4.75-1.68 9.74-3.08 14.84-2.68Z" transform="translate(-118.66 -3.77)"></path><path fill="url(#g)" d="m434.56 291.96-.29 6.92 12.08-.1.9-9.48-12.69 2.66z"></path><path fill="#68e1fd" d="m434.28 298.88 12.07-.1 3.7 4.77s16.08 5.8 19.13 13.72 2.39 12 2.39 12l-42.44-13.92Z"></path><path fill="#25233a" d="M345.99 183.34s85.11.63 92.11 5.51c19.42 13.53 13.42 100.86 13.42 100.86l-17.33 5-14.05-74.23Z"></path><path fill="#68e1fd" d="M171.82 284.67h71.48v49.56h-71.48zM166.86 334.23h71.48v55.24h-71.48z"></path><path fill="url(#b)" d="M201.68 284.67h12.88v10.08h-12.88z"></path><path fill="url(#h)" d="M194.68 334.04h12.88v10.08h-12.88z"></path><path fill="url(#c)" d="M208.12 294.76h.99v39.29h-.99z"></path><path fill="url(#i)" d="M201.18 344.13h.99v39.29h-.99z"></path><path fill="#68e1fd" d="M172.77 159.32h85.19v59.07h-85.19z"></path><path fill="url(#d)" style="isolation:isolate" d="M172.77 159.32h85.19v59.07h-85.19z"></path><path fill="#68e1fd" d="M166.87 218.38h85.18v65.83h-85.18z"></path><path fill="url(#j)" style="isolation:isolate" d="M166.87 218.38h85.18v65.83h-85.18z"></path><path fill="url(#k)" style="isolation:isolate" d="M208.36 159.32h15.35v12.02h-15.35z"></path><path fill="url(#l)" style="isolation:isolate" d="M200.02 218.16h15.35v12.02h-15.35z"></path><path fill="url(#m)" d="M216.04 171.34h1.18v46.82h-1.18z"></path><path fill="url(#n)" d="M207.77 230.18h1.18V277h-1.18z"></path><path fill="#68e1fd" d="M79.26 244.16h97.15v67.36H79.26z"></path><path fill="url(#o)" style="isolation:isolate" d="M176.41 284.21v27.31H79.26v-67.37h87.61v40.06h9.54z"></path><path fill="url(#p)" style="isolation:isolate" d="M166.87 244.15h9.54v40.06h-9.54z"></path><path fill="#68e1fd" d="M53.85 311.52H151v75.08H53.85z"></path><path fill="url(#q)" style="isolation:isolate" d="M53.85 311.52H151v75.08H53.85z"></path><path fill="url(#r)" style="isolation:isolate" d="M119.84 244.16h17.51v13.7h-17.51z"></path><path fill="url(#s)" style="isolation:isolate" d="M91.66 311.26h17.51v13.7H91.66z"></path><path fill="url(#t)" d="M128.6 257.86h1.35v53.4h-1.35z"></path><path fill="url(#u)" d="M100.5 324.97h1.35v53.4h-1.35z"></path><path fill="#68e1fd" d="M74.64 101.46h97.15v67.36H74.64z"></path><path fill="url(#v)" style="isolation:isolate" d="M74.64 101.46h97.15v67.36H74.64z"></path><path fill="#68e1fd" d="M67.9 168.82h97.15v75.08H67.9z"></path><path fill="url(#w)" style="isolation:isolate" d="M67.9 168.82h97.15v75.08H67.9z"></path><path fill="url(#x)" style="isolation:isolate" d="M115.22 101.46h17.51v13.7h-17.51z"></path><path fill="url(#y)" style="isolation:isolate" d="M105.7 168.56h17.51v13.7H105.7z"></path><path fill="url(#z)" d="M123.97 115.16h1.35v53.4h-1.35z"></path><path fill="url(#A)" d="M114.54 182.27h1.35v53.4h-1.35z"></path><path fill="url(#B)" d="M283.71 207.44v40.23s-14.64-18.68-14-28.84 14-11.39 14-11.39Z" opacity=".2" transform="translate(-118.66 -3.77)"></path><path fill="url(#C)" d="m227.71 258.87-.67 25.34h-60.17v-65.83h5.9v-7.9l8.97 7.9 26.03 22.93 1.18 1.04 18.76 16.52z" opacity=".2"></path><path fill="#68e1fd" d="M268.65 223.23h156.48v108.5H268.65z"></path><path fill="url(#D)" style="isolation:isolate" d="M268.65 223.23h156.48v108.5H268.65z"></path><path fill="#68e1fd" d="M227.71 331.73h156.48v120.93H227.71z"></path><path fill="url(#E)" style="isolation:isolate" d="M227.71 331.73h156.48v120.93H227.71z"></path><path fill="url(#F)" style="isolation:isolate" d="M334.01 223.23h28.2v22.07h-28.2z"></path><path fill="url(#G)" style="isolation:isolate" d="M288.61 331.31h28.2v22.07h-28.2z"></path><path fill="url(#H)" d="M348.11 245.3h2.17v86.01h-2.17z"></path><path fill="url(#I)" d="M302.85 353.39h2.17v86.01h-2.17z"></path><path fill="#68e1fd" d="M222.28 452.66h-21.54V246.95a10.77 10.77 0 0 1 10.77-10.72A10.77 10.77 0 0 1 222.28 247Z"></path><path fill="#68e1fd" d="M216.23 254.46a11.29 11.29 0 0 1-15.84-2l-28.59-36.63a11.29 11.29 0 0 1 2-15.84 11.28 11.28 0 0 1 15.84 1.95l28.59 36.63a11.28 11.28 0 0 1-2 15.89ZM475.52 462.68a10 10 0 0 1-10 10h-244.3a20.05 20.05 0 0 1-20.05-20.05h264.32a10 10 0 0 1 10.03 10.05Z"></path><path fill="#68e1fd" d="M475.52 462.69h-254.3a20 20 0 0 1-17.36-10h261.63a10 10 0 0 1 10.03 10Z"></path><path fill="url(#J)" d="M340.94 456.43H319.4V250.72A10.77 10.77 0 0 1 330.17 240a10.77 10.77 0 0 1 10.77 10.77Z" transform="translate(-118.66 -3.77)" style="isolation:isolate"></path><path fill="url(#K)" d="M334.89 258.23a11.29 11.29 0 0 1-15.84-2l-28.59-36.63a11.29 11.29 0 0 1 2-15.84 11.28 11.28 0 0 1 15.84 1.95l28.59 36.63a11.28 11.28 0 0 1-2 15.89Z" transform="translate(-118.66 -3.77)" style="isolation:isolate"></path><path fill="url(#L)" d="M594.18 466.45a10 10 0 0 1-10 10h-244.3a20.05 20.05 0 0 1-20.05-20.05h264.32a10 10 0 0 1 10.03 10.05Z" transform="translate(-118.66 -3.77)" style="isolation:isolate"></path><path fill="url(#M)" d="M594.18 466.46h-254.3a20 20 0 0 1-17.36-10h261.63a10 10 0 0 1 10.03 10Z" transform="translate(-118.66 -3.77)" style="isolation:isolate"></path><circle cx="363.49" cy="499.62" r="16.6" fill="url(#N)" transform="rotate(-45 299.62 640.973)" style="isolation:isolate"></circle><circle cx="244.92" cy="496.39" r="5.03" fill="url(#O)"></circle><path fill="url(#P)" style="isolation:isolate" d="M249.15 494.25H238.3v-21.54h21.71l-10.86 21.54z"></path><circle cx="546.32" cy="499.62" r="16.6" fill="url(#Q)" transform="rotate(-67.5 484.176 586.538)" style="isolation:isolate"></circle><path fill="url(#R)" d="M541.21 500.16a5 5 0 1 0 5-5 5 5 0 0 0-5 5Z" transform="translate(-118.66 -3.77)"></path><path fill="#68e1fd" d="M423.34 494.25h10.85v-21.54h-21.71l10.86 21.54zM249.15 494.25H238.3v-21.54h21.71l-10.86 21.54z"></path><path fill="url(#S)" style="isolation:isolate" d="M249.15 494.25H238.3v-21.54h21.71l-10.86 21.54z"></path><path fill="url(#T)" style="isolation:isolate" d="M423.34 494.25h10.85v-21.54h-21.71l10.86 21.54z"></path><path fill="url(#U)" d="M176.41 286.22h24.77v103.25h-24.77z" opacity=".2"></path><path fill="url(#V)" d="M268.65 226.01h13.92V331.4h-13.92z" opacity=".2"></path><path fill="url(#W)" d="M228.15 332.98h13.92v116.88h-13.92z" opacity=".2"></path><path fill="#3f3d56" d="M329.72 182.23s-5.29 31.51 17.17 38.49 78.24 2.51 78.24 2.51l51.21 60.6 12.62-17.08-46.23-63.37a50.55 50.55 0 0 0-33.52-15.85Z"></path><path fill="url(#X)" d="m476.38 283.84 5.29 4.47 7.32-9.61-4.33-6.07-8.28 11.21z"></path><path fill="#68e1fd" d="m481.67 288.3 7.32-9.6h6s14.44-9.16 22.57-6.72 11 5.5 11 5.5l-37 25Z"></path><path fill="url(#Y)" d="m552.94 302.65 12.07-.1 3.7 4.77s16.08 5.8 19.13 13.72 2.39 12 2.39 12l-42.44-13.92Z" transform="translate(-118.66 -3.77)" style="isolation:isolate"></path><path fill="url(#Z)" d="m600.33 292.07 7.32-9.6h6s14.44-9.16 22.57-6.72 11 5.5 11 5.5l-37 25Z" transform="translate(-118.66 -3.77)" style="isolation:isolate"></path><path fill="#68e1fd" d="M385.88 184.15h-56.39a4.16 4.16 0 0 1-.65-.33c-1.75-1-6.42-4.75-5.88-15 .39-7.35 7-34.47 12.38-55.74 3.83-15.14 7.09-27.32 7.09-27.32s7-18.38 18.66-23.25c4.38-1.84 10-4.85 16.09-3.66 10.1 2 20.49 11.1 22 23.38 1.3 10.39-13.3 101.92-13.3 101.92Z"></path><path fill="url(#aa)" d="M504.54 187.92h-56.39a4.16 4.16 0 0 1-.65-.33c-1.75-1-6.42-4.75-5.88-15 .39-7.35 7-34.47 12.38-55.74 3.83-15.14 7.09-27.32 7.09-27.32s7-18.38 18.66-23.25c4.38-1.84 10-4.85 16.09-3.66 10.1 2 20.49 11.1 22 23.38 1.3 10.39-13.3 101.92-13.3 101.92Z" transform="translate(-118.66 -3.77)" style="isolation:isolate"></path><path fill="#68e1fd" d="M349.2 138.75s-12.82 33.83-3.21 44.59l-17.15.48c-1.75-1-6.42-4.75-5.88-15 .39-7.35 7-34.47 12.38-55.74l22.68-30.85Z"></path><path fill="url(#ab)" d="m495.85 62.62 6-16.53s7-26.54 23.08-27.89 29.6 10.25 22.81 28.91-10.24 21.47-16.78 22.71-12.75-1.88-12.75-1.88l-5.77 8.82s-1.85 5.09-16.59-14.14Z" transform="translate(-118.66 -3.77)"></path><path fill="#68e1fd" d="M348.77 78.01s-27.4 39.57-25.85 49.87c1.82 12.08 53.6 13.95 74.83 14.64a12.33 12.33 0 0 0 12.22-8.83v-.11a12.33 12.33 0 0 0-10.17-15.71l-35.46-4.78 16.74-31.78c4.26-13.54-11.69-24.47-22.74-15.55a37.89 37.89 0 0 0-9.57 12.25Z"></path><path fill="url(#ac)" style="isolation:isolate" d="m412.98 124.05-4.37-.53-2.96 16.45h4.86l2.47-15.92z"></path><path fill="#3f3d56" d="M397.89 38.17c-1.39-.64-.2-3-1.12-4.23-.79-1.07-2.53-.52-3.51.39a7.69 7.69 0 0 0-2.15 7.37 5.52 5.52 0 0 1 .37 2.61c-.45 1.78-2.91 2-4.69 1.56s-3.8-1.21-5.33-.18c-1.3-2.19-.4-5 .62-7.32a92 92 0 0 1 6.36-12 6.62 6.62 0 0 0 1.18-2.66c.16-1.45-.67-2.8-1.24-4.14-2.64-6.34.96-14.04 6.96-17.34s13.48-2.7 19.71 0c2.27 1 4.46 2.3 6.9 2.8 3.6.73 7.32-.35 11-.28a18 18 0 0 1 14.42 8 18 18 0 0 1 1.51 16.48c-1.29 3.12-4.45 6.16-7.64 5.07-1.31-.44-2.3-1.5-3.51-2.18s-2.95-.83-3.77.28a3.4 3.4 0 0 1-.62.83c-1.07.79-2-1.08-2.61-2.25-1.44-2.66-5.18-2.61-8.2-2.52a40.94 40.94 0 0 1-8.7-.7c-2.06-.39-4.85-1.79-7-1.21-2.36.61-8.64 11.75-8.94 11.62Z"></path><path fill="url(#ad)" style="isolation:isolate" d="m472.98 111.12 2.63 3.19-59.17 42.66-2.36-3.34 58.9-42.51z"></path><path fill="url(#ae)" d="M545.42 153.3s1 3.28 6.16 1.62 14.55-7.16 15.27-8.63 8.81-9.72 8.31-14.43Z" transform="translate(-118.66 -3.77)"></path></svg>
						<div class="flex justify-start">
							<div class="w-3/6 flex justify-start">
							<div>
							<img class="object-scale-down" src="./resources/goblogo.png">
							</div>
							<div>
							<img class="object-scale-down" src="./resources/dirlogo.png">
							</div>
							</div>
						</div>
					</div>
					  <div class="font-karla bg-opacity-80 flex flex-col pt-16 md:pt-24 justify-evenly pb-12 gap-3 md:w-5/12 px-10 md:px-16 bg-gray-100">
						  <div>					  
						  <h1 class="text-3xl pb-4 font-rubik">Iniciar Sesión</h1>
						  <p class="text-gray-400">Bienvenid@ al Sistema de Control y Seguimiento de Bienes de la Oficina de Administración y Gestión de Bienes</p>
						  </div>
						      <form id="box" action="" method="POST">
						      <div class="flex flex-col">
						        <label class="text-gray-400" for="nombre_usuario">Usuario</label>
						        <input class="bg-gray-200 px-6 py-3" required name="nombre_usuario" placeholder="Usuario" type="text">
						      </div>
						      <br>
						      <div class="flex flex-col">
						        <label class="text-gray-400" for="contrasena">Contraseña</label>
						        <input required placeholder="******" class="bg-gray-200 px-6 py-3" name="contrasena" type="password">
						      </div>
						      <br>
						        <input  value="login" name="formEnviada" type="hidden">
						      <input class="w-full px-6 py-3 cursor-pointer hover:bg-blue-300 hover:text-blue-600 bg-blue-600 text-white" value="Iniciar Sesión" type="submit">
						    </form>
						    <a class="text-blue-500 hover:text-blue-300" href="/recuperar.php">¿Olvidaste tu contraseña?</a>
						    <a class="text-blue-500 hover:text-blue-300" href="/manual.html" target="_blank">Manual de Usuario</a>
						    					  						<div class="flex md:hidden justify-start">
							<div class="w-6/6 flex justify-start">
							<div>
							<img class="object-scale-down" src="./resources/goblogo.png">
							</div>
							<div>
							<img class="object-scale-down" src="./resources/dirlogo.png">
							</div>
							</div>
						</div>
						</div>
					  </div>
					</body>
				</html>
		';
	}


	function muestraRegistro($header){
		echo'
				<html lang="en">
					<head>
					    <meta charset="UTF-8">
					    <meta name="viewport" content="width=device-width, initial-scale=1.0">
					    <title>Registro de Usuario</title>
					    <link rel="stylesheet" href="css/output.css">
					</head>
					<body class="w-11/12 mx-auto font-karla">
					 '.$header.'
					 <h1 class="mt-12 mb-4 md:mt-28 md:mb-10 text-4xl md:text-6xl font-rubik text-sky-900 font-bold">Registro de Usuario</h1>
					      <form id="box" class="flex flex-col gap-6 mt-12 mx-auto md:w-6/12 bg-gray-100 bg-opacity-80 rounded-xl p-4 md:p-10 font-karla text-gray-400" action="" method="POST">
					      <div class="flex flex-col gap-1">
					        <label class="label "for="nombre_usuario">Nombre de Usuario</label>
					        <input required class="w-64 md:w-96 bg-gray-50 shadow-inner px-4 py-2" name="nombre_usuario" type="text">
					      </div>
					      
					      <div class="flex flex-col gap-1">
					        <label class="label" for="clave">Contraseña</label>
					        <input required class="w-64 md:w-96 bg-gray-50 shadow-inner px-4 py-2" name="contrasena" type="password">
					      </div>
					      							<div class="py-2 w-36">
							  <label class="label" for="n_acceso">Nivel de Acceso</label>	
							  <select class="py-2" name="n_acceso">
							    <option value="3">Superadministrador</option>
							    <option value="2">Administrador</option>
							    <option value="1">Usuario</option>
							  </select>
							</div>
					      <div class="flex flex-col gap-1">
					        <label class="label" for="pregunta">Pregunta de Seguridad</label>
					        <select class="w-64 md:w-96 py-2" name="pregunta">
							    <option value="¿Cual es tu ciudad natal?">¿Cual es tu ciudad natal?</option>
							    <option value="¿Cual es tu libro favorito?">¿Cual es tu libro favorito?</option>
							    <option value="¿Cual fue el nombre de tu primera mascota?">¿Cual fue el nombre de tu primera mascota?</option>
							    <option value="¿Cual es tu comida favorita?">¿Cual es tu comida favorita?</option>
							    <option value="¿Cual es tu deporte favorito?">¿Cual es tu deporte favorito?</option>
							  </select>
					      </div>
					      <div class="flex flex-col gap-1">
					        <label class="label" for="clave">Respuesta</label>
					        <input required class="w-64 md:w-96 bg-gray-50 shadow-inner px-4 py-2" name="respuesta" type="text">
					      </div>
					      
					        <input  value="registro" name="formEnviada" type="hidden">
					       
			<div class="flex justify-end">
				<input class="bg-blue-500 cursor-pointer text-white hover:text-blue-950 rounded-xl hover:bg-white px-4 py-2" value="Registrar" type="submit">
			</div>
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
		echo'                        <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Registro Inicial</title>                     
                        <link href="./css/output.css" rel="stylesheet">
                    </head>
                    <body>
                    <div class="flex h-full justify-end">
                    <div class="w-full h-full flex flex-col justify-end items-center">
                    <svg class="w-7/12 mb-16" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 548.27 592.23" id="logistics"><defs><linearGradient id="f" x1="210.14" x2="771.37" y1="263.34" y2="830.29" gradientUnits="userSpaceOnUse"><stop offset="0" stop-opacity="0"></stop><stop offset=".2" stop-color="#09080c" stop-opacity=".14"></stop><stop offset=".57" stop-color="#201f2b" stop-opacity=".5"></stop><stop offset=".99" stop-color="#3f3d56"></stop></linearGradient><linearGradient id="e" x1="549.95" x2="548.92" y1="157.43" y2="155.02" gradientUnits="userSpaceOnUse"><stop offset=".09" stop-color="#68e1fd"></stop><stop offset=".2" stop-color="#65d5f1"></stop><stop offset=".4" stop-color="#5db7d2"></stop><stop offset=".66" stop-color="#51859f"></stop><stop offset=".97" stop-color="#404059"></stop><stop offset=".99" stop-color="#3f3d56"></stop></linearGradient><linearGradient id="a" x1="528.8" x2="567.69" y1="133.77" y2="133.77" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#ecc4d7"></stop><stop offset=".42" stop-color="#efd4d1"></stop><stop offset="1" stop-color="#f2eac9"></stop></linearGradient><linearGradient id="g" x1="-1217.56" x2="-1226.39" y1="2615.31" y2="2624.21" gradientTransform="rotate(52.4 1989.299 3144.169)" xlink:href="#a"></linearGradient><linearGradient id="b" x1="201.68" x2="214.56" y1="289.71" y2="289.71" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#183866"></stop><stop offset="1" stop-color="#1a7fc1"></stop></linearGradient><linearGradient id="h" x1="194.68" x2="207.56" y1="339.08" y2="339.08" xlink:href="#b"></linearGradient><linearGradient id="c" x1="223.94" x2="205.18" y1="310.91" y2="315.18" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#68e1fd"></stop><stop offset="1" stop-color="#69b9eb"></stop></linearGradient><linearGradient id="i" x1="217.01" x2="198.25" y1="360.28" y2="364.55" xlink:href="#c"></linearGradient><linearGradient id="d" x1="193.69" x2="271.42" y1="165.01" y2="250.51" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#fff" stop-opacity="0"></stop><stop offset=".09" stop-color="#fff" stop-opacity=".05"></stop><stop offset=".24" stop-color="#fff" stop-opacity=".17"></stop><stop offset=".45" stop-color="#fff" stop-opacity=".37"></stop><stop offset=".69" stop-color="#fff" stop-opacity=".65"></stop><stop offset=".98" stop-color="#fff"></stop><stop offset=".98" stop-color="#fff"></stop></linearGradient><linearGradient id="j" x1="173.29" x2="266.57" y1="208.68" y2="318.61" xlink:href="#d"></linearGradient><linearGradient id="k" x1="219.12" x2="211.4" y1="-200.44" y2="715.79" xlink:href="#e"></linearGradient><linearGradient id="l" x1="211.27" x2="203.55" y1="-200.5" y2="715.72" xlink:href="#e"></linearGradient><linearGradient id="m" x1="234.89" x2="212.53" y1="190.59" y2="195.68" xlink:href="#c"></linearGradient><linearGradient id="n" x1="226.62" x2="204.27" y1="249.43" y2="254.52" xlink:href="#c"></linearGradient><linearGradient id="o" x1="88.05" x2="162.09" y1="236.68" y2="323.71" xlink:href="#d"></linearGradient><linearGradient id="p" x1="152.74" x2="193.39" y1="241.97" y2="289.75" xlink:href="#d"></linearGradient><linearGradient id="q" x1="84.41" x2="167.15" y1="330.44" y2="415.94" xlink:href="#d"></linearGradient><linearGradient id="r" x1="132.41" x2="124.69" y1="-201.17" y2="715.06" xlink:href="#e"></linearGradient><linearGradient id="s" x1="104.79" x2="97.07" y1="-201.4" y2="714.82" xlink:href="#e"></linearGradient><linearGradient id="t" x1="150.1" x2="124.6" y1="279.82" y2="285.62" xlink:href="#c"></linearGradient><linearGradient id="u" x1="122" x2="96.5" y1="346.92" y2="352.73" xlink:href="#c"></linearGradient><linearGradient id="v" x1="73.36" x2="195.51" y1="100.24" y2="185.75" xlink:href="#d"></linearGradient><linearGradient id="w" x1="58.2" x2="185.89" y1="158.73" y2="263.11" xlink:href="#d"></linearGradient><linearGradient id="x" x1="126.58" x2="118.86" y1="-201.22" y2="715.01" xlink:href="#e"></linearGradient><linearGradient id="y" x1="117.63" x2="109.92" y1="-201.29" y2="714.93" xlink:href="#e"></linearGradient><linearGradient id="z" x1="145.48" x2="119.98" y1="137.12" y2="142.93" xlink:href="#c"></linearGradient><linearGradient id="A" x1="136.05" x2="110.55" y1="204.22" y2="210.03" xlink:href="#c"></linearGradient><linearGradient id="B" x1="290.11" x2="267.85" y1="217.89" y2="240.34" xlink:href="#b"></linearGradient><linearGradient id="C" x1="203.56" x2="156.16" y1="243.66" y2="291.46" xlink:href="#b"></linearGradient><linearGradient id="D" x1="317.25" x2="411.63" y1="232.15" y2="376.5" xlink:href="#d"></linearGradient><linearGradient id="E" x1="227.71" x2="384.2" y1="392.2" y2="392.2" xlink:href="#d"></linearGradient><linearGradient id="F" x1="351.76" x2="344.05" y1="-199.32" y2="716.9" xlink:href="#e"></linearGradient><linearGradient id="G" x1="307.28" x2="299.56" y1="-199.69" y2="716.53" xlink:href="#e"></linearGradient><linearGradient id="H" x1="382.75" x2="341.68" y1="280.67" y2="290.02" xlink:href="#c"></linearGradient><linearGradient id="I" x1="337.49" x2="296.42" y1="388.75" y2="398.1" xlink:href="#c"></linearGradient><linearGradient id="J" x1="319.4" x2="340.94" y1="348.19" y2="348.19" xlink:href="#d"></linearGradient><linearGradient id="K" x1="288.07" x2="339.23" y1="231.02" y2="231.02" xlink:href="#d"></linearGradient><linearGradient id="L" x1="319.83" x2="594.18" y1="466.45" y2="466.45" xlink:href="#d"></linearGradient><linearGradient id="M" x1="322.52" x2="594.18" y1="461.44" y2="461.44" xlink:href="#d"></linearGradient><linearGradient id="N" x1="359.62" x2="364.69" y1="451.83" y2="514.36" gradientTransform="rotate(45 363.49 499.622)" xlink:href="#e"></linearGradient><linearGradient id="O" x1="244.78" x2="246.32" y1="494.74" y2="513.67" xlink:href="#b"></linearGradient><linearGradient id="P" x1="238.3" x2="260.01" y1="483.48" y2="483.48" xlink:href="#d"></linearGradient><linearGradient id="Q" x1="3325.19" x2="3330.26" y1="451.83" y2="514.36" gradientTransform="matrix(-.38 -.92 -.92 .38 2281.89 3384.08)" xlink:href="#e"></linearGradient><linearGradient id="R" x1="3329.01" x2="3330.55" y1="498.51" y2="517.44" gradientTransform="matrix(-1 0 0 1 3875.38 0)" xlink:href="#b"></linearGradient><linearGradient id="S" x1="234.89" x2="256.5" y1="478.53" y2="486.03" xlink:href="#d"></linearGradient><linearGradient id="T" x1="412.48" x2="434.19" y1="483.48" y2="483.48" xlink:href="#d"></linearGradient><linearGradient id="U" x1="220.75" x2="156.85" y1="305.63" y2="370.06" xlink:href="#b"></linearGradient><linearGradient id="V" x1="305.4" x2="245.81" y1="248.65" y2="308.74" xlink:href="#b"></linearGradient><linearGradient id="W" x1="267.78" x2="202.44" y1="358.48" y2="424.37" xlink:href="#b"></linearGradient><linearGradient id="X" x1="487.83" x2="478.11" y1="275.77" y2="285.56" xlink:href="#a"></linearGradient><linearGradient id="Y" x1="547.79" x2="590.29" y1="317.81" y2="317.81" xlink:href="#f"></linearGradient><linearGradient id="Z" x1="600.33" x2="647.23" y1="290.79" y2="290.79" xlink:href="#f"></linearGradient><linearGradient id="aa" x1="526.29" x2="326.57" y1="117.86" y2="168.65" xlink:href="#f"></linearGradient><linearGradient id="ab" x1="869.38" x2="925.86" y1="-109.47" y2="-109.47" gradientTransform="rotate(-7 2034.654 3080.446)" xlink:href="#a"></linearGradient><linearGradient id="ac" x1="417.45" x2="402.02" y1="117.36" y2="145.49" xlink:href="#e"></linearGradient><linearGradient id="ad" x1="439.96" x2="446.39" y1="125.66" y2="136.59" xlink:href="#e"></linearGradient><linearGradient id="ae" x1="566.8" x2="535.15" y1="135.85" y2="177.35" xlink:href="#a"></linearGradient></defs><path fill="#68e1fd" d="M208 584.23c-117.3-17-188.48-100.1-204.52-199-9.86-60.76 1.08-127.51 34.92-188.59C127.34 36.08 351.99 32.35 351.99 32.35c129.28-6.41 103.82 211.93 96.34 259-4 24.93 21.18 58.21 47.3 93.89 23.15 31.64 47.06 65.18 52.13 96.48C558.55 548.28 397.34 611.74 208 584.23Z" opacity=".18"></path><path fill="#68e1fd" d="M208 585.65c-117.3-17-188.48-100.12-204.52-199h492.15c23.15 31.63 47.06 65.17 52.13 96.47C558.55 549.69 397.34 613.14 208 585.65Z"></path><path fill="url(#f)" d="M326.66 589.42c-117.3-17-188.48-100.12-204.52-199h492.15c23.15 31.63 47.06 65.17 52.13 96.47C677.21 553.46 516 616.91 326.66 589.42Z" transform="translate(-118.66 -3.77)" style="isolation:isolate"></path><path fill="#1d2741" d="M209.42 500.53c-5.93-.12-13-.92-15.73-6.22-1.34-2.63-1.17-5.74-1.62-8.66-4-26.3-50-25.15-57.55-50.65-.65-2.21-.86-4.85.67-6.56 1.37-1.51 3.62-1.76 5.65-1.9l66.85-4.69c2.7-9.88-7.86-18.31-17.62-21.44-43.71-14-93.33 14.77-136.26-1.52-2.29-.87-4.71-2-5.79-4.22-3.1-6.33 6.84-10.67 13.88-10.78l169.24-2.66c3.56-.06 7.2-.1 10.6 1 5.23 1.69 9.21 5.85 13 9.86 16.03 17.14 32.6 34.55 53.6 45.05 38.5 19.24 84.48 12.13 126.75 20.25 12.25 2.35 27.25 10.76 24.25 22.84-9.58 37.76-80.87 23.64-106.53 23.13Z" opacity=".18"></path><path fill="#68e1fd" d="m399.19 82.23.08 32.94L412.12 149l10.46-5a5.78 5.78 0 0 1 7.5 2.3l2.84 4.9a7.57 7.57 0 0 1-3 10.51c-7.56 4-23 13.58-25.89 13.58-4.43 0-17.17-19.83-17.17-19.83Z"></path><path fill="url(#e)" d="M545.42 147.4s-4.73 3.67-2.5 7.7 9.69 4.19 9.69 4.19 6.13-10.85-7.19-11.89Z" transform="translate(-118.66 -3.77)" style="isolation:isolate"></path><path fill="url(#a)" d="M548.8 125.08a44 44 0 0 1 6.91 1.28l10.42 2.52a2.21 2.21 0 0 1 1.38.72c.56.85-.3 2-1.24 2.35-3 1.22-6.8-1-9.48.75-1.5 1-2 3-3.2 4.34-1.52 1.84-4 2.58-6.38 2.84s-4.8.15-7.12.69c-3.49.81-7.23 3.06-10.36 1.31a1.94 1.94 0 0 1-.78-.72c-.39-.74.08-1.63.46-2.37a13.08 13.08 0 0 0 1.4-6.79c-.15-3.29-.24-3.05 3.15-4.24 4.75-1.68 9.74-3.08 14.84-2.68Z" transform="translate(-118.66 -3.77)"></path><path fill="url(#g)" d="m434.56 291.96-.29 6.92 12.08-.1.9-9.48-12.69 2.66z"></path><path fill="#68e1fd" d="m434.28 298.88 12.07-.1 3.7 4.77s16.08 5.8 19.13 13.72 2.39 12 2.39 12l-42.44-13.92Z"></path><path fill="#25233a" d="M345.99 183.34s85.11.63 92.11 5.51c19.42 13.53 13.42 100.86 13.42 100.86l-17.33 5-14.05-74.23Z"></path><path fill="#68e1fd" d="M171.82 284.67h71.48v49.56h-71.48zM166.86 334.23h71.48v55.24h-71.48z"></path><path fill="url(#b)" d="M201.68 284.67h12.88v10.08h-12.88z"></path><path fill="url(#h)" d="M194.68 334.04h12.88v10.08h-12.88z"></path><path fill="url(#c)" d="M208.12 294.76h.99v39.29h-.99z"></path><path fill="url(#i)" d="M201.18 344.13h.99v39.29h-.99z"></path><path fill="#68e1fd" d="M172.77 159.32h85.19v59.07h-85.19z"></path><path fill="url(#d)" style="isolation:isolate" d="M172.77 159.32h85.19v59.07h-85.19z"></path><path fill="#68e1fd" d="M166.87 218.38h85.18v65.83h-85.18z"></path><path fill="url(#j)" style="isolation:isolate" d="M166.87 218.38h85.18v65.83h-85.18z"></path><path fill="url(#k)" style="isolation:isolate" d="M208.36 159.32h15.35v12.02h-15.35z"></path><path fill="url(#l)" style="isolation:isolate" d="M200.02 218.16h15.35v12.02h-15.35z"></path><path fill="url(#m)" d="M216.04 171.34h1.18v46.82h-1.18z"></path><path fill="url(#n)" d="M207.77 230.18h1.18V277h-1.18z"></path><path fill="#68e1fd" d="M79.26 244.16h97.15v67.36H79.26z"></path><path fill="url(#o)" style="isolation:isolate" d="M176.41 284.21v27.31H79.26v-67.37h87.61v40.06h9.54z"></path><path fill="url(#p)" style="isolation:isolate" d="M166.87 244.15h9.54v40.06h-9.54z"></path><path fill="#68e1fd" d="M53.85 311.52H151v75.08H53.85z"></path><path fill="url(#q)" style="isolation:isolate" d="M53.85 311.52H151v75.08H53.85z"></path><path fill="url(#r)" style="isolation:isolate" d="M119.84 244.16h17.51v13.7h-17.51z"></path><path fill="url(#s)" style="isolation:isolate" d="M91.66 311.26h17.51v13.7H91.66z"></path><path fill="url(#t)" d="M128.6 257.86h1.35v53.4h-1.35z"></path><path fill="url(#u)" d="M100.5 324.97h1.35v53.4h-1.35z"></path><path fill="#68e1fd" d="M74.64 101.46h97.15v67.36H74.64z"></path><path fill="url(#v)" style="isolation:isolate" d="M74.64 101.46h97.15v67.36H74.64z"></path><path fill="#68e1fd" d="M67.9 168.82h97.15v75.08H67.9z"></path><path fill="url(#w)" style="isolation:isolate" d="M67.9 168.82h97.15v75.08H67.9z"></path><path fill="url(#x)" style="isolation:isolate" d="M115.22 101.46h17.51v13.7h-17.51z"></path><path fill="url(#y)" style="isolation:isolate" d="M105.7 168.56h17.51v13.7H105.7z"></path><path fill="url(#z)" d="M123.97 115.16h1.35v53.4h-1.35z"></path><path fill="url(#A)" d="M114.54 182.27h1.35v53.4h-1.35z"></path><path fill="url(#B)" d="M283.71 207.44v40.23s-14.64-18.68-14-28.84 14-11.39 14-11.39Z" opacity=".2" transform="translate(-118.66 -3.77)"></path><path fill="url(#C)" d="m227.71 258.87-.67 25.34h-60.17v-65.83h5.9v-7.9l8.97 7.9 26.03 22.93 1.18 1.04 18.76 16.52z" opacity=".2"></path><path fill="#68e1fd" d="M268.65 223.23h156.48v108.5H268.65z"></path><path fill="url(#D)" style="isolation:isolate" d="M268.65 223.23h156.48v108.5H268.65z"></path><path fill="#68e1fd" d="M227.71 331.73h156.48v120.93H227.71z"></path><path fill="url(#E)" style="isolation:isolate" d="M227.71 331.73h156.48v120.93H227.71z"></path><path fill="url(#F)" style="isolation:isolate" d="M334.01 223.23h28.2v22.07h-28.2z"></path><path fill="url(#G)" style="isolation:isolate" d="M288.61 331.31h28.2v22.07h-28.2z"></path><path fill="url(#H)" d="M348.11 245.3h2.17v86.01h-2.17z"></path><path fill="url(#I)" d="M302.85 353.39h2.17v86.01h-2.17z"></path><path fill="#68e1fd" d="M222.28 452.66h-21.54V246.95a10.77 10.77 0 0 1 10.77-10.72A10.77 10.77 0 0 1 222.28 247Z"></path><path fill="#68e1fd" d="M216.23 254.46a11.29 11.29 0 0 1-15.84-2l-28.59-36.63a11.29 11.29 0 0 1 2-15.84 11.28 11.28 0 0 1 15.84 1.95l28.59 36.63a11.28 11.28 0 0 1-2 15.89ZM475.52 462.68a10 10 0 0 1-10 10h-244.3a20.05 20.05 0 0 1-20.05-20.05h264.32a10 10 0 0 1 10.03 10.05Z"></path><path fill="#68e1fd" d="M475.52 462.69h-254.3a20 20 0 0 1-17.36-10h261.63a10 10 0 0 1 10.03 10Z"></path><path fill="url(#J)" d="M340.94 456.43H319.4V250.72A10.77 10.77 0 0 1 330.17 240a10.77 10.77 0 0 1 10.77 10.77Z" transform="translate(-118.66 -3.77)" style="isolation:isolate"></path><path fill="url(#K)" d="M334.89 258.23a11.29 11.29 0 0 1-15.84-2l-28.59-36.63a11.29 11.29 0 0 1 2-15.84 11.28 11.28 0 0 1 15.84 1.95l28.59 36.63a11.28 11.28 0 0 1-2 15.89Z" transform="translate(-118.66 -3.77)" style="isolation:isolate"></path><path fill="url(#L)" d="M594.18 466.45a10 10 0 0 1-10 10h-244.3a20.05 20.05 0 0 1-20.05-20.05h264.32a10 10 0 0 1 10.03 10.05Z" transform="translate(-118.66 -3.77)" style="isolation:isolate"></path><path fill="url(#M)" d="M594.18 466.46h-254.3a20 20 0 0 1-17.36-10h261.63a10 10 0 0 1 10.03 10Z" transform="translate(-118.66 -3.77)" style="isolation:isolate"></path><circle cx="363.49" cy="499.62" r="16.6" fill="url(#N)" transform="rotate(-45 299.62 640.973)" style="isolation:isolate"></circle><circle cx="244.92" cy="496.39" r="5.03" fill="url(#O)"></circle><path fill="url(#P)" style="isolation:isolate" d="M249.15 494.25H238.3v-21.54h21.71l-10.86 21.54z"></path><circle cx="546.32" cy="499.62" r="16.6" fill="url(#Q)" transform="rotate(-67.5 484.176 586.538)" style="isolation:isolate"></circle><path fill="url(#R)" d="M541.21 500.16a5 5 0 1 0 5-5 5 5 0 0 0-5 5Z" transform="translate(-118.66 -3.77)"></path><path fill="#68e1fd" d="M423.34 494.25h10.85v-21.54h-21.71l10.86 21.54zM249.15 494.25H238.3v-21.54h21.71l-10.86 21.54z"></path><path fill="url(#S)" style="isolation:isolate" d="M249.15 494.25H238.3v-21.54h21.71l-10.86 21.54z"></path><path fill="url(#T)" style="isolation:isolate" d="M423.34 494.25h10.85v-21.54h-21.71l10.86 21.54z"></path><path fill="url(#U)" d="M176.41 286.22h24.77v103.25h-24.77z" opacity=".2"></path><path fill="url(#V)" d="M268.65 226.01h13.92V331.4h-13.92z" opacity=".2"></path><path fill="url(#W)" d="M228.15 332.98h13.92v116.88h-13.92z" opacity=".2"></path><path fill="#3f3d56" d="M329.72 182.23s-5.29 31.51 17.17 38.49 78.24 2.51 78.24 2.51l51.21 60.6 12.62-17.08-46.23-63.37a50.55 50.55 0 0 0-33.52-15.85Z"></path><path fill="url(#X)" d="m476.38 283.84 5.29 4.47 7.32-9.61-4.33-6.07-8.28 11.21z"></path><path fill="#68e1fd" d="m481.67 288.3 7.32-9.6h6s14.44-9.16 22.57-6.72 11 5.5 11 5.5l-37 25Z"></path><path fill="url(#Y)" d="m552.94 302.65 12.07-.1 3.7 4.77s16.08 5.8 19.13 13.72 2.39 12 2.39 12l-42.44-13.92Z" transform="translate(-118.66 -3.77)" style="isolation:isolate"></path><path fill="url(#Z)" d="m600.33 292.07 7.32-9.6h6s14.44-9.16 22.57-6.72 11 5.5 11 5.5l-37 25Z" transform="translate(-118.66 -3.77)" style="isolation:isolate"></path><path fill="#68e1fd" d="M385.88 184.15h-56.39a4.16 4.16 0 0 1-.65-.33c-1.75-1-6.42-4.75-5.88-15 .39-7.35 7-34.47 12.38-55.74 3.83-15.14 7.09-27.32 7.09-27.32s7-18.38 18.66-23.25c4.38-1.84 10-4.85 16.09-3.66 10.1 2 20.49 11.1 22 23.38 1.3 10.39-13.3 101.92-13.3 101.92Z"></path><path fill="url(#aa)" d="M504.54 187.92h-56.39a4.16 4.16 0 0 1-.65-.33c-1.75-1-6.42-4.75-5.88-15 .39-7.35 7-34.47 12.38-55.74 3.83-15.14 7.09-27.32 7.09-27.32s7-18.38 18.66-23.25c4.38-1.84 10-4.85 16.09-3.66 10.1 2 20.49 11.1 22 23.38 1.3 10.39-13.3 101.92-13.3 101.92Z" transform="translate(-118.66 -3.77)" style="isolation:isolate"></path><path fill="#68e1fd" d="M349.2 138.75s-12.82 33.83-3.21 44.59l-17.15.48c-1.75-1-6.42-4.75-5.88-15 .39-7.35 7-34.47 12.38-55.74l22.68-30.85Z"></path><path fill="url(#ab)" d="m495.85 62.62 6-16.53s7-26.54 23.08-27.89 29.6 10.25 22.81 28.91-10.24 21.47-16.78 22.71-12.75-1.88-12.75-1.88l-5.77 8.82s-1.85 5.09-16.59-14.14Z" transform="translate(-118.66 -3.77)"></path><path fill="#68e1fd" d="M348.77 78.01s-27.4 39.57-25.85 49.87c1.82 12.08 53.6 13.95 74.83 14.64a12.33 12.33 0 0 0 12.22-8.83v-.11a12.33 12.33 0 0 0-10.17-15.71l-35.46-4.78 16.74-31.78c4.26-13.54-11.69-24.47-22.74-15.55a37.89 37.89 0 0 0-9.57 12.25Z"></path><path fill="url(#ac)" style="isolation:isolate" d="m412.98 124.05-4.37-.53-2.96 16.45h4.86l2.47-15.92z"></path><path fill="#3f3d56" d="M397.89 38.17c-1.39-.64-.2-3-1.12-4.23-.79-1.07-2.53-.52-3.51.39a7.69 7.69 0 0 0-2.15 7.37 5.52 5.52 0 0 1 .37 2.61c-.45 1.78-2.91 2-4.69 1.56s-3.8-1.21-5.33-.18c-1.3-2.19-.4-5 .62-7.32a92 92 0 0 1 6.36-12 6.62 6.62 0 0 0 1.18-2.66c.16-1.45-.67-2.8-1.24-4.14-2.64-6.34.96-14.04 6.96-17.34s13.48-2.7 19.71 0c2.27 1 4.46 2.3 6.9 2.8 3.6.73 7.32-.35 11-.28a18 18 0 0 1 14.42 8 18 18 0 0 1 1.51 16.48c-1.29 3.12-4.45 6.16-7.64 5.07-1.31-.44-2.3-1.5-3.51-2.18s-2.95-.83-3.77.28a3.4 3.4 0 0 1-.62.83c-1.07.79-2-1.08-2.61-2.25-1.44-2.66-5.18-2.61-8.2-2.52a40.94 40.94 0 0 1-8.7-.7c-2.06-.39-4.85-1.79-7-1.21-2.36.61-8.64 11.75-8.94 11.62Z"></path><path fill="url(#ad)" style="isolation:isolate" d="m472.98 111.12 2.63 3.19-59.17 42.66-2.36-3.34 58.9-42.51z"></path><path fill="url(#ae)" d="M545.42 153.3s1 3.28 6.16 1.62 14.55-7.16 15.27-8.63 8.81-9.72 8.31-14.43Z" transform="translate(-118.66 -3.77)"></path></svg>
                        <div class="flex justify-start">
                            <div class="w-3/6 flex justify-start">
                            <div>
                            <img class="object-scale-down" src="./resources/goblogo.png">
                            </div>
                            <div>
                            <img class="object-scale-down" src="./resources/dirlogo.png">
                            </div>
                            </div>
                        </div>
                    </div>
                      <div class="font-karla bg-opacity-80 flex flex-col pt-16 justify-evenly pb-12 gap-3 w-5/12 px-16 bg-gray-100">
                          <div>                   
                          <h1 class="text-3xl pb-4 font-rubik">Registro Inicial</h1>
                          <p class="text-gray-400">Bienvenid@ al Sistema de Control y Seguimiento de Bienes de la Oficina de Administración y Gestión de Bienes</p>
                          </div>
                              <form id="box" action="" method="POST">
                              <div class="flex flex-col">
                                <label class="text-gray-400" for="nombre_usuario">Usuario</label>
                                <input class="bg-gray-200 px-6 py-3" required name="nombre_usuario" placeholder="Usuario" type="text">
                              </div>
                              <br>
                              <div class="flex flex-col">
                                <label class="text-gray-400" for="contrasena">Contraseña</label>
                                <input required placeholder="******" class="bg-gray-200 px-6 py-3" name="contrasena" type="password">
                              </div>
                              <br>
                              <div class="flex flex-col text-gray-400">
                            <label class="label" for="pregunta">Pregunta de Seguridad</label>
                            <select class="w-96 bg-gray-200 py-3" name="pregunta">
                                <option value="¿Cual es tu ciudad natal?">¿Cual es tu ciudad natal?</option>
                                <option value="¿Cual es tu libro favorito?">¿Cual es tu libro favorito?</option>
                                <option value="¿Cual fue el nombre de tu primera mascota?">¿Cual fue el nombre de tu primera mascota?</option>
                                <option value="¿Cual es tu comida favorita?">¿Cual es tu comida favorita?</option>
                                <option value="¿Cual es tu deporte favorito?">¿Cual es tu deporte favorito?</option>
                              </select>
                          </div>
                          <br>
                          <div class="flex flex-col text-gray-400">
                            <label class="label" for="clave">Respuesta</label>
                            <input required class="bg-gray-200 px-6 py-3" name="respuesta" type="text">
                          </div>
                                <input  value="registroInicial" name="formEnviada" type="hidden">
                                <input  value="3" name="n_acceso" type="hidden">
                                <br>
                              <input class="w-full px-6 py-3 cursor-pointer hover:bg-blue-300 hover:text-blue-600 bg-blue-600 text-white" value="Iniciar Sesión" type="submit">
                            </form>
                            <a class="text-blue-500 hover:text-blue-300" href="/manual.html" target="_blank">Manual de Usuario</a>
                        </div>
                      </div>
                    </body>
                </html>';
		
	}

 ?>