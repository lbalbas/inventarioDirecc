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
  
   <div class="columns is-centered">
        <form id="box" class="box" action="" method="POST">
          <div class="control">
            <label class="label "for="articulo">Descripción - '.$artiModificar['descripcion'].'</label>
            <input class="input" name="descripcion" type="text">
          </div>
          <br>
          <div class="control">
            <label class="label" for="fabricante">Marca - '.$artiModificar['fabricante'].'</label>
            <input class="input" name="fabricante" type="text">
          </div>
          <br>
          <div class="control">
            <label class="label" for="serial_fabrica">Serial de Fábrica - '.$artiModificar['serial_fabrica'].'</label>
            <input class="input" name="serial_fabrica" type="text">
          </div>
          <br>

          <div class="control">
            <label class="label" for="modelo">Modelo - '.$nombre_modelo.'</label>
            <input class="input" name="nombre_modelo" type="text">
          </div><br>

          <div class="control">
            <label class="label" for="nro_identificacion">Nro. de Identificación - '.$n_identificacion.'</label>
            <input class="input" name="n_identificacion" type="text">
          </div>
          <br>
          <div class="control">
              <label class="label" for="monto_valor">Valor - '.$artiModificar['monto_valor'].'</label>
                <input class="input" type="text" name="monto_valor" id="monto_valor" oninput="formatDecimalInput(this)">
          </div>
                              <br>
          <div class="buttonContainer">
            <input name="accion" class="button is-link" type="submit" value="Guardar">  
            <input name="accion" id="is-right" class="button  is-danger" type="submit" value="Eliminar">
          </div>
      </form>
    </div>
    '.$scriptRespaldo.'
    <script language="javascript">
      function formatDecimalInput(input) {
            let value = input.value.replace(/,/g, ""); // Elimina las comas
            value = parseInt(value, 10); // Convierte el valor a un número entero
            if (!isNaN(value)) {
                value = value / 100; // Divide por 100 para mover la coma dos posiciones a la izquierda
                input.value = value.toFixed(2).replace(".", ","); // Formatea el número con dos decimales y cambia el punto por una coma
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
  