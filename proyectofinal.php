<!DOCTYPE html>
<html>
	<head>
		
		<title>Proyecto final</title>
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
			if (!isset($_REQUEST['numero'])) {
				echo "<script>location.assign('proyectosfinales.php');</script>";
			} else {
				require 'fuentes/conexion.php';
				$numero = $_REQUEST['numero'];
				
				if ($numero == 'nuevo') {
					$nuevo = true;
					$readonly = '';
					$numero = 'nuevo';
				} else {
					$nuevo = false;
					$readonly = 'readonly';
					
					$query = "SELECT * 
								FROM proyectos_finales
								WHERE id = '{$numero}'";
					$result = $mysqli->query($query);
					$datos_proyecto = array();
					while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
						
						$datos_proyecto = $row;
					}
					
					//print_r($datos_proyecto);
				}
			}
			
			
								
				
								
		?>
		<?php
			require_once('./fuentes/botonera.php');
			require("./fuentes/panelNav.php");
		?>
		<div class="formularioLateral">
			<h2 class="formularioLateral">Proyecto Final</h2>
			<div id="mostrarFormulario">Buscar</div>
			<div id="tabs">	  
			  <ul>
				  
				<li><a href="#formulario">Formulario</a></li>
				<?php 
					if (!$nuevo) {
				?>
				<li><a href="#autores">Autores</a></li>
				<li><a href="#evaluaciones">Evaluaciones</a></li>
				<?php } ?>
			  </ul>
				<div id="formulario">
					
					<fieldset class="formularioLateral">
						<form method="post" class="formularioLateral" action="#" id="agregarProyecto">
							<label class="formularioLateral" for="cod" >Número:</label>
							<input type="text" class="formularioLateral iconCod" name="numero" style="width: 50px;"
								id="numero" maxlength="10" readonly value="<?php echo $numero; ?>"/>
							<br />
							<label class="formularioLateral" for="modalidad">Modalidad: </label>
							<select name="modalidad" class="formularioLateral iconModalidad"  required id="modalidad">
								<option value="">Seleccione la modalidad</option>
								<?php
									$modalidades = ['Trabajo Final', 'Investigación Orientada', 'Seminario TFPP'];
									foreach ($modalidades as $modalidad) {
										$selected = '';
										if (isset($datos_proyecto['modalidad']) and $datos_proyecto['modalidad'] == $modalidad) {
											$selected = "selected";
										}
										echo "<option value='{$modalidad}' {$selected}>{$modalidad}</option>";
									}
								?>
							
							</select><br />
							<label class="formularioLateral" for="titulo">Título: </label>
							<textarea name="titulo" class="formularioLateral"  
								required="required" id="titulo" type="text" maxlength="250" <?php echo $readonly; ?>
								rows='3' style="display: inline; width:380px; height: 56px;"><?php if (isset($datos_proyecto['titulo'])) 
								{ echo $datos_proyecto['titulo']; } ?></textarea>
							<br />
							
							<label class="formularioLateral" for="inicio">Fecha de inicio: </label>
							<input name="inicio" class="formularioLateral iconCuatrimestre datepicker"  
								required="required" id="inicio" <?php echo $readonly; ?> 
								<?php if (isset($datos_proyecto['fecha_inicio'])) { echo "value='" . $datos_proyecto['fecha_inicio'] . "'"; } ?>>
							<br />
							
							<label class="formularioLateral" for="estado">Estado:</label>
							<select name="estado" class="formularioLateral iconModalidad"  id="estado">
								<option value="">Seleccione el estado</option>
								<?php
									$estados = ['Proyecto presentado', 
												'Prorrogado 30 días',
												'Prorrogado 60 días', 
												'Preliminar entregado', 
												'Preliminar aprobado', 
												'Preliminar rechazado', 
												'En evaluación', 
												'Aprobado'];
									foreach ($estados as $estado) {
										$selected = '';
										if (isset($datos_proyecto['estado']) and $datos_proyecto['estado'] == $estado) {
											$selected = "selected";
										}
										echo "<option value='{$estado}' $selected>{$estado}</option>";
									}
								?>
							
							</select><br />
							<label class="formularioLateral" for="final">Fecha de cierre: </label>
							<input name="final" class="formularioLateral iconCuatrimestre datepicker" id="final" 
								<?php if (isset($datos_proyecto['fecha_final'])) { echo "value='" . $datos_proyecto['fecha_final'] . "'"; } ?>>
							<br />
							
							
							<label class="formularioLateral" for="nota">Nota:</label>
							<input name="nota" class="formularioLateral iconNombre"  
								id="nota" type="text" maxlength="2" 
								<?php if (isset($datos_proyecto['nota'])) { echo "value='" . $datos_proyecto['nota'] . "'"; } ?>>
							<br />
							<label class="formularioLateral" for="cantidad_ejemplares">Cantidad de Ejemplares:</label>
							<input name="cantidad_ejemplares" class="formularioLateral iconNombre"  
								id="cantidad_ejemplares" type="text" maxlength="10" 
								<?php if (isset($datos_proyecto['cantidad_ejemplares'])) { echo "value='" . $datos_proyecto['cantidad_ejemplares'] . "'"; } ?>>
							<br />
							<label class="formularioLateral" for="digital">Ejemplar digital:</label>
							
							<?php
								$checked = '';
								if (isset($datos_proyecto['digital']) and $datos_proyecto['digital'] == 1) {
									$checked = "checked";
								}
							?>
							<input name="digital" class="formularioLateral iconNombre"  
								id="digital" type="checkbox" value="1" <?php echo $checked; ?>>
							<br />
							<label class="formularioLateral" for="licencia">Licencia:</label>
							<?php
								$checked = '';
								if (isset($datos_proyecto['licencia']) and $datos_proyecto['licencia'] == 1) {
									$checked = "checked";
								}
							?>
							<input name="licencia" class="formularioLateral iconNombre"  
								id="licencia" type="checkbox" value="1" <?php echo $checked; ?>>
							<br />
							
							<label class="formularioLateral" for="observaciones">Observaciones: </label>
							<textarea name="observaciones" class="formularioLateral" id="observaciones" 
								style="height:100px;"><?php if (isset($datos_proyecto['observaciones'])) { 
									echo $datos_proyecto['observaciones']; } ?></textarea>
							<br />
							<button type="submit" class="formularioLateral iconAgregar" id="guardarCargarOtro">Guardar</button>
						</form>
					</fieldset>
				</div>
				
				<?php if (!$nuevo) { ?>
				<div id="autores">
					
					<div class="tablaParticipantes">
					<table class="autores">
						<thead>
							<tr class="autores">
								<th class='subtitulo'style='width:16%;'>Rol</th>
								<th class='subtitulo'style='width:58%;'>Participante</th>
								<th class='subtitulo' style='width:20%;'>Carrera</th>
								<th class='subtitulo' style='width:10%;'>Eliminar</th>
							</tr>
						</thead>
						<tbody class="tablaParticipantes"></tbody>
					</table>
					
					</div>
					<hr>
					<div id="formulario autores">
						<fieldset class="formularioLateral">
							<form method="post" class="formularioLateral agregarAutores" action="#" id="agregarAutores">
								<input type="text" class="formularioLateral iconCod" name="numero" style="width: 50px;"
								id="numero" maxlength="10" hidden value="<?php echo $numero; ?>"/>
							<br />
								<label class="formularioLateral" for="rol">Rol: </label>
								<select name="rol" class="formularioLateral iconModalidad"  required id="rol">
									<option value="">Seleccione el Rol</option>
									<?php
										$roles = array('Autor' => 'autor', 
														'Tutor' => 'tutor', 
														'Director' => 'director'
												);
										foreach ($roles as $rol => $tabla) {
											
											echo "<option value='{$tabla}'>{$rol}</option>";
										}
									?>
								</select>
								<br />
								<div class="buscarParticipantes"></div>
								<!--<label class="formularioLateral" for="participante">Participante: </label>
								<select name="participante" class="formularioLateral iconModalidad"  required id="participantes">
									<option value="">Seleccione el Participante</option>
								</select>
								<br />
								<label class="formularioLateral" for="carrera">Carrera:</label>
								<select name="carrera" class="formularioLateral iconModalidad"  id="carrera">
									<option value="">Seleccione la carrera</option>
								</select>
								<br />-->
								
								
								
								
								
								<label class="formularioLateral" for="observaciones">Observaciones: </label>
								<textarea name="observaciones" class="formularioLateral" id="observaciones" 
									style="height:100px;"><?php if (isset($datos_proyecto['observaciones'])) { 
										echo $datos_proyecto['observaciones']; } ?></textarea>
								<br />
								<!--<button type="submit" class="formularioLateral iconAgregar" id="guardarCargarOtro">Guardar</button>-->
							</form>
						</fieldset>
					</div>
					
				</div>
				<div id="evaluaciones">
					<div class="tablaEvaluaciones">
					<table class="evaluaciones" style='width:98%'>
						<thead>
							<tr class="evaluaciones">
								<th class='subtitulo'style='width:40%;'>Evaluador</th>
								<th class='subtitulo'style='width:30%;'>Estado</th>
								<th class='subtitulo' style='width:10%;'>Fecha</th>
								<th class='subtitulo' style='width:10%;'>Nota</th>
								<th class='subtitulo' style='width:10%;'>Eliminar</th>
							</tr>
						</thead>
						<tbody class="tablaEvaluaciones"></tbody>
					</table>
					
					</div>
					<hr>
					<div id="formulario evaluaciones">
						<fieldset class="formularioLateral">
							<form method="post" class="formularioLateral agregarEvaluacion" action="#" id="agregarEvaluacion">
								<input type="text" class="formularioLateral iconCod evaluaciones numero" name="numero" style="width: 50px;"
								id="numero" maxlength="10" hidden value="<?php echo $numero; ?>"/>
								<input type="text" class="formularioLateral iconCod evaluaciones numeroEvaluacion" name="numeroEvaluacion" style="width: 50px;"
								id="numeroEvaluacion" maxlength="10" hidden value="0"/>
							<br />
								<label class="formularioLateral" for="rol">Evaluador: </label>
								<select name="evaluador" class="formularioLateral iconModalidad evaluaciones evaluador"  required id="evaluador">
									<option value="">Seleccione el Evaluador</option>
									<?php
										$query = "SELECT id, CONCAT(apellido, ', ', nombres) AS docente
										FROM docente
										WHERE activo = 1
										ORDER BY apellido, nombres";
										$result = $mysqli->query($query);
										
										$participantes = array();
										while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
											$participantes[$row['id']] = $row['docente'];
										}
																	
										foreach ($participantes as $id => $participante) {
											echo "<option value='{$id}'>{$participante}</option>";
										}
									?>
								</select>
								<br />
								<label class="formularioLateral" for="estadoEvaluacion">Estado: </label>
								<select name="estadoEvaluacion" class="formularioLateral iconModalidad evaluaciones estado"  required id="estadoEvaluacion">
									<option value="">Seleccione el Estado</option>
									<?php
										$estados = ['Asignado', 'Notificado', 'Retirado', 'Aprobado', 'En revisión'];
															
										foreach ($estados as $estado) {
											echo "<option value='{$estado}'>{$estado}</option>";
										}
									?>
								</select>
								<br />
								<label class="formularioLateral" for="notificacion">Fecha de Notificación: </label>
								<input name="notificacion" class="formularioLateral iconCuatrimestre datepicker evaluaciones notificacion"  
									 id="notificacion" <?php //echo $readonly; ?> >
								<br />
								<label class="formularioLateral" for="retiro">Fecha de Retiro: </label>
								<input name="retiro" class="formularioLateral iconCuatrimestre datepicker evaluaciones retiro"  
									 id="retiro" <?php //echo $readonly; ?> >
								<br />
								
								<label class="formularioLateral" for="nota">Nota: </label>
								<input name="nota" class="formularioLateral evaluaciones nota"  
									 id="nota" <?php //echo $readonly; ?> >
								<br />
								<label class="formularioLateral" for="dictamen">Fecha de Dictamen: </label>
								<input name="dictamen" class="formularioLateral iconCuatrimestre datepicker evaluaciones dictamen"  
									 id="dictamen" <?php //echo $readonly; ?> >
								<br />
								
								<label class="formularioLateral" for="observaciones">Observaciones: </label>
								<textarea name="observaciones" class="formularioLateral evaluaciones observaciones" id="observaciones" 
									style="height:100px;"></textarea>
								<br />
								<button type="submit" class="formularioLateral iconAgregar" id="guardarCargarOtro">Guardar</button>
							</form>
						</fieldset>
					</div>
				</div>
				<?php } ?>
			</div>
			
		</div>
		
	</body>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
	<script src="./fuentes/funciones.js"></script>
	
	<script>
		$(document).ready(function() {
			
			var actualizarTabla = function() {
				
				//formValues = $('form.filtros').serialize();
				//console.log(formValues);
				var proyecto = $('#numero').val();
				
				$('tbody.tablaParticipantes').load("fuentes/AJAX.php?act=tablaParticipantes", {"proyecto": proyecto}, function(data) {
					//alert(proyecto);
					$('.botonEliminar.participantes').click(function() {
						var id = $(this).data('id');
						$.post("./fuentes/AJAX.php?act=eliminarParticipante", {"id":id, }, function(data) {
							actualizarTabla();
						});
					});
				});
				
				$('tbody.tablaEvaluaciones').load("fuentes/AJAX.php?act=tablaEvaluaciones", {"proyecto": proyecto}, function(data) {
					//alert(proyecto);
					$('.botonEliminar.evaluaciones').click(function() {
						var id = $(this).data('id');
						$.post("./fuentes/AJAX.php?act=eliminarEvaluacion", {"id":id, }, function(data) {
							actualizarTabla();
							
						});
					});
					
					$('td.masInfo.evaluaciones').click(function() {
						var id = $(this).data('id');
						
						$.post('./fuentes/AJAX.php?act=editarEvaluacion', {'id': id}, function(data) {
							data =JSON.parse(data);
							//alert(data.evaluador);
							$('input.evaluaciones.numeroEvaluacion').val(data.id);
							$('select.evaluaciones.evaluador').val(data.evaluador);
							$('select.evaluaciones.estado').val(data.estado);
							$('input.evaluaciones.notificacion').val(data.fecha_notificacion);
							$('input.evaluaciones.retiro').val(data.fecha_retiro);
							$('input.evaluaciones.nota').val(data.nota);
							$('input.evaluaciones.dictamen').val(data.fecha_dictamen);
							$('textarea.evaluaciones.observaciones').text(data.observaciones);
						});
					});
				});
			} 
			actualizarTabla();
			
			$('form#agregarProyecto').submit(function(event){
				event.preventDefault();
				
				formValues = $('#agregarProyecto').serialize();
				/*nombre = $('#nombre').val();
				carrera = $('#carrera').val();
				cuatrimestre = $('#cuatrimestre').val();
				plan = $('#plan').val();
				contenidosminimos = $('#contenidosminimos').val();*/
				
				$.post("./fuentes/AJAX.php?act=agregarProyecto", formValues, function(data) {
					if (data.tipo == "error") {
						alert('Error desconocido');
					} else if(data.tipo == "modificacion") {
						alert('Se ha realizado la modificación');
					} else if(data.tipo == "agregado") {
						alert('Se ha agregado el proyecto');
					}
					actualizarTabla();
				});
			});
			
			$('#rol').change(function() {
				rol = $('#rol').val();
				
				$('#agregarAutores').one('submit', function(event) {
					//alert('test');
					event.preventDefault();
					
					var values = $(this).serialize();
					$.post("./fuentes/AJAX.php?act=agregarParticipantes", values, function(data) {
						//alert(data);
						//location.reload();
						actualizarTabla();
					});
					
				});
				
				$("div.buscarParticipantes").load("./fuentes/AJAX.php?act=buscarParticipantes", {"rol":rol, }, function(data) {
						$('button.buscarAlumno').click(function() {
							var dni = $('input.buscarAlumno').val();
							$('div.datosAlumno').load("./fuentes/AJAX.php?act=buscarAlumnoTFPP", {"dni": dni}, function(data) {
								//$('form.agregar').off();
								$('#agregarAutores').one('submit', function(event) {
									//alert('test');
									event.preventDefault();
									
									var values = $(this).serialize();
									$.post("./fuentes/AJAX.php?act=agregarParticipantes", values, function(data) {
										//alert(data);
										//location.reload();
										actualizarTabla();
									});
									
								});
							});
						});
						//console.log(data);
						//alert(rol);
						
				});
			});
			
			$('form.agregarEvaluacion').submit(function(event) {
				event.preventDefault();
				var values = $(this).serialize();
				$.post('./fuentes/AJAX.php?act=agregarEvaluacion', values, function(data) {
					actualizarTabla();
				});
			});
			
			$('.botonEliminar').click(function() {
				cod = $(this).data('cod');
				$.post("./fuentes/AJAX.php?act=eliminarMateria", {"cod":cod, }, function(data) {
					//alert(data);
					location.reload();
				});
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
				location.assign('proyectosfinales.php');
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
			$('#mostrarFiltros').click();
			
			$('#filtro').on('keyup', function(event) {
				if ($(this).val().length > 1) {
					actualizarTabla();
				}
			});
			
			$('#filtro').focus();
			
			$('#filtrosForm').submit(function(event) {
				event.preventDefault();
			});
			
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
			
			$("#tabs").tabs();
			
			//$("select").combobox();
			
			
			
		});
	</script>
</html>
