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
                $fregresoFormatted = $fregreso. " 23:59:59"; // Ensure proper formatting for each article

                if ($operacion === "Traspaso Temporal") {  
                    $queryTraspasoTemp = "INSERT INTO traspasos_temporales(articulo_id, fecha_de_retorno, id_operacion) VALUES('".$articulo['id']."','".$fregresoFormatted."','".$idOperacion."')";
                    mysqli_query($conec, $queryTraspasoTemp);
                } else if ($operacion === "Retorno") {
                    $queryUbicacion = "UPDATE articulos SET ubicacion = '2' WHERE id = '".$articulo['articulo_id']."'";
                    $queryOperacion = "DELETE FROM traspasos_temporales WHERE articulo_id = '".$articulo['articulo_id']."'";
                } else if ($operacion === "Extensión") {
                    $queryUbicacion = "UPDATE traspasos_temporales SET fecha_de_retorno = '".$fregresoFormatted."' WHERE articulo_id = '".$articulo['id']."'";
                } else if ($operacion === "Retiro") {
                    $queryRetiro = "UPDATE articulos SET esta_retirado = 1 WHERE id = '".$articulo['id']."'";
                    mysqli_query($conec, $queryRetiro);
                } else if ($operacion === "Reentrada") {
                    $queryUbicacion = "UPDATE articulos SET ubicacion = '2' WHERE id = '".$articulo['id']."'";
                    mysqli_query($conec, $queryUbicacion);
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
     if(isset($_POST['excel']) && $_POST['excel'] == "true") {
        setcookie('excel', $idOperacion, time() + 5, '/');
    }
    if(isset($_POST['nota']) && $_POST['nota'] == "true") {
        setcookie('nota', $idOperacion, time() + 5, '/');
    }
        session_destroy();
        header('Location: index.php');
    } 
    if(isset($_POST['button2'])) { 
        session_destroy();
        header('Location: index.php');
    } 

 	echo '
 	 <!DOCTYPE html>
 	 <html lang="en">
	 <head>
	 	<meta charset="UTF-8">
	 	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	 	<title>Confirmar Operación</title>
        <link rel="stylesheet" href="css/output.css">
        <link href="./datatables.min.css" rel="stylesheet">
	 </head>
	 <body>
    '.$header.'
     <div class="flex items-start justify-between">
        <div class="w-full  px-2 py-6">
        <table id="tableConfirm" class="w-8/12 text-sm font-karla display text-sky-900 bg-blue-200 bg-opacity-30 rounded-xl m-4 px-4">
    <thead>
        <tr>
            <th></th>
            <th>Serial</th>
            <th>Descripción</th>
            <th>Marca</th>
            <th>Valor</th>
        </tr>
    </thead>
    <tbody>
    '.$filas.'
    </tbody>
    <tfoot>
            <tr>
            <th></th>
            <th>Serial</th>
            <th>Descripción</th>
            <th>Marca</th>
            <th>Valor</th>
            </tr>
        </tfoot>
</table>
</div>
        <div class="font-karla text-gray-400 h-screen w-4/12 p-10 bg-gray-100 bg-opacity-80 flex flex-col gap-4 justify-end">
    	    <p>Está a punto de realizar la siguiente operación con los artículos indicados:</p>
    	 	<p><strong>Tipo de Operación:</strong> '.$operacion.' </p>
    	 	'.confirmarDestino($operacion, $divisionDestino["nombre_division"], $articulos[0]).'
    	 	<p><strong>Fecha de Regreso:</strong> '.
            confirmarFechaRegreso($operacion, $fregreso).'</p>
        	<form class="flex flex-col" method="post"> 
                            <label style="display: none;" class="checkbox" for="excel"><input type="checkbox" name="excel" value="true"> Descargar BMU-2</label> 
                            <label style="display: none;" class="checkbox" for="nota"><input type="checkbox" name="nota" value="true"> Generar Nota de Salida</label> 
                        <p class="mt-16 text-center font-bold">¿Proceder con la operación?</p> 
                <input type="submit" class="cursor-pointer my-2 bg-blue-500 hover:bg-white hover:text-blue-950 text-white rounded-xl px-5 py-3" name="button1"
                        value="Sí"/> 
                  
                <input type="submit" class="hover:bg-rose-400 my-2 cursor-pointer hover:text-white rounded-xl px-5 py-3" name="button2"
                        value="No"/>
                        <br>

            </form> 
        </div>
    </div>
    <script src="./datatables.min.js"></script>
<script language="javascript">
$(document).ready(function () {

  var table = $("#tableConfirm").DataTable({
    language: {
      url: "./resources/lng-es.json",
    },
    paging: false,
    scrollY: "600px",
    scrollX: false,
    scrollCollapse: true,
    searching: false,
    "columnDefs": [ {
        width: "30px",
"targets": 0,
"orderable": false
},{
    targets: 4,
    width: "75px"
    } ],
  });

  table.on("click", "td.dt-control", function (e) {
    let tr = e.target.closest("tr");
    let row = table.row(tr);
 
    if (row.child.isShown()) {
        // This row is already open - close it
        row.child.hide();
    }
    else {
        // Open this row
       row.child(format(tr.dataset.modelo, tr.dataset.nid)).show();

    }
});
});
function format (modelo, nid) {
    return `<div class="flex flex-col items-start">
    <div class="py-2 border-b border-solid border-gray-300">Modelo:  ` + modelo +  `</div><div class="py-2">Nro. Id.:  ` + nid +  `</div></div> `;
}
</script>
    '.$scriptRespaldo.'
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        var operacion = "'.$operacion.'";
        var excelCheckbox = document.querySelector(\'label[for="excel"]\');
        var notaCheckbox = document.querySelector(\'label[for="nota"]\');

        if (operacion === "Traspaso" || operacion === "Traspaso Temporal") {
            // Mostrar ambas checkboxes
            excelCheckbox.style.display = "block";
            notaCheckbox.style.display = "block";
        } else if (operacion === "Retiro") {
            // Mostrar solo la checkbox de BMU-2
            excelCheckbox.style.display = "block";
            notaCheckbox.style.display = "none";
        } else {
            // Ocultar ambas checkboxes
            excelCheckbox.style.display = "none";
            notaCheckbox.style.display = "none";
        }
    });
</script>

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
          $n_id = !empty($articulos[$x]['n_identificacion']) ? $articulos[$x]['n_identificacion'] : "S.C";
            $modelo = !empty($articulos[$x]['nombre_modelo']) ? $articulos[$x]['nombre_modelo'] : "Modelo no especificado";

 $a = '
             <tr class="font-karla" data-modelo="'.$modelo.'" data-nid="'.$n_id.'">
        <td class="dt-control"></td>
                <td>'.$articulos[$x]["serial_fabrica"].'</td>
                <td>'.$articulos[$x]["descripcion"].'</td>
                <td>'.$articulos[$x]["fabricante"].'</td>
                <td>'.$articulos[$x]["monto_valor"].'</td>
             </tr>';
        $temp .= $a;
    }
    return $temp;
}
?>

