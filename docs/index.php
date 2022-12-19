<?php require_once 'config/datos.php';?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="css/index.css">
	<title>Inspector: Lista Diaria</title>

</head>
<body>
<div id="formulario">
	<?=$mensaje?>
	<form action="index.php" method="POST">
		<input type="hidden" name="a" id="a" value="generar">
		<input type="date" id="fecha" name="fecha">
		<input type="checkbox" name="escribir" id="escribir" value="escribir">
		<label for="escribir">¿Modificar?</label>
		<input type="submit" name="enviar" value="Generar">
	</form>
</div>
<div id="tabla">
	<table>
		<thead>
			<tr>
				<th>Día</th><th>Fecha</th><th>Op.</th><th>n1</th><th>n2</th><th>--</th>
			</tr>
		</thead>
		<tbody>
<?php
foreach ($lista2 as $dia) {
	$fecha = $dia['id'];
	$a = $dia['op'];
	list($y, $m, $d) = explode('-', $fecha);
	$i = 0;
	$n = count($a) - 1;
	foreach ($a as $op => $p) {
		$diat = ($i == 0 ? "<td rowspan='{$n}'>{$p['dd']}</td>" : '');
		$fechat = ($i == 0 ? "<td style='text-align: center;' rowspan='{$n}'>{$d}-{$m}<br>{$y}</td>" : '');
		$i = 1;
		if ($op != 'total') {
			echo "
				<tr>
					{$diat}{$fechat} <td>{$op}</td><td>{$p['n1']}</td><td>{$p['n2']}</td>
					<td><a href='fecha.php?f={$fecha}&op={$op}' target='_BLANK'>VER</a></td>
				</tr>";
		} else {
			echo "
				<tr class='total'>
					<td colspan='3'>Total</td> <td>{$p['n1']}</td> <td>{$p['n2']}</td>
					<td><!-- a href='fecha.php?f={$fecha}&op={$op}' target='_BLANK'>VER</a --></td>
				</tr>";
		}
	}
}
?>
		</tbody>
	</table>
</div>
<div id="graficoa">
	<canvas id="miChart"></canvas>
</div>


<div id="graficob">
		<canvas id="semana"></canvas>
</div>

<script src="config/chartjs/chart.min.js"></script>
<script>
  const data = {
  	labels: ['<?=join("','", $labels)?>'],
    datasets: [{
      label: 'Sospechosos',
      backgroundColor: 'rgb(55, 99, 132)',
      borderColor: 'rgb(55, 99, 132)',
      data: [<?=join(',', $graf1)?>],
    },{
      label: 'Llamadas analizadas',
      backgroundColor: 'rgb(255, 99, 132)',
      borderColor: 'rgb(255, 99, 132)',
      data:[<?=join(',', $graf2)?>],
    }]
  };

  const config = {
    type: 'line',
    data: data,
    options: {
		plugins: {
			title: {
				display: true,
				text: 'Llamadas analizadas y sospechosas por día'
			}
		}
	}

  };

  const data2 = {
  	labels:['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
  	  datasets: [
    {
      label: 'Semanal',
      data: [<?=join(',', $semana)?>],
      backgroundColor: ['#729FCF','#8AE234','#EDD400','#EF2929','#AD7FA8','#888A85','#C17D11'],
    }
  ]
  };

const config2 = {
	type: 'pie',
	data: data2,
	options: {
		plugins: {
			title: {
				display: true,
				text: 'Incidencias por día de semana'
			}
		}
	}
};


</script>
<script>
  const miChart = new Chart(document.getElementById('miChart'),config);
  const semana = new Chart(document.getElementById('semana'),config2);
</script>
</body>
</html>