<!DOCTYPE html>

<html>
	<?php
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");;
		header( "Content-disposition: attachment; filename=asignacion_comisiones.xls" );
		require './fuentes/conexion.php';
		require './fuentes/constantes.php';
		
		$periodo = $_REQUEST['periodo'];
		
		
		
		/*$query = "SELECT DISTINCT ca.materia,
					ca.nombres,
					ca.nombre_comision,
					ca.horario,
					IFNULL(au.aula, '') AS aula,
					IFNULL(au.cantidad_alumnos, '') AS cantidad_alumnos,
					CONCAT(d.apellido, ', ', d.nombres) AS docentes,
					
					a.tipoafectacion AS Cargo,
					IF(aula_virtual = 1, 'Sí', 'No') AS aula_virtual,
					ca.responsable
					
				FROM programas.vista_comisiones_abiertas_con_responsables AS ca
				LEFT JOIN asignacion_comisiones AS ac
					ON ca.materia = ac.materia  
					AND ca.anio = ac.anio
					AND ca.cuatrimestre = ac.cuatrimestre
					AND ca.nombre_comision = ac.comision OR ca.nombre_comision = CONCAT(ac.comision, 'A')
				LEFT JOIN docente AS d
					ON ac.docente = d.id
				LEFT JOIN (SELECT a.docente, m.conjunto, a.anio, a.cuatrimestre, a.tipoafectacion
					FROM afectacion AS a
					LEFT JOIN materia AS m
						ON a.materia = m.cod
					WHERE a.activo = 1
					GROUP BY m.conjunto, a.docente, a.anio, a.cuatrimestre, a.tipoafectacion) AS a
					ON a.anio = ca.anio AND a.cuatrimestre = ca.cuatrimestre
						AND a.conjunto = ca.materia AND ac.docente = a.docente
				LEFT JOIN asignacion_aulas AS au
					ON au.materia LIKE CONCAT(ca.materia, '%') AND au.comision_real = ca.nombre_comision
						AND au.anio = ca.anio AND au.cuatrimestre = ca.cuatrimestre
				WHERE CONCAT(ca.anio, ' - ', ca.cuatrimestre) = '{$periodo}'
				GROUP BY ca.materia, ca.nombre_comision, docentes";*/
		
		$query = "SELECT DISTINCT ca.materia, m.nombres, c.cod, ca.turno, 
					ca.nombre_comision, t.dia, tpn.descripcion,
					CONCAT(d.apellido, ', ', d.nombres) AS docente,
					IF(acc.aula_virtual = 1, 'Sí', 'No') AS aula_virtual,
					GROUP_CONCAT(DISTINCT CONCAT(p.apellido, ', ', p.nombres) SEPARATOR ' / ') AS responsables
				FROM comisiones_abiertas AS ca
				LEFT JOIN vista_materias_por_conjunto AS m
					ON ca.materia = m.conjunto

				LEFT JOIN turnos_con_conjunto AS t
					ON t.anio = ca.anio
						AND t.cuatrimestre = ca.cuatrimestre
						AND t.materia = CONCAT(ca.materia, IFNULL(ca.observaciones, ''))
						AND LEFT(t.turno, 1) = ca.turno
				LEFT JOIN asignacion_comisiones_calendario AS acc
					ON acc.anio = ca.anio
						AND acc.cuatrimestre = ca.cuatrimestre
						AND acc.materia = ca.materia
						AND acc.comision = ca.nombre_comision
						AND acc.dia = t.dia
						AND acc.horario = t.turno
				LEFT JOIN turnos_por_nombre AS tpn
					ON tpn.nombre = t.turno
				LEFT JOIN docente AS d ON d.id = acc.docente
				LEFT JOIN carrera AS c ON m.carrera = c.id
				LEFT JOIN (SELECT DISTINCT r.usuario, m.conjunto
							FROM responsable AS r
							LEFT JOIN materia AS m
								ON m.cod = r.materia
							WHERE r.activo = 1) AS r
					ON r.conjunto = ca.materia
				LEFT JOIN personal AS p
					ON r.usuario = p.id
				WHERE CONCAT(ca.anio, ' - ', ca.cuatrimestre) = '{$periodo}'
				GROUP BY ca.materia, ca.nombre_comision, t.dia, t.turno, acc.docente 
				ORDER BY ca.materia, ca.nombre_comision, 
					FIELD(t.dia, 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'),
					t.turno";		
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
				<th>Carrera</th>
				<th>Turno</th>
				<th>Comision</th>
				<th>Día</th>
				<th>Horario</th>
				<th>Docentes</th>
				<th>Aula Virtual</th>
				<th>Responsable</th>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach ($datosTabla as $key => $value) {
					echo "<tr style='vertical-align: middle; border: 1px solid black;'>";
					foreach ($value as $k => $v) {
						if ($k == 'materia' and !strpos($v, ', ')) {
							$v = "'" . $v;
						}
						echo "<td>" . $v . "</td>";
					}
					echo "</tr>";
				}	
			?>
		</tbody>
	</table>
<html>
