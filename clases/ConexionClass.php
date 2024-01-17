<?php

require_once('ConfigClass.php');

class MySQL
{

	private $conexion;

	public function __construct()
	{
		if (!isset($this->conexion)) {
			$this->conexion = new mysqli(Config::DB_HOST, Config::DB_USER, Config::DB_PASS, Config::DB_NAME);
		}

		if (!$this->conexion) {
			// throw new Exception('No Funciona la conexcion. El Error es el siguiente: ' . @mysqli_error());
		}
	}

	public function consulta($consulta)
	{
		// linea que se agrego para arreglar la codificacion  
		$this->conexion->set_charset("utf8");

		$resultado = $this->conexion->query($consulta); // @mysqli_query($this->conexion,$consulta);

		if (!$resultado) {
			//throw new Exception('Ocurrio el siguiente error: ',$this->conexion->connect_error);
			echo 'Ocurrio el siguiente error: ' . $this->conexion->connect_error . ' <br> Query: ' . $consulta . $this->conexion->errno;
		} else {
			return $resultado;
		}
	}

	// Funciones para obtener los array. parametro el resultado de la consulta

	public function fetch_array($consulta)
	{
		return @mysqli_fetch_array($consulta);
	}

	public function fetch_assoc($consulta)
	{
		return @mysqli_fetch_assoc($consulta);
	}

	public function fetch_row($consulta)
	{
		return @mysqli_fetch_row($consulta);
	}


	public function fetch_object($consulta)
	{
		return @mysqli_fetch_object($consulta);
	}

	public function real_escape_string($cadena)
	{
		return @mysqli_real_escape_string($this->conexion, $cadena);
	}



	//consulta para obtener el numero de filas
	public function num_rows($consulta)
	{
		return @mysqli_num_rows($consulta);
	}


	public function ultimo_id()
	{
		return @mysqli_insert_id($this->conexion);
	}

	//preparando la base para insercion de datos
	public function begin()
	{
		@mysqli_query($this->conexion, "BEGIN;");
		//@mysql_query("BEGIN;");
	}

	public function commit()
	{
		@mysqli_query($this->conexion, "COMMIT;");
		//@mysql_query("COMMIT;");
	}

	public function rollback()
	{
		@mysqli_query($this->conexion, "ROLLBACK;");
		//@mysql_query("ROLLBACK;");
	}

	public function liberar($q)
	{
		@mysqli_free_result($q);
	}

	public function m_error($err)
	{

		$e =  array(
			1 => 'Error al intentar conectarse a la base de datos, revise su usuario y contraseña',
			2000 => "No conoce este Error en Mysql",
			1451 => "Este valor ya tiene un historial, no puede ser eliminado.",
			1146 => "Error al hacer la consulta tal vez la tabla no existe o esta mal escrita",
			1064 => "La sentencia sql no esta bien escrita error en una palabra reservada o la llave primaria es nula",
			1054 => "Un campo en la consulta no existe o esta mal escrito",
			1062 => "la llave primaria no tiene valor AI, o estas repitiendo una llave primaria",
			10000 => "No existe Valores en la Matriz de la Sesion",
			1452 => "Esperando solucion a este problema"
		);

		return $e[$err];
	}
}
