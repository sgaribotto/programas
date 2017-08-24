<!DOCTYPE html>

<html>
	<head>
	</head>
	<body>
	<?php
	
		$periodo = $_REQUEST['periodo'];
		$reporte = $_REQUEST['reporte'];
		
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");;
		header( "Content-disposition: attachment; filename={$reporte}{$periodo}.xls" );
		require './fuentes/conexion.php';
		require './fuentes/constantes.php';
		
		switch ($reporte) {
		
			case "suma":
		
		
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
								if ($k == 'materia' and !strpos($v, ', ')) {
									$v = "'" . $v;
								}
								echo "<td>" . mb_convert_encoding($v, 'utf16', 'utf8') . "</td>";
							}
							echo "</tr>";
						}	
					?>
				</tbody>
			</table>
			
			<?php
				break;
				
				case "comisiones_abiertas":
					
					$query = "SELECT m.conjunto AS materia,
							GROUP_CONCAT(DISTINCT m.nombre ORDER BY m.cod SEPARATOR '/') AS nombre,
							REPLACE(REPLACE(RIGHT(i.nombre_comision, LENGTH(i.nombre_comision) - LENGTH(i.nombre_comision + 0)), 'MT', 'M'), 'MS', 'S') AS comision_agrupada,
							COUNT(DISTINCT i.nro_documento) AS cantidad,
							GROUP_CONCAT(DISTINCT CONCAT(ca.nombre_comision, ' (', ca.horario, ')') SEPARATOR '<br />') AS com_abiertas
						FROM programas.inscriptos AS i
						LEFT JOIN materia AS m
							ON m.cod = i.materia
						RIGHT JOIN comisiones_abiertas AS ca
							ON ca.materia = m.conjunto
								AND ca.anio = i.anio_academico
								AND ca.cuatrimestre = i.periodo_lectivo
								AND ca.turno = LEFT(
									REPLACE(REPLACE(RIGHT(i.nombre_comision, LENGTH(i.nombre_comision) - LENGTH(i.nombre_comision + 0)), 'MT', 'M'), 'MS', 'S')
									, 1)
						WHERE CONCAT(i.anio_academico, ' - ', i.periodo_lectivo + 0) = '{$periodo}'
							AND i.estado != 'P'
						GROUP BY m.conjunto, comision_agrupada
						#HAVING isNULL(com_abiertas)
						ORDER BY materia;";
					$result = $mysqli->query($query);
					//echo $query;
					$datosTabla = array();
					while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
						$datosTabla[] = $row;
					}
				?>
					<table border="1">
						<thead>
							<tr>
								<th>Materia</th>
								<th>Nombre Materia</th>
								<th>Turno</th>
								<th>Inscriptos</th>
								<th>Comisiones Abiertas</th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach ($datosTabla as $key => $value) {
									echo "<tr>";
									foreach ($value as $k => $v) {
										if ($k == 'materia' and !strpos($v, ', ')) {
											$v = "'" . $v;
										}
										echo "<td style='border: 1px solid black; vertical-align:middle;'>" . $v . "</td>";
									}
									echo "</tr>";
								}	
							?>
						</tbody>
					</table>
					
				<?php
					break;
					
					
			}
			
			?>
			
			<?php $mysqli->close(); ?>
	</body>
<html>
