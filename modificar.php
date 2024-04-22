<?php
  header('Content-Type: text/html; charset=UTF-8');
  $conec = mysqli_connect('localhost', 'root', '','inventario');
	if(! $conec) {
		die ('No se pudo conectar con la base de datos: '. mysqli_connect_errno());
	}
  include './alerta.php';
  include "./helpers.php";
  $urlId = htmlspecialchars($_GET["id"]);
  if(isset($_GET['confirmar'])){
    $elimConfirm = htmlspecialchars($_GET['confirmar']);
    $queryEliminar = 'DELETE FROM articulos WHERE id ="'.$urlId.'"';
      $queryHistorial = "INSERT INTO historial_transacciones(tipo_operacion, id_articulo, id_operacion) VALUES('Eliminar','".$urlId."','Removido del Sistema')";
      mysqli_begin_transaction($conec, MYSQLI_TRANS_START_READ_WRITE);
      mysqli_query($conec,$queryEliminar);
      mysqli_query($conec,$queryHistorial);
      mysqli_commit($conec);
      mysqli_close($conec);
      header('Location: index.php');
  }
  $getQuery = 'SELECT * FROM `articulos` 
LEFT JOIN `modelo_articulo` ON `modelo_articulo`.`id_articulo` = `articulos`.`id` 
LEFT JOIN `nro_identificacion_articulo` ON `nro_identificacion_articulo`.`id_articulo` = `articulos`.`id` 
WHERE `articulos`.`id` = '.$urlId;
  $resultado = mysqli_query($conec, $getQuery);
  $artiModificar = mysqli_fetch_all($resultado, MYSQLI_ASSOC)[0];
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if ($_POST['accion'] == 'Guardar') {
      checkVacio($_POST, $urlId);
      $nombre_modelo = mysqli_real_escape_string($conec,$_POST['nombre_modelo']);
      $n_identificacion = mysqli_real_escape_string($conec,$_POST['n_identificacion']);
      if($_POST['serial_fabrica'] !== ""){
        $serial_fabrica = mysqli_real_escape_string($conec,$_POST['serial_fabrica']);
        if($serial_fabrica !== $artiModificar['serial_fabrica']){
          $getQuery = 'SELECT * FROM articulos WHERE serial_fabrica = "'.$serial_fabrica.'"';
          $resultado = mysqli_query($conec, $getQuery);
          $duplicadoCheck = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
          if($duplicadoCheck == true OR ! $duplicadoCheck == ""){
            echo '<script language="javascript">
              alert("El nuevo código de artículo especificado ya se encuentra en inventario");
              document.location="/modificar.php?serial='.$urlId.'"
            </script>';
          }
        }else{
          $serial_fabrica = $artiModificar['serial_fabrica'];
        }
      }else{
        $serial_fabrica = $artiModificar['serial_fabrica'];
      }
      if($_POST['descripcion'] !== ""){
        $descripcion = mysqli_real_escape_string($conec,$_POST['descripcion']);
      }else{
        $descripcion = $artiModificar['descripcion'];
      }
      if($_POST['fabricante'] !== ""){
        $fabricante = mysqli_real_escape_string($conec,$_POST['fabricante']);
      }else{
        $fabricante = $artiModificar['fabricante'];
      }
      if($_POST['monto_valor'] !== ""){
        $monto_valor = mysqli_real_escape_string($conec,$_POST['monto_valor']);
      }else{
        $monto_valor = $artiModificar['monto_valor'];
      }

      $queryActu = 'UPDATE articulos 
                        SET descripcion = "'.$descripcion.'",
                            serial_fabrica = "'.$serial_fabrica.'",
                            fabricante = "'.$fabricante.'",
                            monto_valor = "'.$monto_valor.'"
                        WHERE id = "'.$urlId.'"'; 
      mysqli_query($conec,$queryActu);

      if($_POST['nombre_modelo'] !== ""){
    if($artiModificar['nombre_modelo']){
       $queryModelo = "UPDATE modelo_articulo SET nombre_modelo = '".$nombre_modelo."' WHERE id_articulo = ".$urlId;
    }else{
        $queryModelo = "INSERT INTO modelo_articulo(id_articulo,nombre_modelo) VALUES('".$urlId."','".$nombre_modelo."')";
    }
    mysqli_query($conec, $queryModelo);
}

      if($_POST['n_identificacion'] !== ""){
        if($artiModificar['n_identificacion']){
          $queryIdentificacion = "UPDATE nro_identificacion_articulo SET n_identificacion = ".$n_identificacion." WHERE id_articulo = ".$urlId;
        }else{
          $queryIdentificacion = "INSERT INTO nro_identificacion_articulo(id_articulo,n_identificacion) VALUES(".$urlId.",".$n_identificacion.")";
        }
        mysqli_query($conec, $queryIdentificacion);
      }

      mysqli_close($conec);
      //header("Location: index.php");
    }else if ($_POST['accion'] == 'Eliminar') {
      if($_COOKIE['login'] == 'admin'){
        echo '<script language="javascript">
              var res = window.confirm("¿Desea eliminar el artículo en cuestión?");
              if(res){
                document.location="/modificar.php?serial='.$urlId.'&confirmar=s"
              }else{
                document.location="/"
              }
            </script>';
      }else{
        echo '<script language="javascript">
                alert("No tiene el permiso para realizar esta acción");
                document.location="/";
        </script>';
      }

    }
	}

  $nombre_modelo = $artiModificar['nombre_modelo'] ? $artiModificar['nombre_modelo'] : "Modelo no Especificado";
  $n_identificacion = $artiModificar['n_identificacion'] ? $artiModificar['n_identificacion'] : "Sin código";
	
  echo '<html>
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Gestión de Artículo</title>
      <link rel="stylesheet" href="css/output.css">
  </head>
  <body class="w-11/12 mx-auto">
    '.$header.'
     <h1 class="mt-28 mb-10 text-6xl font-rubik text-sky-900 font-bold">Modificar un Artículo</h1>

        <form id="box" class="justify-between mx-auto rounded-xl bg-gray-100 shadow-4xl bg-opacity-70 flex flex-wrap p-10 font-karla text-gray-400" action="" method="POST">
          <div class="flex flex-col w-72">
            <label class="font-bold" for="articulo">Descripción - '.$artiModificar['descripcion'].'</label>
            <input oninput="validateInput(this)" class="w-full bg-blue-50 shadow-inner px-4 py-2"  name="descripcion" type="text">
          </div>
          <br>
          <div class="flex flex-col w-72">
            <label class="font-bold" for="fabricante">Marca - '.$artiModificar['fabricante'].'</label>
            <input class="w-full bg-blue-50 shadow-inner px-4 py-2"  name="fabricante" type="text">
          </div>
          <br>
          <div class="flex flex-col w-72">
            <label class="font-bold" for="serial_fabrica">Serial de Fábrica - '.$artiModificar['serial_fabrica'].'</label>
            <input oninput="validateInput(this)" class="w-full bg-blue-50 shadow-inner px-4 py-2"  name="serial_fabrica" type="text">
          </div>
          <br>

          <div class="flex flex-col w-72">
            <label class="font-bold" for="modelo">Modelo - '.$nombre_modelo.'</label>
            <input class="w-full bg-blue-50 shadow-inner px-4 py-2"  name="nombre_modelo" type="text">
          </div><br>

          <div class="flex flex-col w-72">
            <label class="font-bold" for="nro_identificacion">Nro. de Identificación - '.$n_identificacion.'</label>
            <input class="w-full bg-blue-50 shadow-inner px-4 py-2"  name="n_identificacion" type="text">
          </div>
          <br>
          <div class="flex flex-col w-72">
              <label class="font-bold" for="monto_valor">Valor - '.$artiModificar['monto_valor'].'</label>
                <input class="w-full bg-blue-50 shadow-inner px-4 py-2"  type="text" name="monto_valor" id="monto_valor" value="'.$artiModificar['monto_valor'].'" oninput="formatDecimalInput(this)">
          </div>
                              <br>
          <div class="w-full my-4 flex justify-end">
            <input name="accion"  class="justify-self-end place-self-end self-end bg-blue-500 cursor-pointer text-white hover:text-blue-950 rounded-xl hover:bg-white px-4 py-2" type="submit" value="Guardar">  
          </div>
      </form>
    </div>
    '.$scriptRespaldo.'
    <script language="javascript">
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
  </body>
  </html>';
  
  function checkVacio($cambios, $idActual){
    $cont = count($cambios);
    $cambios['accion'] = "";
    $i = 0;
    foreach ($cambios as $key => $val) {
      if($val !== ""){
        break;
      }
      if(++$i === $cont) {
        echo '<script language="javascript">
                alert("No se encontró ningún cambio");
                document.location="/modificar.php?id='.$idActual.'"
              </script>';
      }
    }
  };
  ?>
  