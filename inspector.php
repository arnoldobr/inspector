#!/usr/bin/env php
<?php
require_once 'debug.php';

$opt = getopt('d:f:whv', ['date:', 'function:', 'nowrite', 'help', 'version']);

if (isset($opt['function'])) {
	$opt['f'] = $opt['function'];
	unset($opt['function']);
}

if (isset($opt['date'])) {
	$opt['d'] = $opt['date'];
	unset($opt['date']);
}

if (isset($opt['nowrite'])) {
	$opt['w'] = $opt['nowrite'];
	unset($opt['nowrite']);
}

if (isset($opt['help'])) {
	$opt['h'] = $opt['help'];
	unset($opt['help']);
}

if (isset($opt['version'])) {
	$opt['v'] = $opt['version'];
	unset($opt['version']);
}
/********************************/

if (isset($opt['h'])) {
	ayuda();
	exit();
}

if (isset($opt['v'])) {
	echo "Version 0.1 (2022-04-19)\n";
	exit();
}

$fecha = $opt['d'] ?? date('Y-m-d');
$funcion = $opt['f'] ?? 0;
$accion = isset($opt['w']) ? 'NO_WRITE' : 'WRITE';

if (!f_valida($fecha)) {
	echo "Error en fecha\n";
	ayuda();
	exit();
}

if (!func_valida($funcion)) {
	echo "Error en la función\n";
	ayuda();
	exit();
}

$CFG = parse_ini_file('private/config.php', true);
require_once 'myBD.php';
require_once 'f1.php';
require_once 'f2.php';
require_once 'f3.php';
require_once 'f4.php';
require_once 'f5.php';
require_once 'f6.php';
require_once 'f0.php';
require_once 'calculo.php';
//require_once 'hacer_pagina.php';
//require_once 'actualizar_lista.php';

$bd_insp = new myBD($CFG['bd_insp']['host'], $CFG['bd_insp']['bd'], $CFG['bd_insp']['login'], $CFG['bd_insp']['pass']);
$bd_mb = new myBD($CFG['bd_mb']['host'], $CFG['bd_mb']['bd'], $CFG['bd_mb']['login'], $CFG['bd_mb']['pass']);

foreach ($CFG['op'] as $operadora) {
	/*
		Coloca las variables de la operadora en la raíz de $CFG
	*/
	foreach ($operadora as $key => $value) {
		$CFG[$key] = $value;
	}

	echo $CFG['nombre'], "...\n";

	# Limpio la tabla inspector
	$bd_insp->sql('TRUNCATE TABLE inspector');
	echo $bd_insp->sql2value('SELECT COUNT(*) FROM inspector');

	$llamadas_analizadas = llamadas($bd_mb, $bd_insp, $fecha);

	switch ($funcion) {
	case 0:
		consolidar($bd_insp, $fecha, $accion);
		break;
	case 1:
		ver(obten_listado($bd_insp, $fecha));
		break;
	case 2:
		ver(obten_origenes($bd_insp, $fecha));
		break;
	case 3:
		ver(obten_intervalo($bd_insp, $fecha));
		break;
	case 4:
		ver(obten_tiempo_igual_sesion($bd_insp, $fecha));
		break;
	case 5:
		ver(obten_tiempo_igual_sesion($bd_insp, $fecha));
		break;
	default:
		echo "Error en función $funcion.";
		exit;
		break;
	}

	$bd_insp->sql('TRUNCATE TABLE inspector');
}

############################################################################
/**
 * Guarda en la tabla inspector las llamadas
 *
 * Busca las llamadas realizadas en la fecha dada y las copia en la tabla
 * inspector para su procesado.
 *
 * @param  myBD   $bd_mb   Conexión de BD de mbilliong
 * @param  myBD   $bd_insp Conexión de BD de inspector
 * @param  string $fecha   Se procesarán llamadas de esta $fecha
 * @return [type]          [description]
 */
function llamadas(myBD $bd_mb, myBD $bd_insp, string $fecha) {
	global $CFG;
	$calledstation = "^{$CFG['prefijo_pais']}(" . str_replace(',', '|', $CFG['prefijo'])
		. ').+';
	echo $calledstation;

	$sql = "SELECT * FROM(
		(SELECT
			a.uniqueid id,
			a.id_user,
			a.callerid,
			a.uniqueid,
			a.starttime,
			a.calledstation,
			a.src,
			a.terminatecauseid,
			a.sessiontime,
			'' sessionid,
			0 hangupcause
		FROM pkg_cdr a
		WHERE
			a.calledstation REGEXP ?
		AND a.starttime >= ?
		AND a.starttime < ? + INTERVAL 1 DAY)
	UNION
		(SELECT
			b.uniqueid id,
			b.id_user,
			b.callerid,
			b.uniqueid,
			b.starttime,
			b.calledstation,
			b.src,
			b.terminatecauseid, 0 sessiontime,
			b.sessionid,
			b.hangupcause
		FROM pkg_cdr_failed b
		WHERE
			b.calledstation REGEXP ?
		AND	b.starttime >= ?
		AND b.starttime < ? + INTERVAL 1 DAY)
	) c";

	$d = $bd_mb->sql2array($sql, ['ssssss', $calledstation, $fecha, $fecha, $calledstation, $fecha, $fecha]);

	$sql = 'INSERT INTO inspector (
							id, id_user, callerid, uniqueid, starttime,
							calledstation, src, terminatecauseid,
							sessiontime, sessionid, hangupcause)
			VALUES(?,?,?,?,?,?,?,?,?,?,?)';

	foreach ($d as $dd) {
		$bd_insp->ins($sql, [
			'sisssssiisi',
			NULL, $dd['id_user'], $dd['callerid'], $dd['uniqueid'], $dd['starttime'],
			$dd['calledstation'], $dd['src'], $dd['terminatecauseid'], $dd['sessiontime'] ?? 0,
			$dd['sessionid'] ?? 0, $dd['hangupcause'],
		]);
	}

	return $bd_insp->sql2value('SELECT COUNT(*) FROM inspector');
}

/**
 * Verifica la validez de la fecha en formato 'yyyy-mm-dd'
 * @param  string $d Fecha en formato 'yyyy-mm-=dd'. Ej.: '1967-09-03'
 * @return bool      Devuelve true si es válida... y false si NO es Válida
 */
function f_valida($d) {
	$f = explode('-', $d);
	return (count($f) == 3) && checkdate($f[1], $f[2], $f[0]);
}

function func_valida($d) {
	return in_array($d, [0, 1, 2, 3, 4, 5, 6]);
}

/**
 * Muestra la ayuda del programa
 * @return none
 */
function ayuda() {
	echo <<<LRDTAB
NOMBRE
	./inspector.php - Evalúa si un número telefónico es un cliente válido de las
	                  operadoras  de telefonía móvil en Venezuela
SINOPSIS
	./inspector.php [-daaaa-mm-dd] [-ffuncion] [-w]

DESCRIPCIÓN
	El programa analiza un histórico de llamadas para buscar patrones que
	permitan decidir si cierto número pertenece a un cliente válido de las
	operadoras de telefonía móvil o es un sistema automático de respuesta
	(IVR) y modifica la lista blanca y la lista negra en el sistema
	(una excepción es cuando se usa la opción -w --ver más abajo--).

EJEMPLOS
	./inspector.php -d2022-01-03 -f0 -w
		Revisará la fecha 2022-01-03 sin modificar las listas blanca y
		negra (-w) generando una página web (-f0).

	./inspector.php -f3
		Revisará la fecha actual (por defecto) generando la salida de los
		intervalos entre llamadas. Puesto que solo hace uso de la
		función 3 (-f3), no se genera página y tampoco se modifican las listas.

OPCIONES
	-daaaa-mm-dd, --date=aaaa-mm-dd
		aaaa-mm-dd es la fecha que se quiere analizar. Si no se usa esta opción,
		el programa asumirá la fecha del sistema operativo.

	-ffuncion, --func=funcion
		funcion es un número entre 0 y 6 inclusive que determinará el resultado
		a obtener de acuerdo con la tabla:

		0	Ejecuta las funciones 1, 2, 3, 4, 5 y 6; y genera una página web
			con los resultados consolidados.
		1	Obtiene el listado de las llamadas repetidas y las muestra
			por consola.
		2	Obtener orígenes y las muestra por consola.
		3	intervalo entre llamadas con intervalos iguales y las muestra
			por consola.
		4	obtiene tiempos de igual sesión y las muestra por consola.
		5	Porcentaje de conexión.
		6	Porcentaje según causa de desconexión.

	-w, --nowrite
		Al escribir esta opción, el programa no modifica la lista blanca ni
		la lista negra.

	-h. --help
		Muestra esta ayuda y finaliza el programa.

	-v, --version
		Muestra la versión del programa y finaliza el mismo.

LRDTAB;
}
