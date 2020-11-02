<?php
  $conec = mysqli_connect('localhost', 'root', '','inventario');
	if(! $conec) {
		die ('No se pudo conectar con la base de datos: '. mysqli_connect_errno());
	}

	if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $articulo = $_POST['articulo'];
    $descripcion = $_POST['descripcion'];
    $marca = $_POST['marca'];
    $serial = $_POST['serial'];
    $ubicacion = $_POST['ubicacion'];

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
    <title>Document</title>
    <link rel="stylesheet" href="css/bulma.css">
</head>
<body>
  <nav class="navbar is-dark">
        <div class="navbar-menu">
            <div class="navbar-start">
                <a href="index.php" class="navbar-item">Inicio</a>
                <a href="insertar.php" class="navbar-item">Insertar</a>
                <a href="transferencia.php" class="navbar-item">Transferir</a>
                <a href="historico.php" class="navbar-item">Historico</a>
                <a href="prestamos.php" class="navbar-item">Préstamos</a>
            </div>
        </div>
  </nav>
  <div class="column"></div>

 <div class="columns is-centered">
      <form action="" method="POST">
      <div class="control">
        <label class="label "for="articulo">Articulo</label>
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
</body>
</html>'

?>