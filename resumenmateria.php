<link rel="stylesheet" type="text/css" href="./css/general.css">
	<img src="./images/logo_eeyn.png" class="logo-print print-only" Alt="EEYN - UNSAM"/>
	
		<?php
			//print_r($_REQUEST);
			//$_REQUEST['materia'] = 1006;
			
			header('Content-Type: text/html; charset=utf-8');
			require_once 'programas.autoloader.php';
			include './fuentes/constantes.php';
			
			$anio = $ANIO;
			$cuatrimestre = $CUATRIMESTRE;
			
			
			if (isset($_REQUEST['periodo'])) {
				
				$periodo = explode('-', $_REQUEST['periodo']);
				$anio = $periodo[0];
				$cuatrimestre = $periodo[1];
			}
			
			$materia = new clases\Materia($_REQUEST['materia']);
			$equipoDocente = $materia->mostrarEquipoDocente('*', $anio, $cuatrimestre, true);
			$cantidadComisiones = $materia->mostrarCantidadComisiones($anio, $cuatrimestre);
			$comisionesAsignadas = $materia->mostrarResumenAsignacionComisiones($anio, $cuatrimestre);
			$turnosMateria = $materia->mostrarTurnos();
			$inscriptosMateria = $materia->mostrarInscriptos($anio, $cuatrimestre);
			$correlativas = $materia->mostrarCorrelativas();
			$ratios = $materia->mostrarRatioAprobacion($anio, $cuatrimestre);
			$inscriptosPorTurno = $materia->mostrarInscriptosPorTurno($anio, $cuatrimestre);
			$carrera = $materia->datosMateria['cod_carrera'];
			$carreras = $materia->mostrarCarreras();
			$codigosConjunto = $materia->mostrarCodigosConjunto();
			$conjunto = $materia->mostrarConjunto();
			
			/*$inscriptosEstimados = $materia->mostrarEstimacionPreliminar($ANIO, $CUATRIMESTRE, $carrera);
			$segundaEstimacion = $materia->segundaEstimacion($ANIO + 1, $CUATRIMESTRE - 1, $carrera);
			$estimacionPool = $materia->mostrarEstimacionPool($ANIO + 1, $CUATRIMESTRE, $carrera);
			
			$inscriptosPorMateria = array();
			$ratiosAprobacion = array();
			foreach ($codigosConjunto AS $codigo) {
				$materiaDelConjunto = new clases\Materia($codigo);
				$carrera1 = $materiaDelConjunto->datosMateria['cod_carrera'];
				$ratioAprobacionPorCod = $materiaDelConjunto->mostrarInscriptosPorTurnoPorCod($ANIO, $CUATRIMESTRE);
				//echo $carrera1;
				/*$correlativasConjunto = $materiaDelConjunto->mostrarCorrelativas();
				$inscriptosCorr = array();
				foreach ($correlativasConjunto as $codigocorr => $nombre) {
					$correlativa = new clases\Materia($codigocorr);
					$inscriptosCorrelativa[$codigocorr] = $correlativa->mostrarInscriptosPorTurnoPorCod($ANIO,$CUATRIMESTRE, $carrera1);
				}
				//$inscriptosPorCod[$codigo] = $materiaDelConjunto->mostrarInscriptosPorTurnoPorCod($ANIO, $CUATRIMESTRE, $carrera1);
				
				$inscriptosEstimadosPorCod[$codigo] = $materiaDelConjunto->mostrarEstimacionPreliminarPorCod($ANIO, $CUATRIMESTRE, $carrera1);
				
			}*/
			//print_r($inscriptosCorrelativa);
			//echo "Correlativas: ";
			//print_r($correlativas);
			echo "<br />";
		?>
		
		<?php if(!isset($_GET['print'])) { 
			session_start();
			$animationEvent = "true";
		?>
			<a href='resumenmateria.php?print=1&materia=<?php echo $materia->mostrarCod();?>' 
					class='no-print' target='_new'>Imprimir</a>
		<?php } else { 
			require 'fuentes/meta.html';
			$animationEvent = "false";
			
			?>
			<style>
				div.chart {
					width: 720;
				}
			</style>
			<script>$(document).ready(function() { window.print(); });</script>
		<?php } ?>
			
		<div class="">
			<h2 class="formularioLateral tituloMateria">
				<?php 
					echo "<span class='tituloMateria'>{$conjunto}</span>";
					print_r($materia->mostrarNombresConjunto());
				?>
			</h2>
			
			<h4 class='formularioLateral carreras' style="text-decoration:none;">Carreras:</h4>
			<ul class='formularioLateral carreras' >
				<?php
					foreach ($carreras as $carrera) {
						echo "<li class='carreras'>{$carrera}</li>";
					}
				?>
			</ul>
			
			
			<h4 class='formularioLateral responsables' style="text-decoration:none;">Responsable: 
				<span >
				<?php
					$responsables = $materia->mostrarResponsable();
					foreach ($responsables as $nro => $responsable) {
						if ($nro > 0) {
							echo " / ";
						}
						echo $responsable;
					}
 				?>
 				</span>
 			</h4>
 			
 			
 			<h4 class='formularioLateral listadoComsiones'>
				<form method="GET" action="#" id="selectorPeriodo">
					<label for="periodo">Comisiones </label>
					<select name="periodo" class="selectorPeriodo"> 
						<?php 
							require 'conexion.php';
							
							
							$query = "SELECT DISTINCT CONCAT(anio, '-', cuatrimestre) AS periodo
										FROM asignacion_comisiones
										WHERE materia = '{$conjunto}'
										ORDER BY anio, cuatrimestre;";
							
							$result = $mysqli->query($query);
							if ($mysqli->errno) {
								echo "</select>ERROR MYSQL: " . $mysqli->error;
							}
							
							while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
								$selected = "";
								$periodoComparacion = $anio . '-' . $cuatrimestre;
								if ($row['periodo'] == $periodoComparacion) {
									$selected = "selected";
									
								}
								echo "<option value='{$row['periodo']}' {$selected}>{$row['periodo']}</option>";
							}
							
							$mysqli->close();
							
						
						?>
					</select>
				</form>
			</h4>
			<ul class="formularioLateral turnos">
				<?php
					foreach ($comisionesAsignadas as $horario => $comisiones) {
						echo "<li><b>{$horario}</b>: <ul>";
						
						foreach ($comisiones as $detalle) {
							echo "<li>{$detalle['nombre_comision']}: {$detalle['docentes']}</li>";
						}
						
						echo "</ul></li>";
						
					}
						
				 ?>
			</ul>
			
			<h4 class='formularioLateral listadoComsiones' style="text-decoration:underline;">Equipo Docente</h4>
			<table class='aceptarDesignacion'><thead class='aceptarDesignacion'>
						<tr class='plantelActual' style="text-align:left;">
							<th class='aceptarDesignacion' style='width:50%;'>Docente</th>
							
							<th class='aceptarDesignacion' style='width:25%;'>Cargo</th>
							<th class='aceptarDesignacion' style='width:10%;'>Comision</th>
						</tr></thead>
				<tbody class="tablaInfo" style="width:100%;">
				<?php 
				
					
					foreach ($equipoDocente as $key) {
						echo "<tr class='aceptarDesignacion'>
								<td class='aceptarDesignacion'>$key[docente]</td>
								<td class='aceptarDesignacion'>$key[tipoafectacion]</td>
								<td class='aceptarDesignacion'>$key[comision]</td>";
								
								
					} ?>
				</tbody>
			</table>
			
			<hr />
						
			<h3 class="formularioLateral tablaInfo" style="margin:0;">Cuatrimestres anteriores</h3>
					
			<div class="marcoTablaResumenMateria">
				<table class="formularioLateral tablaInfo">
					<thead class="formularioLateral tablaInfo">
						<tr class="formualrioLateral tablaInfo">
							<th class="formularioLateral tablaInfo">Periodo</th>
							<th class="formularioLateral tablaInfo">Inscriptos</th>
							<th class="formularioLateral tablaInfo">Profesores</th>
							<th class="formularioLateral tablaInfo">Auxiliares</th>
							<th class="formularioLateral tablaInfo">Promoción</th>
							<th class="formularioLateral tablaInfo">Aprobados</th>
							<th class="formularioLateral tablaInfo">Reprobados</th>
							<th class="formularioLateral tablaInfo">Ausentes</th>
							
						</tr>
					</thead>
					<tbody class="formularioLateral tablaInfo">
						<?php 
							require "fuentes/conexion.php";
							
							/*$query = "SELECT CONCAT(anio_academico, '-', LEFT(periodo_lectivo, '1') ) as periodo, COUNT(*) as cantidad, 
									COUNT(DISTINCT REPLACE(IF(nombre_comision LIKE '%AE%', NULL, nombre_comision), materia, '') ) as cantidad_comisiones
								FROM inscriptos
								WHERE materia IN {$materia->datosMateria['conjunto']} AND anio_academico >= 2012
								GROUP BY periodo 
								ORDER BY anio_academico DESC, periodo DESC";*/
								
							$query = "SELECT CONCAT(a.anio_academico, '-', a.periodo_lectivo) AS periodo, 
										COUNT(*) AS inscriptos,
										SUM(IF(LEFT(a.resultado, 5) = 'Aprob', 1, 0)) AS aprobados,
										SUM(IF(LEFT(a.resultado, 6) = 'Reprob', 1, 0)) AS reprobados,
										SUM(IF(a.resultado = 'Ausente', 1, 0)) AS ausentes,
										SUM(IF(LEFT(a.resultado, 6) = 'Promoc', 1, 0)) AS promociones
									FROM actas AS a
									WHERE anio_academico >= 2012 AND materia IN {$materia->mostrarConjunto()}
										AND (anio_academico < {$anio} 
											OR (anio_academico = {$anio} 
												AND periodo_lectivo < {$cuatrimestre}))
									GROUP BY anio_academico, periodo_lectivo
									ORDER BY anio_academico, periodo_lectivo ";
									
							$result = $mysqli->query($query);
							//echo $mysqli->error;
							$data = array();
							$dataChart = array();
							while ( $row = $result->fetch_array(MYSQLI_ASSOC) ) {
								/*$data[$row['periodo']]['inscriptos'] = $row['cantidad'];
								$data[$row['periodo']]['comisiones'] = $row['cantidad_comisiones'];*/
								$data[$row['periodo']]['inscriptos'] = $row['inscriptos'];
								$data[$row['periodo']]['aprobados'] = $row['aprobados'];
								$data[$row['periodo']]['reprobados'] = $row['reprobados'];
								$data[$row['periodo']]['ausentes'] = $row['ausentes'];
								$data[$row['periodo']]['promociones'] = $row['promociones'];
								
								$dataChart['promociones'][$row['periodo']] = $row['promociones'];
								$dataChart['aprobados'][$row['periodo']] = $row['aprobados'];
								$dataChart['reprobados'][$row['periodo']] = $row['reprobados'];
								$dataChart['ausentes'][$row['periodo']] = $row['ausentes'];
							}
							
							$tabla = array();
							foreach ($dataChart as $periodo => $calidad) {
								$tabla[$periodo] = "{ name: '" . $periodo . "', data: [";
								foreach ($calidad as $calidad => $cantidad) {
									$tabla[$periodo] .= "{name: '". $calidad . "', y:";
									$tabla[$periodo] .= $cantidad . "}, ";
								}
								$tabla[$periodo] .= "], stacking: 'normal', ";
								
								$tabla[$periodo] .= " }";
							}
							
							$tablaChart = "[" . implode(', ', $tabla) ."]";
							//echo $tablaChart;
							
							/*$TablaDrilldown = array();
							foreach ($periodos as $periodo) {
								$tablaDrilldown[$periodo] = "{ name: '" . $periodo . "', id: '" . $periodo . "', showInLegend: true, data: [";
								foreach ($carreras as $carrera) {
									
									$tablaDrilldown[$periodo] .= "{name: '". $carrera . "', y:";
									
									$cantidad = 0;
									if (isset($cantidadInscriptos[$carrera][$periodo])) {
										$cantidad = $cantidadInscriptos[$carrera][$periodo];
									}
									
									$tablaDrilldown[$periodo] .= $cantidad . "}, ";
									
									
									
									
								}
								$tablaDrilldown[$periodo] .= "], type: 'pie', dataLabels: { 
												enabled:true,
												format: '<b>{point.name}</b>: <b>{point.y}</b>',
												
											}, legend:
												{ 
													enabled:true,
												},
											}";
							}
							$drilldownSeries = "series: [" . implode(', ', $tablaDrilldown) . "]";
							print_r($drilldownSeries);*/
							
							$result->free();
							
							/*$query = "SELECT CONCAT(a.anio, '-', a.cuatrimestre) as periodo, a.tipoafectacion, COUNT(*) as cantidad
										FROM afectacion as a
										WHERE materia IN {$materia->datosMateria['conjunto']}
										
										 AND activo = 1 AND estado NOT LIKE 'Rechazado%'
										GROUP BY tipoafectacion";*/
							$query = "SELECT CONCAT(a.anio, '-', a.cuatrimestre) as periodo, 
										SUM(IF(a.tipoafectacion IN ('titular', 'adjunto', 'asociado'), 1, 0)) AS profesores,
										SUM(IF(a.tipoafectacion NOT IN ('titular', 'adjunto', 'asociado'), 1, 0)) AS auxiliares
										FROM afectacion as a
										WHERE materia IN {$materia->datosMateria['conjunto']}
										
										 AND activo = 1 AND estado NOT LIKE 'Rechazado%'
										 AND a.anio >= 2012
										 AND (anio < {$anio} 
											OR (anio = {$anio} 
												AND cuatrimestre < {$cuatrimestre}))
										GROUP BY periodo
										ORDER BY a.anio DESC, a.cuatrimestre DESC";
										
							$result = $mysqli->query($query);
							
							
							while ( $row = $result->fetch_array(MYSQLI_ASSOC) ) {
								$data[$row['periodo']]['profesores'] = $row['profesores'];
								$data[$row['periodo']]['auxiliares'] = $row['auxiliares'];
							}
							
							krsort($data);
							
							//$cargos = ['inscriptos', 'comisiones', 'Titular', 'Asociado', 'Adjunto', 'JTP', 'Ayudante graduado','Ayudante alumno', 'otro'];
							//print_r($data);
							foreach ( $data as $periodo => $valores ) {
								echo "<tr class='formularioLateral tablaInfo'>";
								echo "<td class='formularioLateral tablaInfo'>$periodo</td>";
								
								$columnas = array(
									'inscriptos',
									'profesores',
									'auxiliares',
									'promociones',
									'aprobados',
									'reprobados',
									'ausentes',
								);
								
								foreach ($columnas as $columna) {
									$cantidad = "";
									if (isset($data[$periodo][$columna])) {
										$cantidad = $data[$periodo][$columna];
									}
									echo "<td class='formularioLateral tablaInfo'>" 
										. $cantidad . "</td>";
								}
								echo "</tr>";
							}
							
							$result->free();
							
							//Datos tabla de cuantas cursadas
							$anioMin = $anio - 5;
							
							$query = "SELECT IF(cantidad > 3, '4+', cantidad) AS recursadas, COUNT(*) AS alumnos
										FROM (
											SELECT a1.nro_documento, COUNT(*) AS cantidad
											FROM actas AS a1
											LEFT JOIN actas AS a2 ON a1.nro_documento = a2.nro_documento 
												AND a1.materia = a2.materia
											WHERE a1.resultado IN ('Aprob', 'Promocion') 
												AND a1.materia IN {$materia->mostrarConjunto()}
												AND a1.anio_academico > {$anioMin}
											GROUP BY nro_documento
										) AS b
										GROUP BY recursadas;";
							$result = $mysqli->query($query);
							
							$dataCuantasCursadas = array();
							while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
								$dataCuantasCursadas[$row['recursadas']] = $row['alumnos'];
							}
							
							$tablaCuantasCursadas = array();
							foreach ($dataCuantasCursadas as $recursadas => $alumnos) {
							
								$tablaCuantasCursadas[$recursadas] = "{name: 'Inscripciones: ". $recursadas . "', y:";
								$tablaCuantasCursadas[$recursadas] .= $alumnos . "} ";
							}
								
							
							$tablaCuantasCursadasChart = "data: [" . implode(', ', $tablaCuantasCursadas) ."]";
							//echo $tablaCuantasCursadasChart;
							$mysqli->close();
						?>
					</tbody>
				</table>
				<div class="chart" id="chart_div_resultados"></div>
				<div class="chart" id="chart_div_cuantasCursadas"></div>
			</div>
			
			
			
		</div>
		
	<script src="./fuentes/funciones.js"></script>
	<script>
		$(document).ready( function() {
			$('select.aceptarDesignacion').change(function() {
				var id = $(this).data('id');
				var estado = $(this).val();
				$.post("./fuentes/AJAX.php?act=cambiarEstadoDesignacion", {"id":id, "estado":estado}, function(data) {
					//console.log(data);
				});
			});
			
			$('#mostrarFormulario').click(function() {
				$('div #formulario').toggle();
			});
			
			$('select.selectorPeriodo').change(function() {
				var periodo = $(this).val();
				
				var cod = $('span.tituloMateria').text();
				
				cod = cod.substr(1, 6);
				//alert(cod);
				//console.log(string);
				cod = parseFloat(cod);
				
				//var cod = 1006;
				
				$('#dialogResumenMateria').empty();
				$('#dialogResumenMateria').load('resumenmateria.php', {"materia":cod, "periodo":periodo});
				//$('#dialogResumenMateria').dialog(dialogOptions).dialog('open');
			});
			
			
		});
	</script>
	<script>
		$(document).ready(function() {
			
			$("#agregarDocente").submit(function(event) {
				event.preventDefault();
				$('.errorValidar').text(''); //RESETEO EL MENSAJE DE ERROR
				dniDocente = $('#dniDocente').val();
				//nombreDocente = $('#nombreDocente').val();
				cargoDocente = $('#cargoDocente').val();
				
				var url = $('h2.tituloMateria').text();
				url = url.substr(1, 6);
				url = parseFloat(url);	
							
				materia = url;
				
				if (cargoDocente != "" && dniDocente != "") {
					$.get("./fuentes/AJAX.php", {"act":"agregarAfectacion", "dni":dniDocente, "tipo":cargoDocente, "materia":materia}, function(data) {
						if (data == 1) {
							console.log(data);
								
							$('#dialogResumenMateria').empty();	
							$('#dialogResumenMateria').load('resumenmateria.php?materia=' + url);
						} else {
							$.get("./fuentes/AJAX.php?act=errorLogging", {"error": "agregarAfectación"}, function(data) {
								alert('Error en la carga del docente, se ha notificado al webmaster. Inténtelo nuevamente más tarde. Si el error perdura, comuníquese a weeyn@unsam.edu.ar');
								//console.log(data);
							});
							
						}
					});
				} else {
					
					/*if (cargoDocente == "") {
						$('.errorValidar').text('Debe elegir un cargo');
					}
					
					if ( dniDocente == "" ) {
						$('.errorValidar').text('Debe elegir un docente');
					}*/
				}
			});
			
		});
	</script>
	
	<!-- GRAFICO HIGHCHARTS RESULTADOS-->
	
	
	<script>
			$(document).ready( function () { 
				var labels = false;
				var options = {
					credits: {
						enabled:false,
					},
					chart: {
						renderTo: 'chart_div_resultados',
						type: 'column',
						zoomType: 'x',
						style: {'font-family': 'Cantarell'},
						events: {
						},
						
					},
					title: {
						text: 'Resultados de las cursadas',
					},
					plotOptions: {
						series: {
							dataLabels: { 
								enabled:true,
							},
							animation: <?php echo $animationEvent; ?>
						},
					},
					xAxis: {
						type: 'category',
						/*categories: <?php echo $categoriasPeriodos; ?>,*/
						title: {text: "Periodo lectivo (año - cuatrimestre)"},
						labels: {rotation: 45}
					},
					
					yAxis: {
						allowDecimals: false,
						//tickInterval: 100,
						title: {text: "Cantidad de inscriptos"},
						stackLabels: {
							enabled: true,
						},
					},
					series: <?php echo $tablaChart; ?>,
					legend: {
						enabled: true,
					},
					 exporting: {
						 
						enabled: true,
						type: 'application/pdf',
						buttons: {
							contextButton: {
								menuItems: [{
									text: 'Ver evolución',
									onclick: function () {
											
										options.plotOptions.stacking = null;
										options.chart.type = 'line';
										
										chart = new Highcharts.Chart(options);
										var charta = $('#chart_div').highcharts(),
											s = charta.series,
											sLen = s.length;
										
										for(var i =0; i < sLen; i++){
											s[i].update({
												stacking: null   
											}, false);   
										}
										
										chart.redraw();
										console.log(chart);
										/*this.exportChart({
											width: 250
										});*/
									}
								}, {
									text: 'Cambiar a columnas',
									onclick: function () {
										options.chart.type = 'column';
										chart = new Highcharts.Chart(options);
										/*this.exportChart({
											width: 250
										});*/
									}
								},{
									text: 'Cambiar a áreas',
									onclick: function () {
										options.chart.type = 'area';
										chart = new Highcharts.Chart(options);
										/*this.exportChart({
											width: 250
										});*/
									}
								},{
									text: 'Mostrar etiquetas',
									onclick: function () {
										//console.log(chart);
										
										labels = !labels;
										for (i = 0; i < chart.series.length; i++) {
											
											chart.series[i].update({
												dataLabels: {
													enabled: labels,
													style: {fontSize: '.8em'}
												}
											});
										}
										
										
										
										//chart = new Highcharts.Chart(options);
										/*this.exportChart({
											width: 250
										});*/
									}
								},{
									text: 'Exportar como PDF',
									
									onclick: function () {
										for (i = 0; i < chart.series.length; i++) {
											
											chart.series[i].update({
												dataLabels: {
													enabled: true,
													style: {fontSize: '.5em'} ,
												}
											});
										}
										chart.exportChart();
										for (i = 0; i < chart.series.length; i++) {
											chart.series[i].update({
													dataLabels: {
														enabled: labels,
														style: {fontSize: '.8em'} ,
													}
												});
											}
									},
									separator: false
								}]
							}
						}
					}
					
				};
				
				Highcharts.setOptions({
					colors: ['#66cc99', '#2d8659', '#ff6666', '#e60000']
				});
				
				var chart = new Highcharts.Chart(options);
				
			});
		</script>
	
	<!-- GRAFICO HIGHCHARTS CUANTAS CURSADAS-->
	
	
	<script>
			$(document).ready( function () { 
				var labels = false;
				var options2 = {
					credits: {
						enabled:false,
					},
					chart: {
						renderTo: 'chart_div_cuantasCursadas',
						type: 'pie',
						style: {'font-family': 'Cantarell'},
						events: {
						},
						
					},
					title: {
						text: '¿Cuántas veces se inscribe un alumno para aprobar la cursada?',
					},
					plotOptions: {
						series: {
							dataLabels: { 
								enabled:true,
								format: '<b>{point.name}</b><br /> {point.y} Alumnos',
							},
							animation: <?php echo $animationEvent; ?>
						},
					},
					
					series: [{<?php echo $tablaCuantasCursadasChart; ?>}],
					legend: {
						enabled: true,
					},
					 exporting: {
						 
						enabled: true,
						type: 'application/pdf',
						buttons: {
							contextButton: {
								menuItems: [{
									text: 'Ver evolución',
									onclick: function () {
											
										options.plotOptions.stacking = null;
										options.chart.type = 'line';
										
										chart = new Highcharts.Chart(options);
										var charta = $('#chart_div').highcharts(),
											s = charta.series,
											sLen = s.length;
										
										for(var i =0; i < sLen; i++){
											s[i].update({
												stacking: null   
											}, false);   
										}
										
										chart.redraw();
										console.log(chart);
										/*this.exportChart({
											width: 250
										});*/
									}
								}, {
									text: 'Cambiar a columnas',
									onclick: function () {
										options.chart.type = 'column';
										chart = new Highcharts.Chart(options);
										/*this.exportChart({
											width: 250
										});*/
									}
								},{
									text: 'Cambiar a áreas',
									onclick: function () {
										options.chart.type = 'area';
										chart = new Highcharts.Chart(options);
										/*this.exportChart({
											width: 250
										});*/
									}
								},{
									text: 'Mostrar etiquetas',
									onclick: function () {
										//console.log(chart);
										
										labels = !labels;
										for (i = 0; i < chart.series.length; i++) {
											
											chart.series[i].update({
												dataLabels: {
													enabled: labels,
													style: {fontSize: '.8em'}
												}
											});
										}
										
										
										
										//chart = new Highcharts.Chart(options);
										/*this.exportChart({
											width: 250
										});*/
									}
								},{
									text: 'Exportar como PDF',
									
									onclick: function () {
										for (i = 0; i < chart.series.length; i++) {
											
											chart.series[i].update({
												dataLabels: {
													enabled: true,
													style: {fontSize: '.5em'} ,
												}
											});
										}
										chart.exportChart();
										for (i = 0; i < chart.series.length; i++) {
											chart.series[i].update({
													dataLabels: {
														enabled: labels,
														style: {fontSize: '.8em'} ,
													}
												});
											}
									},
									separator: false
								}]
							}
						}
					}
					
				};
				
				Highcharts.setOptions({
					colors: ['#66cc99', '#2d8659', '#ff6666', '#e60000']
				});
				
				var chart2 = new Highcharts.Chart(options2);
				
			});
		</script>
	<script>
		
	//COMBOBOX CON BÚSQUEDA
	  (function( $ ) {
		$.widget( "custom.combobox", {
		  _create: function() {
			this.wrapper = $( "<span>" )
			  .addClass( "custom-combobox" )
			  .insertAfter( this.element );
	 
			this.element.hide();
			this._createAutocomplete();
			this._createShowAllButton();
		  },
	 
		  _createAutocomplete: function() {
			var selected = this.element.children( ":selected" ),
			  value = selected.val() ? selected.text() : "";
	 
			this.input = $( "<input>" )
			  .appendTo( this.wrapper )
			  .val( value )
			  .attr( "title", "" )
			  .addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
			  .autocomplete({
				delay: 0,
				minLength: 0,
				source: $.proxy( this, "_source" )
			  })
			  .tooltip({
				tooltipClass: "ui-state-highlight"
			  });
	 
			this._on( this.input, {
			  autocompleteselect: function( event, ui ) {
				ui.item.option.selected = true;
				this._trigger( "select", event, {
				  item: ui.item.option
				});
			  },
	 
			  autocompletechange: "_removeIfInvalid"
			});
		  },
	 
		  _createShowAllButton: function() {
			var input = this.input,
			  wasOpen = false;
	 
			$( "<a>" )
			  .attr( "tabIndex", -1 )
			  .attr( "title", "Show All Items" )
			  .tooltip()
			  .appendTo( this.wrapper )
			  .button({
				icons: {
				  primary: "ui-icon-triangle-1-s"
				},
				text: false
			  })
			  .removeClass( "ui-corner-all" )
			  .addClass( "custom-combobox-toggle ui-corner-right" )
			  .mousedown(function() {
				wasOpen = input.autocomplete( "widget" ).is( ":visible" );
			  })
			  .click(function() {
				input.focus();
	 
				// Close if already visible
				if ( wasOpen ) {
				  return;
				}
	 
				// Pass empty string as value to search for, displaying all results
				input.autocomplete( "search", "" );
			  });
		  },
	 
		  _source: function( request, response ) {
			var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
			response( this.element.children( "option" ).map(function() {
			  var text = $( this ).text();
			  if ( this.value && ( !request.term || matcher.test(text) ) )
				return {
				  label: text,
				  value: text,
				  option: this
				};
			}) );
		  },
	 
		  _removeIfInvalid: function( event, ui ) {
	 
			// Selected an item, nothing to do
			if ( ui.item ) {
			  return;
			}
	 
			// Search for a match (case-insensitive)
			var value = this.input.val(),
			  valueLowerCase = value.toLowerCase(),
			  valid = false;
			this.element.children( "option" ).each(function() {
			  if ( $( this ).text().toLowerCase() === valueLowerCase ) {
				this.selected = valid = true;
				return false;
			  }
			});
	 
			// Found a match, nothing to do
			if ( valid ) {
			  return;
			}
	 
			// Remove invalid value
			this.input
			  .val( "" )
			  .attr( "title", value + " didn't match any item" )
			  .tooltip( "open" );
			this.element.val( "" );
			this._delay(function() {
			  this.input.tooltip( "close" ).attr( "title", "" );
			}, 2500 );
			this.input.autocomplete( "instance" ).term = "";
		  },
	 
		  _destroy: function() {
			this.wrapper.remove();
			this.element.show();
		  }
		});
		$("select.combo").combobox();
	  })( jQuery );
 

  </script>
  <style>
	  .custom-combobox {
		position: relative;
		display: inline-block;
	  }
	  .custom-combobox-toggle {
		position: absolute;
		top: 0;
		bottom: 0;
		margin-left: -1px;
		padding: 0;
	  }
	  .custom-combobox-input {
		margin: 0;
		padding: 5px 10px;
		width:300px;
	  }
	  .resaltar {
		  color:#D4A190;
		  font-weight:bold;
	  }
	  
  </style>

