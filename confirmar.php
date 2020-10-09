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
        }else if($operacion === "Asignación"){
        	$queryUbicacion = "DELETE  FROM articulos_en_inventario WHERE serial = '".$serial."'";
        }
        	$queryOperacion = "INSERT INTO articulos_en_prestamo(serial, fecha_de_retorno, destino) VALUES('".$serial."','".$fregreso."','".$destino."')";
            $queryHistorial = "INSERT INTO historial_operaciones(tipo_operacion, serial, destino) VALUES('".$operacion."','".$serial."','".$destino."')";
            mysqli_begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
            mysqli_query($conec,$queryOperacion);
            mysqli_query($conec,$queryUbicacion);
            mysqli_query($conec,$queryHistorial);
            mysqli_commit();
            mysqli_close();
            header('Location: transferencia.php');
    } 
    if(isset($_POST['button2'])) { 
        header('Location: transferencia.php');
    } 
 	echo'
 	 <!DOCTYPE html>
 	 <html lang="en">
	 <head>
	 	<meta charset="UTF-8">
	 	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	 	<title>Document</title>
	 </head>
	 <body>
	 Está realizando esta operación:
	 	Tipo de Operación: '.$operacion.'
	 	Articulo: '.$articulo["articulo"].'
	 	Descripción: '.$articulo["descripcion"].'
	 	Marca: '.$articulo["marca"].'
	 	Serial: '.$articulo["serial"].'
	 	Receptor: '.$destino.'
	 	Fecha de Regreso: '.$fregreso.'
¿Proceder con la operación?
	<form method="post"> 
        <input type="submit" name="button1"
                value="Sí"/> 
          
        <input type="submit" name="button2"
                value="No"/> 
    </form> 
	 </body>
	 </html>
 	'
 ?>

