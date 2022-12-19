<?php
function causa_desconexion(&$d) {
	global $bd_insp;
	foreach ($d as $num => $llamada) {
		$sql = "";
		$d[$num]['f6'] = $bd_insp->sql2array('SELECT terminatecauseid, count(*) n FROM inspector WHERE calledstation = ? GROUP BY terminatecauseid WITH ROLLUP', ['i', $num]);
	}
}
