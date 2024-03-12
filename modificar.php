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
  $getQuery = 'SELECT * FROM articulos WHERE id = "'.$urlId.'"';
  $resultado = mysqli_query($conec, $getQuery);
  $artiModificar = mysqli_fetch_all($resultado, MYSQLI_ASSOC)[0];
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if ($_POST['accion'] == 'Guardar') {
      checkVacio($_POST, $urlId);
      if($_POST['codigo_unidad'] !== ""){
        $codigo_unidad = mysqli_real_escape_string($conec,$_POST['codigo_unidad']);
        if($codigo_unidad !== $artiModificar['codigo_unidad']){
          $getQuery = 'SELECT * FROM articulos WHERE codigo_unidad = "'.$codigo_unidad.'"';
          $resultado = mysqli_query($conec, $getQuery);
          $duplicadoCheck = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
          if($duplicadoCheck == true OR ! $duplicadoCheck == ""){
            echo '<script language="javascript">
              alert("El nuevo código de artículo especificado ya se encuentra en inventario");
              document.location="/modificar.php?serial='.$urlId.'"
            </script>';
          }
        }else{
          $codigo_unidad = $artiModificar['codigo_unidad'];
        }
      }else{
        $codigo_unidad = $artiModificar['codigo_unidad'];
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
      $queryActu = 'UPDATE articulos 
                        SET descripcion = "'.$descripcion.'",
                            codigo_unidad = "'.$codigo_unidad.'",
                            fabricante = "'.$fabricante.'"
                        WHERE id = "'.$urlId.'"';
      $queryHistorial = "INSERT INTO historial_transacciones(tipo_operacion, id_articulo, id_operacion) VALUES('Gestión','".$codigo_unidad."','Gestión de Artículo')"; 
      mysqli_begin_transaction($conec,MYSQLI_TRANS_START_READ_WRITE);
      mysqli_query($conec,$queryActu);
      mysqli_query($conec,$queryHistorial);
      mysqli_commit($conec);
      mysqli_close($conec);
      header("Location: index.php");
    } else if ($_POST['accion'] == 'Eliminar') {
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
            <label class="label "for="articulo">Artículo - '.$artiModificar['descripcion'].'</label>
            <input class="input" name="descripcion" type="text">
          </div>
          <br>
          <div class="control">
            <label class="label" for="fabricante">Fabricante - '.$artiModificar['fabricante'].'</label>
            <input class="input" name="fabricante" type="text">
          </div>
          <br>
          <div class="control">
            <label class="label" for="codigo_unidad">Código de Artículo - '.$artiModificar['codigo_unidad'].'</label>
            <input class="input" name="codigo_unidad" type="text">
          </div>
          <br>
          <div class="buttonContainer">
            <input name="accion" class="button is-link" type="submit" value="Guardar">  
            <input name="accion" id="is-right" class="button  is-danger" type="submit" value="Eliminar">
          </div>
      </form>
    </div>
    '.$scriptRespaldo.'
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
  