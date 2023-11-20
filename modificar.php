<?php
  header('Content-Type: text/html; charset=UTF-8');
  $conec = mysqli_connect('localhost', 'root', '','inventario');
	if(! $conec) {
		die ('No se pudo conectar con la base de datos: '. mysqli_connect_errno());
	}
  include './alerta.php';
  $urlSerial = htmlspecialchars($_GET["serial"]);
  if(isset($_GET['confirmar'])){
    $elimConfirm = htmlspecialchars($_GET['confirmar']);
    $queryEliminar = 'DELETE FROM articulos_en_inventario WHERE serial ="'.$urlSerial.'"';
      $queryHistorial = "INSERT INTO historial_operaciones(tipo_operacion, serial, destino) VALUES('Eliminar','".$urlSerial."','Removido del Sistema')";
      mysqli_begin_transaction($conec, MYSQLI_TRANS_START_READ_WRITE);
      mysqli_query($conec,$queryEliminar);
      mysqli_query($conec,$queryHistorial);
      mysqli_commit($conec);
      mysqli_close($conec);
      header('Location: index.php');
  }
  $getQuery = 'SELECT * FROM articulos_en_inventario WHERE serial = "'.$urlSerial.'"';
  $resultado = mysqli_query($conec, $getQuery);
  $artiModificar = mysqli_fetch_all($resultado, MYSQLI_ASSOC);

  if($artiModificar == false OR $artiModificar == ""){
      echo '<script language="javascript">
              alert("El artículo especificado no existe en inventario");
              document.location="/"</script>';
  }else{
    $artiModificar = $artiModificar[0];
    if(strpos($artiModificar['ubicacion'], "En préstamo a") !== false){
      echo '<script language="javascript">
              alert("El artículo especificado se encuentra en préstamo");
              document.location="/"
            </script>';
    }
  }
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if ($_POST['accion'] == 'Guardar') {
      checkVacio($_POST, $urlSerial);
      if($_POST['serial'] !== ""){
        $serial = mysqli_real_escape_string($conec,$_POST['serial']);
        if($serial !== $artiModificar['serial']){
          $getQuery = 'SELECT * FROM articulos_en_inventario WHERE serial = "'.$serial.'"';
          $resultado = mysqli_query($conec, $getQuery);
          $duplicadoCheck = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
          if($duplicadoCheck == true OR ! $duplicadoCheck == ""){
            echo '<script language="javascript">
              alert("El nuevo serial especificado ya se encuentra en inventario");
              document.location="/modificar.php?serial='.$urlSerial.'"
            </script>';
          }
        }else{
          $serial = $artiModificar['serial'];
        }
      }else{
        $serial = $artiModificar['serial'];
      }
      if($_POST['articulo'] !== ""){
        $articulo = mysqli_real_escape_string($conec,$_POST['articulo']);
      }else{
        $articulo = $artiModificar['articulo'];
      }
      if($_POST['descripcion'] !== ""){
        $descripcion = mysqli_real_escape_string($conec,$_POST['descripcion']);
      }else{
        $descripcion = $artiModificar['descripcion'];
      }
      if($_POST['marca'] !== ""){
        $marca = mysqli_real_escape_string($conec,$_POST['marca']);
      }else{
        $marca = $artiModificar['marca'];
      }
      if($_POST['ubicacion'] !== ""){
        $ubicacion = mysqli_real_escape_string($conec,$_POST['ubicacion']);
      }else{
        $ubicacion = $artiModificar['ubicacion'];
      }
      $queryActu = 'UPDATE articulos_en_inventario 
                        SET articulo = "'.$articulo.'",
                            serial = "'.$serial.'",
                            descripcion = "'.$descripcion.'",
                            marca = "'.$marca.'",
                            ubicacion = "'.$ubicacion.'"
                        WHERE serial = "'.$urlSerial.'"';
      $queryHistorial = "INSERT INTO historial_operaciones(tipo_operacion, serial, destino) VALUES('Gestión','".$serial."','".$ubicacion."')"; 
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
                document.location="/modificar.php?serial='.$urlSerial.'&confirmar=s"
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
  <nav class="navbar is-link">
  <div class="navbar-brand">
                <a role="button" class="navbar-burger burger" onclick="document.querySelector(`.navbar-menu`).classList.toggle(`is-active`);" aria-label="menu" aria-expanded="false">
                  <span aria-hidden="true"></span>
                  <span aria-hidden="true"></span>
                  <span aria-hidden="true"></span>
                </a>
        </div>
        <div class="navbar-menu">
            <div class="navbar-start">
                <a href="index.php" class="navbar-item">Inicio</a>
                <a href="insertar.php" class="navbar-item">Registro</a>
                <a href="transferencia.php" class="navbar-item">Transferencias</a>
                <a href="historico.php" class="navbar-item">Histórico</a>
                <a href="prestamos.php" class="navbar-item">Préstamos</a>
                '.$opcionesUsuario.'

            </div>
            '.$alerta.'
        </div>
  </nav>
  <div class="column"></div>

 <div class="columns is-centered">
      <form id="box" class="box" action="" method="POST">
        <div class="control">
          <label class="label "for="articulo">Artículo - '.$artiModificar['articulo'].'</label>
          <input class="input" name="articulo" type="text">
        </div>
        <br>
        <div class="control">
          <label class="label" for="descripcion">Descripción - '.$artiModificar['descripcion'].'</label>
          <input class="input" name="descripcion" type="text">
        </div>
        <br>
        <div class="control">
          <label class="label" for="marca">Marca - '.$artiModificar['marca'].'</label>
          <input class="input" name="marca" type="text">
        </div>
        <br>
         <div class="control">
          <label class="label" for="serial">Serial - '.$artiModificar['serial'].'</label>
          <input class="input" name="serial" type="text">
        </div>
        <br>
         <div class="control">
          <label class="label" for="ubicacion">Ubicación - '.$artiModificar['ubicacion'].'</label>
          <input class="input" name="ubicacion" type="text">
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

function checkVacio($cambios, $serialActual){
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
              document.location="/modificar.php?serial='.$serialActual.'"
            </script>';
    }
  }
};
?>