<!DOCTYPE html>

<html>
	<?php
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");;
		header( "Content-disposition: attachment; filename=total_inscriptos.xls" );
		require './fuentes/conexion.php';
		require './fuentes/constantes.php';
		
		//$periodo = $_REQUEST['periodo'];
		
		$periodo = '2017 - 2';
		
		$query = "SELECT b.materia, b.nombre, b.comision_agrupada,
					GROUP_CONCAT(cantidad ORDER by b.cod SEPARATOR ' + ') AS detalle,
					SUM(cantidad) AS total
				FROM (
					SELECT m.conjunto AS materia, m.cod,
						REPLACE(RIGHT(nombre_comision, LENGTH(nombre_comision) - LENGTH(nombre_comision + 0)), 'MT', 'M') AS comision_agrupada,
									
						GROUP_CONCAT(DISTINCT m.nombre ORDER BY m.cod SEPARATOR '/') AS nombre,
						
						COUNT(DISTINCT i.nro_documento) AS cantidad
					FROM programas.inscriptos AS i
					LEFT JOIN materia AS m
						ON m.cod = i.materia
					WHERE CONCAT(anio_academico, ' - ', periodo_lectivo + 0) = '{$periodo}'
						AND i.estado != 'P'
					GROUP BY m.conjunto, comision_agrupada, m.cod
					ORDER BY materia
				) AS b
				GROUP BY b.materia, b.comision_agrupada";
		
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
				<th>detalle</th>
				<th>total</th>
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
