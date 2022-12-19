<?php

/**
 * Obtiene el listado de las llamadas que se repitieron
 * @param  myBD   $bd_insp Base de datos inspector
 * @param  string $fecha   Fecha que se procesa
 * @return array          Array con las llamadas repetidas
 */
function obten_listado(myBD $bd_insp, string $fecha) {
	global $CFG;
	$n_llamadas = $CFG['n_llamadas'];
	$llamadas = $bd_insp
		->sql2array('SELECT calledstation, count(*) n FROM inspector GROUP by calledstation ORDER BY n DESC');

	// $salida = '';
	$llamadas_bloq = [];
	$intentos_acumulados = 0;
	foreach ($llamadas as $d) {
		// $salida .= $d['calledstation'] . ', ' . $d['n'] . "\n";
		if ($d['n'] >= $n_llamadas) {
			$llamadas_bloq[] = $d;
			$intentos_acumulados += $d['n'];
		}
	}
	// echo $salida;
	// echo $intentos_acumulados;
	return $llamadas_bloq;
}
