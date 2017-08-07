<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<style>
			
			
			
				div.cuerpo {
					page-break-after: always;
					font-size: 1.3em;
					line-height: 180%;
					width: 100%;
					text-align: justify;
					
				}
				
				p.cuerpo {
					text-indent: 1cm;
				}
				
				h1, h2, h3 {
					text-align: center;
					margin: 5px;
					padding: 5px;
				}
				
				h1 {
					font-size: 48px;
				}
				
				h2 {
					font-size: 32px;
					text-decoration: underline;
				}
				
				img { 
					margin: auto;
					width: 100%;
					height: 80px;
				}
				
				table.turnos-comisiones {
					page-break-after: always;
					margin: auto;
					border-collapse:collapse;
					border: 1px solid black;
					width: 97%;
				}
				
				tr {
					page-break-inside: avoid;
				}
				
				td, th {
					border: 1px solid black;
					text-align: center;
					font-weight: bold;
				}
				
				th {
					background-color:gray;
					font-size:1.6em;
				}
				
				td {
					font-size: 1.4em;
				}
				
				@media print {
					div.cuerpo {
						
					}
				}
			
			
			
		</style>
		
		<?php //require_once('./fuentes/meta.html'); ?>
		<?php
			
			include 'fuentes/constantes.php';
			require 'conexion.php';
			
			$periodo = $_REQUEST['periodo'];
			
			$dia = $_REQUEST['dia'];
			
			if ($dia != 'Todas') {
				$dia = " AND t.dia = '{$dia}' ";
			} else {
				$dia = " ";
			}
			
			
			
			$nombres_planes = array(
				'1999' => 'VIEJO',
				'2010' => 'VIEJO',
				'2014' => 'NUEVO',
				'2016' => 'NUEVO',
				'CCCP' => 'Contador Público'
			);
			
			$diasTurnos = array(
				'M' => 'Mañana', 
				'T' => 'Tarde', 
				'N' => 'Noche',
				'S' => 'Mañana',
				'Otro' => 'Otro'
			);
			
			$nombres_carreras = array(
				//'EYN-3' => 'Lic. Administración y Gestión Empresarial',
				//'EYN-2 - EYN-3' => 'Lic. Administración y Gestión Empresarial',
				//'AE - EYN-3' => 'Lic. Administración y Gestión Empresarial',
				//'EYN-4' => 'Lic. Economía',
				//'EYN-2 - EYN-4' => 'Lic. Economía',
				//'AE - EYN-4' => 'Lic. Economía',
				'EYN-3' => 'Lic. Administración y Gestión Empresarial - Lic. Economía',
				'EYN-2 - EYN-3' => 'Lic. Administración y Gestión Empresarial - Lic. Economía',
				'AE - EYN-3' => 'Lic. Administración y Gestión Empresarial - Lic. Economía',
				'EYN-4' => 'Lic. Administración y Gestión Empresarial - Lic. Economía',
				'EYN-2 - EYN-4' => 'Lic. Administración y Gestión Empresarial - Lic. Economía',
				'AE - EYN-4' => 'Lic. Administración y Gestión Empresarial - Lic. Economía',
				'EYN-3 - EYN-4' => 'Lic. Administración y Gestión Empresarial - Lic. Economía',
				'AE - EYN-3 - EYN-4' => 'Lic. Administración y Gestión Empresarial - Lic. Economía',
				'EYN-2 - EYN-3 - EYN-4' => 'Lic. Administración y Gestión Empresarial - Lic. Economía',
				'LITUR' => 'Lic. Turismo',
				'GTUR - LITUR' => 'Lic. Turismo',
				'CCCCP' => 'Ciclo Complementario Contador Público',
			);
			
			$query = "SELECT  DISTINCT b.carrera, b.plan, b.dia, b.turno, b.materia_ AS materia, b.nombre_materia,
							b.nombre_comision, b.comision, b.primero, b.ultimo, b.horario, aa.aula, aa.comision_real, b.comision_real
					FROM (
						SELECT GROUP_CONCAT(DISTINCT 
								i.carrera ORDER BY i.carrera SEPARATOR ' - '
							) AS carrera,
							m.plan,
							t.dia,
							ca.turno,
							REPLACE(i.nombre_comision, i.nombre_comision + 0, '') AS comision_real,
							i.nombre_comision + 0 AS materia_,
							i.nombre_comision,
							i.comision,
							MIN(i.nombre_alumno) AS primero,
							MAX(i.nombre_alumno) AS ultimo,
							GROUP_CONCAT(DISTINCT t.turno ORDER BY t.turno SEPARATOR ' - ') AS horario,
							i.anio_academico,
							i.periodo_lectivo,
							CONCAT(ca.materia, IFNULL(ca.observaciones, '')) AS conjunto,
							i.nombre_materia
							
						FROM inscriptos AS i
						LEFT JOIN materia AS m
							ON i.materia = m.cod
						LEFT JOIN comisiones_abiertas AS ca
							ON m.conjunto = ca.materia
								AND ca.nombre_comision = REPLACE(
										REPLACE(REPLACE(i.nombre_comision, i.nombre_comision + 0, ''), 'MTS', 'S')
                                    , 'MT', 'M')
								AND ca.anio = i.anio_academico AND ca.cuatrimestre = i.periodo_lectivo
						LEFT JOIN turnos_con_conjunto AS t
							ON  t.materia = CONCAT(ca.materia, IFNULL(ca.observaciones, ''))
								AND LEFT(t.turno, 1) = ca.turno
								AND t.anio = ca.anio AND t.cuatrimestre = ca.cuatrimestre

						WHERE CONCAT(i.anio_academico, ' - ', periodo_lectivo + 0) = '{$periodo}'
							AND i.estado != 'P' {$dia}
						GROUP BY i.nombre_comision, t.dia
						#ORDER BY  turno, t.dia, m.plan, carrera, materia_, i.nombre_comision
					) AS b
					LEFT JOIN asignacion_aulas AS aa
						ON aa.anio = b.anio_academico AND aa.cuatrimestre = b.periodo_lectivo
							AND aa.comision_real = REPLACE(
										REPLACE(b.comision_real, 'MTS', 'S')
                                    , 'MT', 'M')
							AND aa.materia = b.conjunto
					#WHERE b.turno IN ('M', 'N', 'T') AND b.horario != ''
					ORDER BY FIELD(b.dia, 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'), b.turno, materia, b.horario, b.nombre_comision";
			
			//echo $query;
			$result = $mysqli->query($query);
			echo $mysqli->error;
			$carteles = array();
			
			
			while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$carteles[$row['dia']][$row['turno']][$row['plan']][$nombres_carreras[$row['carrera']]][$row['materia']][$row['nombre_comision']]['primero'] = $row['primero'];
				$carteles[$row['dia']][$row['turno']][$row['plan']][$nombres_carreras[$row['carrera']]][$row['materia']][$row['nombre_comision']]['ultimo'] = $row['ultimo'];
				$carteles[$row['dia']][$row['turno']][$row['plan']][$nombres_carreras[$row['carrera']]][$row['materia']][$row['nombre_comision']]['horario'] = $row['horario'];
				$carteles[$row['dia']][$row['turno']][$row['plan']][$nombres_carreras[$row['carrera']]][$row['materia']][$row['nombre_comision']]['aula'] = $row['aula'];
				$carteles[$row['dia']][$row['turno']][$row['plan']][$nombres_carreras[$row['carrera']]][$row['materia']][$row['nombre_comision']]['nombre_materia'] = $row['nombre_materia'];
				if (!isset($carteles[$row['dia']][$row['turno']][$row['plan']][$nombres_carreras[$row['carrera']]][$row['materia']]['cantidad'])) {
					$carteles[$row['dia']][$row['turno']][$row['plan']][$nombres_carreras[$row['carrera']]][$row['materia']]['cantidad'] = 1;
				} else {
					$carteles[$row['dia']][$row['turno']][$row['plan']][$nombres_carreras[$row['carrera']]][$row['materia']]['cantidad']++;
				}
					
			}
			
			foreach ($carteles as $dia => $turnos) {
				foreach($turnos as $turno => $planes) {
					foreach ($planes as $plan => $carreras) {
						foreach ($carreras as $carrera => $materias) {
							echo "<img src='images/logo.jpg' />";
							echo "<h1>AULAS PLAN " . $nombres_planes["{$plan}"] . "</h1>";
							echo "<h2>DIA " . mb_strtoupper($dia, 'UTF8') . " - TURNO " . mb_strtoupper($diasTurnos[$turno], 'UTF8') . "</h2>";
							echo "<h3>{$carrera}</h3>";
							
							echo "<table class='turnos-comisiones'>";
							echo "<tr>";
							echo "<th style='width:13%'>COM.</th>";
							echo "<th style='width:63%'>MATERIA</th>";
							echo "<th style='width:15%'>HORARIO</th>";
							echo "<th style='width:9%'>AULA</th>";
							echo "</tr>";
							
							foreach ($materias as $materia => $comisiones) {
								foreach ($comisiones as $comision => $detalle) {
									
									if ($comision != 'cantidad') {
										echo "<tr>";
										echo "<td>{$comision}</td>";
										echo "<td style='text-align:left;'>{$detalle['nombre_materia']}";
										if ($comisiones['cantidad'] > 1) {
											echo "<br /><span style='font-weight:normal'>(Desde </span>{$detalle['primero']} <span style='font-weight:normal'>hasta</span> {$detalle['ultimo']})</td>";
										}
										echo "<td>{$horasTurno[$detalle['horario']]}</td>";
										echo "<td>{$detalle['aula']}</td>";
										echo "</tr>";
									}
								}
							}
							echo "</table>";
						}
					}
				}
			}
			
		?>
			
	</body>
</html>

