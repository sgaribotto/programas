<?php
	//CLASE Alumno
	
	class Alumno {
		
		//Propiedades
		
		public $dni;
		
		public $carreras = array();
		
		public $nombre;
		
		public $detalleMateriasAprobadas = array();
		
		public $existe = false;
		
		public $errorNoExiste = "No tiene materias aprobadas en Administración o economía <a href='ingreso.php'>volver</a>";
		
		public $tablaEquivalencias = array();
		
		public $debeCursar = array();
		
		
		//Métodos
		
		public function __construct($dni) {
			
			//$dni = mysql_real_escape_string($dni);
			$this->dni = $dni;
			require 'conexion.php';
			
			$query = "SELECT dni, carrera, nombre_alumno, count(*) as cantidadaprobadas
							FROM materias_aprobadas 
							WHERE dni = '$dni' AND carrera in ('EYN-3', 'EYN-4')
							GROUP BY carrera";
			//echo $query;
			$result = $mysqli->query($query);
			
			if ($result->num_rows > 0) {
				
				$this->existe = true;
				while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
					$this->carreras[$row['carrera']] = $row['cantidadaprobadas'];
					$this->nombre = $row['nombre_alumno'];
				}
				
				$result->free();
			}
			
			$mysqli->close();
		}
		
		public function seleccionarTablaEquivalencias($carrera) {
			
			
			if (!$this->existe ) {
				return $this->errorNoExiste;
			} else {
					require 'conexion.php';
					
					arsort($this->carreras);
					$this->tablaEquivalencias['origen']['carrera'] = key($this->carreras);
					switch(strtoupper($this->tablaEquivalencias['origen']['carrera'])) {
						case 'EYN-3':
							$nombreCarrera = "Lic. Administración";
							break;	
						case 'EYN-4':
							$nombreCarrera = "Lic. Economía";
							break;	
							
						default:
							$NombreCarrera = "Otra";
						}
						
					$this->tablaEquivalencias['origen']['nombreCarrera'] = $nombreCarrera;
					$this->tablaEquivalencias['origen']['plan'] = '1999';
					
					$query = "SELECT COUNT(*) FROM planes WHERE carrera = '{$this->tablaEquivalencias['origen']['carrera']}' AND plan = '{$this->tablaEquivalencias['origen']['plan']}' ";
					$result = $mysqli->query($query);
					$row = $result->fetch_row();
					$this->tablaEquivalencias['origen']['cantidadMaterias'] = $row[0];
					$result->free();
					
					$this->tablaEquivalencias['destino']['carrera'] = $carrera;
					switch(strtoupper($this->tablaEquivalencias['destino']['carrera'])) {
						case 'EYN-3':
							$nombreCarrera = "Lic. Administración";
							break;	
						case 'EYN-4':
							$nombreCarrera = "Lic. Economía";
							break;	
							
						default:
							$nombreCarrera = "Otra";
						}
						
					$this->tablaEquivalencias['destino']['nombreCarrera'] = $nombreCarrera;
					$this->tablaEquivalencias['destino']['plan'] = '2014';
					$query = "SELECT COUNT(*) FROM planes WHERE carrera = '{$this->tablaEquivalencias['destino']['carrera']}' AND plan = '{$this->tablaEquivalencias['destino']['plan']}' ";
					$result = $mysqli->query($query);
					$row = $result->fetch_row();
					$this->tablaEquivalencias['destino']['cantidadMaterias'] = $row[0];
					
					$result->free();
					$mysqli->close();
			}
			
			
			
		}
		
		public function mostrarDetalleMateriasAprobadas() {
			require 'conexion.php';
			
			$query = "SELECT materia, nombre_materia, carrera FROM materias_aprobadas WHERE dni = '$this->dni' ORDER by carrera ";
			
			$result = $mysqli->query($query);
			
			while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$this->detalleMateriasAprobadas[$row['carrera']][$row['materia']] = utf8_encode($row['nombre_materia']);
			}
			
			$result->free();
			$mysqli->close();
		}
		
		public function mostrarMateriasDelPlanOrigen() {
			require 'conexion.php';
			
		$query = "SELECT CM, materia FROM planes WHERE carrera = '{$this->tablaEquivalencias['origen']['carrera']}' AND plan = '{$this->tablaEquivalencias['origen']['plan']}' ";
			$result = $mysqli->query($query);
			
			$materias = array();
			
			while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$materias[$row['CM']] = utf8_encode($row['materia']);
			}
			
			return $materias;
		}
		
		public function mostrarMateriasDelPlanDestino() {
			require 'conexion.php';
			
		$query = "SELECT CM, materia FROM planes WHERE carrera = '{$this->tablaEquivalencias['destino']['carrera']}' AND plan = '{$this->tablaEquivalencias['destino']['plan']}' ";
			$result = $mysqli->query($query);
			
			$materias = array();
			
			while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$materias[$row['CM']] = utf8_encode($row['materia']);
			}
			
			return $materias;
		}
		
		public function mostrarMateriasDebeCursarOrigen() {
			$materiasPlanOrigen = $this->mostrarMateriasDelPlanOrigen();
			$carreraOrigen = $this->tablaEquivalencias['origen']['carrera'];
			$debeCursar = array_diff_key($materiasPlanOrigen, $this->detalleMateriasAprobadas[$carreraOrigen]);
			$this->debeCursar['origen'] = $debeCursar;
			return $debeCursar;
		}
		
		public function mostrarEquivalenciasDadas() {
			require "conexion.php";
			//Lista para el in
			$inList = "('0'";
			$carreraOrigen = $this->tablaEquivalencias['origen']['carrera'];
			foreach ($this->detalleMateriasAprobadas[$carreraOrigen] as $key => $value) {
				$inList .= ", $key";
			}
			$inList .= ")";
			
			$query = "SELECT p.cm, p.materia 
								FROM planes AS p left JOIN equivalencias AS e ON p.cm = e.dada_cm AND p.carrera = e.carrera_destino AND p.plan = e.plan_destino
								WHERE  e.carrera_destino = '{$this->tablaEquivalencias['destino']['carrera']}' AND e.plan_destino = '{$this->tablaEquivalencias['destino']['plan']}'
								AND e.carrera_origen = '{$this->tablaEquivalencias['origen']['carrera']}' AND e.plan_origen = '{$this->tablaEquivalencias['origen']['plan']}'
								AND (e.pide_cm_1  IN $inList AND e.pide_cm_2 IN $inList )";
			
			$debeCursar = array();
			$result = $mysqli->query($query);
			
			while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
				$debeCursar[$row['cm']] = utf8_encode($row['materia']);
			}
			
			$result->free();
			$mysqli->close();
			
			return $debeCursar;
			
		}
		
		public function mostrarMateriasDebeCursarDestino() {
			$equivalencias = $this->mostrarEquivalenciasDadas();
			$materiasPlanDestino = $this->mostrarMateriasDelPlanDestino();
			$debeCursar = array_diff($materiasPlanDestino, $equivalencias);
			ksort($debeCursar);
			$this->debeCursar['destino'] = $debeCursar;
			return $debeCursar;
		}
		
		public function estaEnElPlanNuevo($carrera) {
			require 'conexion.php';
			
			$query = "SELECT COUNT(*) AS materiasplannuevo FROM materias_aprobadas WHERE dni = {$this->dni} AND materia > 1000 AND carrera = '$carrera'";
			$result = $mysqli->query($query);
			
			$cantidadMaterias = $result->fetch_row();
			
			if ($cantidadMaterias['0'] > 0) {
				return true;
			} else {
				return false;
			}
			
			$result->free();
			$mysqli->close();
		}
		
	}
