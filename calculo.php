<?php
function calcula_pesos(&$d) {
	global $CFG;
	foreach ($d as $num => &$z) {
		//ver($z);
		$p = 0;

		/*
			 * f1 cuando el número de intentos sea mayor
			 * o igual que el dado en config, 40p.
			 * En otro caso 0.
		*/
		$z['p']['f1'] = isset($z['f1']) ? $CFG['peso_f1'] : 0;

		/*
			 * f2 cuando exista f2 diferente de vacío, 10p
		*/
		$z['p']['f2'] = isset($z['f2']) ? $CFG['peso_f2'] : 0;

		/*
			 * f3 Si hay mas de 2 intervalos y alguno de los tiempos
			 * fuera de  [10, 25],
			 * se asigna 20p
		*/
		$t_en_intervalo = 0;
		$z['p']['f3'] = 0;
		if (isset($z['f3'])) {
			$nfilas = count($z['f3']['intervalos']);
			foreach ($z['f3']['intervalos'] as $interv) {
				if ($interv['segundos'] < $CFG['f3_interv_min'] ||
					$interv['segundos'] > $CFG['f3_interv_max']) {
					$t_en_intervalo = 1;
					break;
				}
			}
			if ($nfilas >= 2 && $t_en_intervalo == 1) {
				$z['p']['f3'] = $CFG['peso_f3'];
			}
		}

		/*
			 *f4 Si la tabla tiene una fila 10p, 2 o más filas 20p
		*/
		if (isset($z['f4'])) {
			$z['p']['f4'] = $CFG['peso_f4_1'];
			if (count($z['f4']['duracion']) >= 2) {
				$z['p']['f4'] += $CFG['peso_f4_2'] - $CFG['peso_f4_1'];
			}
		} else {
			$z['p']['f4'] = 0;
		}

		/*
			 * f5 45% o más -20p. Menos de 45% +10p
		*/

		if (isset($z['f5']['tmax'])) {
			if (($z['f5']['porc'] >= $CFG['f5_porc_lim']) or
				($z['f5']['tmax'] >= $CFG['f5_t_max'])) {
				$z['p']['f5'] = $CFG['peso_f5_m'];
			}
		} else {
			$z['p']['f5'] = $CFG['peso_f5_p'];
		}

		/*
			 * f6 30% o más 20p en causa 4
		*/

		$temp_porc = 100;
		$temp_causa = 0;
		foreach ($z['f6'] as $d6) {
			if ($d6['terminatecauseid'] == 4) {
				$temp_causa = $d6['n'];
			}
			if ($d6['terminatecauseid'] == NULL) {
				$temp_porc = $d6['n'];
			}
		}
		if (($temp_causa * 100.0 / $temp_porc) >= $CFG['f6_porc4']) {
			$z['p']['f6'] = $CFG['peso_f6_4'];
		} else {
			$z['p']['f6'] = 0;
		}

		$xmax = $CFG['peso_f1'] + $CFG['peso_f2'] + $CFG['peso_f3']
		 	+ $CFG['peso_f4_2'] + $CFG['peso_f5_p'] + $CFG['peso_f6_4'];

		$xmin = $CFG['peso_f5_m'];

		$z['p']['tot'] = $z['p']['f1'] + $z['p']['f2'] + $z['p']['f3']
		 	+ $z['p']['f4'] + $z['p']['f5'] + $z['p']['f6'];

		$z['p']['porc'] = round(100.0 * ($z['p']['tot'] - $xmin) / ($xmax - $xmin), 2);
	}
}
