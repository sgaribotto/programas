﻿<!DOCTYPE html>
<html>
	<head>
		
		<title>Constantes</title>
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
			<h2 class="formularioLateral">Docentes</h2>
			<div id="mostrarFormulario">Mostrar Formulario</div>
			<div id="mostrarFiltros">Mostrar Filtros</div>
			<div id="formulario">
				<fieldset class="formularioLateral">
					<form method="post" class="formularioLateral" action="procesardatos.php?formulario=constantes" id="cargarNuevo">
						<label class="formularioLateral" for="nombre">Nombre:</label>
						<input type="text" class="formularioLateral iconCod" name="nombre" required="required" id="nombre" maxlength="254"/>
						<br />
						<label class="formularioLateral" for="valor">Valor: </label>
						<input name="valor" class="formularioLateral iconNombre"  required="required" id="valor" type="text" maxlength="254" />
						<br />
						
						<button type="submit" class="formularioLateral iconAgregar" id="guardarCargarOtro">Guardar</button>
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
				$('#tablaDatos').load("fuentes/AJAX.php?act=tablaConstantes", formValues, function(data) {
					$('.botonEliminar').click(function() {
						if (confirm('¿Desea Eliminar la constante?')) {
							var id = $(this).data('id');
							$.post("./fuentes/AJAX.php?act=eliminarConstante", {"id":id, }, function(data) {
								actualizarTabla();
							});
						}
					});
				});
			} 
			actualizarTabla();
			
			$("#cargarNuevo").submit(function(event) {
				event.preventDefault();
				formValues = $("#cargarNuevo").serialize();
				
				
				$.post("./fuentes/AJAX.php?act=agregarConstante", formValues, function(data) {
					alert(data);
					actualizarTabla();
				});
				
			});
			
			
			$('#dni').change(function() {
				dni = $('#nombre').val();
				$.post("./fuentes/AJAX.php?act=buscarConstante", {"dni":dni, }, function(data) {
					if (data != "nuevo") {
						datosDocente = data.split(',');
						$('#nombre').val(datosDocente[1]);
						
					}
					
				});
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
			
			$(".datepicker").datepicker({
				changeMonth:true,
				changeYear:true,
				dateFormat:'yy-mm-dd',
				yearRange:'c-80:c',
				
			});
			
		});
	</script>
</html>
