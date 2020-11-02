<?php 
	$conec = mysqli_connect('localhost', 'root', '','inventario');
	if(! $conec) {
		die ('No se pudo conectar con la base de datos: '. mysqli_connect_errno());
	}
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
 		if($operacion === "Préstamo"){ 
            $queryUbicacion = "UPDATE articulos_en_inventario SET ubicacion = 'En préstamo a ".$destino."' WHERE serial = '".$serial."'";
            $queryOperacion = "INSERT INTO articulos_en_prestamo(serial, fecha_de_retorno, destino) VALUES('".$serial."','".$fregreso."','".$destino."')";
        }else if($operacion === "Asignación"){
            $queryUbicacion = "DELETE  FROM articulos_en_inventario WHERE serial = '".$serial."'";
        }else if($operacion === "Retorno"){
            $queryUbicacion = "UPDATE articulos_en_inventario SET ubicacion = '".$destino."' WHERE serial = '".$serial."'";
            $queryOperacion = "DELETE FROM articulos_en_prestamo WHERE serial = '".$serial."'";
        }
        $queryHistorial = "INSERT INTO historial_operaciones(tipo_operacion, serial, destino) VALUES('".$operacion."','".$serial."','".$destino."')";
        mysqli_begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
        if($queryOperacion){ 
            mysqli_query($conec,$queryOperacion);
        }
        mysqli_query($conec,$queryUbicacion);
        mysqli_query($conec,$queryHistorial);
        mysqli_commit();
        mysqli_close();
        session_destroy();
        header('Location: transferencia.php');
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
        <link rel="stylesheet" href="css/bulma.css">
	 </head>
	 <body>
     <br>
     <br>
     <br>
     <div class="box is-centered has-text-justified">
        <div>
    	    Está a punto de realizar la siguiente operación: <br>
    	 	<strong>Tipo de Operación:</strong> '.$operacion.' <br>
    	 	<strong>Articulo:</strong> '.$articulo["articulo"].' <br>
    	 	<strong>Descripción:</strong> '.$articulo["descripcion"].'<br>
    	 	<strong>Marca:</strong> '.$articulo["marca"].' <br>
    	 	<strong>Serial:</strong> '.$articulo["serial"].' <br>
    	 	<strong>Destino:</strong> '.$destino.' <br>
    	 	<strong>Fecha de Regreso:</strong> '.
            confirmarFechaRegreso($operacion, $fregreso).' <br>
            <p class="has-text-centered">¿Proceder con la operación?</p>  
        	<form class="has-text-centered" method="post"> 
                <input type="submit" name="button1"
                        value="Sí"/> 
                  
                <input type="submit" name="button2"
                        value="No"/> 
            </form> 
        </div>
    </div>
	 </body>
	 </html>
 	';

    function confirmarFechaRegreso($operacion, $fregreso){
        if($operacion !== 'Préstamo'){ 
            return "Ninguna";
        }else{
            return date_format(date_create($fregreso), "d-m-Y");
        }
    };
?>

