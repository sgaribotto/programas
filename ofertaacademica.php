<!DOCTYPE html>
<html>
	<head>
		
		<title>Oferta Académica</title>
		<?php 
			require_once('./fuentes/meta.html');
			require 'fuentes/constantes.php';
			
			function __autoload($class) {
				$classPathAndFileName = "./clases/" . $class . ".class.php";
				require_once($classPathAndFileName);
			}
		?>
		
	</head>
	
	<body>

		<?php
			require_once('./fuentes/botonera.php');
			require("./fuentes/panelNav.php");
		?>
		<div class="formularioLateral">
			<h2 class="formularioLateral">Oferta académica</h2>
			<div id="mostrarFormulario">Mostrar filtros</div>
			<div id="copiarOferta">Copiar Oferta Académica</div>
			<a href="#" target="_blank" charset="utf8" id="downloaderOferta" download="oferta.xls" style="display:inline;">
				<div id="descargarXLS">Descargar Excel</div>
			</a>
			
			<div id="formulario" class="desplegable">
				<fieldset class="formularioLateral">
					<form method="post" class="formularioLateral filtros" >
						<label class="formularioLateral" for="carrera">carrera:</label>
						<select class="formularioLateral iconPeriodo filterTrigger" name="carrera" id="carrera"/>
							<option class="formularioLateral" value="">Seleccionar carrera</option>
							<?php
								require "fuentes/conexion.php";
								
								$query = "SELECT c.nombre, c.id FROM carrera AS c ORDER BY c.nombre";
								$result = $mysqli->query($query);
								while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
									echo "<option class='formularioLateral' value='$row[id]'>$row[nombre]</option>";
								}
								
								$result->free();
								$mysqli->close();
							?>
								 
						</select>
						<br />
						<label class="formularioLateral" for="plan">Plan:</label>
						<select class="formularioLateral iconPeriodo filterTrigger" name="plan" id="plan"/>
							<option class="formularioLateral" value="">Seleccionar plan</option>
							<?php
								require "fuentes/conexion.php";
								
								$query = "SELECT GROUP_CONCAT(DISTINCT c.nombre ORDER BY m.carrera) AS nombre, 
											m.plan
											FROM materia AS m
											LEFT JOIN carrera AS c ON m.carrera = c.id
											GROUP BY m.plan";
								$result = $mysqli->query($query);
								while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
									echo "<option class='formularioLateral' value='{$row['plan']}'>
											{$row['plan']} ({$row['nombre']})
										</option>";
								}
								
								$result->free();
								$mysqli->close();
							?>
								 
						</select>
						<br />
						<label class="formularioLateral" for="anio">Periodo:</label>
						<select class="formularioLateral iconPeriodo filterTrigger" name="periodo" id="periodo"/>
							
							<?php
								require "fuentes/conexion.php";
								
								$query = "SELECT DISTINCT CONCAT(anio, ' - ', cuatrimestre) AS periodo
											FROM turnos_con_conjunto
											ORDER BY periodo DESC";
								$result = $mysqli->query($query);
								while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
									echo "<option class='formularioLateral' value='$row[periodo]'>$row[periodo]</option>";
								}
								
								$result->free();
								$mysqli->close();
							?>
								 
						</select>
						<br />
						<label class="formularioLateral" for="turno">Turno:</label>
						<select class="formularioLateral iconPeriodo filterTrigger" name="turno" id="turno"/>
							<option class="formularioLateral" value="">Seleccionar turno</option>
							<option class="formularioLateral" value="M">Mañana</option>
							<option class="formularioLateral" value="T">Tarde</option>
							<option class="formularioLateral" value="N">Noche</option>
								 
						</select>
						<br />
						
						<label class="formularioLateral" for="materia">Materia: </label>
						<input name="materia" class="formularioLateral iconMateria filterTrigger" id="materia" type="text" maxlength="30">
						<br />
						<button type="submit" class="formularioLateral">Actualizar</button>
					</form>
				</fieldset>
			</div>
			<div id="formularioCopiarOferta">
				<fieldset class="formularioLateral">
					<form method="post" class="copiarOferta" action="#" >
						<label class="formularioLateral" for="copiarDe">Copiar periodo:</label>
						<select class="formularioLateral iconPeriodo" name="copiarDe" id="copiarDe"/>
									
							<?php
								require "fuentes/conexion.php";
								
								$query = "SELECT DISTINCT CONCAT(anio, ' - ', cuatrimestre) AS periodo
											FROM turnos_con_conjunto
											ORDER BY periodo DESC";
								$result = $mysqli->query($query);
								while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
									echo "<option class='formularioLateral' value='$row[periodo]'>$row[periodo]</option>";
								}
								
								$result->free();
								$mysqli->close();
							?>
								 
						</select>
						<br />
						<label class="formularioLateral" for="copiarA">Al periodo:</label>
						<select class="formularioLateral iconPeriodo" name="copiarA" id="copiarA"/>
									
							<?php
								$anio = $ANIO;
								$cuatrimestre = $CUATRIMESTRE;
								
								$periodo = $anio . ' - ' . $cuatrimestre;
								echo "<option class='formularioLateral' value='{$periodo}'>{$periodo}</option>";
								for ($i = 1; $i<=2; $i++) {
									if ($cuatrimestre == 1) {
										$cuatrimestre++;
										$periodo = $anio . ' - ' . $cuatrimestre;
										echo "<option class='formularioLateral' value='{$periodo}'>{$periodo}</option>";
									} else {
										$anio++;
										$cuatrimestre--;
										$periodo = $anio . ' - ' . $cuatrimestre;
										echo "<option class='formularioLateral' value='{$periodo}'>{$periodo}</option>";
									}
								}
							?>
								 
						</select>
						<br />
						<button type="submit" class="formularioLateral">Copiar Oferta</button>
					</form>
					<br />
					<form method="post" class="importarOferta" action="./fuentes/importararchivos.php?act=ofertaAcademica" enctype="multipart/form-data">
						<label class="formularioLateral" for="importarA">Al periodo:</label>
						<select class="formularioLateral iconPeriodo" name="importarA" id="importarA"/>
									
							<?php
								require "fuentes/conexion.php";
								
								$query = "SELECT MAX(anio) AS anio_max, MIN(anio) AS anio_min
											FROM turnos_con_conjunto
											ORDER BY anio DESC";
								$result = $mysqli->query($query);
								while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
									$anioMin = $row['anio_min'];
									$anioMax = $row['anio_max'];
									
								}
								for ($anio = $anioMax + 1; $anio >= $anioMin; $anio--) {
									
									for ($cuatrimestre = 1; $cuatrimestre <= 2; $cuatrimestre++) {
										$periodo = $anio . ' - ' . $cuatrimestre;
										echo "<option class='formularioLateral' value='{$periodo}'>{$periodo}</option>";
									}
								}
								
								$result->free();
								$mysqli->close();
							?>
								 
						</select>
						<br />
						<label for="importar">Archivo</label>
						<input type="file" accept="text/txt" name="importar" required>
						<br />
						<button type="submit" class="formularioLateral">Importar Oferta</button>
					</form>
				</fieldset>
			</div>
			
			<div class="dialog resumenMateria" id="dialogResumenMateria"></div>
			<!--<a href="#" target="_blank" charset="utf8" id="downloaderOferta" download="oferta.xls" style="display:inline;">Downloader</a>-->
			<hr>
			
		
			<div id="tablaAceptarDesignacion"></div>
		
	</body>
	<?php require "./fuentes/jqueryScripts.html"; ?>
	<script src="./fuentes/funciones.js"></script>
	
	<script>
		$(document).ready(function() {
			
			$.ajaxSetup({
				contentType: "application/x-www-form-urlencoded;charset=UTF-8"
			});
			var dialogOptions = {
				autoOpen: false,
				width:1000,
				height: 600,
				modal: true,
				appendTo: "#Botonera",
				close: function() {
					$('#mostrarFormulario').off('click');
					$('#mostrarFormulario').click(function() {
						$('div #formulario').slideToggle();
					
					});
				},
					
			};
			
			$('div.dialog').dialog(dialogOptions);
			
			$('#dialogResumenMateria').dialog('option', 'title', 'Resumen de la materia');
			
			var actualizarTabla = function() {
				formValues = $('form.filtros').serialize();
				//console.log(formValues);
				$('#tablaAceptarDesignacion').load("fuentes/AJAX.php?act=tablaOfertaAcademica", formValues, function(data) {
					htmlTest = $('#tablaAceptarDesignacion').html();
					var textToSave = htmlTest;
					
					$('#downloaderOferta').attr('href',  'data:attachment/html, ' + encodeURI((textToSave)))
				});
			} 
			actualizarTabla();
			
			$('#tablaAceptarDesignacion').on('click', 'td.masInfo', function(event) {
				
				var string = event.target.innerHTML;
				string = string.substring(0, 6);
				//console.log(string);
				cod = parseFloat(string);
				$('#dialogResumenMateria').empty();
				
				$('#dialogResumenMateria').load('comisionesabiertas.php', {"materia":cod});
				
				$('#dialogResumenMateria').dialog(dialogOptions).dialog('open');
				
				$('#dialogResumenMateria').on('dialogclose', function() {
					actualizarTabla();
					//alert('close');
				});
				
			});
			
			
			
			$('#mostrarFormulario').click(function() {
				$('div #formulario').slideToggle(function() {
					if ($('div#formulario').is(':visible')) {
						$('#mostrarFormulario').css('backgroundColor', 'black');
						$('#mostrarFormulario').css('color', gris);
					} else {
						$('#mostrarFormulario').css('backgroundColor', gris);
						$('#mostrarFormulario').css('color', 'black');
					}
				});
				$('div #formularioCopiarOferta').slideUp();
				
				var gris = '#f9f9f9';
				
				$('#copiarOferta').css('backgroundColor', gris);
				$('#copiarOferta').css('color', 'black');
			});
			
			$('#downloaderOferta').click(function() {
				var ref = $(this).attr('href');
				//console.log(ref);
			});
			$('#copiarOferta').click(function() {
				
				$('div #formularioCopiarOferta').slideToggle(function() {
					if ($('div#formularioCopiarOferta').is(':visible')) {
						$('#copiarOferta').css('backgroundColor', 'black');
						$('#copiarOferta').css('color', gris);
					} else {
						$('#copiarOferta').css('backgroundColor', gris);
						$('#copiarOferta').css('color', 'black');
					}
				});
				$('div #formulario').slideUp();
				
				var gris = '#f9f9f9';
				
				$('#mostrarFormulario').css('backgroundColor', gris);
				$('#mostrarFormulario').css('color', 'black');
			});
			
			$('#mostrarFormulario').click();
			
			$('#materia').focus();
			
			$('input.filterTrigger').on('keyup blur change', function() {
				val = $(this).val();
				
				if (val.length > 2 ) {
					//alert(val);
					actualizarTabla(); 
				}
				
			});
			
			$('select.filterTrigger').change(function(event) {
				actualizarTabla();
			});
			
			/*function download(data, filename, type) {
				var a = document.createElement("a"),
					file = new Blob([data], {type: type});
				if (window.navigator.msSaveOrOpenBlob) // IE10+
					window.navigator.msSaveOrOpenBlob(file, filename);
				else { // Others
					var url = URL.createObjectURL(file);
					a.href = url;
					a.download = filename;
					document.body.appendChild(a);
					a.click();
					setTimeout(function() {
						document.body.removeChild(a);
						window.URL.revokeObjectURL(url);  
					}, 0); 
				}
			}*/
			
			$('form.copiarOferta').submit(function(event) {
				event.preventDefault();
				var values = $('form.copiarOferta').serialize();
				console.log(values);
				
				var acepta = confirm('¿Desea copiar la oferta del periodo ' +
					$('#copiarDe').val() + ' al periodo ' + $('#copiarA').val() + '?');
				if (acepta) {
					//console.log(values);
					$.post('fuentes/AJAX.php?act=copiarOferta', values, function(data) {
						alert("Se ha copiado la oferta académica y la apertura de comisiones");
						actualizarTabla();
						//location.reload();
					});
				}
				
			});
			
			$('form.filtros').submit(function(event) {
				event.preventDefault();
				//alert('ok');
				actualizarTabla();
			});
			
		});
	</script>
	<style>
	td.materia {
		text.align: center;
	}
	</style>
</html>
