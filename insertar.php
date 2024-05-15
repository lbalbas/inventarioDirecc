<?php
  header('Content-Type: text/html; charset=UTF-8');
  $conec = mysqli_connect('localhost', 'root', '','inventario');
	if(! $conec) {
		die ('No se pudo conectar con la base de datos: '. mysqli_connect_errno());
	}
  include './alerta.php';
  include "./helpers.php";
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $serial =  mysqli_real_escape_string($conec,$_POST['serial']);
    $descripcion =  mysqli_real_escape_string($conec,$_POST['descripcion']);
    $fabricante =  mysqli_real_escape_string($conec,$_POST['fabricante']);
    $monto =  mysqli_real_escape_string($conec,$_POST['monto']);
    $ubicacion =  2;


// Iniciar la transacción
mysqli_begin_transaction($conec, MYSQLI_TRANS_START_READ_WRITE);

try {
    // Consulta para insertar en la tabla de artículos
    $queryInsertar = 'INSERT INTO articulos(serial_fabrica, descripcion, fabricante, ubicacion, monto_valor) VALUES("'.$serial.'","'.$descripcion.'","'.$fabricante.'","'.$ubicacion.'","'.$monto.'")';
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

    if(isset($_POST['nro_id'])){
      mysqli_query($conec, "INSERT INTO nro_identificacion_articulo(id_articulo, n_identificacion) VALUES('".$idArt."','".$_POST['nro_id']."')") or die(mysqli_error($conec));
    }
     if(isset($_POST['modelo'])){
      mysqli_query($conec, "INSERT INTO modelo_articulo(id_articulo, nombre_modelo) VALUES('".$idArt."','".$_POST['modelo']."')") or die(mysqli_error($conec));
    }
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
    <link rel="stylesheet" href="css/output.css">
</head>
<body class="w-11/12 mx-auto">
  '.$header.'
 <h1 class="mt-28 mb-10 text-6xl font-rubik text-sky-900 font-bold">Registrar Artículo</h1>
 <form class="justify-between mx-auto rounded-xl bg-gray-100 shadow-4xl bg-opacity-70 flex flex-wrap p-10 font-karla text-gray-400" action="" method="POST">
    <div class="flex flex-col w-72">
      <label class="font-bold" for="nro_id">Nro. de Identificación</label>
      <input required oninput="validateInput(this)" class="w-full bg-gray-50 shadow-inner px-4 py-2" name="nro_id" type="text" id="nro_id">
      
      <label class="flex items-center gap-2" for="sinNumeroAsignado"><input type="checkbox" id="sinNumeroAsignado" onclick="toggleInput(\'nro_id\', this)">Sin Número Asignado</label>
    </div>
    <div class="flex flex-col w-72">
      <label class="font-bold" for="serial">Serial de Fabrica</label>
      <input required oninput="validateInput(this)" class="w-full bg-gray-50 shadow-inner px-4 py-2" name="serial" type="text">
    </div>
    <div class="flex flex-col w-72">
      <label class="font-bold" for="descripcion">Descripción</label>
      <input required class="w-full bg-gray-50 shadow-inner px-4 py-2" name="descripcion" type="text">
    </div>
    <div class="flex flex-col w-72">
      <label class="font-bold" for="marca">Marca</label>
      <input required class="w-full bg-gray-50 shadow-inner px-4 py-2" name="fabricante" type="text">
    </div>
    <div class="flex flex-col w-72">
      <label class="font-bold" for="modelo">Modelo</label>
      <input required class="w-full bg-gray-50 shadow-inner px-4 py-2" name="modelo" type="text" id="modelo">
      <label class="flex items-center gap-2" for="modeloNoEspecificado"><input type="checkbox" id="modeloNoEspecificado" onclick="toggleInput(\'modelo\', this)">Modelo no especificado</label>
    </div>
    <div class="flex flex-col w-72">
      <label class="font-bold" for="monto">Valor</label>
      <input type oninput="formatDecimalInput(this)" required class="w-full bg-gray-50 shadow-inner px-4 py-2" name="monto" value="0,00" type="text">
    </div>
    <div class="w-full my-4 flex justify-end">
    <input class="justify-self-end place-self-end self-end bg-blue-500 cursor-pointer text-white hover:text-blue-950 rounded-xl hover:bg-white px-4 py-2" value="Registrar" type="submit">
    </div>
 </form>

<script>
 function toggleInput(inputId, checkbox) {
    var input = document.getElementById(inputId);
    if (checkbox.checked) {
      input.removeAttribute("required");
      input.disabled = true;
      input.value = ""
    } else {
      input.setAttribute("required", "");
      input.disabled = false;
    }
 }
     function formatDecimalInput(input) {
    // Use a regular expression to replace any non-numeric characters with an empty string
    let value = input.value.replace(/[^0-9]/g, ""); // Elimina cualquier carácter que no sea un número
    value = parseInt(value, 10); // Convierte el valor a un número entero
    if (!isNaN(value)) {
        value = value / 100; // Divide por 100 para mover la coma dos posiciones a la izquierda
        input.value = value.toFixed(2).replace(".", ","); // Formatea el número con dos decimales y cambia el punto por una coma
    }
}
  function validateInput(input) {
    // Regular expression that allows only numbers and hyphens
    var regex = /^[0-9-]+$/;
    // Check if the input matches the regular expression
    if (!regex.test(input.value)) {
        // If not, clear the input field
        input.value = input.value.replace(/[^0-9-]/g, "");
    }
}
</script>
'.$scriptRespaldo.'
</body>
</html>'

?>