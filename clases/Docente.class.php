<?php
namespace clases {

	/**
	 * Clase Docente
	 * 
	 * @author Santiago
	 * @version 1.1
	 */
	class Docente {
		
		//PROPIEDADES
		//datos personales
		public $dni;
		public $id;
		public $datosDocente = array();
		
		//afectaciones
		public $afectacion = array();
		
		//designaciones
		public $designacion = array();
		
		//mensajes
		public $errorNoEncuentro = "El docente buscado no está en la tabla";
		
		/**
		 * Datos de contacto
		 * @readwrite
		 */
		protected $datosContacto = array();
		
		//MÉTODOS
		
		public function __construct($cod) {
			
			require('./conexion.php');
			
			//busc por id
			$query = "SELECT id FROM docente WHERE id = $cod OR dni = $cod";
			$result = $mysqli->query($query);
			
			if ($result->num_rows == 1) {
				$row = $result->fetch_array(MYSQLI_ASSOC);
				$this->id = $row['id'];
				$this->cargarMasDatosDocente();
				
			} else {
				echo $this->errorNoEncuentro;
			}
			
			$result->free();
			$mysqli->close();
			
		}
		
		/**
		 * Carga todos los datos de un docente en datosDocente
		 * @return (void)
		 */		
		public function cargarMasDatosDocente() {
			require("./conexion.php");
			
			$query = "SELECT * FROM docente WHERE id = {$this->id} ";
			
			$result = $mysqli->query($query);
			$row = $result->fetch_array(MYSQLI_ASSOC);
			
			foreach ($row as $key => $value) {
				$this->datosDocente[$key] = $value;
			}
			
			$result->free();
			$mysqli->close();
		}
		
		/**
		 * Devuelve los datos del docente
		 * @return (array) dato => valor
		 */
		public function mostrarDatosDocente() {
			if (empty($this->datosDocente)) {
				$this->cargarMasDatosDocente();
			}
			return $this->datosDocente;
		}
		
		/**
		 * Agrega una afectación a un docente incluyéndolo en el equipo
		 * docente de la materia
		 * @param (str) cod de materia
		 * @param (str) tipo de afectacion (adjunto, titular, etc.)
		 * @param (int) anio
		 * @param (int) cuatrimestre
		 * @return (str) error
		 */		
		public function agregarAfectacion($materia, $tipo, $anio, $cuatrimestre) {
			require("./conexion.php");
			
			
			$query = "REPLACE INTO afectacion (docente, materia, anio, cuatrimestre, tipoafectacion, estado)
							VALUES ('$this->id', '$materia', '$anio', '$cuatrimestre', '$tipo', 'Pendiente')";
			
			$error = 'success';
			if (!$mysqli->query($query)) {
				$error = $mysqli->error;
			}
			return $error;
		}
		
		/**
		 * Muestra las afectaciones del docente
		 * @param (int) anio
		 * @param (int) cuatrimestre
		 * @return (array) afectaciones
		 */
		public function mostrarAfectaciones($anio, $cuatrimestre = '*') {
			require 'conexion.php';
			
			$whereCuatrimestre = '';
			if ($cuatrimestre != '*') {
				$whereCuatrimestre = "AND ac.cuatrimestre = {$cuatrimestre}";
			}
			
			$query = "SELECT a.id, m.conjunto, a.tipoafectacion, 
					GROUP_CONCAT(DISTINCT m.nombre ORDER BY m.cod SEPARATOR '/')
						AS materia, 
					IFNULL(GROUP_CONCAT(DISTINCT ac.comision ORDER BY ac.comision SEPARATOR ' - '), 
						'No tiene comisiones asignadas') AS comision,
					ac.anio, ac.cuatrimestre
				FROM afectacion AS a
				LEFT JOIN materia AS m ON m.cod = a.materia
				LEFT JOIN asignacion_comisiones AS ac 
					ON ac.materia = m.conjunto AND ac.docente = {$this->id}
						AND ac.anio = a.anio AND ac.cuatrimestre = a.cuatrimestre
				WHERE a.docente = {$this->id}
					AND a.anio = {$anio} 
					{$whereCuatrimestre}
				GROUP BY m.conjunto
				ORDER BY m.cod";
			$result = $mysqli->query($query);
			if ($mysqli->error) {
				echo $mysqli->error;
			}
			
			$afectaciones = array();
			while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$afectaciones[] = $row;
			}
			
			$result->free();
			$mysqli->close();
			
			return $afectaciones;
		}
		
		/**
		 * Muestra las comisiones asignadas al docente
		 * @param (int) anio
		 * @param (int) cuatrimestre (optional)
		 * @return (array) afectaciones
		 */
		public function mostrarComisionesAsignadas($anio, $cuatrimestre = '*') {
			require 'conexion.php';
			
			$whereCuatrimestre = '';
			if ($cuatrimestre != '*') {
				$whereCuatrimestre = "AND ac.cuatrimestre = {$cuatrimestre}";
			}
			
			$query = "SELECT a.id, m.conjunto, a.tipoafectacion, 
					GROUP_CONCAT(DISTINCT m.nombre ORDER BY m.cod SEPARATOR '/')
						AS materia, 
					IFNULL(GROUP_CONCAT(DISTINCT ac.comision ORDER BY ac.comision SEPARATOR ' - '), 
						'No tiene comisiones asignadas') AS comision,
					ac.anio, ac.cuatrimestre
				FROM asignacion_comisiones AS ac
				LEFT JOIN materia AS m ON m.conjunto = ac.materia
				LEFT JOIN afectacion AS a 
					ON a.materia = m.cod AND a.docente = ac.docente
						AND ac.anio = a.anio AND ac.cuatrimestre = a.cuatrimestre
				WHERE ac.docente = {$this->id}
					AND ac.anio = {$anio} 
					{$whereCuatrimestre}
				GROUP BY m.conjunto, ac.comision
				ORDER BY m.cod";
			$result = $mysqli->query($query);
			if ($mysqli->error) {
				echo $mysqli->error;
			}
			
			$afectaciones = array();
			while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$afectaciones[] = $row;
			}
			
			$result->free();
			$mysqli->close();
			
			return $afectaciones;
		}
		
		/**
		 * Obtiene los datos de contacto
		 * @return (array) tipo => dato
		 */
		 public function mostrarDatosContacto() {
			 require 'conexion.php';
			 
			 $query = "SELECT id, docente, tipo, valor
				FROM datos_docentes
				WHERE activo = 1 AND docente = {$this->id}";
			 $result = $mysqli->query($query);
			 $error = $mysqli->error;
			 echo $error;
			 
			 $datos = array();
			 while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				 $datos[$row['tipo']] = $row;
			 }
			 
			 $result->free();
			 $mysqli->close();
			 return $datos;
		 }
		 
		/**
		 * Agrega datos de contacto en la base de datos
		 * @param (array) Los datos tipo => valor
		 * @return void
		 */
		 public function agregarDatosContactos($datos) {
			 require 'conexion.php';
			 foreach ($datos as $tipo => $valor) {
				 $query = "REPLACE INTO datos_docentes 
					(docente, tipo, valor) VALUES
					({$this->id}, '$tipo', '$valor')";
				$mysqli->query($query);
					
			 }
			 $mysqli->close();
		 }
		 
		 /**
		  * Agrega un dato de contacto en la base de datos
		  * @param (str) tipo
		  * @param (str) valor
		  * @return void
		  */
		  public function agregarDatoContacto($tipo, $valor) {
		     require 'conexion.php';
			 $query = "REPLACE INTO datos_docentes 
				(docente, tipo, valor) VALUES
				({$this->id}, '$tipo', '$valor')";
			 $mysqli->query($query);
					
			 $mysqli->close();
		  }
		  
		  /**
		   * Muestra las designaciones del docente
		   * @param (int) anio
		   * @param (int) cuatrimestre
		   * @return (array) designaciones
		   */
		  public function mostrarDesignaciones($anio, $cuatrimestre) {
			  require 'conexion.php';
			  
			  $query = "SELECT des.id, des.categoria, des.caracter, des.dedicacion,
							des.fecha_alta, des.fecha_baja, ded.horas_requeridas,
							IF(fecha_baja != 0 AND MONTH(fecha_baja) < 8,
								'primero',
								IF (MONTH(fecha_alta) > 5, 'segundo',
								'ambos')
							) AS cuatrimestres
							
						FROM designacion AS des
						LEFT JOIN dedicacion AS ded
							ON des.dedicacion = ded.dedicacion
						WHERE docente = {$this->id}
							AND (YEAR(fecha_alta) = {$anio} OR (YEAR(fecha_alta) <= {$anio}
							AND (YEAR(fecha_baja) >= {$anio} OR fecha_baja = 0)))
						ORDER BY fecha_alta, fecha_baja, categoria, dedicacion, caracter;";
			  $result = $mysqli->query($query);
			  if ($mysqli->errno) {
				  echo $mysqli->error;
			  }
			  
			  $designaciones = array();
			  while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				  $designaciones[] = $row;
			  }
			  $result->free();
			  $mysqli->close();
			  return $designaciones;
		  }
		  
		  /**
		   * Agrega una designación al docente
		   * @param (str) dedicacion 
		   * @param (str) categoria
		   * @param (str) caracter
		   * @param (date) alta
		   * @param (date) baja
		   * @param (str) observaciones
		   * @return (void)
		   */
		  public function agregarDesignación($dedicacion, $categoria, $caracter, $alta, $baja, $observaciones) {
			  require 'conexion.php';
			  
			  $query = "INSERT IN designacion
							(docente, dedicacion, categoria, caracter, alta, baja, observaciones) VALUES
							({$this->id}, '{$dedicacion}', '{$categoria}', '{$caracter}', '{$alta}', 
								'{$baja}', '{$observaciones}')";
			  $result = $mysqli->query($query);
			  if ($mysqli->errno) {
				  echo $mysqli->error;
			  }
			  
			  $result->free();
			  $mysqli->close();
			  return;
		  }
			  
	}		
}
