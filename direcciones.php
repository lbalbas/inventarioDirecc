<?php
  header('Content-Type: text/html; charset=UTF-8');
  $conec = mysqli_connect('localhost', 'root', '','inventario');
	if(! $conec) {
		die ('No se pudo conectar con la base de datos: '. mysqli_connect_errno());
	}
  include './alerta.php';
  include "./helpers.php";
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
$nombre = $_POST['nombre'];
$esRetiro = isset($_POST['retiro']) ? 1 : 0;
$query = "INSERT INTO divisiones(nombre_division, es_destino_retiro) VALUES('".$nombre."','".$esRetiro."')";
mysqli_query($conec, $query);
	}
  $destinos = mysqli_fetch_all(mysqli_query($conec,"SELECT * FROM divisiones"),MYSQLI_ASSOC);

echo '<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Direcciones</title>
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
<div class="table-wrapper">
    <table id="tablaInventario" class="table is-fullwidth is-striped">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>¿Es Destino de Retiro?</th>
          </tr>
        </thead>
        <tbody>
          '.$filas.'
        </tbody>
    </table>
    </div>
 <div class="columns is-fullwidth is-centered">
      <form id="box" class="box" action="" method="POST">
      <div class="control">
        <label class="label "for="nombre">Nombre</label>
        <input required class="input" name="nombre" type="text">
      </div>
      <br>
      <div class="control">
        <label class="label" for="retiro">¿Es Destino de Retiro?</label>
          <input type="checkbox" value="1" id="retiro" name="retiro" />
      </div>
      <br>
      <input class="button" type="submit">
    </form>
  </div>
'.$scriptRespaldo.'
</body>
</html>'

?>