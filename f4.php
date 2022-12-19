<?php

function obten_tiempo_igual_sesion(myBD $bd_insp, string $fecha) {
	global $CFG;

	$min_sessiontime = $CFG['min_sessiontime'];

	$d1 = $bd_insp
		->sql2options(
			'SELECT sessiontime, COUNT(*) n FROM inspector WHERE sessiontime >= ? GROUP BY sessiontime HAVING n > 1',
			['i', $min_sessiontime]
		);

	$d2 = [];
	foreach ($d1 as $sessiontime => $n) {
		$sql = "SELECT calledstation, COUNT(*) nn FROM inspector WHERE sessiontime = ? GROUP BY calledstation HAVING nn > 1";
		$temp = $bd_insp->sql2options($sql, ['i', $sessiontime]);
		if (count($temp) > 0) {
			$d2[$sessiontime] = [$temp, $n];
		}
	}

	$salida = [];
	foreach ($d2 as $sessiontime => $llamadas) {
		foreach ($llamadas[0] as $numero => $cant) {
			$salida[$numero][] = ['cantidad' => $cant, 'sessiontime' => $sessiontime];
		}
	}
	ksort($salida);
	return $salida;
}
