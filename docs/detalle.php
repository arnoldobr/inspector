<?php
$porc = $_GET['porc'] ?? 'porc';
$num = $_GET['t'] ?? 't';
$fecha = $_GET['f'] ?? 'f';
$dirp = $_GET['d'] ?? 'd';
$orden = $_GET['o'] ?? 'starttime asc';

$CFG = parse_ini_file("./config/config.php", true);
require_once "./config/myBD.php";
//require_once '../debug.php';
$my_bd = new myBD(
	$CFG['bd_insp']['host'],
	$CFG['bd_insp']['bd'],
	$CFG['bd_insp']['login'],
	$CFG['bd_insp']['pass']);

$sql = "select *, SUBSTRING(starttime, 1,10) fecha from consolidado where calledstation = '{$num}' ORDER BY {$orden}";

//vq($my_bd->sql2array($sql));
$d = $my_bd->sql2array($sql);
$nll = count($d);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Detalle del número <?=$num?></title>
	<link rel="stylesheet" type="text/css" href="libs/datatables/datatables.min.css" />
	<link rel="stylesheet" type="text/css" href="css/detalle.css">
</head>
<body>
	<h3>Detalle del número: <?=$num?> (<?=$nll?> llamadas)</h3>
	<table id="miTabla">
		<thead>
			<tr>
				<th>N°</th>
				<th>FECHA</th>
				<th>USER</th>
				<th>ORIGEN</th>
				<th>DURACIÓN</th>
				<th>CAUSA DESC.</th>
				<th>AUDIO</th>
			</tr>
		</thead>
		<tfoot>
		</tfoot>
		<tbody>
<?php
foreach ($d as $key => $dd) {
	$n = $key + 1;
	$audio = $dd['sessiontime'] ? "<audio controls src=\"http://201.243.68.28/aup/{$dd['fecha']}/{$dd['src']}-{$num}.{$dd['id']}.mp3\"></audio>" : "";
	echo <<<LRDTAB
<tr>
	<td>{$n}</td>
	<td>{$dd['starttime']}</td>
	<td>{$dd['src']}</td>
	<td>{$dd['callerid']}</td>
	<td align="center">{$dd['sessiontime']}</td>
	<td align="center">{$dd['terminatecauseid']}</td>
	<td>{$audio}</td>
</tr>
LRDTAB;
}
?>
		</tbody>
	</table>
<script type="text/javascript" src="libs/jquery/jquery-3.6.0.min.js"></script>
<!-- script type="text/javascript" src="libs/datatables/DataTables-1.12.1/js/jquery.dataTables.min.js"></script -->
<script type="text/javascript" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
	$(document).ready( function () {
    	$('#miTabla').DataTable({
        paging: false,
        //ordering: false,
        //info: false,
    		});
	} );
</script>
</body>
</html>
