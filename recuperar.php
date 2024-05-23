<?php  
$conec = mysqli_connect('localhost', 'root', '', 'inventario');

if(isset($_POST["contraseña"])){
    $encrypted_password = md5($_POST["contraseña"]);
        $update_query = "UPDATE `usuarios` SET `contrasena` = '$encrypted_password' WHERE `nombre_usuario` = '".$_GET['usuario']."'";
        mysqli_query($conec, $update_query);
        echo "<script>alert('Contraseña actualizada exitosamente');
        document.location('/login.php');</script>";
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
    <div>
        <form method="POST" action="">
            <label for="nueva_contraseña">Nueva Contraseña:</label>
            <input type="password" id="nueva_contraseña" name="contraseña" required>
            <label for="confirmar_contraseña">Confirmar Contraseña:</label>
            <input type="password" id="confirmar_contraseña" name="confirmar_contraseña" required>
            <input type="submit" id="submitBtn" value="Cambiar Contraseña" disabled>
        </form>
    </div>

    <script>
        document.getElementById("nueva_contraseña").addEventListener("input", function() {
            var nuevaContraseña = document.getElementById("nueva_contraseña").value;
            var confirmarContraseña = document.getElementById("confirmar_contraseña").value;

            if(nuevaContraseña === confirmarContraseña) {
                document.getElementById("submitBtn").disabled = false;
            } else {
                document.getElementById("submitBtn").disabled = true;
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
        <h1>Introduzca su nombre de usuario</h1>
        <form id="usuarioForm">
            <input type="text" name="usuario" id="usuario"/>
            <input type="button" value="Enviar" onclick="redirectWithQuery()" />
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
            <h1>Pregunta de Seguridad</h1>
            <h2>'.$result["pregunta_recup"].'</h2>
            <form method="POST" action="">
            <input type="text" name="respuesta" id="respuesta"/>
            <input type="submit" />
            </form>
        </div>
    </body>
    </html>';
}

?>


