<!DOCTYPE html>
<html>
	<head>
		
		<title>Designaciones</title>
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
			<h2 class="formularioLateral">Designaciones</h2>
			<!--<div id="mostrarFormulario">Mostrar Formulario</div>
			<div id="mostrarFiltros">Mostrar Filtros</div>-->
			<div id="formulario">
				<fieldset class="formularioLateral">
					<form method="post" class="formularioLateral" action="procesardatos.php?formulario=agregarDesignacion" id="cargarDesignacion">
						<label class="formularioLateral" for="docente">Docente:</label>
						<select name="docente" class="formularioLateral iconDocente"  required="required" id="docente">
							<option value="">Seleccione el docente</option>
							<?php 
								require "./conexion.php";
								$query = "SELECT id, 
											CONCAT_WS(', ', d.apellido, d.nombres) AS nombre 
											FROM docente AS d 
											WHERE activo = 1 
											ORDER BY apellido, nombre";
								$result = $mysqli->query($query);
								
								while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
									echo "<option value='$row[id]'>$row[nombre]</option>";
								}
								
								$result->free();
								$mysqli->close();
							?>
						</select><br />
						<label class="formularioLateral" for="categoria">Categoría:</label>
						<select name="categoria" class="formularioLateral iconDocente"  required="required" id="categoria">
							<option value="">Seleccione la categoría</option>
							<?php 
								$categorias = ['Titular', 'Asociado', 'Adjunto', 
									'JTP', 'Ayudante Graduado', 'Ayudante Alumno', 'Adscripto', 'Otro'];
									
								
								foreach ($categorias as $categoria) {
									echo "<option value='$categoria'>$categoria</option>";
								}
							?>
						</select><br />
						
						<label class="formularioLateral" for="caracter">Caracter:</label>						
						<select name="caracter" class="formularioLateral iconDocente"  required="required" id="caracter">
							<option value="">Seleccione el caracter</option>
							<?php 
								$categorias = ['Interino', 'Ordinario', 'Contratado', 'Otro'];
									
								
								foreach ($categorias as $categoria) {
									echo "<option value='{$categoria}'>$categoria</option>";
								}
							?>
						</select><br />
						
						<label class="formularioLateral" for="dedicacion">Dedicación:</label>						
						<select name="dedicacion" class="formularioLateral iconDocente"  required="required" id="dedicacion">
							<option value="">Seleccione el caracter</option>
							<?php 
								$categorias = ['Simple', 'Semi-Exclusiva', 'Exclusiva', 'Otra'];
									
								
								foreach ($categorias as $categoria) {
									echo "<option value='{$categoria}'>$categoria</option>";
								}
							?>
						</select><br />
						
						<label class="formularioLateral" for="fechaalta">Fecha de Alta: </label>
						<input name="fechaalta" class="formularioLateral iconFecha datepicker"  required="required" id="fechaalta" type="text">
						<br />
						<label class="formularioLateral" for="fechabaja">Fecha de Baja:</label>
						<input type="text" class="formularioLateral iconFecha datepicker" name="fechabaja" required="required" id="fechabaja" />
						<br />
						<label class="formularioLateral" for="motivacion">Motivación:</label>
						<select name="motivacion" class="formularioLateral iconDocente"  required="required" id="motivacion">
							<option value="">Seleccione la motivación</option>
							<?php 
								$categorias = ['Dictado materia de referencia', 
									'Centro de investigación. Dictado materia de referencia', 
									'Dirección de carrera. Dictado materia de referencia', 
									'Coordinación de carrera. Dictado materia de referencia',
									'Ampliación del cargo concursado. Dictado materia de referencia',
									'Sin comisión asignada'];
									
								
								foreach ($categorias as $categoria) {
									echo "<option value='{$categoria}'>$categoria</option>";
								}
							?>
						</select><br />
						<label class="formularioLateral" for="observaciones"  >Observaciones:</label>
						<textarea class="formularioLateral" name="observaciones" maxlength="508" style="height: 40px;"></textarea>
						<br />
						
						<button type="submit" class="formularioLateral iconAgregar" id="guardarCargarOtro">Guardar y cargar otra</button>
					</form>
				</fieldset>
			</div>
			
			<!--<div id="filtros" class="desplegable">
				<fieldset class="formularioLateral">
					<form method="post" class="formularioLateral filtros" action="" >
						<label class="formularioLateral" for="filtro">Buscar:</label>
						<input type="text" class="formularioLateral iconCod" name="filtro" required="required" id="filtro" maxlength="10"/>
					</form>	
				</fieldset>
			</div>-->
		
			<hr>
			
			<div id="tablaDatos"></div>
			
		</div>
		
	</body>
	<?php require "./fuentes/jqueryScripts.html"; ?>
	<script src="./fuentes/funciones.js"></script>
	
	<script>
		$(document).ready(function() {
			
			var actualizarTabla = function() {
				var docente = $('#docente').val();
				//alert(docente);
				if (docente) {
					$('#tablaDatos').load("fuentes/AJAX.php?act=tablaDesignaciones", {'docente': docente}, function(data) {
						$('.botonEliminar').click(function() {
							if (confirm('¿Desea Eliminar la designación?')) {
								var id = $(this).data('id');
								$.post("./fuentes/AJAX.php?act=eliminarDesignacion", {"id":id, }, function(data) {
									actualizarTabla();
								});
							}
						});
						
						$('.botonRenovar').click(function() {
							var id = $(this).data('id');
							$.post("./fuentes/AJAX.php?act=renovarDesignacion", {"id":id, }, function(data) {
								actualizarTabla();
							});
						});
					});
				}
			} 
			actualizarTabla();
			
			$("#cargarDesignacion").submit(function(event) {
				event.preventDefault();
				formValues = $(this).serialize();
				
				
				$.post("./fuentes/AJAX.php?act=agregarDesignacion", formValues, function(data) {
					alert(data);
					actualizarTabla();
					//$("#cargarDesignacion")[0].reset();
				});
				
			});
			
			/*function togglerButtonColor() {
				
				var gris = '#f9f9f9';
				
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
				
				var gris = '#f9f9f9';
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
				
				var gris = '#f9f9f9';
				
				$('#mostrarFormulario').css('backgroundColor', gris);
				$('#mostrarFormulario').css('color', 'black');
				
				
			});
			//$('#mostrarFiltros').click();
			
			$('#filtro').on('keyup', function(event) {
				if ($(this).val().length > 1) {
					actualizarTabla();
				}
			});
			
			$('#filtro').focus();
			*/
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
			
			$(".datepicker").datepicker({
				changeMonth:true,
				changeYear:true,
				dateFormat:'yy-mm-dd',
				yearRange:'c-80:c',
				
			});
			
			//$('select').combobox();
			
			
			$('select#docente').change(function() {
				actualizarTabla();
			});
		});
	</script>
</html>
