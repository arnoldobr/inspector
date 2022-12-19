<?php

function hacer_pagina($d) {
	global $CFG;
	global $llamadas_analizadas;
	global $fecha;

	$encontradas = count($d);
	$m_llamadas_analizadas = $encontradas . '/' . $llamadas_analizadas . " ($fecha)";
	$tabla = hacer_tabla($d);
	$cab = file_get_contents($CFG['ruta'] . '/cab.html');
	$cab = explode('|||', $cab);
	$pie = file_get_contents($CFG['ruta'] . '/pie.html');

	file_put_contents($CFG['ruta'] . "/docs/{$fecha}.html",
		$cab[0] . $m_llamadas_analizadas . $cab[1] . $tabla . $pie);

}

function hacer_tabla($d) {
	global $CFG;
	global $fecha;
	$dirp = substr(__FILE__, 0, -strlen('hacer_tabla.php') - 2);
	$tabla = '';
	if ($d == null) {
		return $tabla;
	}

	if (count($d) > 0) {
		$n = 0;
		$tabla .= '
<table id="tabla_principal">
	<thead>
		<tr>
			<th>N°</th>
			<th>Número</th>
			<th>Intentos</th>
			<th>nOríg./nInt.</th>
			<th>Intervalos</th>
			<th>Duraciones</th>
			<th>Conectadas</th>
			<th>Causa de terminación</th>
			<th>Factor</th>
			</tr>
		</thead>
	<tbody>';
		foreach ($d as $numero => $dd) {
			$n++;
			$estilo = $dd['p']['porc'] >= $CFG['factor_limite'] ? 'class="alerta"' : '';
			$s_f1 = $dd['f1']['intentos'] ?? '';
			$s_f2 = [$dd['f2']['norigenes'] ?? '', $dd['f2']['intentos'] ?? ''];
			$s_f3 = isset($dd['f3']) ? hacer_tabla_intervalos($dd['f3']['intervalos']) : '';
			$s_f4 = isset($dd['f4']) ? hacer_tabla_duracion($dd['f4']['duracion']) : '';
			$s_f5 = isset($dd['f5']) ? hacer_tabla_porc_conex($dd['f5']) : '';
			$s_f6 = isset($dd['f6']) ? hacer_tabla_causas_cuelgues($dd['f6']) : '';
			$tabla .= "
			<tr>
			<td rowspan='2'>{$n}</td>
			<td rowspan='2'><a href='detalle.php?t={$numero}&f={$fecha}&d={$dirp}&porc={$dd['p']['porc']}' target='_BLANK'>{$numero}</a></td>
			<td>{$dd['p']['f1']}p.</td>
			<td>{$dd['p']['f2']}p.</td>
			<td>{$dd['p']['f3']}p.</td>
			<td>{$dd['p']['f4']}p.</td>
			<td>{$dd['p']['f5']}p.</td>
			<td>{$dd['p']['f6']}p.</td>
			<td {$estilo}>{$dd['p']['tot']}p.</td>
			</tr>
			<tr>
			<td>{$s_f1}</td>
			<td>{$s_f2[0]}/{$s_f2[1]}</td>
			<td>{$s_f3}</td>
			<td>{$s_f4}</td>
			<td>{$s_f5}</td>
			<td>{$s_f6}</td>
			<td {$estilo}>{$dd['p']['porc']}%</td>
		</tr>
			";
		}
		$tabla .= '</tbody></table>';
	}
	return $tabla;
}

function hacer_tabla_intervalos($d) {
	$tabla = '';
	if (count($d) > 0) {
		$tabla .= '<table class="interna"><thead><tr>
		<th>Seg.</th><th>Cant.</th>
		</tr></thead><tbody>';
		foreach ($d as $dd) {
			$tabla .= "<tr>
		<td>{$dd['segundos']}</td>
		<td>{$dd['cantidad']}</td>
		</tr>";
		}
		$tabla .= '	</tbody></table>';
	}
	return $tabla;
}

function hacer_tabla_duracion($d) {
	$tabla = '';
	if (count($d) > 0) {
		$tabla .= '<table class="interna"><thead><tr>
		<th>Cantidad</th><th>sessiontime</th>
		</tr></thead><tbody>';
		foreach ($d as $dd) {
			$tabla .= "<tr>
		<td>{$dd['cantidad']}</td>
		<td>{$dd['sessiontime']}</td>
		</tr>";
		}
		$tabla .= '	</tbody></table>';
	}
	return $tabla;
}

function hacer_tabla_porc_conex($d) {
	$tabla = '';
	if (count($d) > 0) {
		$tabla = "<table class='interna'><thead><tr>
		<th>Conex.</th><th>Int.</th><th>%</th><th>Maxt</th>
		</tr></thead><tbody><tr>
		<td>{$d['conectadas']}</td>
		<td>{$d['n']}</td>
		<td>{$d['porc']}%</td>
		<td>{$d['maxt']}s</td>
		</tr></tbody></table>";
	}
	return $tabla;
}

function hacer_tabla_causas_cuelgues($d) {
	$total = array_pop($d)['n'];

	$tabla = '';
	if (count($d) > 0) {
		$tabla .= "<table class='interna'><thead><tr><th>Causa</th><th>Cnt.</th>
					<th>%</th></tr></thead><tfoot><tr><th>Total</th>
					<th align='right'>{$total}</th><th></th></tr></tfoot><tbody>";
		foreach ($d as $l) {
			$porc = round($l['n'] * 100.0 / $total, 2);
			$tabla .= "
		<tr>
			<td align='center'>{$l['terminatecauseid']}</td>
			<td align='right'>{$l['n']}</td>
			<td align='right'>{$porc}%</td>
		</tr>";
		}
		$tabla .= "
			</tbody>
		</table>";
	}
	return $tabla;
}
