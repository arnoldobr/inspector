<?php require_once 'config/datos.php';
$fecha = $_GET['f'];
$op = $_GET['op'];

foreach ($CFG['op'] as $operadora) {
	if ($operadora['prefijo'] == $op) {
		foreach ($operadora as $key => $value) {
			$CFG[$key] = $value;
		}
	}
}

$p = $my_bd->sql2row('SELECT id,operadora op, n1,n2,pagina d FROM paginas WHERE id = ? AND operadora = ?', ['ss', $fecha, $op]);

$tabla = hacer_tabla(unserialize($p['d']));
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
	<link rel="stylesheet" href="./css/estilo.css">
</head>
<body>
<h3>Total de llamadas analizadas: <?=$p['n1']?>/<?=$p['n2']?> (<?=$p['id']?> - <?=$CFG['nombre']?> <?=$p['op']?>)</h3>

<?=$tabla?>

</body>
</html>