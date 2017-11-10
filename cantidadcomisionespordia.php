<!DOCTYPE html>
<html>
	<head>
		
		<title>Cantidad de comisiones abiertas por día</title>
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
			require "./fuentes/constantes.php";
			
			$PERIODO = $ANIO . ' - ' . $CUATRIMESTRE;
		?>
		<div class="formularioLateral">
			<h2 class="formularioLateral">Cantidad de comisiones abiertas por día</h2>
			<div id="mostrarFormulario">Mostrar Formulario</div>
			<div id="formulario">
				<fieldset class="formularioLateral">
					<form method="post" class="formularioLateral" action="#" id="formularioCarga" >
						
						<label class="formularioLateral" for="periodo">Periodo: </label>
						<select name="periodo" class="formularioLateral" class="periodo" id="periodo">
							<?php
								require 'fuentes/conexion.php';
								
								$query = "SELECT DISTINCT CONCAT(anio, ' - ', cuatrimestre) AS periodo
											FROM comisiones_abiertas
											ORDER BY anio DESC, cuatrimestre DESC
											LIMIT 10";
								$result = $mysqli->query($query);
								
								while ($row = $result->fetch_array(MYSQL_ASSOC)) {
									$selected = "";
									
									if ($row['periodo'] == $PERIODO) {
										$selected = 'selected';
									}
									echo "<option value='{$row['periodo']}' $selected>{$row['periodo']}</option>";
								}
								
								$mysqli->close();
							?>
						</select>
						<br />
						
						<!--<button type="submit" class="formularioLateral" id="guardarCargarOtro">Mostrar resultado</button>-->
					</form>
				</fieldset>
			</div>
			
		
			<hr>
			
			<div class="tabla"></div>
		
			
		
	</body>
	<?php require "./fuentes/jqueryScripts.html"; ?>
	<script src="./fuentes/funciones.js"></script>
	
	<script>
		$(document).ready(function() {
			
			$('#mostrarFormulario').click(function() {
				$('div #formulario').toggle();
			});
			
			$('#periodo').change(function(event) {
				var periodo = $(this).val();
				//console.log(periodo);
				//alert(periodo);
				$('div.tabla').load("./fuentes/AJAX.php?act=tablaComisionesPorDia", 
					{'periodo': periodo}, function(data) {
						
					
				});
			});
			
			$('#periodo').change();
			
		});
	</script>
	

</html>
