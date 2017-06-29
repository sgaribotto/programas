﻿<!DOCTYPE html>
<html>
	<head>
		
		<title>Estado Carga de comisiones</title>
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
			<h2 class="formularioLateral">Consultar comisiones</h2>
			<div id="mostrarFormulario">Mostrar filtros</div>
			<div id="descargarXLS">Reporte Excel</div>
			<div id="formulario" class="desplegable">
				<fieldset class="formularioLateral">
					<form method="post" class="formularioLateral filtros" >
						<label class="formularioLateral" for="periodo">Periodo:</label>
						<select class="formularioLateral iconPeriodo filterTrigger" name="periodo" id="periodo"/>
							<?php
								require "fuentes/conexion.php";
								
								$query = "SELECT DISTINCT CONCAT(anio, ' - ', cuatrimestre) AS periodo 
											FROM comisiones_abiertas
											ORDER BY anio, cuatrimestre";
								$result = $mysqli->query($query);
								while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
									echo "<option class='formularioLateral' value='$row[periodo]' selected='selected'>$row[periodo]</option>";
								}
								
								$result->free();
								$mysqli->close();
							?>
								 
						</select>
						<br />
						<label class="formularioLateral" for="cuatrimestre">Cuatrimestre:</label>
						<select class="formularioLateral iconPeriodo filterTrigger" name="cuatrimestre" id="cuatrimestre"/>
							<option class="formularioLateral" value="">Seleccionar cuatrimestre</option>
							<?php
								require "fuentes/conexion.php";
								
								$query = "SELECT DISTINCT IF( cuatrimestre BETWEEN 1 AND 9, cuatrimestre, 'otro') as cuatrimestre 
											FROM materia";
								$result = $mysqli->query($query);
								while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
									echo "<option class='formularioLateral' value='$row[cuatrimestre]'>$row[cuatrimestre] cuatrimestre</option>";
								}
								
								$result->free();
								$mysqli->close();
							?>
								 
						</select>
						<br />
						<label class="formularioLateral" for="carrera">Carrera:</label>
						<select class="formularioLateral iconPeriodo filterTrigger" name="carrera" id="carrera"/>
							<option class="formularioLateral" value="">Seleccionar carrera</option>
							<?php
								require "fuentes/conexion.php";
								
								$query = "SELECT DISTINCT id, nombre
											FROM carrera";
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
						<label class="formularioLateral" for="materia">Materia: </label>
						<input name="materia" class="formularioLateral iconMateria filterTrigger" id="materia" type="text" maxlength="30">
						<br />
					</form>
				</fieldset>
			</div>
			<div class="dialog resumenMateria" id="dialogResumenMateria"></div>
		
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
				$('#tablaAceptarDesignacion').load("fuentes/AJAX.php?act=tablaConsultaComisiones", formValues, function(data) {
				
				});
			} 
			actualizarTabla();
			
			$('form.filtros').submit(function(event) {
				event.preventDefault();
			});
			
			$('#tablaAceptarDesignacion').on('click', 'td.masInfo', function(event) {
				
				var string = event.target.innerHTML;
				string = string.substring(1, 6);
				//console.log(string);
				cod = parseFloat(string);
				$('#dialogResumenMateria').empty();
				
				$('#dialogResumenMateria').load('resumenmateria.php', {"materia":cod});
				
				$('#dialogResumenMateria').dialog(dialogOptions).dialog('open');
			});
			
			
			
			$('#mostrarFormulario').click(function() {
				$('div #formulario').slideToggle();
			});
			$('#mostrarFormulario').click();
			
			$('#materia').focus();
			
			$('input.filterTrigger').on('keyup blur change', function() {
				val = $(this).val();
				
				if (val.length > 0 ) {
					//alert(val);
					actualizarTabla(); 
				}
				
			});
			
			$('select.filterTrigger').change(function(event) {
				actualizarTabla();
			});
			
			$('#descargarXLS').click(function(event) {
				event.preventDefault();
				var periodo = $('#periodo').val();
				//periodo = htmlentities(periodo);
				location.assign('asignacioncomisionesexcel.php?periodo=' + periodo);
			});
			
		});
	</script>
</html>
