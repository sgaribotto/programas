<!DOCTYPE html>

<html>
	<?php
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");;
		header( "Content-disposition: attachment; filename=asignacion_comisiones.xls" );
		require './fuentes/conexion.php';
		require './fuentes/constantes.php';
		
		$periodo = $_REQUEST['periodo'];
		
		$query = "SELECT ca.materia,
					ca.nombres,
					ca.nombre_comision,
					ca.horario,
					GROUP_CONCAT(DISTINCT CONCAT(d.apellido, ', ', d.nombres) SEPARATOR ' / ') AS docentes,
					ca.responsable
				FROM programas.vista_comisiones_abiertas_con_responsables AS ca
				LEFT JOIN asignacion_comisiones AS ac
					ON ca.materia = ac.materia  
					AND ca.anio = ac.anio
					AND ca.cuatrimestre = ac.cuatrimestre
					AND ca.nombre_comision = ac.comision OR ca.nombre_comision = CONCAT(ac.comision, 'A')
				LEFT JOIN docente AS d
					ON ac.docente = d.id
				WHERE CONCAT(ca.anio, ' - ', ca.cuatrimestre) = '{$periodo}'
				GROUP BY ca.materia, ca.nombre_comision";
				
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
				<th>Horario</th>
				<th>Docentes</th>
				<th>Responsable</th>
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
<html>
