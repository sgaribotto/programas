<!DOCTYPE html>
<html>
	<head>
		
		<title>Reporte asignación de aulas</title>
		<?php 
			require_once('./fuentes/meta.html');
			require_once('fuentes/constantes.php');
			
			function __autoload($class) {
				$classPathAndFileName = "./clases/" . $class . ".class.php";
				require_once($classPathAndFileName);
			}
		?>
		<?php require "./fuentes/jqueryScripts.html"; ?>
		<script src="./fuentes/funciones.js"></script>
		<link rel="stylesheet" type="text/css" href="css/aulificator2015.css">
		
		<style>
			table.tabla-M, table.tabla-N, table.tabla-T {
				margin: auto;
			}
			
			table.interior {
				width: 100%;
				height: 100%;
				margin:0;
				padding:0;
			}
			
			table.interior-1, table.interior-2 {
				width: 100%;
				height: 50%;
				margin: 0;
			}
			
			table {
				border-collapse:collapse;
				font-size: .7em;
			}
			
			tr.simple td{
				height: .9em;
			}
			
			tr.doble td {
				height: 1.8em;
			}
			
			td.interior-1, td.interior-2 {
				padding:0;
				margin:0;
				
			}
			
			td.interior-1 {
				border-bottom:1px solid black;
			}
			
		</style>
		<style media="print">
			#botonera { display:none; }
			
			table {
				border-collapse:collapse;
				font-size: .7em;
			}
			
			a.turnoTitulo {
				display:none;
			}
			
			a.turnoTitulo.selected {
				display:initial;
			}
			
			td.cantidad {
				background-color:red;
			}
			
			tr.aula {
				page-break-inside: avoid;
			}
			
			button#btnExport {
				display:none;
			}
			
			
		</style>
		
		
	</head>
	
	<body>

		<?php
			require_once('./fuentes/botonera.php');
			//require("./fuentes/panelNav.php");
			//require 'fuentes/conexion.php';
		?>
		
		<script>
			$(document).ready(function() {
				
				
				
				var dias = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
				var turnos = {"M":"Mañana", "N":"Noche", "T":"Tarde"};
				var aulas;
				
				$.get('fuentes/aulificatorAJAX.php', {"act": "listadoAulas"}, function(dataAulas) {
					aulas = dataAulas;
				
					$.get('fuentes/aulificatorAJAX.php', {"act": "report"}, function(data) {
						//console.log(data);
						$('body').append('<h2 class="tituloTurno">Requerimientos de aulas y materias del turno </h2>');
						$.each(turnos, function(key, turno) {
							
							
							$tabla = $('<table class="reporte center-text" border="1">\
										<thead class="reporte">\
											<tr class="reporte headers">\
												<th class="reporte headers chico">Aula</th>\
											</tr>\
										</thead>\
										<tbody class="reporte"></tbody>\
									</table>');
							$tabla.addClass('tabla-' + key);
							$('h2.tituloTurno').append('<a href="#" class="turnoTitulo" data-turno="' + key + '">' + turno  + '</a>');
							
							$('body').append($tabla);
							$('a.turnoTitulo:first-child').addClass('selected');
							
						});
						
						$.each(aulas, function(cod, aula) { //Agrega las aulas con sus capacidades
							$('tbody.reporte').append('<tr class="reporte aula aula-' + aula.cod + '" data-capacidad="' + aula.capacidad + '">\
															<td class="reporte aula center-text">' + aula.cod + '<br />\n\
															' + aula.capacidad + 'A</td>\
														</tr>');
						});
						
						$.each(dias, function(num, dia) { //AGREGA LOS HEADERS EN CADA TABLA
							$('tr.reporte.headers').append('<th class="reporte headers grande center-text">' + dia + '</th>');
							$('tr.reporte.aula').append('<td class="reporte aula ' + dia + '"><table class="interior ' + dia + '">\
								<tr class="interior-1">\
									<td class="interior-1">\
										<table class="interior-1">\
											<tr class="doble nombre"><td class="nombre"></td></tr>\
											<tr class="simple" horario><td class="horario"></td></tr>\
											<tr class="simple" cantidad><td class="cantidad"></td></tr>\
											<tr class="simple" comision><td class="comision"></td></tr>\
											<tr class="simple"><td></td></tr>\
										</table>\
									</td>\
								</tr>\
								<tr class="interior-2">\
									<td class="interior-2">\
										<table class="interior-2">\
											<tr class="simple"><td></td></tr>\
											<tr class="doble nombre"><td class="nombre"></td></tr>\
											<tr class="simple" horario><td class="horario"></td></tr>\
											<tr class="simple" cantidad><td class="cantidad"></td></tr>\
											<tr class="simple" comision><td class="comision"></td></tr>\
										</table>\
									</td>\
								</tr>\
								</table></td>');
						
						});
						
						$.each(data, function(key, val) { //RECORRE TODOS LOS TURNOS ASIGNADOS
							
							
							var turno = val.turno;
							var subTurno = turno.substr(1, 1);
							var letraTurno = turno.substr(0, 1);
							var dia = val.dia;
							var aula = val.aula;
							var nombre = val.nombre;
							var cantidad = val.cantidad_alumnos;
							var materia = val.materia;
							var comision = val.comision;
							
							if (dia != 'sábado') {
								
								horario = "";
								switch (turno) {
									case 'M':
										horario = "8:30 a 12:30";
										break;
									case 'M1':
										horario = "8:30 a 10:30";
										break;
									case 'M2':
										horario = "10:30 a 12:30";
										break;
									case 'T':
										horario = "14:00 a 18:00";
										break;
									case 'T1':
										horario = "14:00 a 16:00";
										break;
									case 'T2':
										horario = "16:00 a 18:00";
										break;
									case 'N':
										
										horario = "18:30 a 22:30";
										break;
									case 'N1':
										horario = "18:30 a 20:30";
										break;
									case 'N2':
										horario = "20:30 a 22:30";
										break;
									case 'S':
										horario = "8:30 a 12:30";
										break;
									case 'S1':
										horario = "8:30 a 10:30";
										break;
									case 'S2':
										horario = "10:30 a 12:30";
										break;
								}
								
								
								
								if (subTurno == 1 || subTurno == 2) {
									//$('table.tabla-' + letraTurno + ' tr.aula-' + aula + ' td.' + dia + ' tr.interior-' + subTurno + ' td.nombre').addClass('rojo');
									$('table.tabla-' + letraTurno + ' tr.aula-' + aula + ' td.' + dia + ' table.interior-' + subTurno + ' td.nombre').text(nombre);
									$('table.tabla-' + letraTurno + ' tr.aula-' + aula + ' td.' + dia + ' table.interior-' + subTurno + ' td.horario').text(horario);
									$('table.tabla-' + letraTurno + ' tr.aula-' + aula + ' td.' + dia + ' table.interior-' + subTurno + ' td.cantidad').text(cantidad + ' Alumnos');
									$('table.tabla-' + letraTurno + ' tr.aula-' + aula + ' td.' + dia + ' table.interior-' + subTurno + ' td.comision').text(materia + comision);
									
								
								} else {
									console.log('simple');
									$subtabla = $('<table class="subTabla turno-' + turno + '"></table>');
									$subtabla.append('<tr class="center-text subTabla"><td class="center-text subTabla">' + nombre + '\n</td></tr>');
									$subtabla.append('<tr class="center-text subTabla"><td class="center-text subTabla">' + horario + '\n</td></tr>');
									$subtabla.append('<tr class="center-text subTabla cantidad" data-cantidad="' + cantidad + '"><td class="center-text subTabla cantidad" data-cantidad="' + cantidad + '">' + cantidad + ' Alumnos\n</td></tr>');
									$subtabla.append('<tr class="center-text subTabla"><td class="center-text subTabla">' + materia + comision + '\n</td></tr>');
									$subtabla.append('<tr class="center-text subTabla"><td class="center-text subTabla"><br /></td></tr>');
									$subtabla.wrap('<tr class="interior-unico"></tr>');
									
								}
								
								
								
								
								/*if (!$celda.is(':empty')) {
									$celda.append('<hr class="not-empty" />');
									
								} else if (turno.substr(1,1) == 2) {
									$celda.append('<table class="whaaat"><tr><td><br /></td><tr /><tr><td><br /></td><tr /><tr><td><br /></td><tr /><tr><td><br /></td><tr /></table><hr />');
								}*/
									
								//$celda.append($subtabla);
							} else {
								
								/*horario = "";
								switch (turno) {
									case 'M':
										horario = "8:30 a 12:30";
										break;
									case 'M1':
										horario = "8:30 a 10:30";
										break;
									case 'M2':
										horario = "10:30 a 12:30";
										break;
									case 'T':
										horario = "14:00 a 18:00";
										break;
									case 'T1':
										horario = "14:00 a 16:00";
										break;
									case 'T2':
										horario = "16:00 a 18:00";
										break;
									case 'N':
										
										horario = "8:30 a 12:30";
										break;
									case 'N1':
										horario = "8:30 a 10:30";
										break;
									case 'N2':
										horario = "10:30 a 12:30";
										break;
									case 'S':
										horario = "8:30 a 12:30";
										break;
									case 'S1':
										horario = "8:30 a 10:30";
										break;
									case 'S2':
										horario = "10:30 a 12:30";
										break;
								}
								$subtabla = $('<table class="subTabla turno-' + turno + '"></table>');
								$subtabla.append('<tr class="center-text subTabla"><td class="center-text subTabla">' + nombre + '\n</td></tr>');
								$subtabla.append('<tr class="center-text subTabla"><td class="center-text subTabla">' + horario + ' (' + turno + ')\n</td></tr>');
								$subtabla.append('<tr class="center-text subTabla cantidad" data-cantidad="' + cantidad + '"><td class="center-text subTabla cantidad" data-cantidad="' + cantidad + '">' + cantidad + ' Alumnos\n</td></tr>');
								$subtabla.append('<tr class="center-text subTabla"><td class="center-text subTabla">' + materia + comision + '\n</td></tr>');
								
								
								
								$celda = $('table.tabla-' + 'M' + ' tr.aula-' + aula + ' td.' + dia);
								//console.log($celda);
								
								if (!$celda.is(':empty')) {
									$celda.append('<hr />');
								} else if (turno.substr(1,1) == 2) {
									$celda.append('<br /><table><tr><td><br /></td><tr /><tr><td><br /></td><tr /><tr><td><br /></td><tr /><tr><td><br /></td><tr /></table><hr />');
								}
								
								$celda.append($subtabla);
								
								$subtabla = $('<table class="subTabla turno-' + turno + '></table>');
								$subtabla.append('<tr class="center-text subTabla"><td class="center-text subTabla">' + nombre + '\n</td></tr>');
								$subtabla.append('<tr class="center-text subTabla"><td class="center-text subTabla">' + horario + ' (' + turno + ') \n</td></tr>');
								$subtabla.append('<tr class="center-text subTabla cantidad" data-cantidad="' + cantidad + '"><td class="center-text subTabla cantidad" data-cantidad="' + cantidad + '">' + cantidad + ' Alumnos\n</td></tr>');
								$subtabla.append('<tr class="center-text subTabla"><td class="center-text subTabla">' + materia + comision + '\n</td></tr>');
								
								
								
								$celda = $('table.tabla-' + 'N' + ' tr.aula-' + aula + ' td.' + dia);
								//console.log($celda);
								
								if (!$celda.is(':empty')) {
									$celda.append('<hr />');
								} else if (turno.substr(1,1) == 2) {
									$celda.append('<br /><table><tr><td><br />><br /></td><tr /><tr><td><br /></td><tr /><tr><td><br /></td><tr /></table><hr />');
								}
								
								$celda.append($subtabla);*/
							}
							
						
							
							
						});
						
						var $tables = {"M": $('table.tabla-M'), "N": $('table.tabla-N'), "T": $('table.tabla-T')};
						
						$tables.N.hide();
						$tables.T.hide();
						
						$('a.turnoTitulo').click(function(event) {
							$clicked = $(this);
							turno = $clicked.data('turno');
							event.preventDefault();
							$.each($tables, function(index) {
								$(this).hide();
							});
							$tables[turno].show();
							$('a.turnoTitulo').removeClass('selected');
							$clicked.addClass('selected');
						});
						
						$('td.subTabla.cantidad').each(function(key, val) {
							$this = $(this);
							
							capacidad = $this.closest('tr.aula').data('capacidad');
							
							
							if ($this.data('cantidad') > capacidad) {
								$this.addClass('destacar');
							}
						});
						
						
						
						
					}, 'json');
				}, 'json');
				
				var printingTable = $('table.tabla-M').html();
				//console.log($('table.tabla-M').html());
				
				
			});
		</script>
		<button id="btnExport">Excel</button>
		<div class="reporte">
			
		</div> 
	</body>
</html>
