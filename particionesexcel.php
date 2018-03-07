<!DOCTYPE html>

<html>
	<?php
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");;
		header( "Content-disposition: attachment; filename=asignacion_comisiones.xls" );
		require './fuentes/conexion.php';
		require './fuentes/constantes.php';
		
		//$periodo = $_REQUEST['periodo'];
		$anio = 2018;
		$cuatrimestre = 1;
		
		$query = "SELECT aa.materia,
					ca.materia AS conjunto,
					CONCAT(ca.turno, IFNULL(ca.observaciones, '')) AS observaciones,
					aa.comision_real,
					GROUP_CONCAT(DISTINCT aa.cantidad_alumnos ORDER BY aa.aula SEPARATOR '***') AS cantidades,
					GROUP_CONCAT(DISTINCT aa.aula ORDER BY aa.dia SEPARATOR ' / ') AS aulas,
					GROUP_CONCAT(DISTINCT aa.dia ORDER BY aa.dia SEPARATOR ' / ') AS dias
				FROM asignacion_aulas AS aa
                LEFT JOIN comisiones_abiertas AS ca
					ON aa.materia LIKE CONCAT(ca.materia, IFNULL(ca.observaciones, ''))
					AND aa.comision_real = ca.nombre_comision
				WHERE aa.anio = {$anio} AND aa.cuatrimestre = {$cuatrimestre}
				GROUP BY aa.materia, aa.comision_real
				ORDER BY aa.materia, aa.comision_real
				#LIMIT 164";
				
		$result = $mysqli->query($query);
		
		if ($mysqli->errno) {
			echo $mysqli->error;
		}
		
		$comisiones = array();
		while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
			$comisiones[$row['materia']][$row['observaciones']]['comisiones'][$row['comision_real']]['cantidad'] = $row['cantidades'];
			$comisiones[$row['materia']][$row['observaciones']]['puestos'] = 0;
			$comisiones[$row['materia']][$row['observaciones']]['comisiones'][$row['comision_real']]['aulas'] = $row['aulas'];
			$comisiones[$row['materia']][$row['observaciones']]['comisiones'][$row['comision_real']]['dias'] = $row['dias'];
			$comisiones[$row['materia']][$row['observaciones']]['comisiones'][$row['comision_real']]['conjunto'] = $row['conjunto'];
			$comisiones[$row['materia']][$row['observaciones']]['comisiones'][$row['comision_real']]['materias'] = array();
		}		
		
		//print_r($comisiones);
		$materiaCorriente = '';
		
		foreach ($comisiones as $materia => $turnos) {
			
			foreach ($turnos as $turno => $particiones) {
				
				//print_r($particiones);
				foreach ($particiones['comisiones'] as $comision => $datos) {
					
					
			
					$query = "SELECT materia, COUNT(DISTINCT nro_documento) AS cantidad FROM (
									SELECT materia, nro_documento, nombre_alumno
									FROM inscriptos
									WHERE anio_academico = 2018 AND periodo_lectivo = 1
										AND materia IN {$datos['conjunto']}
										AND nombre_comision LIKE '%{$turno}'
										
									ORDER BY nombre_alumno
									LIMIT {$particiones['puestos']}, {$datos['cantidad']} 
								) AS b
								GROUP BY materia
								ORDER BY materia;";								
								
					$result = $mysqli->query($query);
					
					if ($mysqli->errno) {
						echo $mysqli->error;
					}
					while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
						$comisiones[$materia][$turno]['comisiones'][$comision]['materias'][$row['materia']] = $row['cantidad'];
						$comisiones[$materia][$turno]['comisiones'][$comision]['query'] = $query;
					}
					
					$particiones['puestos'] += $datos['cantidad'];
					
				}
				
			}
			
			//$materiaCorriente = $value['materia'] . $value['observaciones'];
			//$inicial += $value['cantidades'];
		}
		//echo '<hr>';
		//print_r($comisiones);
		$mysqli->close();
		
	?>
	
	<table>
		<thead>
			<tr>
				<th>Materia</th>
				<th>Sale de</th>
				<th>Comisión</th>
				
				<th>Total</th>
				<th>dias</th>
				<th>Aula</th>
				<th>Particiones</th>
				<th>Query</th>
				
			</tr>
		</thead>
		<tbody>
			<?php
				foreach ($comisiones as $materia => $turnos) {
			
					foreach ($turnos as $turno => $particiones) {
						foreach ($particiones['comisiones'] as $comision => $datos) {
							echo "<tr style='vertical-align: middle; border: 1px solid black;'>";
							
							echo "<td>{$materia}</td>";
							echo "<td>{$turno}</td>";
							echo "<td>{$comision}</td>";
							echo "<td>{$datos['cantidad']}</td>";
							echo "<td>{$datos['dias']}</td>";
							echo "<td>{$datos['aulas']}</td>";
							echo "<td><table>";
							foreach ($datos['materias'] as $cod => $cantidadParcial) {
								echo "<tr><td>{$cod}-->{$cantidadParcial}</td></tr>";
							}
							echo "</table></td>";
								if (isset($datos['query'])) {
									//echo "<td>{$datos['query']}</td>";
								}
							echo "</tr>";
						}
					}
				}
			?>
		</tbody>
	</table>
<html>
