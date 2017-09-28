<?php
	require 'fuentes/conexion.php';
	
	$anio = 2016;
	$cuatrimestre = 1;
	$distribuciones = array();
	
	$query = "SELECT CONCAT(materia, IFNULL(observaciones, '')) AS materia, 
				turno, 
				nombre_comision
			FROM comisiones_abiertas
			WHERE anio = {$anio}
				AND cuatrimestre = {$cuatrimestre}
			ORDER BY materia, turno;";
	$result = $mysqli->query($query);
	
	while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
		$distribuciones[$row['materia']][$row['turno']]['comision_abierta'][] = $row['nombre_comision'];
	}
	
	$result->free();
	
	$query = "SELECT DISTINCT materia,
				comision,
				LEFT(turno, 1) AS turno
			FROM asignacion_aulas
			WHERE anio = {$anio}
				AND cuatrimestre = {$cuatrimestre}
			ORDER BY materia, turno;";
	$result = $mysqli->query($query);
	
	while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
		$distribuciones[$row['materia']][$row['turno']]['comision_aulas'][] = $row['comision'];
	}
	
	//print_r($distribuciones);
	
	echo "<table>";
	echo "<tr>
			<th>Materia</th>
			<th>Turno</th>
			<th>com_ABIERTA</th>
			<th>com_AULAS</th>
		</tr>";
	foreach ($distribuciones as $materia => $turnos) {
		foreach ($turnos as $turno => $tipos) {
			
			$abiertas = 0;
			$aulas = 0;
			if (isset($tipos['comision_abierta'])) {
				$abiertas = $tipos['comision_abierta'];
			}
			if (isset($tipos['comision_aulas'])) {
				$aulas = $tipos['comision_aulas'];
			}
			$max = max(count($abiertas), count($aulas));
			
			for ($i = 0; $i < $max; $i++) {
				echo "<tr><td class='materia'>{$materia}</td>
					<td class='turno'>{$turno}</td>";
				
				$comis = false;
				$aulis = false;
				if (isset($tipos['comision_abierta'][$i])) {
					echo "<td class='com_abierta'>";
					echo $tipos['comision_abierta'][$i];
					echo "</td>";
					$comis = $tipos['comision_abierta'][$i];	
				} else {
					echo "<td></td>";
				}
				
				if (isset($tipos['comision_aulas'][$i])) {
					echo "<td class='com_aulas'>";
					echo $tipos['comision_aulas'][$i];
					echo "</td>";
					$aulis = $tipos['comision_aulas'][$i];	
				} else {
					echo "<td></td>";
				}
					
				
				
				
				if ($comis and $aulis /*and ($comis != $aulis)*/) {
					$query = "UPDATE asignacion_aulas
						SET comision_real = '{$comis}'
						WHERE materia = '{$materia}' AND LEFT(turno, 1) = '{$turno}'
							AND comision = '{$aulis}'
							AND anio = {$anio} AND cuatrimestre = {$cuatrimestre}";
					echo "<td>" . $query . "</td>";
					$mysqli->query($query);
				}
				echo "</tr>";
			}
			
			
		}
	}
			
	echo "</table>";
	
	echo "<script>
		location.assign('aulificator2015.php')
	</script>";
	
	$mysqli->close();
?>
