<!DOCTYPE html>
<html>
	<head>
		
		<title>Campus Virtual</title>
		<?php 
			require_once('./fuentes/meta.html');
			
			require 'programas.autoloader.php';
			
			include './fuentes/constantes.php';
			$programa = new clases\Programa($_SESSION['materia'], $_SESSION['id']);
			$campos = $programa->mostrarCampo($ANIO, $CUATRIMESTRE);
		?>
	</head>
	
	<body>
		<?php
			require_once('./fuentes/botonera.php');
			require("./fuentes/panelNav.php");
		?>
		<div class="formularioLateral">
			<h2 class="formularioLateral">Campus Virtual</h2>
			<div id="formulario">
				<fieldset class="formularioLateral">
					<form method="post" class="formularioLateral" action="procesardatos.php?formulario=campusvirtual">
						<textarea class="formularioLateral" name="campusvirtual" placeholder="Uso del Campus Virtual"><?php $detalle = (isset($campos['campusvirtual'])) ? $campos['campusvirtual'] : ""; echo $detalle; ?></textarea>
						<button type="submit" class="formularioLateral iconGuardar">Guardar y continuar</button>
					</form>
					
				</fieldset>
			</div>
		</div>
		
	</body>
</html>
