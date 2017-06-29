<!DOCTYPE html>
<html>
	<head>
		
		<title>Correlativas</title>
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
			<h2 class="formularioLateral">Materias</h2>
			<!--<div id="mostrarFormulario">Mostrar Formulario</div>-->
			<div id="formulario1">
				<fieldset class="formularioLateral">
					
					
					<form method="post" class="formularioLateral" action="#" id="agregarConjunto">
						<label class="formularioLateral" for="cod">Código:</label>
						<input type="text" class="formularioLateral iconCod" name="cod" required="required" id="cod" maxlength="10"/>
						<hr />
						<label class="formularioLateral" for="requisito">Correlativa:</label>
						<input type="text" class="formularioLateral iconCod" name="requisito" required="required" id="agregar" maxlength="10"/>
						<br />
						<label class="formularioLateral" for="tipo">Tipo de correlatividad:</label>
						<select class="formularioLateral" name="tipo">
							<option class="formularioLateral" value="Total" selected="selected">Total</option>
							<option class="formularioLateral" value="Cursada">Cursada</option>
						</select>
						<button type="submit" class="formularioLateral iconAgregar" id="guardarCargarOtro">Agregar</button>
						<div id="materiasUnidas" class="materiasUnidas">
							<h3>Materias agrupadas</h3>
							<table class="materiasUnidas" style="width:100%;">
								<thead>
									<th style="width:5%;">Eliminar</th>
									<th style="width:15%;">Tipo</th>
									<th style="width:20%;">Código</th>
									<th style="width:60%;">Nombre</th>
									
									<th></th>
								</thead>
								<tbody></tbody>
							</table>
						</div>
						
						
					</form>
				</fieldset>
			</div>
		</div>
		
	</body>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
	<script src="./fuentes/funciones.js"></script>
	
	<script>
		$(document).ready(function() {
			
			
			
			$("#agregarConjunto").submit(function(event) { //HACERLO CON FORM SERIALIZE
				event.preventDefault();
				$form = $(this);
				values = $form.serialize();
				//values.act = 'agregarConjunto';

				$.post("./fuentes/AJAX.php?act=agregarCorrelativa", values, function(data) {
					data = eval('(' + data + ')');
					if (data.exito != 1) {
						alert(data.error);
						$('#agregar').val('').focus();
					} else {
						$('#cod').change();
					}
					
				});
				
			});
			
			
			$('#cod').change(function() {
				$('#materiasUnidas table.materiasUnidas tbody').empty();
				
				var cod = $('#cod').val();
				//alert(cod);	
				$.post("./fuentes/AJAX.php?act=buscarCorrelativa", {'cod':cod}, function(data) {
					console.log(data);
					data = eval( "(" + data + ")");
					
					if (data.error) {
						alert(data.error);
						$('#cod').val('').focus();
					} else {
						//console.log(data);
						$.each(data, function(key, val) {
							//console.log(val.cod);
							html = "<tr class='materiasUnidas'>";
							html += "<td><button class='botonEliminar' type='button' data-requisito='" + val.requisito + "' data-tipo='" + val.tipo + "'>X</button></td>";
							html += "<td>" + val.tipo + " </td><td> " + val.requisito + " </td><td> " + val.nombre + "</td>";
							
							html += "</tr>";
							//alert(html);
							$('#materiasUnidas table.materiasUnidas tbody').append(html);
						});
						
						$('.botonEliminar').click(function() {
							//var cod = $(this).data('cod');
							var requisito =$(this).data('requisito');
							var tipo = $(this).data('tipo');
							$.post("./fuentes/AJAX.php?act=eliminarCorrelativa", {"cod":cod, "requisito":requisito, "tipo":tipo}, function(data) {
								
									$('#cod').change();
								
							});
						});
					}
				
				});
			});
			
			
			
			$('#mostrarFormulario').click(function() {
				$('div #formulario').toggle();
			});
			
		});
	</script>
</html>
