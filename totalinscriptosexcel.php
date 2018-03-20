<!DOCTYPE html>

<html>
	<head>
	</head>
	<body>
	<?php
		//print_r($_REQUEST);
		$periodo = $_REQUEST['periodo'];
		$reporte = $_REQUEST['reporte'];
		$excel = false;
		
		if (isset($_REQUEST['excel'])) {
			$excel = true;
		}
		
		if ($excel) {
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");;
			header( "Content-disposition: attachment; filename={$reporte}{$periodo}.xls" );
		} else {
			require_once './fuentes/meta.html';
			require_once('./fuentes/botonera.php');
			require("./fuentes/panelNav.php");
			
			echo "<div class='formularioLateral'>";
		}
		require './fuentes/conexion.php';
		require './fuentes/constantes.php';
		
		echo "<button class='excel' id='reporteXLS' 
				onclick='location.assign(\"totalinscriptosexcel.php?periodo={$periodo}&reporte={$reporte}&excel=1\")'>
					Reporte en Excel
			</button>";
		
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
				
				echo $query;
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
						LEFT JOIN comisiones_abiertas AS ca
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
					//echo $query;
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
					
				case "cantidad_comisiones_abiertas":
					
					$query = "SELECT t.materia,
								m.nombres,
								COUNT(DISTINCT ca.nombre_comision) AS cantidad_comisiones,
								LEFT(t.turno, 1) AS turno
							FROM turnos_con_conjunto AS t
							LEFT JOIN comisiones_abiertas AS ca
									ON t.materia = CONCAT(ca.materia, IFNULL(ca.observaciones, ''))
								AND t.anio = ca.anio
								AND t.cuatrimestre = ca.cuatrimestre
								AND LEFT(t.turno, 1) = ca.turno
							LEFT JOIN vista_materias_por_conjunto AS m
								ON m.conjunto = ca.materia
							WHERE CONCAT(t.anio, ' - ', t.cuatrimestre) = '2017 - 2'
							GROUP BY t.materia, turno
							ORDER BY t.materia, turno";
					$result = $mysqli->query($query);
					//echo $mysqli->error;
					$datosTabla = array();
					while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
						$datosTabla[$row['materia']][$row['turno']]['cantidad'] = $row['cantidad_comisiones'];
						$datosTabla[$row['materia']]['nombres'] = $row['nombres'];
					}
				?>
					<table border="1">
						<thead>
							<tr>
								<th>Materia</th>
								<th>Nombre Materia</th>
								<th>M</th>
								<th>N</th>
								<th>T</th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach ($datosTabla as $materia => $detalle) {
									echo "<tr>";
										
										//print_r($detalle);
										if (!isset($detalle['N'])) {
											$detalle['N'] = 0;
										}
										if (!isset($detalle['M'])) {
											$detalle['M'] = 0;
										}
										if (!isset($detalle['T'])) {
											$detalle['T'] = 0;
										}
										
										
										
										echo "<td>" . $materia . "</td>";
										echo "<td>" . $detalle['nombres'] . "</td>";
										echo "<td>" . $detalle['N']['cantidad'] . "</td>";
										echo "<td>" . $detalle['M']['cantidad'] . "</td>";
										echo "<td>" . $detalle['T']['cantidad'] . "</td>";
									
									echo "</tr>";
								}	
							?>
						</tbody>
					</table>
					
				<?php
					break;
				case "abiertasVSoferta":
					
					$query = "SELECT t.materia, 
								m.nombres,
								LEFT(t.turno, 1) AS turno,
								GROUP_CONCAT(DISTINCT CONCAT(t.dia, ' ', t.turno) 
												ORDER BY FIELD(t.dia, 'lunes', 'martes', 
													'miércoles', 'jueves', 'viernes', 
													'sábado'), t.turno) AS horario,
								COUNT(DISTINCT ca.nombre_comision) AS cantidad_comisiones,
								GROUP_CONCAT(DISTINCT ca.nombre_comision ORDER BY nombre_comision) AS comisiones,
								GROUP_CONCAT(DISTINCT ca.horario SEPARATOR '/') AS horarios_abiertos
							FROM comisiones_abiertas AS ca
							LEFT JOIN turnos_con_conjunto AS t
								ON CONCAT(ca.materia, IFNULL(ca.observaciones, '')) = t.materia
									AND ca.anio = t.anio AND ca.cuatrimestre = t.cuatrimestre
									AND ca.turno = LEFT(t.turno, 1)
							LEFT JOIN vista_materias_por_conjunto AS m
								ON t.materia LIKE CONCAT(m.conjunto, '%')
							WHERE CONCAT(t.anio, ' - ', t.cuatrimestre) = '{$periodo}'
							GROUP BY t.materia, LEFT(t.turno, 1)
							ORDER BY ca.materia, turno, comisiones
							#HAVING horarios_abiertos LIKE '%/%'
							#HAVING ISNULL(comisiones)";
					$result = $mysqli->query($query);
					//echo $mysqli->error;
					$datosTabla = array();
					while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
						$datosTabla[] = $row;
					}
				?>
					<table border="1">
						<thead>
							<tr>
								<th>Cod</th>
								<th>Materia</th>
								<th>turno</th>
								<th>horario</th>
								<th>Cantidad Comisiones</th>
								<th>Comisiones</th>
								<th>Horarios Abiertos</th>
							</tr>
						</thead>
						<tbody>
							<?php
								
								foreach ($datosTabla as $k => $detalles) {
									echo "<tr>";
										$rojo = "";
										$horario = "";
									foreach($detalles as $col => $detalle) {
										if ($col == 'horario') {
											$detalle = strtr($detalle, $horasTurno);
											$horario = $detalle;
										} 
										if ($col == 'horarios_abiertos' and $detalle != $horario) {
											$rojo = "style='background-color: yellow;'";
										}
											
										
										echo "<td $rojo>" . $detalle . "</td>";
									}
									
									echo "</tr>";
								}	
								
							?>
						</tbody>
					</table>
					
				<?php
					break;
					
				case "ofertaVSabiertas":
					
					$query = "";
					$result = $mysqli->query($query);
					//echo $mysqli->error;
					$datosTabla = array();
					while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
						$datosTabla[$row['materia']][$row['turno']]['cantidad'] = $row['cantidad_comisiones'];
						$datosTabla[$row['materia']]['nombres'] = $row['nombres'];
					}
				?>
					<table border="1">
						<thead>
							<tr>
								<th>Materia</th>
								<th>Nombre Materia</th>
								<th>M</th>
								<th>N</th>
								<th>T</th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach ($datosTabla as $materia => $detalle) {
									echo "<tr>";
										
										//print_r($detalle);
										if (!isset($detalle['N'])) {
											$detalle['N'] = 0;
										}
										if (!isset($detalle['M'])) {
											$detalle['M'] = 0;
										}
										if (!isset($detalle['T'])) {
											$detalle['T'] = 0;
										}
										
										
										
										echo "<td>" . $materia . "</td>";
										echo "<td>" . $detalle['nombres'] . "</td>";
										echo "<td>" . $detalle['N']['cantidad'] . "</td>";
										echo "<td>" . $detalle['M']['cantidad'] . "</td>";
										echo "<td>" . $detalle['T']['cantidad'] . "</td>";
									
									echo "</tr>";
								}	
							?>
						</tbody>
					</table>
					
				<?php
					break;
					
				case "inscriptosVSabiertas":
					
					$query = "SELECT i.conjunto, i.cantidad, i.anio, i.cuatrimestre, i.comision_real,
									IFNULL(ca.observaciones, '') AS letra,
									LEFT(i.comision_real, 1) AS turno,
									m.nombres
								FROM vista_inscriptos_por_conjunto AS i
								LEFT JOIN comisiones_abiertas AS ca
									ON ca.anio = i.anio
										AND ca.cuatrimestre = i.cuatrimestre
										AND ca.nombre_comision = REPLACE(i.comision_real, 'MS', 'S')
										AND ca.materia = i.conjunto
								LEFT JOIN vista_materias_por_conjunto AS m
									ON m.conjunto = i.conjunto
								WHERE CONCAT(i.anio, ' - ', i.cuatrimestre) = '{$periodo}'
									AND ISNULL(ca.materia)
								ORDER BY i.conjunto, i.comision_real";
					$result = $mysqli->query($query);
					echo $mysqli->error;
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
								<th>comision</th>
								<th>Cantidad</th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach ($datosTabla as $detalle) {
									echo "<tr>";
										echo "<td>" . $detalle['conjunto'] . "</td>";
										echo "<td>" . $detalle['nombres'] . "</td>";
										echo "<td>" . $detalle['turno'] . "</td>";
										echo "<td>" . $detalle['comision_real'] . "</td>";
										echo "<td>" . $detalle['cantidad'] . "</td>";
										
									
									echo "</tr>";
								}	
							?>
						</tbody>
					</table>
					
				<?php
					break;
					
				case "abiertasVSinscriptos":
					
					$query = "SELECT ca.materia, ca.anio, ca.cuatrimestre, ca.nombre_comision,
									IFNULL(ca.observaciones, '') AS letra, m.nombres,
									ca.turno
								FROM comisiones_abiertas AS ca
								LEFT JOIN vista_inscriptos_por_conjunto AS i
									ON ca.anio = i.anio
										AND ca.cuatrimestre = i.cuatrimestre
										AND ca.nombre_comision = REPLACE(i.comision_real, 'MS', 'S')
										AND ca.materia = i.conjunto
								LEFT JOIN vista_materias_por_conjunto AS m
									ON m.conjunto = ca.materia
								WHERE CONCAT(ca.anio, ' - ', ca.cuatrimestre) = '{$periodo}'
									AND ISNULL(i.conjunto)
								ORDER BY ca.materia, ca.nombre_comision";
					$result = $mysqli->query($query);
					echo $mysqli->error;
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
								<th>comision</th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach ($datosTabla as $detalle) {
									echo "<tr>";
										echo "<td>" . $detalle['materia'] . "</td>";
										echo "<td>" . $detalle['nombres'] . "</td>";
										echo "<td>" . $detalle['turno'] . "</td>";
										echo "<td>" . $detalle['nombre_comision'] . "</td>";
										
									
									echo "</tr>";
								}	
							?>
						</tbody>
					</table>
					
				<?php
					break;
					
				case "inscriptosVSaulas":
					
					$query = "SELECT i.conjunto, SUM(i.cantidad) AS total_turno,
								REPLACE(CONCAT(LEFT(i.comision_real, 1), IFNULL(ca.observaciones, '')), 'SS', 'S') AS turno_llave,
								m.nombres
							FROM vista_inscriptos_por_conjunto AS i
							LEFT JOIN comisiones_abiertas AS ca
								ON ca.materia = i.conjunto
									AND ca.nombre_comision = i.comision_real
									AND ca.anio = i.anio
									AND ca.cuatrimestre = ca.cuatrimestre
							LEFT JOIN vista_materias_por_conjunto AS m
								ON m.conjunto = i.conjunto

							WHERE CONCAT(i.anio, ' - ', i.cuatrimestre) = '{$periodo}'
							GROUP BY turno_llave, i.conjunto
							ORDER BY i.conjunto, turno_llave";
					$result = $mysqli->query($query);
					echo $mysqli->error;
					$datosTabla = array();
					while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
						$datosTabla[$row['conjunto']][$row['turno_llave']]['inscriptos'] = $row['total_turno'];
						$datosTabla[$row['conjunto']][$row['turno_llave']]['en_aulas'] = 0;
						$datosTabla[$row['conjunto']]['nombre'] = $row['nombres'];
					}
					
					
					$query = "SELECT aa.materia AS prueba, 
									LEFT(aa.turno, 1) AS turno_llave, 
									SUM(aa.cantidad_alumnos) / COUNT(DISTINCT CONCAT(aa.dia, aa.turno)) AS cantidad,
									LEFT(aa.materia, LOCATE(')', aa.materia)) AS conjunto,
									REPLACE(CONCAT(LEFT(aa.turno, 1), 
												SUBSTRING(aa.materia, LOCATE(')', aa.materia) + 1)),
											'SS', 'S'
									) AS letra
									
								FROM asignacion_aulas AS aa
								WHERE aa.anio = 2018
									AND aa.cuatrimestre = 1
								GROUP BY aa.materia, turno_llave";
					$result = $mysqli->query($query);
					echo $mysqli->error;
					while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
						$datosTabla[$row['conjunto']][$row['letra']]['en_aulas'] = $row['cantidad'];
						if (!isset($datosTabla[$row['conjunto']][$row['letra']]['inscriptos'])) {
							$datosTabla[$row['conjunto']][$row['letra']]['inscriptos'] = 0;
						}
					}
					
					//print_r($datosTabla);
					
				?>
					<table border="1">
						<thead>
							<tr>
								<th>Materia</th>
								<th>Nombre Materia</th>
								<th colspan="3">
									<table><tr><td colspan='3'>M</td></tr>
										<tr><td>Insc.</td><td>Aulas</td><td>Dif.</td></tr>
									</table>
								</th>
								<th colspan="3">
									<table><tr><td colspan='3'>M</td></tr>
										<tr><td>Insc.</td><td>Aulas</td><td>Dif.</td></tr>
									</table></th>
								<th colspan="3">
									<table><tr><td colspan='3'>M</td></tr>
										<tr><td>Insc.</td><td>Aulas</td><td>Dif.</td></tr>
									</table></th>
								<th colspan="3">
									<table><tr><td colspan='3'>M</td></tr>
										<tr><td>Insc.</td><td>Aulas</td><td>Dif.</td></tr>
									</table></th>
								
							</tr>
						</thead>
						<tbody>
							<?php
								foreach ($datosTabla as $materia => $turnos) {
									echo "<tr>";
									echo "<td>" . $materia . "</td>";
									echo "<td>" . $turnos['nombre'] . "</td>";
									
									foreach (['M', 'N', 'T', 'S'] as $turno) {
										if (isset($turnos[$turno])) {
											echo "<td>" . $turnos[$turno]['inscriptos'] . "</td>";
											echo "<td>" . (int) $turnos[$turno]['en_aulas'] . "</td>";
											
											
											$diferencia = $turnos[$turno]['inscriptos'] - $turnos[$turno]['en_aulas'];
											
											$resaltado = '';
											if ($diferencia != 0) {
												$resaltado = "style='background-color: red;'";
											}
											echo "<td $resaltado>" . $diferencia . "</td>";
										} else {
											echo "<td></td><td></td><td></td>";
										}
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
