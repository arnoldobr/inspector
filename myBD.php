<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

class myBD {
	private $conecc = NULL;
	public function __construct(
		$host = "localhost",
		$bd = "myDataBaseName",
		$user = "root",
		$password = "") {
		try {
			$this->conecc = new mysqli($host, $user, $password, $bd);
			if (mysqli_connect_errno()) {
				throw new Exception("No se pudo conectar la BD");
			}
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	/**
	 * Ejecuta la query
	 * @param  string $query  Instrucción sql a ejecutar
	 * @param  array  $params Contiene los parámetros a sustituir, pero el primero es una cadena de formato mysqli
	 *                        i 	la variable correspondiente es de tipo entero
	 *                        d 	la variable correspondiente es de tipo double
	 *                        s 	la variable correspondiente es de tipo string
	 *                        b 	la variable correspondiente es un blob y se envía en paquetes
	 * @return mixed         Resultado de la ejecucioónd de la query
	 */
	public function sql($query = "", $params = []) {
		try {
			$stmt = $this
				->conecc
				->prepare($query);
			if ($stmt === false) {
				throw New Exception("No se pudo preparar el query: " . $query);
			}
			if ($params) {
				//Errorcito
				@call_user_func_array([$stmt, 'bind_param'], $params);
			}
			$stmt->execute();
			return $stmt;
		} catch (Exception $e) {
			throw New Exception($e->getMessage());
		}
	}

// Select a row/s in a Database Table
	public function sel($query = "", $params = []) {
		try {
			$stmt = $this->sql($query, $params);
			$result = $stmt
				->get_result()
				->fetch_all(MYSQLI_ASSOC);
			$stmt->close();
			return $result;
		} catch (Exception $e) {
			throw New Exception($e->getMessage());
		}
		return false;
	}

	// Insert a row/s in a Database Table
	public function ins($query = "", $params = []) {
		try {
			$this
				->sql($query, $params)
				->close();
			return $this
				->conecc
				->insert_id;
		} catch (Exception $e) {
			throw New Exception($e->getMessage());
		}
		return false;
	}

// Update a row/s in a Database Table
	public function upd($query = "", $params = []) {
		try {
			$this->sql($query, $params)->close();
		} catch (Exception $e) {
			throw New Exception($e->getMessage());
		}
		return false;
	}

	public function sql2array($sql, $params = []) {
		return $this->sel($sql, $params);
	}

	public function sql2row($sql, $params = []) {
		$temp = $this->sel($sql, $params);
		return $temp[0];
	}

	public function sql2options($sql, $params = []) {
		$d = [];
		foreach ($this->sel($sql, $params) as $value) {
			$temp2 = array_values($value);
			$d[$temp2[0]] = $temp2[1];
		}
		return $d;
	}

	public function sql2value($sql, $params = []) {
		$temp = $this->sel($sql, $params);
		return array_values($temp[0])[0];
	}

} //myBD
