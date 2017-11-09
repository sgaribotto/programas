<!DOCTYPE html>
<html>
	<head>
		
		<title>Situación CVAR</title>
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
						<label class="formularioLateral" for="docente">Docente: </label>
						<input name="docente" class="formularioLateral iconDocente filterTrigger" id="docente" type="text" maxlength="30">
						<br />
					</form>
				</fieldset>
			</div>
			<div class="dialog resumenDocente" id="dialogResumenDocente"></div>
		
			<hr>
			
		
			<div id="tablaDatos"></div>
		
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
			
			$('#dialogResumenDocente').dialog('option', 'title', 'Resumen del docente');
			
			var actualizarTabla = function() {
				formValues = $('form.filtros').serialize();
				console.log(formValues);
				$('#tablaDatos').load("fuentes/AJAX.php?act=tablaSituacionCVAR", formValues, function(data) {
					
					$('input.cvar').change(function() {
						var id = $(this).data('id');
						var check = 0;
						if (this.checked) {
							check = 1;
						}
						$.get("./fuentes/AJAX.php?act=asignarCargaCVAR", {"id": id, "check": check, }, function(data1) {
							//console.log(id);
							
							
						});
					});
					
					$('input.exceptuado_cvar').change(function() {
						var id = $(this).data('id');
						var check = 0;
						if (this.checked) {
							check = 1;
						}
						$.get("./fuentes/AJAX.php?act=asignarExceptuadoCVAR", {"id": id, "check": check, }, function(data1) {
							//console.log(id);
							
							
						});
					});
				
				});
			} 
			actualizarTabla();
			
			$('form.filtros').submit(function(event) {
				event.preventDefault();
			});
			
			$('div#tablaDatos').on('click', 'td.masInfo', function(event) {
				
				var id = $(event.target).data('id');
				$('#dialogResumenDocente').empty();
				
				$('#dialogResumenDocente').load('resumendocente.php', {"docente":id});
				
				$('#dialogResumenDocente').dialog(dialogOptions).dialog('open');
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
				location.assign('situacioncargaCVAREXCEL.php');
			});
			
		});
	</script>
</html>
