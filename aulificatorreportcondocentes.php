<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>Reporte asignación de aulas</title>
		<?php 
			//require_once('./fuentes/meta.html');
			require_once('fuentes/constantes.php');
			
			function __autoload($class) {
				$classPathAndFileName = "./clases/" . $class . ".class.php";
				require_once($classPathAndFileName);
			}
		?>
		<?php require "./fuentes/jqueryScripts.html"; ?>
		<!--<script src="./fuentes/funciones.js"></script>
		<link rel="stylesheet" type="text/css" href="css/aulificator2015.css">-->
		
		<style>

			table.reporte {
				width:98%;
				margin:.5%;
				padding:.5%;
			}

			th {
				width:16%;
				font-weight:bold;
				background-color:#DDD;
				border:1px solid black;
			}
			
			
			th.Aula {
				width: 4%;
			}


			h2.tituloTurno {
				text-align:center;
				margin-top:0px;
				page-break-before: always;
			}

			td {
				border: 1px solid black;
				border-bottom: none;
				border-top: none;
				height: .9em;
				text-align: center;
			}
			
			table.tabla-M, table.tabla-N, table.tabla-T {
				margin: auto;
			}
			
			
	
			table {
				border-collapse:collapse;
				font-size: .7em;
			}
			
			

			
		</style>

		
		
	</head>
	
	<body>
		<a href="aulificatorreportExcelcondocentes.php" target="_blank" class="no-print"><button id="btnExport">Excel</button></a>	
		<?php
			//require_once('./fuentes/botonera.php');
			//require("./fuentes/panelNav.php");
			require 'fuentes/conexion.php';
		?>
		
		<?php
		
			//CONSTANTES
			
			$dias = ['Aula', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
			$turnos = array(
							"M" => "Mañana",
							"N" => "Noche", 
							"T" =>"Tarde"
						);
			//Listado Aulas
			$query = "SELECT id, cod, capacidad 
						FROM aulas 
						WHERE activo = 1 
						";
			$result = $mysqli->query($query);
			
			$aulas = array();
			while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				foreach($row as $key => $value) {
					$aulas[$row['cod']][$key] = $value;
				}
			}
		
			// reporte
			$query = "SELECT aa.aula, aa.dia, 
						TRIM(TRAILING 'B' FROM aa.materia) AS materia, REPLACE(LEFT(aa.turno, 1), 'S', 'M') AS turno, aa.turno AS horario,
						IFNULL(aa.comision_real, CONCAT(LEFT(aa.turno,1), 'X')) AS comision, 
						aa.cantidad_alumnos, m.nombre AS nombre,
						IFNULL(GROUP_CONCAT(DISTINCT CONCAT(d.apellido, ', ', d.nombres) SEPARATOR ' <br> '), 'SIN DOCENTES') AS docentes,
						IF(RIGHT(aa.turno, 1) = 1, 1, 
							IF(RIGHT(aa.turno, 1) = 2, 2, 
							'completo')
						) AS tipoHorario
						FROM asignacion_aulas AS aa
						LEFT JOIN materia AS m 
							ON aa.materia = m.conjunto OR aa.materia LIKE CONCAT(m.conjunto, '_')
						LEFT JOIN asignacion_comisiones AS ac
							ON ac.anio = aa.anio AND ac.cuatrimestre = aa.cuatrimestre
								AND aa.materia LIKE CONCAT(ac.materia, '%') AND ac.comision = aa.comision_real
						LEFT JOIN docente AS d
							ON d.id = ac.docente
						WHERE aa.anio = {$ANIO}
						AND aa.cuatrimestre = {$CUATRIMESTRE}
						AND aa.activo = 1
					GROUP BY aa.turno, aa.dia, aa.aula, aa.materia
					ORDER BY aa.turno, aa.dia, aa.aula, aa.materia;";
						
			$result = $mysqli->query($query);
			echo $mysqli->error;
				
			$datosReporte = array();
			foreach ($turnos as $letra => $turno) {
				foreach ($aulas as $cod => $detalle) {
					for ($i = 0; $i < 14; $i++) {
						foreach ($dias as $dia) {
							$datosReporte[$letra][$cod][$i][$dia] = '';
							$datosReporte[$letra][$cod][0]['Aula'] = $cod;
							$datosReporte[$letra][$cod][13]['Aula'] = $detalle['capacidad'] . 'A';
							$datosReporte[$letra][$cod]['partido']['Aula'] = false;
							$datosReporte[$letra][$cod]['partido'][$dia] = false;
							
						}
					}
				}
			}
				
			while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				
				if ($row['tipoHorario'] == 'completo') {
					$datosReporte[$row['turno']][$row['aula']]['partido'][$row['dia']] = false;
					$datosReporte[$row['turno']][$row['aula']][4][$row['dia']] = $row['nombre'];
					$datosReporte[$row['turno']][$row['aula']][5][$row['dia']] = $horasTurno[$row['horario']];
					$datosReporte[$row['turno']][$row['aula']][6][$row['dia']] = $row['cantidad_alumnos'] . " Alumnos";
					$datosReporte[$row['turno']][$row['aula']][7][$row['dia']] = $row['materia'] . $row['comision'];
					$datosReporte[$row['turno']][$row['aula']][8][$row['dia']] = $row['docentes'];

				} elseif ($row['tipoHorario'] == 1) {
					$datosReporte[$row['turno']][$row['aula']]['partido'][$row['dia']] = true;
					$datosReporte[$row['turno']][$row['aula']][1][$row['dia']] = $row['nombre'];
					$datosReporte[$row['turno']][$row['aula']][2][$row['dia']] = $horasTurno[$row['horario']];
					$datosReporte[$row['turno']][$row['aula']][3][$row['dia']] = $row['cantidad_alumnos'] . " Alumnos";
					$datosReporte[$row['turno']][$row['aula']][4][$row['dia']] = $row['materia'] . $row['comision'];
					$datosReporte[$row['turno']][$row['aula']][5][$row['dia']] = $row['docentes'];
				} elseif ($row['tipoHorario'] == 2) {
					$datosReporte[$row['turno']][$row['aula']]['partido'][$row['dia']] = true;
					$datosReporte[$row['turno']][$row['aula']][8][$row['dia']] = $row['nombre'];
					$datosReporte[$row['turno']][$row['aula']][9][$row['dia']] = $horasTurno[$row['horario']];
					$datosReporte[$row['turno']][$row['aula']][10][$row['dia']] = $row['cantidad_alumnos'] . " Alumnos";
					$datosReporte[$row['turno']][$row['aula']][11][$row['dia']] = $row['materia'] . $row['comision'];
					$datosReporte[$row['turno']][$row['aula']][12][$row['dia']] = $row['docentes'];
				}
			}
			
			//print_r($datosReporte);
			
		
		?>
		
		<?php
			
			foreach($turnos as $letra => $turno) {
				echo "<h2 class='tituloTurno'>Requerimientos de aulas y materias del turno {$turno} </h2>";
				echo "<table class='reporte center-text tabla-{$letra}'>
										<thead class='reporte'>
											<tr class='reporte headers'>";
				foreach ($dias as $dia) {
					echo "<th class='{$dia}' style='background-color:gray; border:1px solid black;'>{$dia}</th>";
				}
											
				echo "</tr>
						</thead>
						<tbody class='reporte'>";
						
				foreach ($aulas as $aula => $detalle) {
					for ($i = 0; $i < 14; $i++) {
							echo "<tr class='reporte linea-{$i}'>";
							foreach ($dias as $dia) {
								$partido = "";
								if ($datosReporte[$letra][$aula]['partido'][$dia] and $i == 7) {
									$partido = 'style="border-top: 1px solid black;"';
								}
								
								if ($i == 13) {
									$partido = 'style="border-bottom: 1px solid black;"';
								}
								
								echo "<td class='{$dia}' {$partido}>";
								echo $datosReporte[$letra][$aula][$i][$dia];
								
								echo "</td>";
								
							}
							
							
							echo "</tr>";
						
					}
					

				}
				
				echo "</tbody></table>";
			}
		?>
			
						
					
	</body>
	
</html>
