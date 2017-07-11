<!DOCTYPE html>
<html>
	<head>
		
		<title>Responsables</title>
		<?php 
			require_once('./fuentes/meta.html');
			
			require_once 'programas.autoloader.php';
		?>
		
	</head>
	
	<body>
		
		<?php
			require_once('./fuentes/botonera.php');
			require("./fuentes/panelNav.php");
		?>
		<div class="formularioLateral">
			<h2 class="formularioLateral">Responsables</h2>
			<div id="mostrarFormulario">Mostrar Formulario</div>
			<div id="mostrarFiltros">Mostrar Filtros</div>
			<div id="formulario">
				<fieldset class="formularioLateral">
					<form method="post" class="formularioLateral" action="procesardatos.php?formulario=equipoDocente" id="agregarResponsable">
						
						<label class="formularioLateral" for="usuario">Usuario: </label>
						<select name="usuario" class="formularioLateral iconUser"  required="required" id="usuario">
							<option value="">Seleccione el usuario</option>
							<?php 
								require "./conexion.php";
								$query = "SELECT id, CONCAT_WS(', ', apellido, nombres) AS nombre FROM personal WHERE activo = 1 AND NOT ISNULL(usuario) ORDER BY apellido, nombre";
								$result = $mysqli->query($query);
								
								while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
									echo "<option value='$row[id]'>$row[nombre]</option>";
								}
								
								$result->free();
								$mysqli->close();
							?>
						
						</select><br />
						<label class="formularioLateral" for="materia">Materia: </label>
						<select name="materia" class="formularioLateral iconMateria"  required="required" id="materia">
							<option value="">Seleccione la materia</option>
							<?php 
								require "./conexion.php";
								$query = "SELECT id, cod, nombre FROM materia WHERE activo = 1";
								$result = $mysqli->query($query);
								
								while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
									echo "<option value='$row[cod]'>$row[cod] - $row[nombre]</option>";
								}
								
								$result->free();
								$mysqli->close();
							?>
						
						</select><br />
						
						<button type="submit" class="formularioLateral iconAgregar" id="guardarCargarOtro">Guardar y cargar otra</button>
					</form>
				</fieldset>
			</div>
			
			<div id="filtros" class="desplegable">
				<fieldset class="formularioLateral">
					<form method="post" class="formularioLateral filtros" action="" >
						<label class="formularioLateral" for="filtro">Buscar:</label>
						<input type="text" class="formularioLateral iconCod" name="filtro" required="required" id="filtro" maxlength="10"/>
					</form>	
				</fieldset>
			</div>
		
			<hr>
		
		
			
					<?php
						/*require "./conexion.php";
						//PAginación
						$cantidadResultados = (isset($_GET['cantidadResultados'])) ? $_GET['cantidadResultados'] : 10;
						$pagina = (isset($_GET['pagina'])) ? (($_GET['pagina'] - 1) * $cantidadResultados) : 0;
						$hasta = $pagina + $cantidadResultados;
						
						$result = $mysqli->query("SELECT COUNT(id) FROM responsable WHERE activo = 1");
						$totalPaginas = $result->fetch_row();
						$result->free();
						
						$totalPaginas = $totalPaginas[0] / $cantidadResultados;
						//tabla y resultados
						$query = "SELECT id, responsable, materia FROM vista_responsable LIMIT $pagina, $cantidadResultados";
						$result = $mysqli->query($query);
						echo $mysqli->error;
						
						while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
							
							echo "<tr class='formularioLateral plantelActual'>";
							
							foreach ($row as $value) {
								echo "<td class='formularioLateral plantelActual'>$value</td>";
							}
							
							echo "<td class='formularioLaterial eliminarEnTabla'><button type='button' class='formularioLateral botonEliminar' id='eliminarDocente' data-cod='$row[id]'>X</button>";
							echo "</tr>";
						}
						
						$result->free();
						$mysqli->close();*/

					?>
				
			<div id="tablaDatos"></div>
			
		</div>
		
	</body>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
	<script src="./fuentes/funciones.js"></script>
	
	<script>
		$(document).ready(function() {
			
			var actualizarTabla = function() {
				formValues = $('form.filtros').serialize();
				//console.log(formValues);
				$('#tablaDatos').load("fuentes/AJAX.php?act=tablaResponsables", formValues, function(data) {
					$('.botonEliminar').click(function() {
						var id = $(this).data('id');
						$.post("./fuentes/AJAX.php?act=eliminarResponsable", {"id":id, }, function(data) {
							
							actualizarTabla();
						});
					});
					
				});
			} 
			actualizarTabla();
			
			$("#agregarResponsable").submit(function(event) {
				event.preventDefault();
				formValues = $("#agregarResponsable").serialize();
				
				$.post("./fuentes/AJAX.php?act=agregarResponsable", formValues, function(data) {
					actualizarTabla();
					alert('Se ha agregado al responsable');
				});
				
			});
			
			$('.botonEliminar').click(function() {
				id = $(this).data('cod');
				$.post("./fuentes/AJAX.php?act=eliminarResponsable", {"id":id, }, function(data) {
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
			$('#mostrarFiltros').click();
			
			$('#filtro').on('keyup', function(event) {
				if ($(this).val().length > 1) {
					actualizarTabla();
				}
			});
			
			$('#filtro').focus();
			
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
			
		});
	</script>
</html>
