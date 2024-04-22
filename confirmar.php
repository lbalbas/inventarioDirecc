<?php 
    header('Content-Type: text/html; charset=UTF-8');
	$conec = mysqli_connect('localhost', 'root', '','inventario');
	if(! $conec) {
		die ('No se pudo conectar con la base de datos: '. mysqli_connect_errno());
	}
    include './alerta.php';
    include "./helpers.php";
	session_start();
	if(isset($_SESSION['articulos'])){
        $articulos = $_SESSION['articulos'];
        $filas = iterarArticulos($articulos, $conec);
        $observaciones = isset($_SESSION['observaciones']) ? $_SESSION['observaciones'] : "Ninguna";
        $destino = $_SESSION['destino'];
        $divisionDestino = mysqli_fetch_all(mysqli_query($conec,'SELECT nombre_division FROM divisiones WHERE id ='.$destino),MYSQLI_ASSOC)[0];
        $fregreso = $_SESSION['fregreso'];
        $operacion = $_SESSION['operacion'];
    }else{
        session_destroy();
        header('Location: index.php');
 	}
 	if(isset($_POST['button1'])){
        // Iniciar la transacción
        mysqli_begin_transaction($conec);
        $queryHistorial = "INSERT INTO historial_operaciones(observaciones, tipo_operacion, destino) VALUES('".$observaciones."','".$operacion."','".$destino."')";
        mysqli_query($conec, $queryHistorial);
        $idOperacion = mysqli_insert_id($conec);

        try {
            foreach ($articulos as $articulo) {
                $queryUbicacion = "UPDATE articulos SET ubicacion = '".$destino."' WHERE id = '".$articulo['id']."'";
                $queryOperacion = "INSERT INTO historial_operaciones_articulos(id_operacion, id_articulo, origen) VALUES('".$idOperacion."','".$articulo['id']."','".$articulo['ubicacion']."')";
                if ($operacion === "Traspaso Temporal") {  
                    $fregreso = $fregreso."  23:59:59";
                    $queryTraspasoTemp = "INSERT INTO traspasos_temporales(articulo_id, fecha_de_retorno, id_operacion) VALUES('".$articulo['id']."','".$fregreso."','".$idOperacion."')";
                    mysqli_query($conec, $queryTraspasoTemp);
                } else if ($operacion === "Retorno") {
                    $queryUbicacion = "UPDATE articulos SET ubicacion = '".$articulo['origen']."' WHERE id = '".$articulo['articulo_id']."'";
                    $queryOperacion = "DELETE FROM traspasos_temporales WHERE articulo_id = '".$articulo['articulo_id']."'";
                } else if ($operacion === "Extensión") {
                    $fregreso = $fregreso."  23:59:59";
                    $queryUbicacion = "UPDATE traspasos_temporales SET fecha_de_retorno = '".$fregreso."' WHERE articulo_id = '".$articulo['id']."'";
                } else if ($operacion === "Retiro") {
                    $queryRetiro = "UPDATE articulos SET esta_retirado = 1 WHERE id = '".$articulo['id']."'";
                    mysqli_query($conec, $queryRetiro);
                }
                $resultadoOp = mysqli_query($conec, $queryOperacion);
                $resultadoUb = mysqli_query($conec, $queryUbicacion);
                if (!$resultadoOp OR !$resultadoUb) {
                    throw new Exception('Error al realizar la operación en el artículo ' . $articulo['id']);
                }
            }

            // Si todas las operaciones se realizaron con éxito, confirmar la transacción
            mysqli_commit($conec);
        } catch (Exception $e) {
            // Si ocurre algún error, revertir la transacción
            mysqli_rollback($conec);

            // Manejar el error, por ejemplo, mostrar un mensaje al usuario
            echo 'Error: ' . $e->getMessage();
        }
        if($_POST['excel'] == "true"){
            setcookie('excel', $idOperacion, time() +  5, '/'); // La cookie expira en  5 segundos
        }
        if($_POST['nota'] == "true"){
            setcookie('nota', $idOperacion, time() +  5, '/'); // La cookie expira en  5 segundos
        }
        session_destroy();
        header('Location: index.php');
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
        <link rel="stylesheet" href="css/output.css">
	 </head>
	 <body>
    '.$header.'
     <div class="flex items-start justify-between">
        <div class="grid grid-cols-1 w-8/12 text-sm bg-blue-100 bg-opacity-60 rounded-xl m-4 px-4">
         <div class="grid grid-cols-12 text-blue-900 rounded-xl bg-white shadow-xl py-4 my-5 font-bold tracking-wider font-rubik rounded-lg">
            <div class="col-span-1 text-lg"></div>
            <div class=" col-start-2 col-end-3 text-lg">Serial</div>
            <div class="col-start-4 col-end-7 text-lg">Descripción</div>
            <div class="col-start-7 col-end-9 text-lg">Marca</div>
            <div class="col-start-9 col-end-10 text-lg">Valor</div>
         </div>
         '.$filas.'
        </div>
        <div class="font-karla text-gray-400 h-screen w-3/12 p-10 bg-gray-100 bg-opacity-80 flex flex-col gap-4 justify-end">
    	    <p>Está a punto de realizar la siguiente operación con los artículos indicados:</p>
    	 	<p><strong>Tipo de Operación:</strong> '.$operacion.' </p>
    	 	'.confirmarDestino($operacion, $divisionDestino["nombre_division"], $articulos[0]).'
    	 	<p><strong>Fecha de Regreso:</strong> '.
            confirmarFechaRegreso($operacion, $fregreso).'</p>
        	<form class="flex flex-col" method="post"> 
                            <label class="checkbox" for="excel"><input type="checkbox" name="excel" value="true"> Descargar BMU-2</label> 
                            <label class="checkbox" for="nota"><input type="checkbox" name="nota" value="true"> Generar Nota de Salida</label> 
                        <p class="mt-16 text-center font-bold">¿Proceder con la operación?</p> 
                <input type="submit" class="cursor-pointer my-2 bg-blue-500 hover:bg-white hover:text-blue-950 text-white rounded-xl px-5 py-3" name="button1"
                        value="Sí"/> 
                  
                <input type="submit" class="hover:bg-rose-400 my-2 cursor-pointer hover:text-white rounded-xl px-5 py-3" name="button2"
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
        if($operacion !== 'Traspaso Temporal' AND $operacion !== 'Extensión'){
            return "Ninguna";
        }else{
            return date_format(date_create($fregreso), "d-m-Y");
        }
    };
    function confirmarDestino($operacion, $destino, $articulo){
        if($operacion !== "Retorno"){
            return "<p><strong>Destino:</strong> ".$destino."</p>";
        }
    }
    function iterarArticulos($articulos,$conec){
    $temp = "";

    for($x =  0; $x < count($articulos); $x++){
 $a = '
             <div class="grid grid-cols-12 text-blue-950 border-blue-300 font-karla border-solid border-b-2 py-2 last:border-0">
                <div class="flex items-center col-start-2 col-end-3">'.$articulos[$x]["serial_fabrica"].'</div>
                <div class="flex items-center col-start-4 col-end-7">'.$articulos[$x]["descripcion"].'</div>
                <div class="flex items-center col-start-7 col-end-9">'.$articulos[$x]["fabricante"].'</div>
                <div class="flex items-center col-start-9 col-end-10">'.$articulos[$x]["monto_valor"].'</div>
             </div>';
        $temp .= $a;
    }
    return $temp;
}
?>

