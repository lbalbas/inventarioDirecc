<?php
  header('Content-Type: text/html; charset=UTF-8');
  $conec = mysqli_connect('localhost', 'root', '','inventario');
	if(! $conec) {
		die ('No se pudo conectar con la base de datos: '. mysqli_connect_errno());
	}
  include './alerta.php';
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $articulo =  mysqli_real_escape_string($conec,$_POST['articulo']);
    $descripcion =  mysqli_real_escape_string($conec,$_POST['descripcion']);
    $marca =  mysqli_real_escape_string($conec,$_POST['marca']);
    $serial =  mysqli_real_escape_string($conec,$_POST['serial']);
    $ubicacion =  mysqli_real_escape_string($conec,$_POST['ubicacion']);


		$queryInsertar = 'INSERT INTO articulos_en_inventario(articulo, descripcion, marca, serial, ubicacion) VALUES("'.$articulo.'","'.$descripcion.'","'.$marca.'","'.$serial.'","'.$ubicacion.'")';
    $queryHistorial = "INSERT INTO historial_operaciones(tipo_operacion, serial, destino) VALUES('Adición','".$serial."','".$ubicacion."')"; 
    mysqli_query($conec,$queryInsertar);
    mysqli_query($conec,$queryHistorial);
    mysqli_close($conec);
	}
	
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

 <div class="columns is-fullwidth is-centered">
      <form id="box" class="box" action="" method="POST">
      <div class="control">
        <label class="label "for="articulo">Artículo</label>
        <input required class="input" name="articulo" type="text">
      </div>
      <br>
      <div class="control">
        <label class="label" for="descripcion">Descripción</label>
        <input required class="input" name="descripcion" type="text">
      </div>
      <br>
      <div class="control">
        <label class="label" for="marca">Marca</label>
        <input required class="input" name="marca" type="text">
      </div>
      <br>
       <div class="control">
        <label class="label" for="serial">Serial</label>
        <input required class="input" name="serial" type="text">
      </div>
      <br>
       <div class="control">
        <label class="label" for="ubicacion">Ubicación</label>
        <input required class="input" name="ubicacion" type="text">
      </div>
      <br>
      <input class="button" type="submit">
    </form>
  </div>
'.$scriptRespaldo.'
</body>
</html>'

?>