<!DOCTYPE html>
<html>
	<head>
		
		<title>Inscriptos</title>
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
			<h2 class="formularioLateral">Importar Inscriptos</h2>
			<div id="mostrarFormulario">Mostrar filtros</div>
			<div id="copiarOferta">Copiar Oferta Académica</div>

					<form method="post" class="importarOferta" action="./fuentes/importararchivos.php?act=inscriptos" enctype="multipart/form-data">
						<label class="formularioLateral" for="importarA">Al periodo:</label>
						<select class="formularioLateral iconPeriodo" name="importarA" id="importarA"/>
									
							<?php
								require "fuentes/conexion.php";
								
								$query = "SELECT MAX(anio_academico) AS anio_max
											FROM inscriptos";
											
								$result = $mysqli->query($query);
								while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
									$anioMax = $row['anio_max'];
									
								}
								for ($anio = $anioMax + 1; $anio >= $anioMax; $anio--) {
									
									for ($cuatrimestre = 1; $cuatrimestre <= 2; $cuatrimestre++) {
										$periodo = $anio . ' - ' . $cuatrimestre;
										echo "<option class='formularioLateral' value='{$periodo}'>{$periodo}</option>";
									}
								}
								
								$result->free();
								$mysqli->close();
							?>
								 
						</select>
						<br />
						<label for="importar">Archivo</label>
						<input type="file" accept="text/txt" name="importar" required>
						<br />
						<button type="submit" class="formularioLateral">Importar Inscriptos</button>
					</form>
				</fieldset>
			</div>
			
			
		</div>
			
		
	</body>
	<?php require "./fuentes/jqueryScripts.html"; ?>
	<script src="./fuentes/funciones.js"></script>
	
	
	<style>
	td.materia {
		text.align: center;
	}
	</style>
</html>
