<!DOCTYPE html>
<html>
	<head>
		
		<title>Autoevaluación CONEAU</title>
		<?php 
			require_once('./fuentes/meta.html');
			
			require 'programas.autoloader.php';
			
			include './fuentes/constantes.php';
			require 'fuentes/conexion.php';
			//$programa = new clases\Programa($_SESSION['materia'], $_SESSION['id']);
			//$campos = $programa->mostrarCampo($ANIO, $CUATRIMESTRE);
			$materia = $_SESSION['materia'];
			$materia = new clases\Materia($materia);
			$conjunto = $materia->mostrarConjunto();
			//print_r($_SESSION);
			//echo $materia;
			
			$query = "SELECT respuesta, pregunta
						FROM autoevaluacion_coneau
						WHERE materia IN {$conjunto}";
			$result = $mysqli->query($query);
			echo $mysqli->error;
			$respuestas = array();
			foreach (['coneau91', 'coneau92', 'coneau93', 'coneau94'] as $pregunta) {
				$respuestas[$pregunta] = "";
			}
			while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$respuestas[$row['pregunta']] = $row['respuesta'];
			}
			
		?>
	</head>
	
	<body>
		<?php
			require_once('./fuentes/botonera.php');
			require("./fuentes/panelNav.php");
		?>
		<div class="formularioLateral">
			<div id="accordion">
				<h3 class="formularioLateral">Analizar y evaluar la suficiencia y adecuación de los 
					ámbitos donde se dearrolla la actividad: aulas, equipamiento didáctico, 
					equipamiento informático, otros; y su disponibilidad para todos los alumnos.
				</h3>
				<div id="formulario">
					<fieldset class="formularioLateral">
						<form method="post" class="formularioLateral" action="procesardatos.php?formulario=autoevaluacion_coneau&pregunta=coneau91" data-pregunta='coneau91'>
							<textarea class="formularioLateral" name="respuesta" placeholder="" style="height:150px;"><?php echo $respuestas['coneau91']; ?></textarea>
						</form>
						
					</fieldset>
				</div>
				<h3 class="formularioLateral">Analizar los datos de la inscripción y promoción de los alumnos. 
					Explicar los datos destacados y enunciar causas probables.
				</h3>
				<div id="formulario">
					
					<fieldset class="formularioLateral">
						<form method="post" class="formularioLateral" action="procesardatos.php?formulario=autoevaluacion_coneau&pregunta=coneau92" data-pregunta='coneau92'>
							<textarea class="formularioLateral" name="respuesta" placeholder="" style="height:150px;"><?php echo $respuestas['coneau92']; ?></textarea>
						</form>
							<img src="images/chart_example.png" />
					</fieldset>
				</div>
				<h3 class="formularioLateral">Analizar y evaluar la composición del equipo docente a cargo de la actividad para llevar adelante
					las funciones de docencia, investigación, extensión y vinculación inherentes a los cargos que han sido designados
				</h3>
				<div id="formulario">
					<fieldset class="formularioLateral">
						<form method="post" class="formularioLateral" action="procesardatos.php?formulario=autoevaluacion_coneau&pregunta=coneau93" data-pregunta='coneau93'>
							<textarea class="formularioLateral" name="respuesta" placeholder="" style="height:150px;"><?php echo $respuestas['coneau93']; ?></textarea>
						</form>
						
					</fieldset>
				</div>
				<h3 class="formularioLateral">Describir las acciones, reuniones, comisiones en las que participa el equipo docente para trabajar
					sobre la articulación vertical y horizontal de los contenidos y la formación.
				</h3>
				<div id="formulario">
					<fieldset class="formularioLateral">
						<form method="post" class="formularioLateral" action="procesardatos.php?formulario=autoevaluacion_coneau&pregunta=coneau94" data-pregunta='coneau94'>
							<textarea class="formularioLateral" name="respuesta" placeholder="" style="height:150px;"><?php echo $respuestas['coneau94']; ?></textarea>
						</form>
						
					</fieldset>
				</div>
			</div>
		</div>
		
	</body>
	  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	  <script>
	  $( function() {
		$( "#accordion" ).accordion({
			collapsible: true,
			heightStyle: "content"
		});
	  } );
	  
	  $(document).ready(function() {
		  $('form').submit(function(event) {
			  event.preventDefault();
			  var values = $(this).serialize();
			  var pregunta = $(this).data('pregunta');
			  $.post("procesardatos.php?formulario=autoevaluacion_coneau&pregunta=" + pregunta, 
					values, 
					function(data) {}
			  );
		  });
		  
	  });
	  
	  $('form').keyup(function() {
		  $(this).submit();
	  });
	  </script>
	  <style>
		textarea {
			height: 100px;
		}
	  </style>
</html>
