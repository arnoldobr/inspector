<?php

/**
 * Muestra el contenido de la variable $v
 * @param  mixed   $v       Variable a mostrar
 * @param  string  $mensaje Mensaje previo
 * @return [type]           [description]
 */
function ver($v, $mensaje = '', $sep = 0) {
	if ('' != $mensaje) {
		echo "\n", $mensaje, ": --------";
	}
	if (is_array($v)) {
		foreach ($v as $key => $value) {
			echo "\n", str_repeat("|  ", $sep), '[', $key, '] => ', ver($value, '', $sep + 1);
		}
	} else {
		var_export($v);
	}
	if ($sep == 0) {
		echo "\n---***---\n";
	}
}

function vq($ara) {
	echo '<pre>';
	ver($ara);
	echo '</pre>';
	exit("\n---***---\n");
}
