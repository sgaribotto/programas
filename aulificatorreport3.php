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
			
			td {
				border: 1px solid black;
				border-top:none;
				border-bottom:none;
				height: .9em;
				
			}
			
			table.tabla-M, table.tabla-N, table.tabla-T {
				margin: auto;
			}
	
			table {
				border-collapse:collapse;
				font-size: .7em;
			}
			
			
						
			tr.nombre td {
				height: 1.8em;
			}
			
			tr.aula-3.interior-2.linea-blanco-abajo {
				page-break-after:always;
			}
			
			tr.linea-blanco-abajo td {
				border-bottom: 1px solid black;
			}
			
			tr.linea-blanco-abajo.interior-1 td.Aula {
				border-bottom: none;
			}
			
			th {
				width: 16%;
			}
			
			th.Aula {
				width: 4%;
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
				
				
				
				var dias = ['Aula', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
				var turnos = {"M":"Mañana", "N":"Noche", "T":"Tarde"};
				var interiores = ['blanco-arriba', 'nombre', 'horario', 'cantidad', 'comision', 'blanco-abajo'];
				var aulas;
				var html;
				
				$.get('fuentes/aulificatorAJAX.php', {"act": "listadoAulas"}, function(dataAulas) {
					aulas = dataAulas;
				
					$.get('fuentes/aulificatorAJAX.php', {"act": "report"}, function(data) {
						//console.log(data);
						$('body').append('<h2 class="tituloTurno">Requerimientos de aulas y materias del turno </h2>');
						$.each(turnos, function(key, turno) {
							
							
							$tabla = $('<table class="reporte center-text" border="1">\
										<thead class="reporte">\
											<tr class="reporte headers">\
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
							$.each(interiores, function(key, value) {
								
								html = '<tr class="reporte aula aula-' + aula.cod + ' interior-1 linea-' + value + '" data-capacidad="' + aula.capacidad + '">';
								
								$.each(dias, function(num, dia) {
									
									html += '<td class="'+ dia +'"></td>';
									//console.log(dia);
								});	
								html += '</tr>';
								$('tbody.reporte').append(html);					
														
							});
							
							$.each(interiores, function(key, value) {
								
								html = '<tr class="reporte aula aula-' + aula.cod + ' interior-2 linea-' + value + '" data-capacidad="' + aula.capacidad + '">';
								
								$.each(dias, function(num, dia) {
									
									html += '<td class="'+ dia +'"></td>';
									//console.log(dia);
								});	
								html += '</tr>';
								$('tbody.reporte').append(html);					
														
							});
							
							
						});
						
						$.each(dias, function(num, dia) { //AGREGA LOS HEADERS EN CADA TABLA
							$('tr.reporte.headers').append('<th class="reporte headers  center-text ' + dia + '">' + dia + '</th>');
						
						});
						
						$.each(aulas, function(cod, aula) {
							$('tr.interior-1.linea-blanco-arriba.aula-' + aula.cod + ' td.Aula').text(aula.cod);
							$('tr.interior-2.linea-blanco-abajo.aula-' + aula.cod + ' td.Aula').text(aula.capacidad + 'A');
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
									$('table.tabla-' + letraTurno + ' tr.aula-' + aula + '.interior-' + subTurno + '.linea-nombre td.' + dia).text(nombre);
									$('table.tabla-' + letraTurno + ' tr.aula-' + aula + '.interior-' + subTurno + '.linea-horario td.' + dia).text(horario);
									$('table.tabla-' + letraTurno + ' tr.aula-' + aula + '.interior-' + subTurno + '.linea-cantidad td.' + dia).text(cantidad + ' Alumnos');
									$('table.tabla-' + letraTurno + ' tr.aula-' + aula + '.interior-' + subTurno + '.linea-comision td.' + dia).text(materia + comision);
									
								
								} else {
									
									$('table.tabla-' + letraTurno + ' tr.aula-' + aula + '.interior-1.linea-comision td.' + dia).text(nombre);
									$('table.tabla-' + letraTurno + ' tr.aula-' + aula + '.interior-1.linea-blanco-abajo td.' + dia).text(horario);
									$('table.tabla-' + letraTurno + ' tr.aula-' + aula + '.interior-1.linea-blanco-abajo td.' + dia).css('border-bottom', 'none');
									$('table.tabla-' + letraTurno + ' tr.aula-' + aula + '.interior-2.linea-blanco-arriba td.' + dia).text(cantidad + ' Alumnos');
									$('table.tabla-' + letraTurno + ' tr.aula-' + aula + '.interior-2.linea-nombre td.' + dia).text(materia + comision);
									
									
									
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
				
				//var printingTable = $('table.tabla-M').html();
				//console.log($('table.tabla-M').html());
				
				$("#btnExport").click(function (e) {
					var htmlTable = '<table>' + $('table.tabla-M').html() + '</table>' + '<table>' + $('table.tabla-N').html() + '</table>' + '<table>' + $('table.tabla-T').html() + '</table>';
					//console.log(htmlTable);
					window.open('data:text/txt,' + htmlTable , 'Download');
					//e.preventDefault();
				});
			});
		</script>
		<button id="btnExport">Excel</button>
		<div class="reporte">
			
		</div> 
	</body>
</html>
