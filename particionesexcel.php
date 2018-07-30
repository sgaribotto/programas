<!DOCTYPE html>
<style>
	td {
		padding: 5px;
	}
	
</style>
<html>
	<?php
		//header("Content-Type:   application/vnd.ms-excel; charset=utf-8");;
		//header( "Content-disposition: attachment; filename=asignacion_comisiones.xls" );
		require './fuentes/conexion.php';
		require './fuentes/constantes.php';
		
		//$periodo = $_REQUEST['periodo'];
		$anio = 2018;
		$cuatrimestre = 2;
		
		$query = "SELECT aa.materia,
					ca.materia AS conjunto,
					CONCAT(ca.turno, IFNULL(REPLACE(ca.observaciones, 'S', ''), '')) AS observaciones,
					aa.comision_real,
					GROUP_CONCAT(DISTINCT aa.cantidad_alumnos ORDER BY aa.aula SEPARATOR '***') AS cantidades,
					GROUP_CONCAT(DISTINCT CONCAT(aa.aula, ' (', a.capacidad, ') ') ORDER BY FIELD(aa.dia, 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado') 
						SEPARATOR ' / ') AS aulas,
					
					GROUP_CONCAT(DISTINCT aa.dia ORDER BY FIELD(aa.dia, 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado')
						SEPARATOR ' / ') AS dias
				FROM asignacion_aulas AS aa
                LEFT JOIN comisiones_abiertas AS ca
					ON aa.materia LIKE CONCAT(ca.materia, IFNULL(ca.observaciones, ''))
					AND aa.comision_real = ca.nombre_comision
				LEFT JOIN aulas AS a
					ON aa.aula = a.cod
				WHERE aa.anio = {$anio} AND aa.cuatrimestre = {$cuatrimestre}
					AND CONCAT(aa.materia, LEFT(aa.turno, 1)) IN (
						SELECT CONCAT(materia, LEFT(turno, 1)) FROM asignacion_aulas 
						WHERE anio = {$anio} 
						AND cuatrimestre = {$cuatrimestre}
						AND comision != 'A')
					#AND NOT ISNULL(ca.materia)
				GROUP BY aa.materia, aa.comision_real
				
				ORDER BY FIELD(aa.dia, 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'), aa.materia, aa.comision_real
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
									WHERE anio_academico = {$anio} AND periodo_lectivo = {$cuatrimestre}
										AND materia IN {$datos['conjunto']}
										AND REPLACE(nombre_comision, 'MT', 'M') LIKE '%{$turno}'
										
									ORDER BY nombre_alumno
									LIMIT {$particiones['puestos']}, {$datos['cantidad']} 
								) AS b
								GROUP BY materia
								ORDER BY materia;";								
								
					$result = $mysqli->query($query);
					
					if ($mysqli->errno) {
						echo $query;
						echo "<br>";
						echo $mysqli->error;
					}
					while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
						$comisiones[$materia][$turno]['comisiones'][$comision]['materias'][$row['materia']] = $row['cantidad'];
						//$comisiones[$materia][$turno]['comisiones'][$comision]['query'] = $query;
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
	
	<table style='border-collapse:collapse;'>
		<thead>
			<tr>
				<th>Materia</th>
				<th>Orig</th>
				<th>Comisión</th>
				<th>Nueva</th>
				<th>Total</th>
				<th>Aula</th>
				<th>Particiones</th>
				
				
			</tr>
		</thead>
		<tbody>
			<?php
				$bgcolor = 'white';
				
				$materiaTratada = '';
				foreach ($comisiones as $materia => $turnos) {
					if ($materia != $materiaTratada) {
						if ($bgcolor == 'white') {
							$bgcolor = '#C1C1C1';
						} else {
							$bgcolor = 'white';
						}
					}
					
					$materiaTratada = $materia;
					foreach ($turnos as $turno => $particiones) {
						foreach ($particiones['comisiones'] as $comision => $datos) {
							
							
							echo "<tr style='vertical-align: middle; border: 1px solid black; background-color:{$bgcolor};'>";
							
							echo "<td>{$materia}</td>";
							echo "<td>{$turno}</td>";
							
							echo "<td style='text-align: center;'>{$comision}</td>";
							
							if ($turno != $comision) {
								echo "<td style='text-align: center;'>X</td>";
							} else {
								echo "<td></td>";
							}
							
							echo "<td style='text-align: center;'>{$datos['cantidad']}</td>";
							//echo "<td>{$datos['dias']}</td>";
							echo "<td style='text-align: center;'>{$datos['aulas']}</td>";
							//echo "<td><table>";
							echo "<td>";
							foreach ($datos['materias'] as $cod => $cantidadParcial) {
								//echo "<td>({$cod})-->{$cantidadParcial} Al.</td>";
								echo "({$cod})-->{$cantidadParcial} Al.  <br>";
							}
							
							//echo "</table></td>";
							echo "</td>";
								/*if (isset($datos['query'])) {
									//echo "<td>{$datos['query']}</td>";
								}*/
							echo "</tr>";
						}
					}
				}
			?>
		</tbody>
	</table>
<html>
