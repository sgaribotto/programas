<!DOCTYPE html>
<html>
	<head>
		
		<title>Cursantes por cohorte</title>
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
			<h2 class="formularioLateral">Cursantes por cohorte</h2>
			<!--<div id="mostrarFormulario">Mostrar Formulario</div>-->
			<div id="mostrarFiltros">Ocultar</div>
			
			<div id="filtros" class="desplegable">
				<fieldset class="formularioLateral">
					<form method="post" class="formularioLateral filtros" action="" >
						<label class="formularioLateral" for="anioInicio">Año Inicio:</label>
						<input type="number" class="formularioLateral iconCod" name="anioInicio" required="required" id="anioInicio" value="2011" min="1994" max="2018" />
						<label class="formularioLateral" for="anioFinal">Año Final:</label>
						<input type="number" class="formularioLateral iconCod" name="anioFinal" required="required" id="anioFinal" value="2018" min="1994" max="2018" />
						<br />
						<label class="formularioLateral" for="carrera">Carrera:</label>
						<select class="formularioLateral iconCod" name="carrera" required="required" id="carrera" >
							<option value="todas">Todas</option>
							<?php
								require 'fuentes/conexion.php';
								$query = "SELECT DISTINCT carrera, nombre_carrera
											FROM analiticos_convenios
												WHERE nombre_ua LIKE '%04%'
											ORDER BY nombre_carrera";
								//echo $query;
								
								$result = $mysqli->query($query);
								
								while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
									echo "<option value='{$row['carrera']}'>({$row['carrera']}) {$row['nombre_carrera']}</option>";
								}	
								
							?>
						</select>
						
						<br />
						<button type="submit">Buscar</button>
					</form>	
				</fieldset>
			</div>
		
			<hr>
			
			<div id="tablaDatos"></div>
			
		</div>
		
	</body>
	<?php require "./fuentes/jqueryScripts.html"; ?>
	<script src="./fuentes/funciones.js"></script>
	
	<script>
		$(document).ready(function() {
			
			var actualizarTabla = function() {
				formValues = $('form.filtros').serialize();
				//console.log(formValues);
				$('#tablaDatos').load("fuentes/AJAX.php?act=cursantesPorCohorteCONEAUIST", formValues, function(data) {
					
				});
			}
			
			$('form.filtros').submit(function(event) {
				event.preventDefault();
				actualizarTabla();
			});
						
			function togglerButtonColor() {
				
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
			$('#mostrarFiltros').click();
			
			/*$('#filtro').on('keyup', function(event) {
				if ($(this).val().length > 1) {
					actualizarTabla();
				}
			});
			
			$('#filtro').focus();*/
			
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
			
			/*$(".datepicker").datepicker({
				changeMonth:true,
				changeYear:true,
				dateFormat:'yy-mm-dd',
				yearRange:'c-80:c',
				
			});*/
			
			$('select').combobox();
		});
	</script>
	<style>
	  .custom-combobox {
		position: relative;
		display: inline-block;
	  }
	  .custom-combobox-toggle {
		position: absolute;
		top: 0;
		bottom: 0;
		margin-left: -1px;
		padding: 0;
	  }
	  .custom-combobox-input {
		margin: 0;
		padding: 5px 10px;
		width:300px;
	  }
  </style>
</html>
