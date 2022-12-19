<?php
/**
 * Une los resultados de cada función de análisis de inspector
 *
 * @param  myBD  $bd_insp Conexión PDO con la Base de Datos
 * @param  string $fecha   Fecha del día a analizar en formato 'yyyy-mm-dd'
 * @return none
 */
function consolidar(myBD $bd_insp, string $fecha, $accion) {
	global $llamadas_analizadas;
	$f1 = obten_listado($bd_insp, $fecha);
	$f2 = obten_origenes($bd_insp, $fecha);
	$f3 = obten_intervalo($bd_insp, $fecha);
	$f4 = obten_tiempo_igual_sesion($bd_insp, $fecha);

	$d = [];

	# f1
	foreach ($f1 as $ll) {
		$d[$ll['calledstation']]['f1'] = ['intentos' => $ll['n']];
	}

	# f2
	foreach ($f2 as $ll) {
		$d[$ll['calledstation']]['f2'] = ['intentos' => $ll['n'], 'norigenes' => $ll['norigenes']];
	}

	# f3
	foreach ($f3 as $ll) {
		if (count($ll['duracion']) < $ll['total_int']) {
			if (isset($ll['zz'])) {
				$ll['zz'] = ordenar_intervalos($ll['zz']);
				$d[$ll['numero']]['f3'] = ['intervalos' => $ll['zz']];

			}
		}
	}

	# f4
	foreach ($f4 as $numero => $data) {
		$d[$numero]['f4'] = ['duracion' => $data];
	}

	# f5
	porc_conex($d);

	# f6
	causa_desconexion($d);

	calcula_pesos($d);

	ordenar_por_peso($d);

	if ($accion == 'WRITE') {
		//actualizar_listas($d);
	}

	guardar_pagina($bd_insp, $d, $llamadas_analizadas, $fecha);
	//hacer_pagina($d);
}

function ordenar_intervalos($d) {
	$salida = [];
	foreach ($d as $dd) {
		$salida[$dd['segundos']] = $dd['cantidad'];
	}
	arsort($salida);

	$salida2 = [];
	foreach ($salida as $segundos => $cantidad) {
		$salida2[] = ['segundos' => $segundos, 'cantidad' => $cantidad];
	}

	return $salida2;
}

function ordenar_por_peso(&$d) {
	$temp1 = [];

	foreach ($d as $telf => $valor) {
		$temp1[$telf] = $valor['p']['porc'];
	}

	arsort($temp1);
	foreach ($temp1 as $telf => $value) {
		$temp1[$telf] = $d[$telf];
	}
	$d = $temp1;
}

function guardar_pagina(myBD $bd_insp, array $d, int $llamadas_analizadas, string $fecha) {
	global $CFG;
	$sql = "REPLACE INTO paginas(id, operadora, n1, n2, pagina) VALUES (?,?,?,?,?)";
	$bd_insp->ins($sql, ['ssiis', $fecha, $CFG['prefijo'], count($d), $llamadas_analizadas, serialize($d)]);
}