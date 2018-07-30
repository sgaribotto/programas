<!DOCTYPE html>
<html>
	<head>
		
		<title>Autoevaluación CONEAU</title>
		<?php 
			require_once('./fuentes/meta.html');
			
			require 'programas.autoloader.php';
			
			include './fuentes/constantes.php';
			require 'fuentes/conexion.php';
			//$programa = new clases\Programa($_SESSION['materia'], $_SESSION['id']);
			//$campos = $programa->mostrarCampo($ANIO, $CUATRIMESTRE);
			$materia = $_SESSION['materia'];
			$materia = new clases\Materia($materia);
			$conjunto = $materia->mostrarConjunto();
			//print_r($_SESSION);
			//echo $materia;
			
			$query = "SELECT respuesta, pregunta
						FROM autoevaluacion_coneau
						WHERE materia IN {$conjunto}";
			$result = $mysqli->query($query);
			echo $mysqli->error;
			$respuestas = array();
			foreach (['coneau91', 'coneau92', 'coneau93', 'coneau94'] as $pregunta) {
				$respuestas[$pregunta] = "";
			}
			while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$respuestas[$row['pregunta']] = $row['respuesta'];
			}
			
		?>
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
						SUM(IF(a.resultado = 'Aprobó', 1, 0)) AS aprobados,
						SUM(IF(a.resultado = 'Reprobó', 1, 0)) AS reprobados,
						SUM(IF(a.resultado = 'Ausente', 1, 0)) AS ausentes,
						SUM(IF(a.resultado = 'Promocionó', 1, 0)) AS promociones
					FROM actas AS a
					WHERE anio_academico >= 2012 AND materia IN {$materia->mostrarConjunto()}
						AND (anio_academico < $ANIO 
							OR (anio_academico = $ANIO 
								AND periodo_lectivo < $CUATRIMESTRE))
					GROUP BY anio_academico, periodo_lectivo
					ORDER BY anio_academico DESC, periodo_lectivo DESC";
					
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
			
			$result->free();
			$mysqli->close();
		?>
	</head>
	
	<body>
		<?php
			require_once('./fuentes/botonera.php');
			require("./fuentes/panelNav.php");
		?>
		<div class="formularioLateral">
			<div id="accordion">
				<h3 class="formularioLateral">Analizar y evaluar la suficiencia y adecuación de los 
					ámbitos donde se desarrolla la actividad: aulas, equipamiento didáctico, 
					equipamiento informático, otros; y su disponibilidad para todos los alumnos.
				</h3>
				<div id="formulario">
					<fieldset class="formularioLateral">
						<form method="post" class="formularioLateral" action="procesardatos.php?formulario=autoevaluacion_coneau&pregunta=coneau91" data-pregunta='coneau91'>
							<textarea class="formularioLateral" name="respuesta" placeholder="" style="height:150px;" maxlength="1990";><?php echo $respuestas['coneau91']; ?></textarea>
							<button class="formularioLateral" type="submit">Guardar</button>
						</form>
						
					</fieldset>
				</div>
				<h3 class="formularioLateral">Analizar los datos de la inscripción y promoción de los alumnos. 
					Explicar los datos destacados y enunciar causas probables.
				</h3>
				<div id="formulario">
					
					<fieldset class="formularioLateral">
						<form method="post" class="formularioLateral" action="procesardatos.php?formulario=autoevaluacion_coneau&pregunta=coneau92" data-pregunta='coneau92'>
							<textarea class="formularioLateral" name="respuesta" placeholder="" style="height:150px;" maxlength="1990"><?php echo $respuestas['coneau92']; ?></textarea>
							<button class="formularioLateral" type="submit">Guardar</button>
						</form>
							
							<!--<img src="images/chart_example.png" />-->
							<div class="graph92" id="graph92"></div>
					</fieldset>
				</div>
				<h3 class="formularioLateral">Analizar y evaluar la composición del equipo docente a cargo de la actividad para llevar adelante
					las funciones de docencia, investigación, extensión y vinculación inherentes a los cargos que han sido designados
				</h3>
				<div id="formulario">
					<fieldset class="formularioLateral">
						<form method="post" class="formularioLateral" action="procesardatos.php?formulario=autoevaluacion_coneau&pregunta=coneau93" data-pregunta='coneau93'>
							<textarea class="formularioLateral" name="respuesta" placeholder="" style="height:150px;" maxlength="1990"><?php echo $respuestas['coneau93']; ?></textarea>
							<button class="formularioLateral" type="submit">Guardar</button>
						</form>
						
					</fieldset>
				</div>
				<h3 class="formularioLateral">Describir las acciones, reuniones, comisiones en las que participa el equipo docente para trabajar
					sobre la articulación vertical y horizontal de los contenidos y la formación.
				</h3>
				<div id="formulario">
					<fieldset class="formularioLateral">
						<form method="post" class="formularioLateral" action="procesardatos.php?formulario=autoevaluacion_coneau&pregunta=coneau94" data-pregunta='coneau94'>
							<textarea class="formularioLateral" name="respuesta" placeholder="" style="height:150px;" maxlength="1990"><?php echo $respuestas['coneau94']; ?></textarea>
							<button class="formularioLateral" type="submit">Guardar</button>
						</form>
						
					</fieldset>
				</div>
			</div>
		</div>
		
	</body>
	  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	  <script>
	  $( function() {
		$( "#accordion" ).accordion({
			collapsible: true,
			heightStyle: "content"
		});
	  } );
	  
	  $(document).ready(function() {
		  $('form').submit(function(event) {
			  event.preventDefault();
			  var values = $(this).serialize();
			  var pregunta = $(this).data('pregunta');
			  $.post("procesardatos.php?formulario=autoevaluacion_coneau&pregunta=" + pregunta, 
					values, 
					function(data) {}
			  );
		  });
		  
	  });
	  
	  $('form').keyup(function() {
		  $(this).submit();
	  });
	  
	  $('textarea').on('change', function() {
		  $(this).parent('form').submit();
	  });
	  
	  
	  </script>
	  
	  <!-- GRAFICO HIGHCHARTS -->
	
	
	<script>
		$(document).ready( function () { 
			var labels = false;
			var options = {
				credits: {
					enabled:false,
				},
				chart: {
					renderTo: 'graph92',
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
						}
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
	  <style>
		textarea {
			height: 100px;
			width: 98%;
		}
	  </style>
</html>
