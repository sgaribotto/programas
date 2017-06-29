<!DOCTYPE html>
<html>
	<head>
		
		<title>Materias</title>
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
			<div id="mostrarFormulario">Mostrar Formulario</div>
			<div id="mostrarFiltros">Mostrar Filtros</div>
			<div id="formulario">
				<fieldset class="formularioLateral">
					<form method="post" class="formularioLateral" action="procesardatos.php?formulario=equipoDocente" id="agregarMateria">
						<label class="formularioLateral" for="cod">Código:</label>
						<input type="text" class="formularioLateral iconCod" name="cod" required="required" id="cod"maxlength="10"/>
						<br />
						<label class="formularioLateral" for="nombre">Nombre: </label>
						<input name="nombre" class="formularioLateral iconNombre"  required="required" id="nombre" type="text" maxlength="100">
						<br />
						<label class="formularioLateral" for="carrera">Carrera: </label>
						<select name="carrera" class="formularioLateral iconCarrera"  required="required" id="carrera">
							<option value="">Seleccione la carrera</option>
							<?php 
								require "./conexion.php";
								$query = "SELECT id, cod, nombre FROM carrera WHERE activo = 1";
								$result = $mysqli->query($query);
								
								while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
									echo "<option value='$row[id]'>$row[nombre]</option>";
								}
								
								$result->free();
								$mysqli->close();
							?>
						
						</select><br />
						<label class="formularioLateral" for="cuatrimestre">Cuatrimestre: </label>
						<input name="cuatrimestre" class="formularioLateral iconCuatrimestre"  required="required" id="cuatrimestre" type="number">
						<br />
						<label class="formularioLateral" for="plan">Plan:</label>
						<input type="text" class="formularioLateral iconPlan" name="plan" required="required" id="plan" />
						<br />
						<label class="formularioLateral" for="contenidosminimos">Contenidos Mínimos: </label>
						<textarea name="contenidosminimos" class="formularioLateral"  required="required" id="contenidosminimos" style="height:100px;"></textarea>
						<br />
						<button type="submit" class="formularioLateral iconAgregar" id="guardarCargarOtro">Guardar y cargar otra</button>
					</form>
				</fieldset>
			</div>
			
			<div id="filtros" class="desplegable">
				<fieldset class="formularioLateral">
					<form method="post" class="formularioLateral filtros" action="" id="filtrosForm" >
						<label class="formularioLateral" for="filtro">Buscar:</label>
						<input type="text" class="formularioLateral iconCod" name="filtro" required="required" id="filtro" maxlength="10"/>
					</form>	
				</fieldset>
			</div>
			
		
			<hr>
		
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
				$('#tablaDatos').load("fuentes/AJAX.php?act=tablaMaterias", formValues, function(data) {
					$('.botonEliminar').click(function() {
						var id = $(this).data('id');
						$.post("./fuentes/AJAX.php?act=eliminarMateria", {"id":id, }, function(data) {
							actualizarTabla();
						});
					});
				});
			} 
			actualizarTabla();
			
			$('form#agregarMateria').submit(function(event){
				event.preventDefault();
				
				formValues = $('#agregarMateria').serialize();
				/*nombre = $('#nombre').val();
				carrera = $('#carrera').val();
				cuatrimestre = $('#cuatrimestre').val();
				plan = $('#plan').val();
				contenidosminimos = $('#contenidosminimos').val();*/
				
				$.post("./fuentes/AJAX.php?act=agregarMateria", formValues, function(data) {
					if (data.tipo == "error") {
						alert('Error desconocido');
					} else if(data.tipo == "modificacion")
						alert('Se ha realizado la modificación');
					} else if(data.tipo == "agregado") {
						alert('Se ha agregado la materia');
					}
				});
			});
			
			$('#cod').change(function() {
				cod = $('#cod').val();
				$.post("./fuentes/AJAX.php?act=buscarMateria", {"cod":cod, }, function(data) {
						console.log(data);
						datosMateria = data.split('///');
						$('#nombre').val(datosMateria[1]);
						$('#carrera').val(datosMateria[2]);
						$('#plan').val(datosMateria[3]);
						$('#cuatrimestre').val(datosMateria[4]);
						$('#contenidosminimos').val(datosMateria[5]);
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
			
		});
	</script>
</html>
