<!DOCTYPE html>
<html>
	<Head>
		<?php 
			include 'meta.php'; 
		?>
		<title>	Consulta Equivalencias cambio al plan 2014</title>
		
		<style media="screen" type="text/css">
			fieldset{margin:5px; width:550px;}
			label {font-size:1.05em; text-align:left;float:left;width:180px;}
			input {text-align:left;width:160px;font-size:0.9em;}
			label.comentarios{width:150px;}
			input.comentarios{width:260px;}
		</style>
		
	</Head>
	<body>
		<?php //header('location:Aviso.php'); ?>
		<?php include 'botonera.php' ?>
		<h1 class="normal"> Consulta Cambio Plan de Estudios</h1>
		<p class="normal">La presente página le permite conocer cuál es su situación frente al cambio del plan 1999 al plan 2014</p>
		
		
			<fieldset  class="normal formularioConsulta">
				
				
				<form method="post" action="Resultados.php">
					<div class="camposFormularioConsulta">
						<label for="DNI" class="formularioConsulta">Ingrese su DNI:</label>
							<input type="text" name="DNI" id="IngDNI" class="formularioConsulta" required="required" maxlength="10"><span class="formularioConsulta" >(Sin puntos. Ej: 12345678)</span><br>
						<br />
						<!--<label for="origen" class="formularioConsulta">Estoy inscripto en:</label>
							<select name="origen" class="formularioConsulta">
								<option class="formularioConsulta" value="eyn-3" selected="selected">Licenciatura en Administración</option>
								<option class="formularioConsulta" value="eyn-4">Licenciatura en Economía</option>
							</select>
						<br />
						<label for="destino" class="formularioConsulta">Quiero pasar a:</label>
							<select name="destino" class="formularioConsulta">
								<option class="formularioConsulta" value="eyn-3" selected="selected">Licenciatura en Administración</option>
								<option class="formularioConsulta" value="eyn-4">Licenciatura en Economía</option>
							</select>-->
							
						<div id="radio">
							<input type="radio" class="formularioConsulta" value="eyn-3" checked="checked" id="radio1" name="Carrera"><label for="radio1" class="radioLabel">Lic. Administración</label>
							<input type="radio" class="formularioConsulta" value="eyn-4" id="radio2" name="Carrera"><label for="radio2" class="radioLabel">Lic. Economía</label>
						</div>
					</div>
					<br />
					<div>
						<button type="Submit" class="formularioConsulta">Consultar Situación Cambio de Plan</button>
					</div>
				</form>
				
			</fieldset>
		
		<p class="normal"> Para concretar el trámite de cambio de plan debe presentar completo el siguiente <a href="Docs/FORMULARIOCAMBIOPLAN.pdf" style="color:red">formulario</a>.</p>
		<p class="normal"> Ante cualquier consulta dirigirse a la Dirección de Asuntos Académicos en su horario de atención: 8:30hs a 12:30hs o 17:00hs a 20:00hs.</p>
		<p class="normal"> Consultas telefónicas al 4580-7250 int. 124 </p>

	<?php
		include 'footer.php';
	?>
	
	  <script>
		  $(document).ready(function() {
			
			$( "button" ).button();
			  
		    $( "#radio" ).buttonset();
			
		  });
  </script>
	</body>
</html>
