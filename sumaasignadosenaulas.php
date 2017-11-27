
	<?php
	
		$periodo = $_REQUEST['periodo'];
		
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");;
		header( "Content-disposition: attachment; filename=dist_analitica{$periodo}.xls" );
		require './fuentes/conexion.php';
		require './fuentes/constantes.php';
		
		
		$query = "	SELECT b.materia, 
						c.nombre_materia,
						b.turno,
						GROUP_CONCAT(CONCAT(b.aula, ' (', b.cantidad_alumnos, ' Al)') SEPARATOR ' + ') AS detalle,
						SUM(b.cantidad_alumnos) AS suma,
						c.cantidad,
						(SUM(b.cantidad_alumnos) - c.cantidad) AS diferencia
						
					FROM (
						SELECT DISTINCT aa.materia, 
							LEFT(aa.turno, 1) AS turno, 
							aa.comision, aa.cantidad_alumnos, aa.aula
						FROM asignacion_aulas AS aa
						WHERE CONCAT(aa.anio, ' - ', aa.cuatrimestre) = '{$periodo}'
					) AS b
					#LEFT JOIN vista_materias_por_conjunto AS m
						#ON m.conjunto = b.materia OR b.materia LIKE CONCAT(m.conjunto, '_')
					LEFT JOIN ( SELECT m.conjunto AS materia,
							GROUP_CONCAT(DISTINCT m.nombre ORDER BY m.cod SEPARATOR '/') AS nombre_materia,
							REPLACE(RIGHT(nombre_comision, LENGTH(nombre_comision) - LENGTH(nombre_comision + 0)), 'MT', 'M') AS comision_agrupada,
										
							GROUP_CONCAT(DISTINCT m.nombre ORDER BY m.cod SEPARATOR '/') AS nombre,
							COUNT(DISTINCT i.nro_documento) AS cantidad
						FROM programas.inscriptos AS i
						LEFT JOIN materia AS m
							ON m.cod = i.materia
						WHERE CONCAT(i.anio_academico, ' - ', i.periodo_lectivo + 0) = '{$periodo}'
							AND i.estado != 'P'
						GROUP BY m.conjunto, comision_agrupada
						ORDER BY materia) AS c
						ON b.materia = CONCAT(c.materia, IF(RIGHT(c.comision_agrupada, 1) IN ('M', 'N', 'T'), '', RIGHT(c.comision_agrupada, 1)))
							AND b.turno = LEFT(c.comision_agrupada, 1)
					GROUP BY b.materia, b.turno ";
		
		//echo $query;
		$result = $mysqli->query($query);
		
		if ($mysqli->errno) {
			echo $mysqli->error;
		}
		
		$datosTabla = array();
		while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
			$datosTabla[] = $row;
		}
		
		
		$mysqli->close();
		
	?>
	
	<table>
		<thead>
			<tr>
				<th>Materia</th>
				<th>Nombre Materia</th>
				<th>Comision</th>
				<th>Detalle</th>
				<th>En aulas</th>
				<th>Inscriptos</th>
				<th>Diferencia</th>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach ($datosTabla as $key => $value) {
					echo "<tr>";
					foreach ($value as $k => $v) {
						if ($k = 'materia' and !strpos($v, ', ')) {
							$v = "'" . $v;
						}
						echo "<td>" . mb_convert_encoding($v, 'utf16', 'utf8') . "</td>";
					}
					echo "</tr>";
				}	
			?>
		</tbody>
	</table>
