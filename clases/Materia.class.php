<?php
namespace clases {
	
	/**
	 * Clase Materia
	 * 
	 * @author Santiago
	 * @version 1.1
	 */
	class Materia {
		
		//Propiedades
		
			public $cod;
			
			public $carrera;
			
			public $datosMateria = array();
			
						
		//Métodos
		
		/**
		 * Construye la materia asignado todos los datos de la db
		 * @param string el codigo de la materia
		 * @return void
		 */
		
		public function __construct($cod) {
			//fijo código
			$this->cod = $cod;
			$_SESSION['materia'] = $cod;
			
			//Abrir la conexión de Mysql
			require('./conexion.php');
			//Hacer una consulta con select
			$query = "SELECT m.id, m.nombre, m.cuatrimestre, c.nombre as carrera, 
						m.plan, m.activo, m.conjunto, m.contenidosminimos, c.cod AS cod_carrera
							FROM materia AS m
							INNER JOIN carrera as c
							ON m.carrera = c.id
							WHERE m.cod = '{$cod}' ";
			$result = $mysqli->query($query);
			//print_r($result);
			//Fijo las propiedades
			while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
				
				foreach ($row as $key => $value) {
					$this->datosMateria[$key] = $value;
				}
			}
			
			//libero resultados y cierro la base de datos
			$result->free();
			$mysqli->close();
			
			return false;
		}
		
		/**
		 * Muestra todas (*) o una unidad temática (nº)
		 * @param String * o la unidad requerida
		 * @param int anio
		 * @param int cuatrimestre
		 * @param boolean Buscar por conjunto
		 * @return array de unidades
		 */
		public function mostrarUnidadesTematicas($unidades, $anio = 2016, $cuatrimestre = 1, $conjunto = false) { // * para traer toda la lista, array para traer varias
			require("./conexion.php");
			
			//echo "CONJUNTO: " . $conjunto;
			$conjuntoClause = '';
			if ($conjunto) {
				$conjunto = $this->mostrarConjunto();
				$conjuntoClause = "OR materia IN $conjunto";
			}
			
			$whereClause = "WHERE activo = 1 AND (materia = '{$this->cod}' {$conjuntoClause}) 
				 AND anio = {$anio} AND cuatrimestre = {$cuatrimestre}";
			if ($unidades != "*") {
				if (is_int($unidades)) {
					$whereClause .= "AND unidad = '{$unidades}' ";
				} elseif (is_array($unidades)) {
					$whereClause .= "AND unidad IN (0, ";
					foreach ($unidades as $value) {
						$whereClause .= $value .", ";
					}
					$whereClause .= ")";
				} else {
					//ERROR
				}
			}
			
			$query = "SELECT unidad, descripcion FROM unidad_tematica $whereClause ORDER BY unidad";
			//echo $query;
			$result = $mysqli->query($query);
			
			$unidadesTematicas = array();
			while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
					$unidadesTematicas[$row['unidad']] = $row['descripcion'];
			}
			
			$result->free();
			$mysqli->close();
			return $unidadesTematicas;
		}

		public function agregarUnidadTematica($unidad, $descripcion, $anio, $cuatrimestre) { //Agregar Unidad temática
			require("./conexion.php");
			
			$descripcion = $mysqli->real_escape_string($descripcion);
			
			$query = "REPLACE INTO unidad_tematica SET materia = '{$this->cod}', 
						unidad = '$unidad', descripcion = '{$descripcion}', 
						anio = $anio, cuatrimestre = {$cuatrimestre},
						activo = 1 ";
			//echo $query;
			$mysqli->query($query);
			
			$mysqli->close();
		}
		
		/**
		 * Muestra la bibliografía
		 * @param int anio
		 * @param int cuatrimestre
		 * @param bool buscar por conjunto
		 * @return array bibliografía
		 */		
		public function mostrarBibliografia($anio, $cuatrimestre, $conjunto = false) {
			require('./conexion.php');
			
			$conjuntoClause = '';
			if ($conjunto) {
				$conjunto = $this->mostrarConjunto();
				$conjuntoClause = "OR materia IN $conjunto";
			}
			
			$query = "SELECT titulo, autor, editorial, paginas, id FROM bibliografia 
				WHERE (materia = {$this->cod} {$conjuntoClause}) 
					AND activo = '1' 
					AND anio = {$anio} 
					AND cuatrimestre = {$cuatrimestre};";
			//echo $query;
			$result = $mysqli->query($query);
			
			$bibliografia = array();
			
			while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
				$bibliografia[] = $row;
			}
			
			return $bibliografia;
			
		}
		
		public function agregarBibliografia($titulo, $autor, $editorial, $paginas, $anio, $cuatrimestre) { //Agregar bibliografía de la materia
			require('./conexion.php');
			
			$titulo = $mysqli->real_escape_string($titulo);
			$editorial = $mysqli->real_escape_string($editorial);
			$paginas = $mysqli->real_escape_string($paginas);
			$autor = $mysqli->real_escape_string($autor);
			
			$query = "INSERT INTO bibliografia (materia, titulo, autor, editorial, paginas, anio, cuatrimestre) VALUES ('{$this->cod}', '$titulo', '$autor', '$editorial', '$paginas', $anio, $cuatrimestre) ";
			$mysqli->query($query);
			
			$mysqli->close();
		}
		
		public function mostrarCronograma($anio, $cuatrimestre) {
			require('./conexion.php');
			
			$query = "SELECT c.clase, c.fecha, c.unidadtematica, GROUP_CONCAT(IF(u.anio = $anio AND u.cuatrimestre = $cuatrimestre, u.descripcion, null) SEPARATOR ':') AS detalleunidadtematica, c.descripcion, c.metodo, c.bibliografia, c.paginas, c.docente, c.activo, c.id
							FROM cronograma AS c
							LEFT JOIN unidad_tematica as u ON c.unidadtematica = u.unidad AND c.materia = u.materia AND c.anio = $anio AND c.cuatrimestre = $cuatrimestre
							WHERE c.materia = {$this->cod} AND c.anio = $anio AND c.cuatrimestre = $cuatrimestre
							GROUP BY c.clase
							ORDER BY c.clase";
			$result = $mysqli->query($query);
			// $query;
			$cronograma = array();
			
			while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
				$cronograma[] = $row;
			}
			
			return $cronograma;
			
		}
		
		public function agregarCronograma($clase, $fecha, $unidadtematica, $descripcion, $metodo, $bibliografia, $paginas, $activo, $docente) { //Agregar bibliografía de la materia
			require('./conexion.php');
			
			$descripcion = $mysqli->real_escape_string($descripcion);
			
			$query = "INSERT INTO cronograma (materia, clase, fecha, unidadtematica, descripcion, metodo, bibliografia, paginas, activo) VALUES 
												('{$this->cod}', '$clase', '$fecha', '$descripcion', '$metodo', '$bibliografia', '$paginas', '$activo', '$docente') ";
			$mysqli->query($query);
			
			$mysqli->close();
		}
		
		public function mostrarPlanDeClase($clase, $anio, $cuatrimestre) { //Muestra el plan de una clase
			require "./conexion.php";
			
			
			$query = "SELECT clase, fecha, docente, unidadtematica, descripcion, metodo, activo, (100 - activo) as pasivo, bibliografia, paginas
								FROM cronograma 
								WHERE materia = '$_SESSION[materia]' AND clase = '$_POST[clase]' AND anio = $anio and cuatrimestre = $cuatrimestre
								LIMIT 1";
			$result = $mysqli->query($query);
			$detalleClase = array();
			if ($result->num_rows === 1) {
				$row = $result->fetch_array(MYSQLI_ASSOC);
				foreach ($row as $key => $value) {
					$detalleClase[$key] = $value;
				}
				$result->free();
			}
			
			$mysqli->close();
			
			return $detalleClase;
		}
		//INSERTAR MATERIA NUEVA
		
		//MODIFICAR DATOS DE LA MATERIA
		
		
		/**
		 * Muestra las correlatividades de la materia
		 * @return (array) corretividades cod => nombre (o string no hay)
		 */
		public function mostrarCorrelativas() {
			require './conexion.php';
			
			$query = "SELECT m.cod, m.nombre FROM correlatividad AS c
							INNER JOIN materia AS m
							ON m.cod = c.requisito
							WHERE c.materia = '{$_SESSION['materia']}' ";
											
			$result = $mysqli->query($query);
			
			$correlativas = array();
			if ($result->num_rows == 0) {
				$result->free();
				$mysqli->close(); 
				//return "La asignatura no tiene correlativas.";
			} else {
			
				while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
					$correlativas[$row['cod']] = $row['nombre'];
				}
				$result->free();
				$mysqli->close(); 
				
				return $correlativas;
			}
		}
		
				/**
		 * Muestra las correlatividades de las materias en el conjunto de la materia
		 * @return (array) corretividades cod => nombre 
		 */
		public function mostrarCorrelativasDelConjunto() {
			require './conexion.php';
			
			$conjunto = $this->mostrarConjunto();
			
			$query = "SELECT m.cod, m.nombre FROM correlatividad AS c
							INNER JOIN materia AS m
							ON m.cod = c.requisito
							WHERE c.materia IN {$conjunto}";
											
			$result = $mysqli->query($query);
			
			$correlativas = array();
			if ($result->num_rows == 0) {
				$result->free();
				$mysqli->close(); 
				//return "La asignatura no tiene correlativas.";
			} else {
			
				while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
					$correlativas[$row['cod']] = $row['nombre'];
				}
				$result->free();
				$mysqli->close(); 
				
				return $correlativas;
			}
		}
		
		
		
		/**
		 * Muestra los alumnos en condiciones de cursar la materia
		 * @param (int) anio
		 * @param (int) cuatrimestre
		 * @return (array) Estimación y desglose todo por turno
		 */
		public function mostrarEstimacionInscriptos($anio, $cuatrimestre) {
			
			$materiasConjunto = $this->mostrarCodigosConjunto();
			$conjuntoOriginal = $this->mostrarConjunto();
			require 'conexion.php';
			
			$estimacion = array();
			foreach ($materiasConjunto as $cod) {
				$materia = new Materia($cod);
				$correlativas = $materia->mostrarCorrelativas();
				
				
				$strCorrelativas = "";
				$strHavingCorrelativas = "";
				if (is_array($correlativas)) {
					$fieldCorrelativas = array();
					$fieldHavingCorrelativas = array();
					foreach ($correlativas as $cod => $nombre) {
						$correlativa = new Materia($cod);
						$conjunto = $correlativa->mostrarConjunto();
						$anioConsiderado = $anio - 1;
						
						$fieldCorrelativas[] = "SUM(IF( materia IN {$conjunto} 
								AND resultado IN ('Aprob', 'Promocion') 
								AND anio_academico >= {$anioConsiderado}, 
							1, 0 )) AS correlativa{$cod}";
						$fieldHavingCorrelativas[] = "correlativa{$cod} > 0 ";
					}
					$strCorrelativas = ', ' . join(', ', $fieldCorrelativas);
					$strHavingCorrelativas = " AND " . join(" AND ", $fieldHavingCorrelativas);
					
					unset($correlativa);
				}
						
				$query = "SELECT turno_comision, COUNT(*) AS cantidad FROM (
							SELECT nro_documento,
								IF(nombre LIKE '%N%', 'N',
									IF(nombre LIKE '%M%' OR nombre LIKE '%S%','M', 'T')
								)
							 AS turno_comision,
								SUM(IF( materia IN {$conjuntoOriginal} 
									AND resultado IN ('Aprob', 'Promocion'), 
								1, 0 )) AS materia_aprobada
								{$strCorrelativas}
							FROM actas
							GROUP BY nro_documento, turno_comision
							HAVING  materia_aprobada = 0 {$strHavingCorrelativas}
						) AS prequery
						GROUP BY turno_comision;";
				//echo "<pre>" . $query . "</pre>";
				
				$result = $mysqli->query($query);
				echo $mysqli->error;
				
				
				while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
					$estimacion[] = $row;
				}
			}
			
			$result->free();
			$mysqli->close();
			
			return $estimacion;
		}
		
		/**
		 * Muestra el radio de aprobación en un periodo oir turno
		 * @param (int) anio
		 * @param (int) cuatrimestre
		 * @return (array) ratio aprobados por turno
		 */
		public function mostrarRatioAprobacion($anio, $cuatrimestre) {
			
			require 'conexion.php';
						
			$conjunto = $this->mostrarConjunto();
			$query = "SELECT  turno_comision,
				   ((aprobados2015 / inscriptos2015) * .6 + 
						(aprobados2014 / inscriptos2014) * .25 + 
						(aprobados2013 / inscriptos2013) * .15) AS ratio_aprobados
				FROM (
				SELECT SUM(IF(anio_academico = {$anio} -1, 1, 0)) AS inscriptos2015,
					SUM(IF(anio_academico = {$anio} - 2, 1, 0)) AS inscriptos2014,
					SUM(IF(anio_academico = {$anio} - 3, 1, 0)) AS inscriptos2013,
					IF(nombre LIKE '%N%', 'N',
						IF(nombre LIKE '%M%' OR nombre LIKE '%S%','M', 'Otro')
					)
					 AS turno_comision,
					SUM(IF(a.resultado IN ('Aprob', 'Promocion') AND anio_academico = {$anio} -1, 1, 0)) AS aprobados2015,
					SUM(IF(a.resultado IN ('Aprob', 'Promocion') AND anio_academico = {$anio} - 2, 1, 0)) AS aprobados2014,
					SUM(IF(a.resultado IN ('Aprob', 'Promocion') AND anio_academico = {$anio} - 3, 1, 0)) AS aprobados2013
					   
					FROM actas AS a
					WHERE periodo_lectivo = {$cuatrimestre} AND materia IN {$conjunto}  
					GROUP BY turno_comision
				) AS sub
				
				ORDER BY turno_comision DESC;";
			$result = $mysqli->query($query);
			//echo "<pre>$query</pre>";
			
			$ratios = array();
			while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$ratios[$row['turno_comision']] = $row['ratio_aprobados'];
			}
			
			$result->free();
			$mysqli->close();
			
			return $ratios;
		}
		
		/**
		 * Muestra el radio de aprobación en un periodo por turno y por cod
		 * @param (int) anio
		 * @param (int) cuatrimestre
		 * @return (array) ratio aprobados por turno
		 */
		public function mostrarRatioAprobacionPorCod($anio, $cuatrimestre) {
			
			require 'conexion.php';
						
			$cod = $this->mostrarCod();
			$query = "SELECT anio_academico, 
						IF(nombre LIKE '%N%', 'N',
							IF(nombre LIKE '%M%' OR nombre LIKE '%S%','M', 'Otro')
						)
						 AS turno,
						COUNT(DISTINCT nro_documento) AS inscriptos,
						SUM(IF(a.resultado IN ('Aprob', 'Promocion') , 1, 0)) AS aprobados
						   
						FROM actas AS a
						WHERE anio_academico > ({$anio} - 4)
							AND periodo_lectivo = {$cuatrimestre} 
							AND materia = {$cod} 
							
						GROUP BY turno, anio_academico, periodo_lectivo";
			$result = $mysqli->query($query);
			
			$cantidades = array();
			while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$cantidades[$row['anio_academico']][$row['turno']]['inscriptos'] = $row['inscriptos'];
				$cantidades[$row['anio_academico']][$row['turno']]['aprobados'] = $row['aprobados'];
			}
			
			$turnos = ['M', 'N', 'Otro'];
			$periodos = [$anio - 1, $anio - 2, $anio - 3];
			$preRatios = array();
			
			foreach ($periodos as $i) {
				foreach($turnos as $turno) {
					$preRatios[$i][$turno] = .54;
					if (isset($cantidades[$i][$turno]['inscriptos']) and $cantidades[$i][$turno]['inscriptos'] > 0) {
						$preRatios[$i][$turno] = round(($cantidades[$i][$turno]['aprobados'] / $cantidades[$i][$turno]['inscriptos']) , 2);
					}
				}
			}
			
			$ratios = array();
			foreach ($turnos as $turno) {
				$ratios[$turno] = ($preRatios[$periodos[0]][$turno] * .7) + 
									($preRatios[$periodos[1]][$turno] * .2) + 
									($preRatios[$periodos[2]][$turno] * .1);
			}
				
			$result->free();
			$mysqli->close();
			
			return $ratios;
		}
		
		//INGRESAR CORRELATIVIDADES
		
		//BORRAR CORRELATIVIDADES
		
		//Mostrar Programa
		
		
		/**
		 * Muestra el equipo docente de la materia
		 * @param String o Array cargo filtro por cargo 
		 * (o se muestran todos con * que es la opción por defecto)
		 * @param int $anio
		 * @param int $cuatrimestre
		 * @param boolean $conjunto si se da el conjunto de la materia
		 * @return array el equipo docente
		 */
		
		public function mostrarEquipoDocente($anio, $cuatrimestre, $conjunto = false, $cargo = "*") {
			require "./conexion.php";
			
			$conjuntoClause = '';
			if ($conjunto) {
				$conjunto = $this->mostrarConjunto();
				$conjuntoClause = "OR a.materia IN $conjunto";
			}
				
			$whereClause = "WHERE a.activo = 1
								AND (a.materia = '{$this->cod}' $conjuntoClause) 
								AND a.anio = {$anio} AND a.cuatrimestre = {$cuatrimestre} ";
			
			if ($cargo != "*") {
				if (is_string($cargo) ) {
					$whereClause .= "AND a.tipoafectacion = '{$cargo}' ";
				} elseif (is_array($cargo)) {
					$whereClause .= "AND a.tipoafectacion IN (0, '";
					foreach ($cargo as $value) {
						$whereClause .= $value ."', '";
					}
					$whereClause .= "') ";
				} else {
					//ERROR
				}
			}
			
			$query = "SELECT CONCAT_WS(', ', d.apellido, d.nombres) AS docente, 
							a.tipoafectacion, d.fechaingreso, a.id, a.estado,
							GROUP_CONCAT(DISTINCT ac.comision ORDER BY ac.comision SEPARATOR ', ') AS comision,
							d.id AS id_docente
						FROM docente AS d
						INNER JOIN afectacion AS a ON d.id = a.docente
						LEFT JOIN materia AS m ON a.materia = m.cod
						LEFT JOIN asignacion_comisiones AS ac ON ac.docente = a.docente
							AND ac.materia = m.conjunto AND a.anio = ac.anio
							AND a.cuatrimestre = ac.cuatrimestre
						$whereClause 
						GROUP BY a.docente
						ORDER BY a.tipoafectacion";
			//echo $query;
			$result = $mysqli->query($query);
			echo $mysqli->error;
			$equipoDocente = array();
			while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
					$equipoDocente[] = ['docente' => $row['docente'], 
											'tipoafectacion' => $row['tipoafectacion'], 
											'fechaingreso' => $row['fechaingreso'],
											'id' => $row['id'],
											'estado' => $row['estado'],
											'comision' => $row['comision'],
											'id_docente' => $row['id_docente'],
										];
			}
			
			$result->free();
			$mysqli->close();
			
			return $equipoDocente;
		}
		
		/**
		 * Muestra el equipo docente de la materia
		 * @param int $anio
		 * @param int $cuatrimestre
		 * @return array el equipo docente Con las restricciones de cada docente
		 */
		
		public function mostrarEquipoDocenteConRestricciones($anio, $cuatrimestre) {
			require "./conexion.php";
			
			
			$conjunto = $this->mostrarConjunto();
			
			$query = "SELECT CONCAT(d.apellido, ', ', d.nombres) AS nombre_docente,
							d.id AS id_docente,
							a.id AS id_afectacion,
							acc.id id_asignacion,
							acc.materia,
							acc.dia,
							acc.horario,
							a.materia
						FROM afectacion AS a
						LEFT JOIN asignacion_comisiones_calendario AS acc
							ON acc.docente = a.docente AND acc.anio = a.anio
								AND acc.cuatrimestre = a.cuatrimestre
						LEFT JOIN docente AS d
							ON d.id = a.docente
						WHERE a.materia IN {$conjunto}
							AND a.anio = {$anio} AND a.cuatrimestre = {$cuatrimestre}
							AND a.activo = 1
						ORDER BY nombre_docente";
			//echo $query;
			$result = $mysqli->query($query);
			if ($mysqli->errno) {
				echo $mysqli->error;
			}
			$equipoDocente = array();
			while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
					
					$equipoDocente[$row['id_docente']][$row['horario']][$row['dia']][] = $row;
					$equipoDocente[$row['id_docente']]['nombre_docente'] = $row['nombre_docente'];
			}
			
			$result->free();
			$mysqli->close();
			
			return $equipoDocente;
		}
		
		/**
		 * Muestra el código de la materia
		 * @return String
		 */
		 public function mostrarCod() {
			 return $this->cod;
		 }
		
		/**
		 * Devuelve todos datos de la materia
		 * @return array los datos
		 */
		 public function mostrarDatos() {
				return $this->datosMateria;
		 }
		
		/**
		 * Devuelve un dato específico de la materia
		 * @param $dato el dato buscado
		 * @return según el dato que se busque
		 */
		 
		 public function mostrarDato($dato) {
			 if (isset($this->datosMateria[$dato])) {
				 return $this->datosMateria[$dato];
			 } else {
				 $this->mostrarDatos();
			 }
		 }
		 
		/**
		 * Devuelve el conjunto al que la materia pertenece
		 * @return String
		 */
		 public function mostrarConjunto() {
			 return $this->datosMateria['conjunto'];
		 }
		 
		 /**
		  * Devuelve los nombres de las materias del conjunto
		  * @return (str) nombres
		  */
		 public function mostrarNombresConjunto() {
			 require "conexion.php";
			 
			 $conjunto = $this->mostrarConjunto();
			 
			 $query = "SELECT GROUP_CONCAT(DISTINCT nombre ORDER BY cod+0
				SEPARATOR ' / ') AS nombreConjunto
				FROM materia
				WHERE conjunto = '$conjunto'";
			$result = $mysqli->query($query);
			$nombreConjunto = $result->fetch_array()[0];
			 
			 $result->free();
			 $mysqli->close();
			 
			 return $nombreConjunto;
		 }
		 
		 /**
		  * Devuelve el listado de los codigos de materias en el conjunto
		  * @return (array) materias por codigo
		  */
		 public function mostrarCodigosConjunto() {
			 require "conexion.php";
			 
			 $conjunto = $this->mostrarConjunto();
			 
			 $query = "SELECT DISTINCT cod
				FROM materia
				WHERE conjunto = '$conjunto'";
			$result = $mysqli->query($query);
			
			$codigos = array();
			while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$codigos[] = $row['cod'];
			}
			 
			 $result->free();
			 $mysqli->close();
			 
			 return $codigos;
		 }
		 
		 /**
		  * Muestra las carreras en las que se dicta la materia
		  * @return (array) carreras
		  */
		 public function mostrarCarreras() {
			 require "conexion.php";
			 
			 $conjunto = $this->mostrarConjunto();
			 
			 $query = "SELECT DISTINCT m.carrera, c.nombre, m.plan
						FROM materia AS m
						LEFT JOIN carrera AS c ON c.id = m.carrera
						WHERE m.cod IN {$conjunto}
						ORDER BY m.plan DESC;";
			
			$result = $mysqli->query($query);
			$carreras = array();
			while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$carreras[] = $row['nombre'] . " (plan " . $row['plan'] .")";
			}
			
			$result->free();
			$mysqli->close();
			
			return $carreras;
		}
		
		/**
		 * Muestra la cantidad de comisiones abiertas en un periodo
		 * desagregadas por turno
		 * @param $anio año lectivo
		 * @param $cuatrimestre el periodo lectivo
		 * @return array( turno =>  int cantidad)
		 */ 
		public function mostrarCantidadComisiones($anio, $cuatrimestre) {
			require "./conexion.php";
			
			$conjunto = $this->mostrarConjunto();
			
			/*$query = "SELECT turno, cantidad
						FROM cantidad_comisiones
						WHERE materia = '$conjunto' AND anio = $anio
							AND cuatrimestre = $cuatrimestre
							ORDER BY turno";*/
			
			$query = "SELECT materia, COUNT(DISTINCT nombre_comision) AS cantidad, turno, dependencia
						FROM comisiones_abiertas 
						WHERE anio = {$anio} AND cuatrimestre = {$cuatrimestre}
							AND materia = '{$conjunto}'
						GROUP BY materia, turno;";
			$result = $mysqli->query($query);
			//echo $mysqli->error;
			
			$cantidadComisiones = array();
			while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$cantidadComisiones[$row['turno']] = $row['cantidad'];
			}
			
			$result->free();
			$mysqli->close();
			return $cantidadComisiones;
		}
		
		/**
		 * Muestra la cantidad de comisiones abiertas en un periodo
		 * desagregado por turno y calculado desde las ACTAS
		 * @param $anio anio
		 * @param $cuatrimestre cuatrimestre
		 * @return array( turno => int cantidad)
		 */
		 public function mostrarCantidadComisionesDesdeActas($anio, $cuatrimestre) {
			 require "./conexion.php";
			 
			 $conjunto = $this->mostrarConjunto();
			 
			 
			 $query = "SELECT m.conjunto, 
							IF(a.nombre LIKE '%N%', 'N',
								IF(a.nombre LIKE '%M%', 'M',
									IF(a.nombre LIKE '%S%', 'S', 'T')
								)
							) AS turno,
							COUNT( DISTINCT IF(RIGHT(a.nombre, 2) = 'MT', 'MT', 
								IF(RIGHT(a.nombre, 3) LIKE 'MT_', CONCAT('MT', RIGHT(a.nombre, 1)),
								IF(LEFT(RIGHT(a.nombre, 2), 1) IN ('0', '1', '2', '3', '4', '5', '6', '7', '8', '9'), 
									RIGHT(a.nombre, 1), RIGHT(a.nombre, 2))
							)) ) AS comision_group

						FROM actas AS a
						LEFT JOIN materia AS m ON m.cod = a.materia
						WHERE a.anio_academico = {$anio}
							AND a.periodo_lectivo = {$cuatrimestre}
							AND a.carrera IN ('EYN-3', 'EYN-4', 'LITUR', 'CCCCP')
						GROUP BY m.conjunto, turno";
			 $result = $mysqli->query($query);
			 
			 $cantidadComsiones = array();
			 while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				 $cantidadComisiones[$row['turno']] = $row['cantidad'];
			 }
			 
			 $result->free();
			 $mysqli->close();
			 return $cantidadComisiones;
		 }
		
		/**
		 * Muestra las asignaciones hechas en el periodo con la 
		 * comision correspondiente y el cargo del docente
		 * @param $anio
		 * @param $cuatrimestre
		 * @return array las asignaciones
		 */
		 public function MostrarAsignacionComisiones($anio, $cuatrimestre) {
			 require "./conexion.php";
			 
			 $cod = $this->mostrarCod();
			 $conjunto = $this->mostrarConjunto();
			 
			 $query = "SELECT ac.id, ac.anio, ac.cuatrimestre, ac.turno, ac.comision,
						ac.dependencia, ac.materia, 
						GROUP_CONCAT(m.nombre SEPARATOR '/') AS nombre_materia,
						ac.docente, d.dni, d.apellido, d.nombres, a.tipoafectacion
					FROM asignacion_comisiones AS ac
					LEFT JOIN materia AS m ON m.conjunto = ac.materia
					LEFT JOIN docente AS d ON ac.docente = d.id
					LEFT JOIN afectacion AS a ON ac.docente = a.docente
						AND a.materia = '$cod'
						AND a.cuatrimestre = ac.cuatrimestre
						AND a.anio = ac.anio
						AND a.activo = 1
					WHERE ac.materia = '$conjunto' AND ac.anio = $anio
						AND ac.cuatrimestre = $cuatrimestre
					GROUP BY ac.comision, ac.docente";
			
			/*$query = "SELECT ca.materia, 
						ca.horario, 
						ca.nombre_comision, 
						ca.turno, ca.anio, 
						ca.cuatrimestre,
						GROUP_CONCAT(DISTINCT CONCAT(d.apellido, ', ', d.nombres) SEPARATOR ' / ') AS docentes
					FROM comisiones_abiertas AS ca
					LEFT JOIN asignacion_comisiones AS ac
						ON ac.materia = ca.materia
							AND ac.anio = ca.anio
							AND ac.cuatrimestre = ca.cuatrimestre
							AND ac.comision = ca.nombre_comision
					LEFT JOIN docente AS d
						ON ac.docente = d.id
					WHERE ca.anio = {$anio}
						AND ca.cuatrimestre = {$cuatrimestre}
						AND ca.materia = '{$conjunto}'
					GROUP BY materia, nombre_comision"*/		
			
			$result = $mysqli->query($query);
			echo $mysqli->error;
			$asignaciones = array();
			while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$asignaciones[$row['turno']][$row['comision']][] = $row;
			}
			
			$result->free();
			$mysqli->close();
			
			return $asignaciones;
		}
		
		/**
		 * Muestra el resumen de asignaciones de una materia para usar en 
		 * el resumen de la materia
		 * @param $anio
		 * @param $cuatrimestre
		 * @return array las asignaciones
		 */
		 public function MostrarResumenAsignacionComisiones($anio, $cuatrimestre) {
			 require "./conexion.php";
			 
			 $cod = $this->mostrarCod();
			 $conjunto = $this->mostrarConjunto();
			 
		
			$query = "SELECT ca.materia, 
						ca.turno,
						ca.horario, 
						ca.nombre_comision, 
						ca.anio, 
						ca.cuatrimestre,
						GROUP_CONCAT(DISTINCT CONCAT(d.apellido, ', ', d.nombres) SEPARATOR ' / ') AS docentes
					FROM comisiones_abiertas AS ca
					LEFT JOIN asignacion_comisiones AS ac
						ON ac.materia = ca.materia
							AND ac.anio = ca.anio
							AND ac.cuatrimestre = ca.cuatrimestre
							AND ac.comision = ca.nombre_comision
					LEFT JOIN docente AS d
						ON ac.docente = d.id
					WHERE ca.anio = {$anio}
						AND ca.cuatrimestre = {$cuatrimestre}
						AND ca.materia = '{$conjunto}'
					GROUP BY materia, nombre_comision";
			
			$result = $mysqli->query($query);
			echo $mysqli->error;
			$asignaciones = array();
			while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				//$asignaciones[$row['turno']][$row['horario']][] = $row;
				$asignaciones[$row['horario']][] = $row;
			}
			
			$result->free();
			$mysqli->close();
			
			return $asignaciones;
		}
		
		/**
		 * Muestra el responsable de la materia
		 * @return (array) responsables
		 */
		 public function mostrarResponsable() {
			 require 'conexion.php';
			 
			 $conjunto = $this->mostrarConjunto();
			 $query = "SELECT DISTINCT CONCAT_WS(', ', p.apellido, 
					p.nombres) AS responsable
				FROM responsable AS r
				LEFT JOIN personal AS p ON r.usuario = p.id
				WHERE r.materia IN {$conjunto} AND r.activo = 1;";
			 $result = $mysqli->query($query);
			 echo $mysqli->error;
			 
			 $responsables = array();
			 while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				 $responsables[] = $row['responsable'];
			 }
			 $result->free();
			 $mysqli->close();
			 
			 return $responsables;
		 }
		 
		 /**
		  * Muestra los turnos asignados a la materia
		  * @param (int) anio
		  * @param (int) cuatrimestre
		  * @return (array) Turnos
		  */
		 public function mostrarTurnos($anio, $cuatrimestre) {
			 require 'conexion.php';
			 
			 $cod = $this->mostrarCod();
			 $conjunto = $this->mostrarConjunto();
			 
			 $query = "SELECT DISTINCT dia, turno
					FROM turnos_con_conjunto
					WHERE materia = '{$conjunto}' OR materia LIKE '{$conjunto}%'
						AND anio = {$anio} and cuatrimestre = {$cuatrimestre}
					ORDER BY FIELD(dia, 'lunes', 'martes', 'miercoles', 
						'jueves', 'viernes', 'sabado'), turno;";
			 $result = $mysqli->query($query);
			 
			 $turnos = array();
			 while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				 $turnos[$row['dia']][$row['turno']] = $row;
			 }
			 
			 $result->free();
			 $mysqli->close();
			 return $turnos;
		 }
		 
		 /**
		  * Muestra los docentes asignados a la materia por comision
		  * @param (int) anio
		  * @param (int) cuatrimestre
		  * @param (str) Turno default *
		  * @return (array) Turnos
		  */
		 public function mostrarDocentesAsignadosPorComision($anio, $cuatrimestre, $turno = '*') {
			 require 'conexion.php';
			 
			 $cod = $this->mostrarCod();
			 $conjunto = $this->mostrarConjunto();
			 
			 $whereTurno = "";
			 if ($turno != '*') {
				 $whereTurno = " AND turno = '{$turno}'";
			 }
			 
			 $query = "SELECT acc.id, acc.docente, 
						LEFT(CONCAT(d.apellido, ', ', d.nombres), 20) AS nombre_docente, 
						acc.materia, acc.dia, acc.horario,
						acc.comision, acc.anio, acc.cuatrimestre, acc.aula_virtual
					FROM asignacion_comisiones_calendario AS acc
					LEFT JOIN docente AS d
						ON d.id = acc.docente
					WHERE acc.materia = '{$conjunto}' AND anio = {$anio}
						AND cuatrimestre = {$cuatrimestre} 
						{$whereTurno}";
			 $result = $mysqli->query($query);
			 
			 $docentesAsignados = array();
			 while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				 $docentesAsignados[$row['dia']][$row['turno']][$row['comision']][] = $row;
			 }
			 
			 $result->free();
			 $mysqli->close();
			 return $docentesAsignados;
		 }
		 
		 /**
		  * Muestra las comisiones abiertas
		  * @param (int) anio
		  * @param (int) cuatrimestre
		  * @param (str) Turno default *
		  * @return (array) Comisiones Abiertas
		  */
		 public function mostrarComisionesAbiertas($anio, $cuatrimestre, $turno = '*') {
			 require 'conexion.php';
			 
			 $cod = $this->mostrarCod();
			 $conjunto = $this->mostrarConjunto();
			 
			 $whereTurno = "";
			 if ($turno != '*') {
				 $whereTurno = " AND ca.turno = '{$turno}'";
			 }
			 
			 $query = "SELECT ca.nombre_comision, ca.horario,
						ca.turno, 
						CONCAT(ca.materia, IFNULL(ca.observaciones, '')) AS materia
					FROM comisiones_abiertas AS ca
					WHERE ca.materia = '{$conjunto}' AND ca.anio = {$anio}
						AND ca.cuatrimestre = {$cuatrimestre} 
						{$whereTurno}";
			 $result = $mysqli->query($query);
			 $comisionesAbiertas = array();
			 while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				 $comisionesAbiertas[] = $row;
			 }
			 
			 $result->free();
			 $mysqli->close();
			 return $comisionesAbiertas;
		 }
		 
		 /**
		  * Muestra La situación de la asignación docente
		  * @param (int) anio
		  * @param (int) cuatrimestre
		  * @param (str) Turno default *
		  * @return (array) Situacion
		  */
		 public function mostrarSituacionAsignacion($anio, $cuatrimestre, $turno = '*') {
			 require 'conexion.php';
			 
			 $cod = $this->mostrarCod();
			 $conjunto = $this->mostrarConjunto();
			 
			 $whereTurno = "";
			 if ($turno != '*') {
				 $whereTurno = " AND turno = '{$turno}'";
			 }
			 $query = "SELECT ca.materia, ca.nombre_comision, t.dia, t.turno, acc.docente,
							CONCAT(d.apellido, ', ', d.nombres) AS nombre_docente,
							acc.id AS id_asignacion
						FROM comisiones_abiertas AS ca
						LEFT JOIN turnos_con_conjunto AS t
							ON t.materia = CONCAT(ca.materia, IFNULL(ca.observaciones, ''))
								AND t.anio = ca.anio AND t.cuatrimestre = ca.cuatrimestre
								AND LEFT(t.turno, 1) = ca.turno
						LEFT JOIN asignacion_comisiones_calendario AS acc
							ON acc.anio = ca.anio AND acc.cuatrimestre = ca.cuatrimestre
								AND acc.comision = ca.nombre_comision AND ca.materia = acc.materia
								AND t.dia = acc.dia AND t.turno = acc.horario
						LEFT JOIN docente AS d
							ON d.id = acc.docente
						WHERE ca.anio = {$anio} AND ca.cuatrimestre = {$cuatrimestre}
							AND ca.materia = '{$conjunto}'
						ORDER BY t.turno, ca.nombre_comision";
			 
			 $comisiones = array();
			 
			 $result = $mysqli->query($query);
			 while ($row = $result->fetch_assoc()) {
				 $comisiones[] = $row;
			 }
			 
			 return $comisiones;
			 
		 }
		 
		 /** muestra el total de inscriptos por turno y comision
		  * @param (int) anio
		  * @param (int) periodo lectivo
		  * @return (array) inscriptos por turno y comision
		  */
		 public function mostrarInscriptos($anio, $cuatrimestre) {
			 require "conexion.php";
			 
			 $conjunto = $this->mostrarConjunto();
			 $codigos = $this->mostrarCodigosConjunto();
			 
			 $query = "SELECT nombre_comision, COUNT(DISTINCT nro_documento) AS inscriptos
						FROM inscriptos
						WHERE materia IN $conjunto
							AND anio_academico = $anio 
							AND periodo_lectivo = $cuatrimestre
						GROUP BY materia, comision;";
			 $result = $mysqli->query($query);
			 
			 $inscriptos = array();
			 while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				
				$needle = (int) $row['nombre_comision'];
				$totalLen = strlen($row['nombre_comision']);
				$intLen = strlen($needle);
				
				if (in_array($needle, $codigos)) {
					$comision = substr($row['nombre_comision'], $intLen);
					if (in_array($comision, ['M', 'T', 'N'])) {
						$comision .= "A";
					}
					if (!isset($inscriptos[$comision])) {
						$inscriptos[$comision] = 0;
					}
					$inscriptos[$comision] += $row['inscriptos'];
				}
				 
			 }
				 
			 
			 $result->free();
			 $mysqli->close();
			 return $inscriptos;
		 }
		 
		 /**
		  * Muestra el total de inscriptos por turno en un periodo
		  * @param (int) anio
		  * @param (int) cuatrimestre
		  * @return (array) inscriptos por turno
		  */
		 public function mostrarInscriptosPorTurno($anio, $cuatrimestre, $carrera = "") {
			 require 'conexion.php';
			 
			 $conjunto = $this->mostrarConjunto();
			 $whereCarrera = "";
			 if ($carrera != "") {
				 if ($carrera == 'Comun') {
					 $whereCarrera = " AND carrera IN ('EYN-3', 'EYN-4', 'LITUR') ";
				 } else {
					 $carrera = str_replace(' Y ', "', '", $carrera);
					 $whereCarrera = " AND carrera IN ('" . $carrera . "')";
				 }
			 }
			//echo $whereCarrera;
				 
			 
			 $query = "SELECT IF(nombre_comision LIKE '%N%', 'N',
						IF(nombre_comision LIKE '%M%' OR nombre_comision LIKE '%S%','M', 'Otro')
					) AS turno_comision, COUNT(DISTINCT nro_documento) AS cantidad
					FROM inscriptos
					WHERE materia IN {$conjunto} AND anio_academico = {$anio}
						AND periodo_lectivo = {$cuatrimestre} {$whereCarrera}
					GROUP BY turno_comision;";
			  $result = $mysqli->query($query);
			  echo $mysqli->error;
			  
			  $inscriptos = array();
			  while($row = $result->fetch_array(MYSQLI_ASSOC)) {
				  $inscriptos[$row['turno_comision']] = $row['cantidad'];
			  }
			  
			  $result->free();
			  $mysqli->close();
			  
			  return $inscriptos;
		  }
		  
		   /**
		  * Muestra el total de inscriptos por turno en un periodo por COD
		  * @param (int) anio
		  * @param (int) cuatrimestre
		  * @return (array) inscriptos por turno
		  */
		 public function mostrarInscriptosPorTurnoPorCod($anio, $cuatrimestre, $carrera = "") {
			 require 'conexion.php';
			 //echo "CARRERA : " . $carrera;
			 $cod = $this->mostrarCod();
			 $whereCarrera = "";
			 switch($carrera) {
				 case 'Comun':
					 $whereCarrera = " AND carrera IN ('EYN-3', 'EYN-4', 'LITUR') ";
					 break;
					 
				 case 'EYN-3 Y EYN-4':
					 $whereCarrera = " AND carrera IN ('EYN-3', 'EYN-4')";
					 break;
				 
				 case '':
					$whereCarrera = "";
					break;
					
				 default:
					$carrera = str_replace(' Y ', "', '", $carrera);
					$whereCarrera = " AND carrera IN ('" . $carrera . "')";
					break;

			 }
			//echo $whereCarrera;
				 
			 
			 $query = "SELECT IF(nombre_comision LIKE '%N%', 'N',
						IF(nombre_comision LIKE '%M%' OR nombre_comision LIKE '%S%','M', 'Otro')
					) AS turno_comision, COUNT(DISTINCT nro_documento) AS cantidad
					FROM inscriptos
					WHERE materia = {$cod} 
						AND anio_academico = {$anio}
						AND periodo_lectivo = {$cuatrimestre} {$whereCarrera}
					GROUP BY turno_comision;";
			  $result = $mysqli->query($query);
			  //echo $query;
			  //echo $mysqli->error;
			  
			  $inscriptos = array();
			  while($row = $result->fetch_array(MYSQLI_ASSOC)) {
				  $inscriptos[$row['turno_comision']] = $row['cantidad'];
			  }
			  
			  $result->free();
			  $mysqli->close();
			  
			  return $inscriptos;
		  }
		  
		  /**
		   * Muestra los resultados por turno a una materia según el acta
		   * @param (int) año
		   * @param (int) cuatrimestre
		   * @param (str) carrera para filtrar
		   * @return (array) resultados por turno
		   */
		  public function mostrarResultadosCursada($anio, $cuatrimestre, $carrera = "") {
			  require "conexion.php";
			  
			  $conjunto = $this->mostrarConjunto();
			  $whereCarrera = "";
			 if ($carrera) {
				 $carrera = str_replace(' Y ', "', '", $carrera);
				 $whereCarrera = " AND carrera IN ('" . $carrera . "')";
			 }
			//echo $whereCarrera;
				 
			 
			 $query = "SELECT IF(nombre LIKE '%N%', 'N',
						IF(nombre LIKE '%M%' OR nombre LIKE '%S%','M', 'Otro')
					) AS turno_comision, 
					COUNT(DISTINCT nro_documento) AS inscriptos,
					SUM(IF(resultado = 'Aprob', 1, 0)) AS aprobado,
					SUM(IF(resultado = 'promocion', 1, 0)) AS promovido,
					SUM(IF(resultado = 'reprob', 1, 0)) AS reprobado,
					SUM(IF(resultado = 'ausente', 1, 0)) AS ausente
					FROM actas
					WHERE materia IN {$conjunto} AND anio_academico = {$anio}
						AND periodo_lectivo = {$cuatrimestre} {$whereCarrera}
					GROUP BY turno_comision;";
			  $result = $mysqli->query($query);
			  if ($mysqli->errno) {
				 echo $mysqli->error;
			  }
			  
			  $resultados = array();
			  while($row = $result->fetch_array(MYSQLI_ASSOC)) {
				  $resultados[$row['turno_comision']]['aprobado'] = $row['aprobado'];
				  $resultados[$row['turno_comision']]['reprobado'] = $row['reprobado'];
				  $resultados[$row['turno_comision']]['promovido'] = $row['promovido'];
				  $resultados[$row['turno_comision']]['ausente'] = $row['ausente'];
				  $resultados[$row['turno_comision']]['inscriptos'] = $row['inscriptos'];
			  }
			  
			  $result->free();
			  $mysqli->close();
			  
			  return $resultados;
		  }
		  
		  /**
		   * Estima la cantidad de recursantes en un periodo
		   * @param (int) anio
		   * @param (int) cuatrimestre
		   * @return (array) Estimacion por turno
		   */
		  private function mostrarEstimacionRecursantes($anio, $cuatrimestre) {
			  
			    $inscriptosPorTurno = $this->mostrarInscriptosPorTurno($anio, $cuatrimestre);
			    $ratiosAprobacion = $this->mostrarRatioAprobacion($anio, $cuatrimestre);
			    $ratioRecursada = 1; // Hay que refinarlo
			    
			    $inscriptosEstimados = array();
				foreach ($inscriptosPorTurno as $turno => $cantidad) {
					if (isset($ratiosAprobacion[$turno])) {
						$inscriptosEstimados[$turno]['recursantes'] = (int) (($cantidad * ( 1 - $ratiosAprobacion[$turno])) * $ratioRecursada);
					} else {
						$inscriptosEstimados[$turno]['recursantes'] = 0; //$turno . ": NR<br />";
					}
				
				
				// A esto habría que descontarle un ratio de deserción en la materia
				}
				
				return $inscriptosEstimados;
		  }
		  
		  /**
		   * Estima la cantidad de recursantes en un periodo Por codigo
		   * @param (int) anio
		   * @param (int) cuatrimestre
		   * @return (array) Estimacion por turno
		   */
		  public function mostrarEstimacionRecursantesPorCod($anio, $cuatrimestre) {
			  
			    $inscriptosPorTurno = $this->mostrarInscriptosPorTurnoPorCod($anio, $cuatrimestre);
			    
			    $ratiosAprobacion = $this->mostrarRatioAprobacionPorCod($anio, $cuatrimestre);
			    $ratioRecursada = 1; // Hay que refinarlo
			    
			    $inscriptosEstimados = array();
				foreach ($inscriptosPorTurno as $turno => $cantidad) {
					if (isset($ratiosAprobacion[$turno])) {
						$inscriptosEstimados[$turno]['recursantes'] = (int) (($cantidad * ( 1 - $ratiosAprobacion[$turno])) * $ratioRecursada);
					} 
				// A esto habría que descontarle un ratio de deserción en la materia
				}
				return $inscriptosEstimados;
		  }
		  
		  /**
		   * Estima la cantidad de nuevos ingresantes a la materia
		   * @param (int) anio
		   * @param (int) cuatrimestre
		   * @param (str) carrera
		   * @return (array) estimacion nuevos alumnos
		   */
		  private function mostrarEstimacionNuevos($anio, $cuatrimestre, $carrera = "") {
			  
			  
		  }
			  
		  
		  /**
		   * Estima los posibles inscriptos según las inscripciones al
		   * ciclo anterior en el periodo similar
		   * @param (int) anio
		   * @param (int) cuatrimestre
		   * @return (array) Estimación por turno
		   */
		   public function mostrarEstimacionPreliminar($anio, $cuatrimestre, $carrera = "") {
				$materiaOriginal = $_SESSION['materia'];
				$inscriptosMateria = $this->mostrarInscriptos($anio, $cuatrimestre);
				
				$ratios = $this->mostrarRatioAprobacion($anio, $cuatrimestre);
				$inscriptosPorTurno = $this->mostrarInscriptosPorTurno($anio, $cuatrimestre);
				
				$inscriptosEstimados = $this->mostrarEstimacionRecursantes($anio, $cuatrimestre);
					
				
				$conjunto = $this->mostrarCodigosConjunto();
				
				foreach ($conjunto as $codMateria) {
					$materia = new Materia($codMateria);
					$correlativas = $materia->mostrarCorrelativas();
					$nuevos = array();
					
					if (is_array($correlativas)) {
						foreach ($correlativas as $cod => $nombre) {
							$correlativa = new Materia($cod);
							
							$inscriptosCuat = $correlativa->mostrarInscriptosPorTurno($anio, $cuatrimestre, $carrera);
							
							$anioCorrelativa = $anio;
							$cuatrimestreCorrelativa = 1;
							
							if ($cuatrimestre == 1) {
								$cuatrimestreCorrelativa = 2;
								$anioCorrelativa = $anioCorrelativa - 1;
							} 
								$cuatrimestreCorrelativa = 1;
							$ratiosCorr = $correlativa->mostrarRatioAprobacion($anioCorrelativa, $cuatrimestreCorrelativa);
							foreach ($inscriptosCuat as $turno => $cantidad) {
								if (!isset($nuevos[$turno]) or $cantidad * ($ratiosCorr[$turno]) > $nuevos[$turno]) {
									if (isset($ratiosCorr[$turno])) {
										$nuevos[$turno] = (int) ($cantidad * ($ratiosCorr[$turno]));
									} else {
										/*echo "ERROR RATIO CORR: " . $correlativa->mostrarCod() . " $turno<br />";
										print_r($ratiosCorr);*/
										$nuevos[$turno] = 0;
									}
								}
							}
						}
					}
					
					foreach ($nuevos as $turno => $cantidad) {
						if (!isset($inscriptosEstimados[$turno]['nuevos'])) {
							$inscriptosEstimados[$turno]['nuevos'] = $nuevos[$turno];
						} else {
							$inscriptosEstimados[$turno]['nuevos'] += $nuevos[$turno];
						}
					}
				}
					
				$_SESSION['materia'] = $materiaOriginal;
				return $inscriptosEstimados;
			}
			
			/**
		   * Estima los posibles inscriptos según las inscripciones al
		   * ciclo anterior en el periodo similar por código de materia 
		   * @param (int) anio
		   * @param (int) cuatrimestre
		   * @return (array) Estimación por turno
		   */
		   public function mostrarEstimacionPreliminarPorCod($anio, $cuatrimestre, $carrera = "") {
				$materiaOriginal = $_SESSION['materia'];
				
				$cuatrimestreTurismo = ($cuatrimestre % 2) + 1;
				$anioTurismo = ($cuatrimestre == 2) ? $anio : ($anio - 1);
				
				if ($carrera != 'LITUR') {				
					$inscriptosPorTurno = $this->mostrarInscriptosPorTurnoPorCod($anio, $cuatrimestre, $carrera);
					$ratios = $this->mostrarRatioAprobacionPorCod($anio, $cuatrimestre);
					$recursantesEstimados = $this->mostrarEstimacionRecursantesPorCod($anio, $cuatrimestre);
				} else {
					$inscriptosPorTurno = $this->mostrarInscriptosPorTurnoPorCod($anioTurismo, $cuatrimestreTurismo, $carrera);
					$ratios = $this->mostrarRatioAprobacionPorCod($anioTurismo, $cuatrimestreTurismo);
					$recursantesEstimados = $this->mostrarEstimacionRecursantesPorCod($anioTurismo, $cuatrimestreTurismo);
				}
				
				$correlativas = $this->mostrarCorrelativas();
				$nuevos = array();
				$inscriptosEstimados = array();
				if (is_array($correlativas)) {
					foreach ($correlativas as $cod => $nombre) {
						$correlativa = new Materia($cod);
						
						$inscriptosCuat = $correlativa->mostrarInscriptosPorTurnoPorCod($anio, $cuatrimestre, $carrera);
						//print_r($inscriptosCuat);
						
						$anioCorrelativa = $anio;
						$cuatrimestreCorrelativa = $cuatrimestre; //1;
						/*if ($cuatrimestre == 1) {
							$cuatrimestreCorrelativa = 2;
							$anioCorrelativa = $anioCorrelativa - 1;
						} */
						if ($carrera != 'LITUR') {
							$ratiosCorr = $correlativa->mostrarRatioAprobacionPorCod($anioCorrelativa, $cuatrimestreCorrelativa);
						} else {
							$ratiosCorr = $correlativa->mostrarRatioAprobacionPorCod($anioTurismo, $cuatrimestreTurismo);
						}
						foreach ($inscriptosCuat as $turno => $cantidad) {
							if (!isset($nuevos[$turno]) or $cantidad * ($ratiosCorr[$turno]) > $nuevos[$turno]) {
								if (isset($ratiosCorr[$turno])) {
									$nuevos[$turno] = (int) ($cantidad * ($ratiosCorr[$turno]));
								} 
							}
						}
					}
					
					
					foreach ($nuevos as $turno => $cantidad) {
						if (!isset($inscriptosEstimados[$turno]['nuevos'])) {
							$inscriptosEstimados[$turno]['nuevos'] = $nuevos[$turno];
						} else {
							$inscriptosEstimados[$turno]['nuevos'] += $nuevos[$turno];
						}
					}
					
					
				}
				
				foreach ($recursantesEstimados as $turno => $cantidad) {
						$inscriptosEstimados[$turno]['recursantes'] = $cantidad['recursantes'];
					}
					
				$_SESSION['materia'] = $materiaOriginal;
				
				return $inscriptosEstimados;
			}
			
			/**
			 * Estima el ratio de recursantes Según el comportamiento 
			 * de los recursantes disponibles de los últimos 2 años
			 * @param (int) anio
			 * @param (int) cuatrimestre
			 * @return (double) ratio de recursantes
			 */
			public function mostrarRatioRecursantes($anio, $cuatrimestre) {
				require 'conexion.php';
				
				$conjunto = $this->mostrarConjunto();
				
				$periodo = array();
				$periodo['actual']['anio'] = $anio - 1;
				$periodo['actual']['cuatrimestre'] = $cuatrimestre;
				
				$periodo['anio_limite']['abajo'] = $anio - 3;
				$periodo['anio_limite']['arriba'] = $periodo['anio_limite']['abajo'] + 1;
				
				$periodo['agregado']['anio'] = ($cuatrimestre == 1) ? $periodo['anio_limite']['abajo'] - 1 : $periodo['anio_limite']['arriba'] + 1;
				$periodo['agregado']['cuatrimestre'] = ($cuatrimestre % 2) + 1;
				
				$query = "SELECT SUM(IF(inscripto > 0, 1, 0)) / SUM(IF(recursante > 0, 1, 0)) AS ratio FROM(
							SELECT DISTINCT nro_documento , apellido,
									SUM(IF((anio_academico BETWEEN {$periodo['anio_limite']['abajo']} AND {$periodo['anio_limite']['arriba']} OR 
										(anio_academico = {$periodo['agregado']['anio']} AND periodo_lectivo = {$periodo['agregado']['cuatrimestre']})) 
										AND resultado IN ('reprob', 'ausente'), 1, 0)) AS recursante,

									SUM(IF(anio_academico = {$periodo['actual']['anio']} 
										AND periodo_lectivo = {$periodo['actual']['cuatrimestre']}, 1, 0)) AS inscripto
								FROM actas
								WHERE materia IN {$conjunto}
								GROUP BY nro_documento
								HAVING recursante > 0
						) AS b";
				
				$result = $mysqli->query($query);
				
				
				$ratio = $result->fetch_array(MYSQLI_ASSOC)['ratio'];
				$result->free();
				$mysqli->close();
				
				return $ratio;
			}
			
			/**
			 * Muestra los recursantes disponibles para inscribirse 
			 * de los últimos 5 cuatrimestres
			 * @param (int) año
			 * @param (int) periodo_lectivo
			 * @return (array) recursantes por turno
			 */
			public function mostrarRecursantes($anio, $cuatrimestre) {
				require 'conexion.php';
				
				$conjunto = $this->mostrarConjunto();
				
				$periodo = array();
				$periodo['actual']['anio'] = $anio - 1;
				$periodo['actual']['cuatrimestre'] = $cuatrimestre;
				
				$periodo['anio_limite']['abajo'] = $anio - 2;
				$periodo['anio_limite']['arriba'] = $periodo['anio_limite']['abajo'] + 1;
				
				$periodo['agregado']['anio'] = ($cuatrimestre == 1) ? $periodo['anio_limite']['abajo'] - 1 : $periodo['anio_limite']['arriba'] + 1;
				$periodo['agregado']['cuatrimestre'] = ($cuatrimestre % 2) + 1;
				
				 
				
				$query = "SELECT COUNT(*) AS recursantes, turno_comision FROM(
					SELECT DISTINCT nro_documento , apellido,
							IF(nombre LIKE '%N%', 'N',
								IF(nombre LIKE '%M%' OR nombre LIKE '%S%','M', 'Otro')
							) AS turno_comision, 
							SUM(IF((anio_academico BETWEEN {$periodo['anio_limite']['abajo']} AND {$periodo['anio_limite']['arriba']} OR 
										(anio_academico = {$periodo['agregado']['anio']} AND periodo_lectivo = {$periodo['agregado']['cuatrimestre']})) 
										AND resultado IN ('reprob', 'ausente'), 1, 0)) AS recursante,
							SUM(IF((anio_academico BETWEEN {$periodo['anio_limite']['abajo']} AND {$periodo['anio_limite']['arriba']} OR 
										(anio_academico = {$periodo['agregado']['anio']} AND periodo_lectivo = {$periodo['agregado']['cuatrimestre']})) 
										AND resultado IN ('aprob', 'promocion'), 1, 0)) AS aprobo
						FROM actas
						WHERE materia IN {$conjunto}
						GROUP BY nro_documento
						HAVING recursante > 0 AND aprobo = 0
				) AS b
				GROUP BY turno_comision;";
				$result = $mysqli->query($query);
				//echo "<pre>" . $query . "</pre>";
				$recursantes = array();
				
				while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
					$recursantes[$row['turno_comision']] = $row['recursantes'];
				}
				
				
				$result->free();
				$mysqli->close();
				
				return $recursantes;
			}
			
			/**
			 * Segunda estimación de inscriptos (ACTAS CARGADAS)
			 * @param (int) anio academico
			 * @param (int) periodo lectivo
			 * @param (str) carreras
			 * @return (array) estimación por turno
			 */
			public function segundaEstimacion($anio, $cuatrimestre, $carrera = "") {
				$materiaOriginal = $_SESSION['materia'];
				$inscriptosMateria = $this->mostrarInscriptos($anio, $cuatrimestre);
				$resultados = $this->mostrarResultadosCursada($anio, $cuatrimestre, $carrera);
				$ratioRecursantes = .8;
				$ratioNuevos = .8; // CALCULAR CUANTOS DE LOS QUE RECURSAN ABANDONAN DESGLOSADO POR MATERIA
				
				$inscriptosEstimados = array();
				foreach ($resultados as $turno => $resultado) {
					$inscriptosEstimados[$turno]['recursantes'] = (int) (($resultado['reprobado'] + $resultado['ausente']) * $ratioRecursantes);
				}
				
				$conjunto = $this->mostrarCodigosConjunto();
				
				
				foreach ($conjunto as $codMateria) {
					$materia = new Materia($codMateria);
					$correlativas = $materia->mostrarCorrelativas();
					$nuevos = array();
					
					if (is_array($correlativas)) {
						foreach ($correlativas as $cod => $nombre) {
							$correlativa = new Materia($cod);
							
							$resultadosCorrelativa = $correlativa->mostrarResultadosCursada($anio, $cuatrimestre, $carrera);
							foreach ($resultadosCorrelativa as $turno => $resultado) {
								$cantidad = $resultado['aprobado'] + $resultado['promovido'];
								if (!isset($nuevos[$turno]) or ($cantidad > $nuevos[$turno])) {
									$nuevos[$turno] = (int) ($cantidad * $ratioNuevos) ;
								}
							}
						}
					}
					
					foreach ($nuevos as $turno => $cantidad) {
						if (!isset($inscriptosEstimados[$turno]['nuevos'])) {
							$inscriptosEstimados[$turno]['nuevos'] = $nuevos[$turno];
						} else {
							$inscriptosEstimados[$turno]['nuevos'] += $nuevos[$turno];
						}
					}
				}
					
				$_SESSION['materia'] = $materiaOriginal;
				return $inscriptosEstimados;
			}
			
			/**
			 * Muestra el ratio de nuevos inscriptos dsede el pool de
			 * posibles candidatos
			 * @param (int) anio
			 * @param (int) cuatrimestre
			 * @param (str) Carreras para el in
			 */
			public function mostrarRatioPoolNuevos($anio, $cuatrimestre, $carrera = "") {
				require 'conexion.php';
				
				$conjunto = $this->mostrarConjunto();
				$correlativas = $this->mostrarCorrelativas();
				
				$periodo = array();
				$periodo['actual']['anio'] = $anio - 1;
				$periodo['actual']['cuatrimestre'] = $cuatrimestre;
				
				$periodo['anio_limite']['abajo'] = $anio - 3;
				$periodo['anio_limite']['arriba'] = $periodo['anio_limite']['abajo'] + 1;
				
				$periodo['agregado']['anio'] = ($cuatrimestre == 1) ? $periodo['anio_limite']['abajo'] - 1 : $periodo['anio_limite']['arriba'] + 1;
				$periodo['agregado']['cuatrimestre'] = ($cuatrimestre % 2) + 1;
				
				$materiaOriginal = $_SESSION['materia'];
				$sumasCorrelativas = array();
				$condicionesCorrelativas = array();
				$textoSumasCorrelativas = "";
				$textoCondicionesCorrelativas = "";
				if (is_array($correlativas)) {
					$i = 1;
					foreach ($correlativas as $cod => $nombre) {
						$correlativa = new Materia($cod);
						$conjuntoCorrelativa = $correlativa->mostrarConjunto();
						
						$sumasCorrelativas[] = "SUM(IF((anio_academico BETWEEN {$periodo['anio_limite']['abajo']} AND {$periodo['anio_limite']['arriba']}
									OR (anio_academico = {$periodo['agregado']['anio']} AND periodo_lectivo = {$periodo['agregado']['cuatrimestre']})) 
									AND materia IN {$conjuntoCorrelativa} AND resultado IN ('aprob', 'promocion'), 1, 0)) AS correlativa{$i}";
						$condicionesCorrelativas[] = " correlativa{$i} > 0 ";
						$i++;
					}
					
					$textoSumasCorrelativas = ", " . join(', ', $sumasCorrelativas);
					$textoCondicionesCorrelativas = " AND " . join(' AND ', $condicionesCorrelativas);
				}
				$_SESSION['materia'] = $materiaOriginal;
				 
				$query = "SELECT SUM(inscripto) / COUNT(*) AS ratio, SUM(inscripto), COUNT(*)  FROM (
							SELECT nro_documento, apellido, 
								SUM(IF((anio_academico BETWEEN {$periodo['anio_limite']['abajo']} AND {$periodo['anio_limite']['arriba']}
									OR (anio_academico = {$periodo['agregado']['anio']} AND periodo_lectivo = {$periodo['agregado']['cuatrimestre']})) 
									AND materia IN {$conjunto}, 1, 0)) AS nuevo,
									SUM(IF(anio_academico = {$periodo['actual']['anio']} AND periodo_lectivo = {$periodo['actual']['cuatrimestre']}
									AND materia IN {$conjunto}, 1, 0)) AS inscripto
									{$textoSumasCorrelativas}
								FROM actas
								WHERE carrera IN ('EYN-4', 'EYN-3')
								GROUP BY nro_documento
								HAVING nuevo = 0 {$textoCondicionesCorrelativas}
							) AS b";
				$result = $mysqli->query($query);
				//echo $mysqli->error;
				//echo "<pre>" . $query . "</pre>";
				$ratio = 0;
				
				while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
					$ratio = $row['ratio'];
				}
				
				
				$result->free();
				$mysqli->close();
				
				return $ratio;
			}
			
			/**
			 * Muestra la cantidad de nuevos inscriptos dsede el pool de
			 * posibles candidatos
			 * @param (int) anio
			 * @param (int) cuatrimestre
			 * @param (str) Carreras para el in
			 */
			public function mostrarPoolNuevos($anio, $cuatrimestre, $carrera = "") {
				require 'conexion.php';
				
				$correlativas = $this->mostrarCorrelativas();
				$conjunto = $this->mostrarConjunto();
				
				$periodo = array();
				$periodo['actual']['anio'] = $anio - 1;
				$periodo['actual']['cuatrimestre'] = $cuatrimestre;
				
				$periodo['anio_limite']['abajo'] = $anio - 2;
				$periodo['anio_limite']['arriba'] = $periodo['anio_limite']['abajo'] + 1;
				
				$periodo['agregado']['anio'] = ($cuatrimestre == 1) ? $periodo['anio_limite']['abajo'] - 1 : $periodo['anio_limite']['arriba'] + 1;
				$periodo['agregado']['cuatrimestre'] = ($cuatrimestre % 2) + 1;
				
				$materiaOriginal = $_SESSION['materia'];
				$sumasCorrelativas = array();
				$condicionesCorrelativas = array();
				$textoSumasCorrelativas = "";
				$textoCondicionesCorrelativas = "";
				if (is_array($correlativas)) {
					$i = 1;
					foreach ($correlativas as $cod => $nombre) {
						$correlativa = new Materia($cod);
						$conjuntoCorrelativa = $correlativa->mostrarConjunto();
						
						$sumasCorrelativas[] = "SUM(IF((anio_academico BETWEEN {$periodo['anio_limite']['abajo']} AND {$periodo['anio_limite']['arriba']}
									OR (anio_academico = {$periodo['agregado']['anio']} AND periodo_lectivo = {$periodo['agregado']['cuatrimestre']})) 
									AND materia IN {$conjuntoCorrelativa} AND resultado IN ('aprob', 'promocion'), 1, 0)) AS correlativa{$i}";
						$condicionesCorrelativas[] = " correlativa{$i} > 0 ";
						$i++;
					}
					
					$textoSumasCorrelativas = ", " . join(', ', $sumasCorrelativas);
					$textoCondicionesCorrelativas = " AND " . join(' AND ', $condicionesCorrelativas);
				}
				$_SESSION['materia'] = $materiaOriginal;
				
				$query = "SELECT (COUNT(*) * .48 * 1.4) AS nuevos, turno_comision, COUNT(*) FROM (
					SELECT nro_documento, apellido, IF(nombre LIKE '%N%', 'N',
											IF(nombre LIKE '%M%' OR nombre LIKE '%S%','M', 'Otro')
										) AS turno_comision,
						SUM(IF((anio_academico BETWEEN {$periodo['anio_limite']['abajo']} AND {$periodo['anio_limite']['arriba']}
									OR (anio_academico = {$periodo['agregado']['anio']} AND periodo_lectivo = {$periodo['agregado']['cuatrimestre']})) 
									AND materia IN {$conjunto}, 1, 0)) AS nuevo
						{$textoSumasCorrelativas}
						
						FROM actas
						WHERE carrera IN ('EYN-4', 'EYN-3')
						GROUP BY nro_documento
						HAVING  nuevo = 0 {$textoCondicionesCorrelativas}
					) AS b
					GROUP BY turno_comision";
				$result = $mysqli->query($query);
				//echo "<pre>" . $query . "</pre>";
				$nuevos = array();
				
				while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
					$nuevos[$row['turno_comision']] = (int) $row['nuevos'];
				}
				
				
				$result->free();
				$mysqli->close();
				
				return $nuevos;
			}
			
			/**
			 * Segunda estimación de inscriptos (ACTAS CARGADAS) Utilizando
			 * los pool de graduados y recursantes
			 * @param (int) anio academico
			 * @param (int) periodo lectivo
			 * @param (str) carreras
			 * @return (array) estimación por turno
			 */
			public function mostrarEstimacionPool($anio, $cuatrimestre, $carrera = "") {
				
				$poolRecursantes = $this->mostrarRecursantes($anio, $cuatrimestre);
				$poolNuevos = $this->mostrarPoolNuevos($anio, $cuatrimestre, $carrera);
				$ratioRecursantes = $this->mostrarRatioRecursantes($anio,$cuatrimestre);
				if ($ratioRecursantes == 0) {
					$ratioRecursantes = .5;
				}
				
				$ratioNuevos = $this->mostrarRatioPoolNuevos($anio, $cuatrimestre);
				if ($ratioNuevos == 0) {
					$ratioNuevos = .5;
				}
				
				$ratioNuevosAntiguos = 1.5;
				$ratioRecursantesAntiguos = 1.4;
				
				$turnos = ['M', 'N', 'Otro'];
				
				$inscriptosEstimados = array();
				
				foreach ($turnos as $turno) {
					$inscriptosEstimados[$turno]['nuevos'] = 0;
					$inscriptosEstimados[$turno]['recursantes'] = 0;
					
					if (isset($poolNuevos[$turno])) {
						$inscriptosEstimados[$turno]['nuevos'] = (int) ($poolNuevos[$turno] * $ratioNuevos * $ratioNuevosAntiguos);
					}
					if (isset($poolRecursantes[$turno])) {
						$inscriptosEstimados[$turno]['recursantes'] = (int) ($poolRecursantes[$turno] * $ratioRecursantes * $ratioRecursantesAntiguos);
					}
				}
				
				return $inscriptosEstimados;
			}
			
			/**
			 * Agregar asignacion desde página de calendario
			 * @param docente
			 * @param dia
			 * @param turno
			 * @param comision
			 * @param anio
			 * @param cuatrimestre
			 * @return bool (success) or error detail
			 **/
			 
			 public function agregarAsignacionComisionCalendario($docente, $dia, $turno, $comision, $anio, $cuatrimestre) {
				 
				 require './conexion.php';
				 $conjunto = $this->mostrarConjunto();
				 $usuario = $_SESSION['usuario'];
				 
				 if ($comision == 'Coord') {
					 $this->eliminarCoordinador($anio, $cuatrimestre);
				 }
				 
				 $query = "INSERT INTO asignacion_comisiones_calendario 
							(docente, materia, horario, comision, usuario_ultima_modificacion, anio, cuatrimestre, dia)
									VALUES ({$docente}, '{$conjunto}', '{$turno}', '{$comision}',
													'{$usuario}', {$anio}, {$cuatrimestre}, '{$dia}')";
						
				$mysqli->query($query);
				$exito = true;
				
				if ($mysqli->errno) {
					$exito = $mysqli->error;
				}
				$mysqli->close();
				
				return $exito;
						
			 }
			 
			/**
			 * ELIMINAR asignacion desde página de calendario
			 * @param id
			 * @return bool (success) or error detail
			 **/
			 
			 public function eliminarAsignacionComisionCalendario($id) {
				 
				 require './conexion.php';
				 $conjunto = $this->mostrarConjunto();
				 $usuario = $_SESSION['usuario'];
				 
				 $query = "DELETE FROM asignacion_comisiones_calendario 
							WHERE id = {$id}";
						
				$mysqli->query($query);
				$exito = true;
				
				if ($mysqli->errno) {
					$exito = $mysqli->error;
				}
				$mysqli->close();
				
				return $exito;
						
			 }
			 
			/**
			 * Mostrar el coordinador de una materia en un periodo
			 * @param anio
			 * @param cuatrimestre
			 * @return array coordinador
			 **/
			 public function mostrarCoordinador($anio, $cuatrimestre) {
				 
				 require './conexion.php';
				 $conjunto = $this->mostrarConjunto();
				 
				 $query = "SELECT CONCAT(d.apellido, ', ', d.nombres) AS nombre, d.id
							FROM asignacion_comisiones_calendario AS acc
							LEFT JOIN docente AS d
								ON d.id = acc.docente
							WHERE acc.anio = {$anio}
								AND acc.cuatrimestre = {$cuatrimestre}
								AND acc.materia = '{$conjunto}'
								AND acc.comision = 'Coord'";
								
				//echo $query;
				$result = $mysqli->query($query);
				echo $mysqli->error;
				$docente = $result->fetch_array(MYSQLI_ASSOC);
				
				$mysqli->close();
				
				return $docente;
			}
					
		    /**
		     * ELIMINAR todos los coordinadores del periodo
		     * @param anio
		     * @param cuatrimestre
		     * @return bool (exito)
		     */
		    public function eliminarCoordinador($anio, $cuatrimestre) {
				require './conexion.php';
				$conjunto = $this->mostrarConjunto();
				
				$query = "DELETE
							FROM asignacion_comisiones_calendario
							
							WHERE anio = {$anio}
								AND cuatrimestre = {$cuatrimestre}
								AND materia = '{$conjunto}'
								AND comision = 'Coord'";
				echo $query;
				$mysqli->query($query);
				$mysqli->close();
				
				return 1;
			}
						
		// ++AGREGAR DOCENTE A LA MATERIA
	}
}
?>
