<!DOCTYPE html>
<html>
	<head>
		
		<title>Inscriptos para asignar en aulas</title>
		<?php 
			require_once('./fuentes/meta.html');
			
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
			<h2 class="formularioLateral">Inscriptos para asignar en aulas</h2>
			
			<div id="mostrarFiltros">Mostrar Filtros</div>
			<div id="mostrarFormulario">Agregar Materia</div>
			<div id="formulario" class="desplegable">
				<fieldset class="formularioLateral">
					<form method="post" class="formularioLateral" action="fuentes/AJAX.php?act=agregarDatosContacto" id="agregarCurso">
						<label class="formularioLateral" for="codigo">Código del Curso: </label>
						<input type="text" class="formularioLateral iconCodigo" name="codigo" id="codigo" required>
						<br />
						<label class="formularioLateral" for="nombre">Nombre del curso: </label>
						<input type="text" class="formularioLateral iconNombre" name="nombre" id="nombre" required>
						<br />
						<label class="formularioLateral" for="M">Inscriptos Mañana: </label>
						<input type="number" class="formularioLateral iconTurno" name="M" id="M" min="0">
						<br />
						<label class="formularioLateral" for="N">Inscriptos Noche: </label>
						<input type="number" class="formularioLateral iconTurno" name="N" id="N" min="0">
						<br />
						<label class="formularioLateral" for="T">Inscriptos Tarde: </label>
						<input type="number" class="formularioLateral iconTurno" name="T" id="T" min="0">
						<br />
						
						<button type="submit" class="formularioLateral iconGuardar" id="guardarCargarOtro">Guardar</button>
					</form>
				</fieldset>
			</div>
			<div id="filtros" class="desplegable">
				<fieldset class="formularioLateral">
					<form method="post" class="formularioLateral filtros" >
						<label class="formularioLateral" for="cuatrimestre">Periodo:</label>
						<select class="formularioLateral iconPeriodo filterTrigger" name="periodo" id="periodo"/>
							<?php
								require "fuentes/conexion.php";
								
								$query = "SELECT DISTINCT CONCAT(anio, ' - ', cuatrimestre) AS periodo
											FROM estimacion
											ORDER BY anio, cuatrimestre";
								$result = $mysqli->query($query);
								while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
									echo "<option class='formularioLateral' value='$row[periodo]'>$row[periodo]</option>";
								}
								
								$result->free();
								$mysqli->close();
							?>
								 
						</select>
						<br />
						
						<label class="formularioLateral" for="materia">Materia: </label>
						<input name="materia" class="formularioLateral iconMateria filterTrigger" id="materia" type="text" maxlength="30">
						<br />
					</form>
					
					<button id="generarDesdeInscriptos">Generar desde inscriptos</button><p id="mensajeCarga"></p>
				</fieldset>
			</div>
			<div class="dialog resumenMateria" id="dialogResumenMateria"></div>
		
			<hr>
			
		
			<div id="tablaInscriptosParaAsignar"></div>
		
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
			
			$('#dialogResumenMateria').dialog('option', 'title', 'Modificar cantidad de inscriptos');
			
			var actualizarTabla = function() {
				formValues = $('form.filtros').serialize();
				//console.log(formValues);
				$('#tablaInscriptosParaAsignar').load("fuentes/AJAX.php?act=tablaInscriptosParaAsignar", formValues, function(data) {
				
				});
			} 
			actualizarTabla();
			
			$('#tablaInscriptosParaAsignar').on('click', 'td.masInfo', function(event) {
				
				var string = event.target.innerHTML;
				string = string.substring(1, 6);
				var periodo = $('#periodo').val();
				//console.log(string);
				cod = parseFloat(string);
				$('#dialogResumenMateria').empty();
				
				$('#dialogResumenMateria').load('modificarinscriptos.php', {"materia":cod, "periodo":periodo});
				
				$('#dialogResumenMateria').dialog(dialogOptions).dialog('open');
			});
			
			
			
			function togglerButtonColor() {
				
				gris = '#f9f9f9';
				
				if ($('div#formulario').is(':visible')) {
					$('#mostrarFormulario').css('backgroundColor', 'black');
					$('#mostrarFormulario').css('color', gris);
				} else {
					$('#mostrarFormulario').css('backgroundColor', gris);
					$('#mostrarFormulario').css('color', 'black');
				}
				
				if ($('div#filtros').is(':visible')) {
					$('#mostrarFiltros').css('backgroundColor', 'black');
					$('#mostrarFiltros').css('color', gris);
				} else {
					$('#mostrarFiltros').css('backgroundColor', gris);
					$('#mostrarFiltros').css('color', 'black');
				}
			}
			
			
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
				$('div #filtros').slideUp();
				
				gris = '#f9f9f9';
				$('#mostrarFiltros').css('backgroundColor', gris);
					$('#mostrarFiltros').css('color', 'black');
				
				
			});
			
			$('#mostrarFiltros').click(function() {
				$('div #filtros').slideToggle(function() {
					if ($('div#filtros').is(':visible')) {
						$('#mostrarFiltros').css('backgroundColor', 'black');
						$('#mostrarFiltros').css('color', gris);
					} else {
						$('#mostrarFiltros').css('backgroundColor', gris);
						$('#mostrarFiltros').css('color', 'black');
					}
				});
				$('div #formulario').slideUp();
				
				gris = '#f9f9f9';
				
				$('#mostrarFormulario').css('backgroundColor', gris);
				$('#mostrarFormulario').css('color', 'black');
				
				
			});
			$('#mostrarFiltros').click();
			
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
			
			$('#generarDesdeInscriptos').click(function(event) {
				event.preventDefault();
				$('#mensajeCarga').text('cargando, por favor espere...');
				
				var getData = new Object();
				getData.periodo = $('#periodo').val();
				
				console.log(getData);
				
				$.post('./fuentes/AJAX.php?act=generarInscriptos', getData, function(data) {
						//MENSAJE DE ÉXITO
						alert("Se ha actualizado desde las inscripciones para el periodo " + getData.periodo); 
				});
				$('#mensajeCarga').text("");
			});
			
			$('#agregarCurso').submit(function(event) {
				event.preventDefault();
				
				formValues = $(this).serialize();
				
				$.post('./fuentes/AJAX.php?act=agregarCurso', formValues, function(data) {
					if (data.error) {
						alert(data.error);
					}
				});
			});
			
			
		});
	</script>
</html>
