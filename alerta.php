<?php 
	if(!isset($_COOKIE['login'])){
		echo '<script language="javascript">
			alert("Por favor inicie sesión e inténtelo de nuevo");
			document.location="/login.php";
		</script>';
	}
	$opcionesUsuario = "";
	$scriptRespaldo =  '
	<script language="javascript">
	    function respaldoVentana(){
	      if(window.confirm("¿Desea crear un respaldo de la base de datos?")){
	        window.open("/respaldo.php","_blank");
	      }
	    }
	  </script>';
	if($_COOKIE['login'] == 'admin'){
		$opcionesUsuario = '
		<div class="navbar-item has-dropdown is-hoverable">
                  <a class="navbar-link"></a>
                  <div class="navbar-dropdown">
		            <a href="login.php?registro=true" class="navbar-item">
		                Registrar Usuario
		            </a>
		            <a class="navbar-item" onclick="respaldoVentana()">
		                Respaldar Base de Datos
		            </a>
		            <a href="login.php?cerrarsesion=true" class="navbar-item">
		                Cerrar Sesión
		            </a>
		          </div>
         </div>
		';
	}else if($_COOKIE['login'] == 'usuario'){
		$opcionesUsuario = '
		<div class="navbar-item has-dropdown is-hoverable">
                  <a class="navbar-link"></a>
                  <div class="navbar-dropdown">
		            <a href="login.php?cerrarsesion=true" class="navbar-item">
		                Cerrar Sesión
		            </a>
		          </div>
         </div>';
	}
	$queryPrestamos = "SELECT * from articulos_en_inventario INNER JOIN articulos_en_prestamo ON articulos_en_inventario.id = articulos_en_prestamo.id_articulo";
	$resultado = mysqli_query($conec, $queryPrestamos);
	$prestamos = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
	$alerta = alertaPrestamo($prestamos);
	function alertaPrestamo($prestamos){
	  	$prestamosCercanos = "";
	  	for($x = 0; $x < count($prestamos); $x++){
	  		date_default_timezone_set('America/Caracas');
	  		$ahora = date_create();
	  		$interval = date_diff($ahora,date_create($prestamos[$x]["fecha_de_retorno"]));
		    if($interval->format('%R%a') <= 5){
		    	if($interval->format('%R%a') == 0){
		    		$prestamosCercanos .= "<a class='navbar-item'>Hoy es la fecha límite del préstamo de ".$prestamos[$x]['articulo']." a ".$prestamos[$x]['destino']."</a>";
				}else if($interval->format('%R%a') < 0){
					$prestamosCercanos .= "<a class='navbar-item is-danger'>La fecha límite del préstamo de ".$prestamos[$x]['articulo']." a ".$prestamos[$x]['destino']." ha pasado</a>";
				}else{
					$prestamosCercanos .= "<a class='navbar-item'>La fecha límite del préstamo de ".$prestamos[$x]['articulo']." a ".$prestamos[$x]['destino']." es en ".$interval->format('%a')." día(s)</a>";
				}
			}
	  	}
	  	if ($prestamosCercanos != ""){
	  		$temp = '
	  			<div class="navbar-end">
	            	<div class="navbar-item has-dropdown is-hoverable">
	                	<div class="navbar-item">
		                	<a class="button is-warning">
		                		<span class="icon is-large">
		                			<img class="" src="resources/alert.svg">
		                		</span>
		                	</a>
	                	</div>
							
					    <div class="navbar-dropdown is-right">
					 	'.$prestamosCercanos.'
					    </div>
				    </div>
				</div>
	  		';
	  		$prestamosCercanos = $temp;
	  	}
	  	return $prestamosCercanos;
  	}	
 ?>