<?php
$CFG = parse_ini_file('config.php', true);
//require_once '../debug.php';
require_once 'myBD.php';
$my_bd = new myBD($CFG['bd_insp']['host'], $CFG['bd_insp']['bd'], $CFG['bd_insp']['login'], $CFG['bd_insp']['pass']);

function ordena($a, $b) {
	return strtotime($b[0]) - strtotime($a[0]);
}

$mensaje = "";
if (isset($_POST['a'])) {
	$escribir = $_POST['escribir'] ? '' : '-w';
	if ($_POST['a'] == 'generar') {
		$comando = " -d {$_POST['fecha']} {$escribir}";
		if (system($CFG['inspector'] . " {$escribir} -d{$_POST['fecha']}") === false) {
			$mensaje = "No se pudo finalizar el comando";
		} else {
			$mensaje = "OK";
		}
	}
}
// Inicializaci'on de los datos para el grafico de torta
$dias = ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'];

$semana = [];
foreach ($dias as $dia) {
	$semana[$dia] = 0;
}

$paginas = $my_bd->sql2array('SELECT id,operadora op,n1,n2, DAYOFWEEK(id) ds FROM paginas ORDER BY id DESC, operadora ASC');
$lista = [];

foreach ($paginas as $p) {
	$p['dd'] = $dias[$p['ds'] - 1];
	$lista[$p['id']][$p['op']] = $p;
}
//vq($lista);
$labels = [];
$graf1 = [];
$graf2 = [];
$lista2 = [];
foreach ($lista as $id => &$p) {
	$total = ['id' => $id, 'op' => 'total', 'n1' => '0', 'n2' => '0', 'ds' => '', 'dd' => ''];
	foreach ($p as $op) {
		$total['id'] = $op['id'];
		$total['n1'] += $op['n1'];
		$total['n2'] += $op['n2'];
		$total['ds'] = $op['ds'];
		$total['dd'] = $op['dd'];
		$semana[$op['dd']] += $op['n1'];
	}
	$p['total'] = $total;
	$labels[] = $id;
	$graf1[] = $total['n1'];
	$graf2[] = $total['n2'];
	$lista2[] = ['id' => $id, 'op' => $p];
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
