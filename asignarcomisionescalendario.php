<!DOCTYPE html>
<html>
	<head>
		
		<title>Asignación de comisiones</title>
		<?php 
			require_once 'programas.autoloader.php';
			require_once('./fuentes/meta.html');
			include './fuentes/constantes.php';
			//$ANIO = 2017;
			//$CUATRIMESTRE = 2;
			$materia = new clases\Materia($_SESSION['materia']);
			//print_r($materia);
			$nombres = $materia->mostrarNombresConjunto();
			$conjunto = $materia->mostrarConjunto();
			$cod = $_SESSION['materia'];
			
		?>
		
	</head>
	
	<body>
		
		<?php
			require_once('./fuentes/botonera.php');
			require("./fuentes/panelNav.php");
		?>
		
		
		
		<div class="formularioLateral">
			<h1 class="nombreMateria"><span class="conjuntoMateria"><?php echo $conjunto; ?></span> <?php echo $nombres?></h1>
			
			<div class="comisiones">
				<h2 class="formularioLateral">Asignar comisiones del periodo <?php echo "{$ANIO} - {$CUATRIMESTRE}";?></h2>
				
				<hr>
				
			</div>
		</div>
	</body>
	<script src="./fuentes/funciones.js"></script>
	<script>
		$(document).ready(function() {
			
						
			/*$("#agregarAsignacionComision").submit(function(event) {
				event.preventDefault();
				values = $(this).serialize();
				values.act = "agregarAsignacionComision";
				
				$.get("./fuentes/AJAX.php?act=agregarAsignacionComision", values, function(data) {
					data = eval('(' + data + ')');
					console.log(data);
					if (data.error) {
						$('p.errorValidar').text(data.error);
						
					} else {
						actualizarTabla();
					}
				});
			});
			
			$("#botonContinuar").click(function() {
				location.assign("./objetivos.php");
			});
			
			$("select").combobox();*/
			
			
			
			var actualizarTabla = function() {
				//formValues = $('form.filtros').serialize();
				//console.log(formValues);
				
				var materia = <?php echo $cod; ?>;
				$('div.comisiones').load("fuentes/AJAX.php?act=tablaComisionesCalendario&materia=" + materia, function(data) {
					$('form.asignarDocente').submit(function(event) {
						event.preventDefault();
						var values = $(this).serialize();
						
						$.get("fuentes/AJAX.php?act=agregarAsignacionComisionCalendario", values, function(data) {
							
						});
						
						
					});				
				});
			} 
			actualizarTabla();
			
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
