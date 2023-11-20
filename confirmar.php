<?php 
    header('Content-Type: text/html; charset=UTF-8');
	$conec = mysqli_connect('localhost', 'root', '','inventario');
	if(! $conec) {
		die ('No se pudo conectar con la base de datos: '. mysqli_connect_errno());
	}
    include './alerta.php';
	session_start();
	if(isset($_SESSION['articulo'])){
        $articulo = $_SESSION['articulo'];
        $serial = $articulo["serial"];
        $destino = $_SESSION['destino'];
        $fregreso = $_SESSION['fregreso'];
        $operacion = $_SESSION['operacion'];
    }else{
        session_destroy();
        header('Location: index.php');
 	}
 	if(isset($_POST['button1'])){
        $destino = mysqli_real_escape_string($conec,$destino);
        $articulo['ubicacion'] = str_replace("En préstamo a ", "", $articulo['ubicacion']);

        $queryHistorial = "INSERT INTO historial_operaciones(tipo_operacion, serial, destino) VALUES('".$operacion."','".$serial."','".$destino."')";

 		if($operacion === "Préstamo"){ 
            $fregreso = $fregreso." 23:59:59";
            $queryUbicacion = "UPDATE articulos_en_inventario SET ubicacion = 'En préstamo a ".$destino."' WHERE serial = '".$serial."'";
            $queryOperacion = "INSERT INTO articulos_en_prestamo(serial, fecha_de_retorno, destino) VALUES('".$serial."','".$fregreso."','".$destino."')";
        }else if($operacion === "Asignación"){
            $queryUbicacion = "DELETE FROM articulos_en_inventario WHERE serial = '".$serial."'";
        }else if($operacion === "Retorno"){
            $queryUbicacion = "UPDATE articulos_en_inventario SET ubicacion = '".$destino."' WHERE serial = '".$serial."'";
            $queryOperacion = "DELETE FROM articulos_en_prestamo WHERE serial = '".$serial."'";
        }else if($operacion === "Extensión"){
            $fregreso = $fregreso." 23:59:59";
            $queryUbicacion = "UPDATE articulos_en_prestamo SET fecha_de_retorno = '".$fregreso."' WHERE serial = '".$serial."'";
            $queryHistorial = "INSERT INTO historial_operaciones(tipo_operacion, serial, destino) VALUES('".$operacion."','".$serial."','".$articulo['ubicacion']."')"; 
        }
        mysqli_begin_transaction($conec,MYSQLI_TRANS_START_READ_WRITE);
        if($queryOperacion){ 
            mysqli_query($conec,$queryOperacion);
        }
        mysqli_query($conec,$queryUbicacion);
        mysqli_query($conec,$queryHistorial);
        mysqli_commit($conec);
        mysqli_close($conec);

        if($_POST['pdf'] == "true"){
            header('Location: pdf.php');
        }else{
            session_destroy();
            header('Location: index.php');
        }
    } 
    if(isset($_POST['button2'])) { 
        session_destroy();
        header('Location: transferencia.php');
    } 

 	echo '
 	 <!DOCTYPE html>
 	 <html lang="en">
	 <head>
	 	<meta charset="UTF-8">
	 	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	 	<title>Confirmar Operación</title>
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
     <div id="box" class="box is-centered has-text-justified">
        <div>
    	    Está a punto de realizar la siguiente operación: <br>
    	 	<strong>Tipo de Operación:</strong> '.$operacion.' <br>
    	 	<strong>Articulo:</strong> '.$articulo["articulo"].' <br>
    	 	<strong>Descripción:</strong> '.$articulo["descripcion"].'<br>
    	 	<strong>Marca:</strong> '.$articulo["marca"].' <br>
    	 	<strong>Serial:</strong> '.$articulo["serial"].' <br>
    	 	'.confirmarDestino($operacion, $destino, $articulo).'
    	 	<strong>Fecha de Regreso:</strong> '.
            confirmarFechaRegreso($operacion, $fregreso).' <br>
 
        	<form class="has-text-centered" method="post"> 
                            <label class="checkbox" for="pdf"><input type="checkbox" name="pdf" value="true"> Generar registro PDF</label> 
                        <p class="has-text-centered">¿Proceder con la operación?</p> 
                <input type="submit" name="button1"
                        value="Sí"/> 
                  
                <input type="submit" name="button2"
                        value="No"/>
                        <br>

            </form> 
        </div>
    </div>
    '.$scriptRespaldo.'
	 </body>
	 </html>
 	';

    function confirmarFechaRegreso($operacion, $fregreso){
        if($operacion !== 'Préstamo' AND $operacion !== 'Extensión'){ 
            return "Ninguna";
        }else{
            return date_format(date_create($fregreso), "d-m-Y");
        }
    };
    function confirmarDestino($operacion, $destino, $articulo){
        if($operacion !== "Extensión"){
            return "<strong>Destino:</strong> ".$destino."<br>";
        }else{
            return "<strong>Ubicación:</strong> ".$articulo['ubicacion']."<br>";
        }
    }
?>

