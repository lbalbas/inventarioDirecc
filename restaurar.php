<?php
// Verificar si el archivo fue subido
if(isset($_FILES['archivo_sql']) && $_FILES['archivo_sql']['error'] == UPLOAD_ERR_OK){
    // Ruta donde se guardará temporalmente el archivo
    $temporal = 'uploads/'. basename($_FILES['archivo_sql']['name']);
    
    // Mover el archivo subido a la carpeta temporal
    move_uploaded_file($_FILES['archivo_sql']['tmp_name'], $temporal);
    
    // Conexión a la base de datos
    $host = 'localhost';
    $usuario = 'root'; // Cambiar según configuración
    $password = ''; // Cambiar según configuración
    $nombreBaseDatos = 'inventario'; // Cambiar según necesidad
    
    try {
        $conexion = new PDO("mysql:host=$host;dbname=$nombreBaseDatos", $usuario, $password);
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Abrir el archivo SQL y leerlo línea por línea
        $sqlFile = fopen($temporal, "r");
        if ($sqlFile) {
            while (($line = fgets($sqlFile))!== false) {
                // Ejecutar cada línea como comando SQL
                $conexion->exec($line);
            }
            fclose($sqlFile);
            
            echo "La base de datos ha sido restaurada exitosamente.";
        } else {
            echo "Error al abrir el archivo SQL.";
        }
    } catch(PDOException $e) {
        die("ERROR: ". $e->getMessage());
    }
} else {
    echo "Error al subir el archivo.";
}
echo '<html>
<head>
    <title>Restaurar Base de Datos</title>
</head>
<body>
    <form action="" method="post" enctype="multipart/form-data">
        Selecciona el archivo SQL:
        <input type="file" name="archivo_sql" id="archivo_sql">
        <br><br>
        <input type="submit" value="Subir y Restaurar">
    </form>
</body>
</html>';
?>

