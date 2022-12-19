<?php




function obten_intervalo(myBD $bd_insp, string $fecha) {
	//intervalo entre llamadas (startime) n intervalos iguales
	$sql = "
		SELECT calledstation, COUNT(*) as n
		FROM inspector
		GROUP BY calledstation
		HAVING n > 2;
	";
	$d = $bd_insp->sql2array($sql);

	$resultados = [];

	foreach ($d as $l) {
		$sql = "
			SELECT
				starttime,
				TIME_TO_SEC(starttime) segundos
			FROM inspector
			WHERE calledstation = '{$l['calledstation']}'
			ORDER BY starttime ASC
		";

		$llamadas = $bd_insp->sql2array($sql);

		$n = count($llamadas) - 1;
		$diff = [];
		for ($i = 0; $i < $n; $i++) {
			$temp = $llamadas[$i + 1]['segundos'] - $llamadas[$i]['segundos'];
			@$diff[$temp]++;
			# @$intervalos[$temp]++; # Para ver los intervalos
		}

		$z = [];
		$zz = [];
		$total = 0;
		foreach ($diff as $intervalo => $cant) {
			$z[] = ['segundos' => $intervalo, 'cantidad' => $cant];
			$total += $cant;
			if ($cant > 1) {
				$zz[] = ['segundos' => $intervalo, 'cantidad' => $cant];
			}
		}

		$resultados[] = ['numero' => $l['calledstation'],
			'llamadas' => $llamadas,
			'duracion' => $z,
			'zz' => $zz,
			'total_int' => $total,
		];
	}
	return $resultados;
}
