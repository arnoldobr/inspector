<?php
/**
 * Obten
 * @param  myPDO  $bd_insp Conexión PDO con la Base de Dstos
 * @param  string $fecha   [description]
 * @return [type]          [description]
 */
function obten_origenes(myBD $bd_insp, string $fecha) {
	global $CFG;
	global $bd_insp;
	$porcentaje = $CFG['porc_origenes'];

	//Contar cuanto origenes iguales tiene un número
	//Origenes... fracción norigenes /totalintentos > 0.x
	global $fecha;
	$sql = "
		SELECT calledstation, COUNT(*) as n, count(DISTINCT callerid) as norigenes
		FROM inspector
    	GROUP BY calledstation
		HAVING n > 3
		ORDER BY n DESC
	";
	$d = $bd_insp->sql2array($sql);
	$salida = [];
	$salida_txt = '';
	foreach ($d as $l) {
		if (($l['p'] = $l['norigenes'] / $l['n']) >= $porcentaje) {
			$salida[] = $l;
			$salida_txt .=
			$l['calledstation']
			. ', ' . $l['n']
			. ', ' . $l['norigenes']
			. ', ' . round($l['p'], 2)
				. "\n";
		}
	}
	return $salida;
}
