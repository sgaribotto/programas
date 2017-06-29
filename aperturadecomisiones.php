<!DOCTYPE html>
<html>
	<head>
		
		<title>Apertura de comisiones</title>
		<?php 
			require_once 'fuentes/meta.html';
			
			require_once 'fuentes/constantes.php';
			require_once 'programas.autoloader.php';
			
		?>
		
	</head>
	
	<body>
		<?php 
			require_once 'fuentes/botonera.php';
			require_once 'fuentes/panelNav.php';
		?>
		<div class="formularioLateral">
			<div id="mostrarFiltros">Mostrar Filtros</div>
			<div id="DescargarXLS">Copiar a la base</div>
			<div id="filtros" class="desplegable">
				<fieldset class="formularioLateral">
					<form method="post" class="formularioLateral filtros" action="" >
						<label class="formularioLateral" for="filtro">Buscar:</label>
						<input type="text" class="formularioLateral iconCod" name="filtro" required="required" id="filtro" maxlength="20"/>
					</form>	
				</fieldset>
			</div>
			<hr />
			<div id="tablaDatos">
				 <div class="loader"></div>
			</div>
		
		</div>	
	</body>
	<?php require "./fuentes/jqueryScripts.html"; ?>
	<script src="./fuentes/funciones.js"></script>
	
	<script>
		$(document).ready(function() {
			
			var actualizarTabla = function() {
				formValues = $('form.filtros').serialize();
				//console.log(formValues);
				$('#tablaDatos').empty();
				$('#tablaDatos').append('<div class="loader"></div>');
				$('#tablaDatos').load("fuentes/AJAX.php?act=tablaAperturaComisiones", formValues, function(data) {
					$('.loader').fadeOut();
					//console.log(data);
				});
			} 
			actualizarTabla();
			
			$('#mostrarFiltros').click(function() {
				$('#filtros').slideToggle();
			});
			
			$('#mostrarFiltros').click();
			
			$('#filtro').on('keyup', function(event) {
				if ($(this).val().length > 1) {
					//console.log($(this).val());
					actualizarTabla();
				}
			});
			
			$('#filtros').focus();
			
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
	<style>
		.loader {
			border: 16px solid #f3f3f3; /* Light grey */
			border-top: 16px solid #f9f9f9; /* Blue */
			border-radius: 50%;
			width: 64px;
			height: 64px;
			animation: spin 2s linear infinite;
			margin: auto;
		}

		@keyframes spin {
			0% { transform: rotate(0deg); }
			100% { transform: rotate(360deg); }
		}
	</style>
</html>
