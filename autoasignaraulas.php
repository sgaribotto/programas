<pre>
<?php
	//display_errors(E_ALL);
	error_reporting(E_ALL);

	//ini_set("memory_limit",-1);

	//Algoritmo para repartir alumnos en aulas

	$turno = $_REQUEST['turno'];
	$letraTurno = substr($turno, 0, 1);
	$anio = $_REQUEST['anio'];
	$cuatrimestre = $_REQUEST['cuatrimestre'];
	//$sobreOcupar = 1 + ($_REQUEST['sobreocupar'] / 100);
	$sobreOcupar = $_REQUEST['sobreocupar'];
	$corteMinimo = $_REQUEST['corteMinimo'];

	//$dia = 'lunes';
	/*$turno = 'N1';
	$letraTurno = 'N';
	$anio = 2018;
	$cuatrimestre = 1;
	$sobreOcupar = 10;
	$corteMinimo = 6;*/
	
	require 'fuentes/conexion.php';
	
	/**
	 * Asigna las aulas de un turno asignando de a un elemnto del array
	 * y trabajando recursivamente
	 * @param inscriptos array ordenado por dobles turnos, varios dias y cantidad de inscriptos
	 * @param turno str
	 * @param double sobreocupar
	 * @param anio int
	 * @param cuatrimestre int
	 * @param mysqli obj conexion a mysql
	 * @return inscriptos sacando el primero
	 **/
	function asignarAulas($inscriptos, $turno, $sobreOcupar, $anio, $cuatrimestre, $mysqli) {
		//print_r($inscriptos);
		//echo count($inscriptos) . "<br>";
		if (isset($inscriptos[0])) {
		
			$letraTurno = substr($turno, 0, 1);
			
			$aulasAbiertas = calcularAulasAbiertas($sobreOcupar, $mysqli);
			$aulasTodas = $aulasAbiertas;
			
			//print_r($inscriptos[0]);
			
			foreach($inscriptos[0]['dia'] as $dia => $detalles) {
				$aulasTurno = array();
				if (in_array($detalles['turnos'], ['M', 'N', 'T', 'S'])) {
					for ($i = 1; $i <= 2; $i++) {
						$aulasTurno[$i] = calcularAulasDisponibles($dia, $detalles['turnos'] . $i, $sobreOcupar, $anio, $cuatrimestre);
					}
					
					$aulasAbiertas = array_intersect_key($aulasAbiertas, $aulasTurno[1], $aulasTurno[2]);
				} else {
					$aulasTurno = calcularAulasDisponibles($dia, $detalles['turnos'], $sobreOcupar, $anio, $cuatrimestre);
					$aulasAbiertas = array_intersect_key($aulasAbiertas, $aulasTurno);
				}
			}
			$cantidadAulasDisponibles = count($aulasAbiertas);
			//print_r($aulasAbiertas);

			//Aulas disponibles para los dias de esa materia
			$aulasDisponibles = sumarCapacidades($aulasAbiertas, 1);
			
			$entra = false;
			$i = 1;
			
			while (!$entra) {
				
				if ($i >= $cantidadAulasDisponibles) {
					echo "NO HAY LUGAR PARA LA MATERIA {$inscriptos[0]['conjunto']} <br>";
					array_shift($inscriptos);
					asignarAulas($inscriptos, $turno, $sobreOcupar, $anio, $cuatrimestre, $mysqli);
					return;
				}
				$aulasDisponibles = sumarCapacidades($aulasAbiertas, $i);
				//print_r($aulasDisponibles);
				
				foreach ($aulasDisponibles as $capacidad => $aulas) {
					if ($capacidad >= $inscriptos[0]['cantidad']) {
						$entra = true;
						
						$porcentajeOcupado = ($inscriptos[0]['cantidad'] / $capacidad);
						
						$aulasParaOcupar = array();
						foreach (explode(' + ', str_replace('AULA', '', key($aulas))) as $cod) {
							$aulasParaOcupar[$cod] = round($aulasTodas[$cod] * $porcentajeOcupado, 0);
						}
						
						//AJUSTE PARA OCUPAR PROPORCIONALMENTE
						$totalParaAsignar = array_sum($aulasParaOcupar);
						if ($totalParaAsignar != $inscriptos[0]['cantidad']) {
							$aulasParaOcupar[key($aulasParaOcupar)] = $aulasParaOcupar[key($aulasParaOcupar)] - ($totalParaAsignar - $inscriptos[0]['cantidad']);
						}
						break;
					} 
				}
				
				$i++;
			}
			
			//print_r($aulasParaOcupar);
			
			
			
			foreach ($aulasParaOcupar as $aula => $cantidad) {
				foreach ($inscriptos[0]['dia'] as $dia => $datos) {
					
					$comision = calcularComision($datos['conjunto'], $dia, $datos['turnos'], $anio, $cuatrimestre, $mysqli);
					
					print_r($datos);
					echo $datos['conjunto'] . "<br>";
					echo $aula . "<br>";
					echo $cantidad . "<br>";
					echo $dia . "<br>";
					echo $datos['turnos'] . "<br>";
					echo "COMISION - CALCULAR" . $comision . "<br>";
					
					$query = "INSERT INTO asignacion_aulas 
						(aula, materia, cantidad_alumnos, dia, turno, comision, anio, cuatrimestre)
								VALUES ('{$aula}', '{$datos['conjunto']}', 
									{$cantidad}, '{$dia}', '{$datos['turnos']}', '{$comision}', 
									{$anio}, {$cuatrimestre})";
						//echo $query;
						$mysqli->query($query);
						$error = $mysqli->error;
				}
			}
			
			array_shift($inscriptos);
			
			asignarAulas($inscriptos, $turno, $sobreOcupar, $anio, $cuatrimestre, $mysqli);
			
		
		} else {
			return "TERMINADO";
		}
		
		
	}
	
	/**
	 * Calcula la siguiente comisión que se debe abrir
	 * @param str conjunto
	 * @param str dia
	 * @param str turno
	 * @param int anio
	 * @param int cuatrimestre
	 * @param obj mysqli connection
	 * @return comision a abrir
	 **/
	function calcularComision($materia, $dia, $turno, $anio, $cuatrimestre, $mysqli) { //PARA USARSE DENTRO DE LA FUNCIÓN ASIGNAR AULAS QUE YA ASIGNA LAS VARIABLES
		
		$query = "SELECT DISTINCT comision + 0 AS comision
					FROM asignacion_aulas
					WHERE dia = '$dia' AND
						turno = '$turno' AND
						anio = $anio AND
						cuatrimestre = $cuatrimestre AND
						materia = '$materia'";
		$result = $mysqli->query($query);
		$comisionesEncontradas = array();
		while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
			$comisionesEncontradas[] = $row['comision'];
		}
			
		echo $mysqli->error;
		for ($i = 1; $i < 11; $i++) {
			if (!in_array($i, $comisionesEncontradas) ) {
				$comision = $i;
				break;
			}
		}
		
		$result->free();
		return $comision;
	}
	
	
	/**
	 * Combina la capacidad de varias aulas
	 * @param array aulas disponibles
	 * @param int cantidad de aulas a combinar
	 * return array combinaciones posibles de aulas ordenadas por capacidad
	 **/
	function sumarCapacidades($aulas, $cantidadAulas) {
		
		$capacidades = array();
		$capacidadesCombinadas = array();
		
		foreach ($aulas as $cod => $capacidad) {
			$capacidades[$capacidad]['AULA' . $cod] = 'AULA' . $cod;
		}
		
		
		if ($cantidadAulas > 1)  {
			
			for ($i = 1; $i < $cantidadAulas; $i++) {
				foreach ($capacidades as $capacidad => $combinaciones) {
					foreach ($combinaciones as $nombreAulas => $combinacion) {
						foreach ($aulas as $cod => $capacidadAula) {
							//print_r($nombreAulas);
							if (!strstr($nombreAulas, 'AULA' . $cod)) {
								
								$capacidadNueva = $capacidad + $capacidadAula;
								$aulasNuevas = explode(' + ', $nombreAulas . ' + AULA' . $cod);
								sort($aulasNuevas);
								$aulasNuevas = implode(' + ', $aulasNuevas);
								
								$capacidadesCombinadas[$capacidadNueva][$aulasNuevas]['suma'] = $capacidad . ' + ' . $capacidadAula;
								$capacidadesCombinadas[$capacidadNueva][$aulasNuevas]['total'] = $capacidad + $capacidadAula;
								
								
							}
							
						}
						
					}
					
				}
				$capacidades = $capacidadesCombinadas;
				$capacidadesCombinadas = array();
			
			}
			
		}
		
		//print_r($capacidadesCombinadas);
		//echo '<hr>';
		
		ksort($capacidades);
			
		return $capacidades;
		
	}
	
	/**
	 * Devuelve todas las aulas abiertas con la capacidad ajustada a un porcentaje
	 * de sobreocupación
	 * @param int sobreocupar
	 * @param obj conexion a SQL
	 * @return array aulas abiertas
	 **/
	function calcularAulasAbiertas($sobreOcupar, $mysqli) {
		
		$aulasAbiertas = array();
		$query = "SELECT cod, ROUND(capacidad * (1 + {$sobreOcupar} / 100)) AS capacidad
			FROM aulas
			WHERE abierta = 1;";
		$result = $mysqli->query($query);
		while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
			$aulasAbiertas[$row['cod']] = $row['capacidad'];
		}
		
		return $aulasAbiertas;
	}
	
	/**
	 * Calcula las aulas diponibles para un dia y turno
	 * @param str dia
	 * @param str turno
	 * @param int porcentaje de sobreocupacion
	 * @param int anio
	 * @param int cuatrimestre
	 * @return array aulas disponibles con su capacidad
	 **/
	function calcularAulasDisponibles($dia, $turno, $sobreOcupar, $anio, $cuatrimestre) {
		
		require 'fuentes/conexion.php';
		$letraTurno = substr($turno, 0, 1);
		$sobreOcupar = 1 + ($sobreOcupar / 100);
		
		$query = "SELECT  
				a.cod, 
				ROUND(a.capacidad * ({$sobreOcupar})) AS capacidad
			FROM aulas AS a
			LEFT JOIN asignacion_aulas AS aa 
				ON (a.cod = aa.aula AND aa.anio = {$anio} 
					AND aa.cuatrimestre = {$cuatrimestre}
					AND aa.dia = '{$dia}'
					AND (aa.turno = '{$turno}' OR aa.turno = '{$letraTurno}'))
					AND aa.activo = 1 
			WHERE a.activo = 1 AND ISNULL(aa.materia)
			ORDER BY a.cod + 0";
	
		//echo $query;
		$result = $mysqli->query($query);
		echo $mysqli->error;
		
		while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
			$aulas[$row['cod']] = $row['capacidad'];
		}
		//echo "aulas: ";
		//print_r($aulas);
		
		$result->free();
		return $aulas;
		
	}
	//$aulas = calcularAulasDisponibles('miercoles', $turno, $sobreOcupar, $anio, $cuatrimestre);
	//print_r($aulas);
	//$capacidades = sumarCapacidades($aulas, 9);
	//print_r($capacidades);
	
	
	/**
	 * Calcula las aula sdisponibles para todos los días y turnos de la letra
	 * @param str turno
	 * @param int sobreocupar
	 * @param int anio
	 * @param int cuatrimestre
	 * @return array aulas disponibles por día y turno
	 **/
	function calcularAulasDisponiblesTodosLosDias($letraTurno, $sobreOcupar, $anio, $cuatrimestre) {
		$diasSemana = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
		
		$disponibilidad = array();
		foreach ($diasSemana as $dia) {
			foreach ([1, 2] as $num) {
				$turno = $letraTurno . $num;
				$disponibilidad[$dia][$turno] = calcularAulasDisponibles($dia, $turno, $sobreOcupar, $anio, $cuatrimestre);
			}
		}
		
		return $disponibilidad;
	}
	
	//print_r(calcularAulasDisponiblesTodosLosDias('M', 10, 2018, 1));
	
	/**
	 * Calcula los inscriptos
	 * @param str letra turno
	 * @param int anio
	 * @param int cuatrimestre 
	 * @return array inscriptos
	 **/
	function calcularInscriptos($letraTurno, $anio, $cuatrimestre) {
		require 'fuentes/conexion.php';
		$query = "SELECT e.materia AS conjunto, 
					m.nombres,
					t.dia,
					#aa.dia,
					aa.cantidad_alumnos AS cantidad_asignada,
					aa.comision,
					e.cantidad AS cantidad,
					IF(GROUP_CONCAT(DISTINCT t.turno ORDER BY t.turno) LIKE '_1,_2', 
						e.turno, GROUP_CONCAT(DISTINCT t.turno ORDER BY t.turno)
					) AS turnos
			FROM estimacion AS e
			LEFT JOIN vista_materias_por_conjunto AS m
				ON e.materia LIKE CONCAT(m.conjunto, '%')
			LEFT JOIN turnos_con_conjunto AS t
				ON t.materia = e.materia
					AND LEFT(t.turno, 1) = e.turno
					AND t.anio = e.anio
					AND t.cuatrimestre = e.cuatrimestre
			LEFT JOIN asignacion_aulas AS aa
				ON aa.materia = t.materia
					AND aa.dia = t.dia
					AND aa.anio = t.anio
					AND aa.cuatrimestre = t.cuatrimestre
					AND aa.turno = t.turno
			WHERE e.anio = {$anio}
				AND e.cuatrimestre = {$cuatrimestre}
				AND e.turno = '{$letraTurno}'
			GROUP BY e.materia, e.turno, t.dia, aa.comision";
		//echo $query;
		$result = $mysqli->query($query);
		echo $mysqli->error;
		$datosMaterias = array();
		while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
			foreach ($row AS $key => $value) {
				if ($key != 'comision' and $key != 'cantidad_asignada') {
					$datosMaterias[$row['conjunto']]['dia'][$row['dia']][$key] = $value;
					//print_r($datosMaterias[$row['conjunto']]);
				} elseif ($key == 'comision') {
					$datosMaterias[$row['conjunto']]['comisiones'][$value] = $row['cantidad_asignada'];
				}
			}
			$datosMaterias[$row['conjunto']]['cantidad'] = $row['cantidad'];
			$datosMaterias[$row['conjunto']]['doble'] = 0;
			$datosMaterias[$row['conjunto']]['varios_dias'] = 0;
		}
		
		//print_r($datosMaterias);
		
		$inscriptos = array();
		foreach ($datosMaterias as $materia => $row) {
			
			$row['cantidad_asignada'] = 0;
			foreach ($row['comisiones'] as $comision => $cantidad) {
				$row['cantidad_asignada'] += $cantidad;
			}
			if (count($row['dia']) > 1) {
				$row['varios_dias'] = 1;
			}
			
			foreach ($row['dia'] as $dia => $datos) {
				if (in_array($datos['turnos'], ['M', 'N', 'T', 'S'])) {
					$row['doble'] = 1;
					break;
				}
			}
			
			$row['conjunto'] = $materia;
			
			$inscriptos[] = $row;
		}
		
		foreach ($inscriptos as $key => $value) {
			if ($value['cantidad'] <= $value['cantidad_asignada']) {
				unset($inscriptos[$key]);
			} else {
				$inscriptos[$key]['faltaAsignar'] = $value['cantidad'] - $value['cantidad_asignada'];
			}
			
		}
		
		//ORDENAMIENTO
		
		usort($inscriptos, function($a, $b) {
				
			$doble = - ($a['doble'] - $b['doble']);
			$varios_dias = - ($a['varios_dias'] - $b['varios_dias']);
			$faltan_asignar = - ($a['faltaAsignar'] - $b['faltaAsignar']);
			
			if ($doble != 0) {
				$orden = $doble;
			} else if ($varios_dias != 0) {
				$orden = $varios_dias;
			} else {
				$orden = $faltan_asignar;
			}
			return  $orden;
		});
		
		$result->free();
		return $inscriptos;
		
	}
	 
	//$inscriptos = calcularInscriptos('M', 2018, 1);
	//print_r($inscriptos);
	
	
	//sumarCapacidades($aulas, 4);
	//print_r(sumarCapacidades($aulas, 3));
				
	//
	//print_r($inscriptos);
	$inscriptos = calcularInscriptos($letraTurno, $anio, $cuatrimestre);
	print_r($inscriptos);
	asignarAulas($inscriptos, $turno, $sobreOcupar, $anio, $cuatrimestre, $mysqli);
?>
</pre>
