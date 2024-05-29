<?php  
$conec = mysqli_connect('localhost', 'root', '', 'inventario');

if(isset($_POST["contraseña"])){
    $encrypted_password = md5($_POST["contraseña"]);
        $update_query = "UPDATE `usuarios` SET `contrasena` = '$encrypted_password' WHERE `nombre_usuario` = '".$_GET['usuario']."'";
        mysqli_query($conec, $update_query);
        echo "<script>alert('Contraseña actualizada exitosamente');
        document.location = '/login.php';</script>";
        exit();
}else if(isset($_POST["respuesta"])){
    $check_response_query = "SELECT `respuesta_recup` FROM `usuarios` WHERE `nombre_usuario` = '".$_GET['usuario']."'";
    $response_check_result = mysqli_query($conec, $check_response_query);
    $response_check_row = mysqli_fetch_assoc($response_check_result);
    if (md5($_POST["respuesta"]) !== $response_check_row["respuesta_recup"]) {
        echo "<script>alert('Respuesta incorrecta. Por favor, intenta de nuevo.');</script>";
        promptForAnswer($conec);
    } else {
        echo '
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperación de Contraseña</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link href="./css/output.css" rel="stylesheet">
    <link href="./datatables.min.css" rel="stylesheet">
</head>
<body class="w-11/12 mx-auto">
        <h1 class="mt-28 mb-10 text-4xl font-rubik text-sky-900 font-bold">Establecer Nueva Contraseña</h1>
        <form class="justify-between mx-auto rounded-xl bg-gray-100 shadow-4xl bg-opacity-70 flex flex-col gap-6 p-10 font-karla text-gray-400" method="POST" action="">
        <div class="flex flex-col w-72">
            <label for="nueva_contraseña">Nueva Contraseña:</label>
            <input  class="w-full bg-gray-50 shadow-inner px-4 py-2" type="password" id="nueva_contraseña" name="contraseña" required>
        </div>
        <div class="flex flex-col w-72">
            <label for="confirmar_contraseña">Confirmar Contraseña:</label>
            <input  class="w-full bg-gray-50 shadow-inner px-4 py-2" type="password" id="confirmar_contraseña" name="confirmar_contraseña" required>
        </div>
            <input class="bg-blue-500 w-48 cursor-pointer text-white hover:text-blue-950 rounded-xl hover:bg-white px-4 py-2" type="submit" id="submitBtn" value="Cambiar Contraseña" disabled>
            <span class="text-rose-400 bold hidden" id="passCheck">Las contraseñas no coinciden.</span>
        </form>

    <script>
        document.getElementById("confirmar_contraseña").addEventListener("input", function() {
            var nuevaContraseña = document.getElementById("nueva_contraseña").value;
            var confirmarContraseña = document.getElementById("confirmar_contraseña").value;

            if(nuevaContraseña === confirmarContraseña) {
                document.getElementById("submitBtn").disabled = false;
                document.getElementById("passCheck").classList.add("hidden");
            } else {
                document.getElementById("submitBtn").disabled = true;
                document.getElementById("passCheck").classList.remove("hidden");
            }
        });
        document.getElementById("nueva_contraseña").addEventListener("input", function() {
            var nuevaContraseña = document.getElementById("nueva_contraseña").value;
            var confirmarContraseña = document.getElementById("confirmar_contraseña").value;

            if(nuevaContraseña === confirmarContraseña) {
                document.getElementById("submitBtn").disabled = false;
                document.getElementById("passCheck").classList.add("hidden");
            } else {
                document.getElementById("submitBtn").disabled = true;
                document.getElementById("passCheck").classList.remove("hidden");
            }
        });
    </script>
</body>
</html>';
    }
}else if(isset($_GET["usuario"])){
    promptForAnswer($conec);
}else{
    echo '<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperación de Contraseña</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link href="./css/output.css" rel="stylesheet">
    <link href="./datatables.min.css" rel="stylesheet">
</head>
<body class="w-11/12 mx-auto">
    <div>
        <h1 class="mt-28 mb-10 text-4xl font-rubik text-sky-900 font-bold">Recuperación de Contraseña</h1>
        <form class="justify-between mx-auto rounded-xl bg-gray-100 shadow-4xl bg-opacity-70 flex flex-col items-start p-10 font-karla text-gray-400 gap-6" id="usuarioForm">
         <div class="flex flex-col w-72">
         <label class="flex items-center gap-2" for="usuario">Nombre de Usuario</label>
            <input  class="w-full bg-gray-50 shadow-inner px-4 py-2" type="text" name="usuario" id="usuario"/>
            </div>
            <input class="bg-blue-500 w-36 cursor-pointer text-white hover:text-blue-950 rounded-xl hover:bg-white px-4 py-2" type="button" value="Enviar" onclick="redirectWithQuery()" />
        </form>
    </div>

    <script>
        function redirectWithQuery() {
            var usuarioInput = document.getElementById("usuario").value;
            window.location.href = "?usuario=" + encodeURIComponent(usuarioInput);
        }
    </script>
</body>
</html>';
}


function promptForAnswer($conec){
    $preg = "SELECT pregunta_recup FROM `usuarios` WHERE `nombre_usuario` = '".$_GET['usuario']."'";
    $query = mysqli_query($conec, $preg);
    $result = mysqli_fetch_all($query, MYSQLI_ASSOC)[0];
    echo '<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Recuperación de Contraseña</title>
        <link rel="stylesheet" href="css/estilo.css">
        <link href="./css/output.css" rel="stylesheet">
        <link href="./datatables.min.css" rel="stylesheet">

    </head>
    <body class="w-11/12 mx-auto">
        <div>
        <h1 class="mt-28 mb-10 text-4xl font-rubik text-sky-900 font-bold">Recuperación de Contraseña</h1>
            <form class="justify-between mx-auto rounded-xl bg-gray-100 shadow-4xl bg-opacity-70 flex flex-col p-10 gap-6 font-karla text-gray-400" method="POST" action="">
            <div class="flex flex-col w-72">
                        <label class="flex items-center gap-2 font-bold" >'.$result["pregunta_recup"].'</label>
            <input  placeholder="Respuesta" class="w-full bg-gray-50 shadow-inner px-4 py-2" type="text" name="respuesta" id="respuesta"/>
            </div>
            <input class="bg-blue-500 cursor-pointer text-white hover:text-blue-950 rounded-xl hover:bg-white px-4 py-2 w-36" type="submit" />
            </form>
        </div>
    </body>
    </html>';
}

?>


