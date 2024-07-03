<?php
// Conexión a la base de datos
$conec = mysqli_connect('localhost', 'root', '', 'inventario');
if (!$conec) {
    die('No se pudo conectar con la base de datos: ' . mysqli_connect_errno());
}
include 'helpers.php';
// Consulta SQL
$lastNDays = isset($_GET['lastNDays']) ?  $_GET['lastNDays'] : 7;
$query = "SELECT historial_operaciones.*, historial_operaciones_articulos.*, divisiones.nombre_division
          FROM historial_operaciones
          LEFT JOIN historial_operaciones_articulos ON historial_operaciones.id = historial_operaciones_articulos.id_operacion LEFT JOIN divisiones ON historial_operaciones.destino = divisiones.id
          WHERE historial_operaciones.fecha_operacion >= DATE_SUB(CURDATE(), INTERVAL ".$lastNDays." DAY)";
$resultado = mysqli_query($conec, $query);

// Verificar si la consulta fue exitosa
if (!$resultado) {
    die('Error en la consulta: ' . mysqli_error($conec));
}

// Inicializar contadores y arrays para destinos y días
$totalOperaciones = 0;
$tiposOperaciones = array();
$destinosFrecuentes = array();
$diasMayoresOperaciones = array();

// Iterar los resultados
while ($fila = $resultado->fetch_assoc()) {
    $totalOperaciones++;
    $tipoOperacion = $fila['tipo_operacion'];
    $destino = $fila['nombre_division']; // Asumiendo que este es el nombre del destino
    $fechaOperacion = $fila['fecha_operacion']; // Asumiendo que esta es la fecha de la operación

    // Contar tipos de operaciones
    if (!isset($tiposOperaciones[$tipoOperacion])) {
        $tiposOperaciones[$tipoOperacion] = 0;
    }
    $tiposOperaciones[$tipoOperacion]++;

    // Contar destinos frecuentes
    if (!isset($destinosFrecuentes[$destino])) {
        $destinosFrecuentes[$destino] = 0;
    }
    $destinosFrecuentes[$destino]++;

    // Extraer solo la parte de la fecha (año, mes, día) de la fecha de operación
    $fechaSinHora = date('Y-m-d', strtotime($fechaOperacion));

    // Contar días con mayores operaciones
    if (!isset($diasMayoresOperaciones[$fechaSinHora])) {
        $diasMayoresOperaciones[$fechaSinHora] = 0;
    }
    $diasMayoresOperaciones[$fechaSinHora]++;
}


$estadisticasTipos = "";
foreach ($tiposOperaciones as $tipo => $cantidad){
            $estadisticasTipos .= "<li>".$tipo.":".$cantidad."</li>";
     }
// Cerrar el resultado
mysqli_free_result($resultado);

// Suponiendo que $tiposOperaciones es tu array de datos
$dataPointsTipoOperacion = array();
foreach ($tiposOperaciones as $tipo => $cantidad) {
    $dataPoint = array("label" => $tipo, "y" => $cantidad);
    array_push($dataPointsTipoOperacion, $dataPoint);
}

// Suponiendo que $destinosFrecuentes es tu array de datos
$dataPointsDestinos = array();
foreach ($destinosFrecuentes as $destino => $cantidad) {
    $dataPoint = array("label" => $destino, "y" => $cantidad);
    array_push($dataPointsDestinos, $dataPoint);
}

// Suponiendo que $diasMayoresOperaciones es tu array de datos
$dataPointsDias = array();
foreach ($diasMayoresOperaciones as $dia => $cantidad) {
    $dataPoint = array("label" => $dia, "y" => $cantidad);
    array_push($dataPointsDias, $dataPoint);
}
echo '
	<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Estadísticas</title>
		<link rel="stylesheet" href="css/output.css">
		<script src="./canvasjs-chart-3.7.43/canvasjs.min.js"></script>
	</head>
	<body class="w-11/12 mx-auto">
	'.$header.'
    <h1 class="mt-28 text-6xl font-rubik text-sky-900 font-bold">Estadísticas de Operaciones</h1>

        <form class="font-karla w-full flex flex-col gap-2 items-center justify-center my-6" id="redirectForm" method="get">
        <select class="rounded-xl py-2 px-3" id="timeFrame" name="lastNDays">
            <option value="7">7 dias</option>
            <option value="15">15 dias</option>
            <option value="30">30 dias</option>
            <option value="90">3 meses</option>
        </select>
        <input class="bg-blue-500 cursor-pointer text-white hover:text-blue-950 rounded-xl hover:bg-white px-4 py-2" type="submit" value="Generar">
    </form>
    <p class="text-center font-karla font-bold text-xl text-blue-950">Total de operaciones en los últimos '.$lastNDays.' días: '.$totalOperaciones.'</p>
	<div class="mx-auto my-4" id="operacionesMasFrecuentes" style="height: 370px; width: 80%;"></div>
    <div class="mx-auto my-4" id="chartContainerDias" style="height: 370px; width: 80%;"></div>
    <div class="mx-auto my-4" id="chartContainerDestinos" style="height: 370px; width: 80%;"></div>
    <script>
                document.getElementById("redirectForm").addEventListener("submit", function(event) {
            event.preventDefault(); // Prevent the default form submission
            var selectedValue = document.getElementById("timeFrame").value;
            window.location.href = window.location.href.split("?")[0] + "?lastNDays=" + selectedValue;
        });
        var dataPointsTipoOperacion = '.json_encode($dataPointsTipoOperacion, JSON_NUMERIC_CHECK).'

        var chart = new CanvasJS.Chart("operacionesMasFrecuentes", {
            theme: "light2",
            animationEnabled: true,
            title: {
                text: "Cantidad de Operaciones por Tipo"
            },
            data: [{
                type: "column",
                dataPoints: dataPointsTipoOperacion
            }]
        });

        chart.render();

        var dataPointsDias = '.json_encode($dataPointsDias, JSON_NUMERIC_CHECK).';
        var chartDias = new CanvasJS.Chart("chartContainerDias", {
        theme: "light2",
        animationEnabled: true,
        title: {
            text: "Días con Mayor Cantidad de Operaciones"
        },
        data: [{
            type: "column",
            dataPoints: dataPointsDias
        }]
    });

    chartDias.render();

        var dataPointsDestinos = '.json_encode($dataPointsDestinos, JSON_NUMERIC_CHECK).';

    var chartDestinos = new CanvasJS.Chart("chartContainerDestinos", {
        theme: "light2",
        animationEnabled: true,
        title: {
            text: "Destinos Más Frecuentes"
        },
        data: [{
            type: "column",
            dataPoints: dataPointsDestinos
        }]
    });

    chartDestinos.render();
    </script>
</body>

'

?>