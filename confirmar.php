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
        $observaciones = $_SESSION['observaciones'];
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
            $_SESSION['destino'] = $divisionDestino["nombre_division"];
            setcookie('excel', 'true', time() +  5, '/'); // La cookie expira en  5 segundos
            header('Location: index.php');
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
    '.$header.'
     <br>
     <div id="box" class="box is-centered has-text-justified">
        <div>
    	    Está a punto de realizar la siguiente operación: <br>
    	 	<strong>Tipo de Operación:</strong> '.$operacion.' <br>
    	 	'.confirmarDestino($operacion, $divisionDestino["nombre_division"], $articulos[0]).'
    	 	<strong>Fecha de Regreso:</strong> '.
            confirmarFechaRegreso($operacion, $fregreso).' <br>
         <div class="table-wrapper">
        <table id="tablaInventario" class="table is-fullwidth is-striped">
                <thead>
                    <tr>
                        <th>Codificación</th>
                        <th>Descripción</th>
                        <th>Fabricante</th>
                        <th>Valor</th>
                        <th>Ubicación</th>
                    </tr>
                </thead>
                <tbody>
                    '.$filas.'
                </tbody>
        </table>
        </div>
        	<form class="has-text-centered" method="post"> 
                            <label class="checkbox" for="excel"><input type="checkbox" name="excel" value="true"> Generar registro Excel</label> 
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
        if($operacion !== 'Traspaso Temporal' AND $operacion !== 'Extensión'){
            return "Ninguna";
        }else{
            return date_format(date_create($fregreso), "d-m-Y");
        }
    };
    function confirmarDestino($operacion, $destino, $articulo){
        if($operacion !== "Retorno"){
            return "<strong>Destino:</strong> ".$destino."<br>";
        }
    }
    function iterarArticulos($articulos,$conec){
    $temp = "";

    for($x =  0; $x < count($articulos); $x++){
        $a = '<tr>
            <td>'.$articulos[$x]["codigo_unidad"].'
            </td><td>'.$articulos[$x]["descripcion"].'
            </td><td>'.$articulos[$x]["fabricante"].'
            </td><td>'.$articulos[$x]["monto_valor"].'
            </td><td>'.$articulos[$x]["nombre_division"].'
            </td></tr>';
        $temp .= $a;
    }
    return $temp;
}
?>

