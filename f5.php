<?php
function porc_conex(&$d) {
	global $bd_insp;

	$numeros = array_keys($d);
	if (count($d) == 0) {
		return [];
	}

	$numeros = join(', ', array_keys($d));

	$sql = "SELECT calledstation, COUNT(*) n, SUM( CASE WHEN sessiontime > 0 THEN 1 ELSE 0 END ) AS 'conectadas', MAX(sessiontime) AS maxt FROM inspector WHERE calledstation IN( {$numeros} ) GROUP BY calledstation; ";
	$res = $bd_insp->sql2array($sql);
	foreach ($res as $z) {
		$z['porc'] = round($z['conectadas'] * 100.0 / $z['n'], 2);
		$d[$z['calledstation']]['f5'] = $z;
	}
}
