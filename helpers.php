<?php  
	$header = '
    <nav>
      <div class="nav-logo">
        <div onclick="burgerShowMenu()" id="burger" class="nav-burger">
          <div class="line line2"></div>
        </div>
      </div>
      <div id="nav-menu" class="nav-menu">
        <div class="nav-start">
          <a href="/index.php" class="nav-item"><img class="nav-icon" src="./resources/home-outline.svg"></img>Inicio</a>
          <a href="/insertar.php" class="nav-item"><img class="nav-icon" src="./resources/create-outline.svg"></img>Registrar Articulo</a>
          <a href="/prestamos.php" class="nav-item"><img class="nav-icon" src="./resources/swap-vertical-outline.svg"></img>Traspasos Temporales</a>
          <a href="/direcciones.php" class="nav-item"><img class="nav-icon" src="./resources/business-outline.svg"></img>Direcciones</a>
          <a href="/historico.php" class="nav-item"><img class="nav-icon" src="./resources/documents-outline.svg"></img>Historial de Operaciones</a>
          <a href="/historial_respaldo.php?m=dosis" class="nav-item"><img class="nav-icon" src="./resources/list-circle-sharp.svg"></img>Historial de Respaldos</a>
          <a href="/reportes.php" class="nav-item"><img class="nav-icon" src="./resources/bar-chart-outline.svg"></img>Reportes</a>
        </div>
        <div class="nav-end">
                        <a class="nav-item" href="/login.php?registro=true"><img class="nav-icon" src="./resources/person-add-outline.svg"></img>Registrar Usuarios</a>
                        <a class="nav-item" href="#" onclick="respaldoVentana()"><img class="nav-icon" src="./resources/save-outline.svg"></img>Respaldar Datos</a>
                        <a class="nav-item" href="/login.php?cerrarsesion=true"><img class="nav-icon" src="./resources/log-out-outline.svg"></img>Cerrar Sesión</a>
        </div>
      </div>
      <script language="javascript">
        function respaldoVentana(){
            if(window.confirm("¿Desea crear un respaldo de la base de datos?")){
                window.open("/respaldo.php","_blank");
            }
        }
        var menu = document.getElementById("nav-menu");
        var burger = document.getElementById("burger");
        function burgerShowMenu() {
            burger.classList.toggle("show");
            menu.classList.toggle("show");
        }
      </script>
    </nav>';
?>