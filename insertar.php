<?php
  header('Content-Type: text/html; charset=UTF-8');
  $conec = mysqli_connect('localhost', 'root', '','inventario');
	if(! $conec) {
		die ('No se pudo conectar con la base de datos: '. mysqli_connect_errno());
	}
  include './alerta.php';
  include "./helpers.php";
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $codigo =  mysqli_real_escape_string($conec,$_POST['codigo']);
    $descripcion =  mysqli_real_escape_string($conec,$_POST['descripcion']);
    $fabricante =  mysqli_real_escape_string($conec,$_POST['fabricante']);
    $monto =  mysqli_real_escape_string($conec,$_POST['monto']);
    $ubicacion =  mysqli_real_escape_string($conec,$_POST['ubicacion']);


// Iniciar la transacción
mysqli_begin_transaction($conec, MYSQLI_TRANS_START_READ_WRITE);

try {
    // Consulta para insertar en la tabla de artículos
    $queryInsertar = 'INSERT INTO articulos(codigo_unidad, descripcion, fabricante, ubicacion, monto_valor) VALUES("'.$codigo.'","'.$descripcion.'","'.$fabricante.'","'.$ubicacion.'","'.$monto.'")';
    mysqli_query($conec, $queryInsertar) or die(mysqli_error($conec));

    // Obtener el ID del artículo insertado
    $idArt = mysqli_insert_id($conec);

    // Consulta para insertar en la tabla de historial de operaciones
    $queryHistorial = "INSERT INTO historial_operaciones(observaciones, tipo_operacion, destino) VALUES('Incorporación','Registro','".$ubicacion."')";  
    mysqli_query($conec, $queryHistorial) or die(mysqli_error($conec));

    // Obtener el ID de la operación insertada
    $idOperacion = mysqli_insert_id($conec);

    // Consulta para insertar en la tabla de historial de operaciones de artículos
    $queryHistorialOp = "INSERT INTO historial_operaciones_articulos(id_operacion, id_articulo, origen) VALUES('".$idOperacion."','".$idArt."',  '".$ubicacion."')";  
    mysqli_query($conec, $queryHistorialOp) or die(mysqli_error($conec));

    // Si todas las consultas se ejecutan con éxito, confirmar la transacción
    mysqli_commit($conec);
} catch (\Exception $e) {
    // Si ocurre algún error, revertir la transacción
    mysqli_rollback($conec);

    // Manejar el error, por ejemplo, mostrar un mensaje al usuario
    echo 'Error: ' . $e->getMessage();
} 
	}
  $destinos = mysqli_fetch_all(mysqli_query($conec,"SELECT * FROM divisiones WHERE es_destino_retiro = 0"),MYSQLI_ASSOC);

  $destinoOptions = "";

  for($x = 0; $x < count($destinos); $x++){
    $destinoOptions .= '<option value="'.$destinos[$x]["id"].'">'.$destinos[$x]["nombre_division"].'</option>';
  };	

  mysqli_close($conec);

echo '<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Artículos</title>
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
  <div class="column"></div>

 <div class="columns is-fullwidth is-centered">
      <form id="box" class="box" action="" method="POST">
      <div class="control">
        <label class="label "for="codigo">Código</label>
        <input required class="input" name="codigo" type="text">
      </div>
      <br>
      <div class="control">
        <label class="label" for="descripcion">Descripción</label>
        <input required class="input" name="descripcion" type="text">
      </div>
      <br>
      <div class="control">
        <label class="label" for="marca">Fabricante</label>
        <input required class="input" name="fabricante" type="text">
      </div>
      <br>
              <div class="control" id="ubicacionControl">
            <label class="label" for="ubicacion">Ubicación</label>
            <select required id="ubicacion" class="input" name="ubicacion">
                ' . $destinoOptions . '
            </select>
        </div>
      <br>
      <div class="control">
        <label class="label" for="monto">Valor</label>
        <input required class="input" name="monto" type="text">
      </div>
      <input class="button" type="submit">
    </form>
  </div>
'.$scriptRespaldo.'
</body>
</html>'

?>