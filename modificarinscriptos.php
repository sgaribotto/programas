<link rel="stylesheet" type="text/css" href="./css/general.css">
	<img src="./images/logo_eeyn.png" class="logo-print print-only" Alt="EEYN - UNSAM"/>
	
		<?php
			header('Content-Type: text/html; charset=utf-8');
			require_once 'programas.autoloader.php';
			include './fuentes/constantes.php';
			
			$periodo = $_REQUEST['periodo'];
			
			$materia = new clases\Materia($_REQUEST['materia']);
			$carrera = $materia->datosMateria['cod_carrera'];
			$conjunto = $materia->mostrarConjunto();
			$carreras = $materia->mostrarCarreras();
			
			$codigosConjunto = $materia->mostrarCodigosConjunto();
			
			require 'fuentes/conexion.php';
			
			$query = "SELECT  turno, cantidad
						FROM estimacion
						WHERE CONCAT(anio, ' - ', cuatrimestre) = '{$periodo}'
							AND materia = '{$conjunto}';";
			$result = $mysqli->query($query);
			
			echo $mysqli->error;
			
			$inscriptos = array();
			while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$inscriptos[$row['turno']] = $row['cantidad'];
			}	
			
		?>
		
		<?php if(!isset($_GET['print'])) { 
			session_start();
			$animationEvent = "true";
		?>
			<a href='resumenmateria.php?print=1&materia=<?php echo $materia->mostrarCod();?>' 
					class='no-print' target='_new'>Imprimir</a>
		<?php } else { 
			require 'fuentes/meta.html';
			$animationEvent = "false";
			
			?>
			<style>
				div.chart {
					width: 720;
				}
			</style>
			<script>$(document).ready(function() { window.print(); });</script>
		<?php } ?>
			
		<div class="">
			<h2 class="formularioLateral tituloMateria">
				<?php 
					echo "<span id='spanConjunto'>";
					echo $materia->mostrarConjunto();
					echo "</span><span id='spanNombre'>";
					echo $materia->mostrarNombresConjunto();
					echo "</span>";
				?>
			</h2>
			
			<h4 class="formularioLateral periodo">Periodo: <span id='spanPeriodo'>
					<?php echo $periodo; ?>
				</span>
			</h4>
			
			<h4 class='formularioLateral carreras' style="text-decoration:none;">Carreras:</h4>
			<ul class='formularioLateral carreras' >
				<?php
					foreach ($carreras as $carrera) {
						echo "<li class='carreras'>{$carrera}</li>";
					}
				?>
			</ul>
			
 			
			
			<h4 class='formularioLateral listadoComsiones' style="text-decoration:underline;">Cantidad de inscriptos</h4>
			<form id="modificacionInscriptos" method="post" action="">
			<table class='aceptarDesignacion'><thead class='aceptarDesignacion'>
						<tr class='plantelActual' style="text-align:left;">
							
							<th class='aceptarDesignacion' style='width:10%;'>Mañana</th>
							<th class='aceptarDesignacion' style='width:10%;'>Noche</th>
							<th class='aceptarDesignacion' style='width:10%;'>Tarde</th>
							<th class='aceptarDesignacion' style='width:20%;'>Aceptar</th>
							<!-- <th class='aceptarDesignacion' style='width:25%;'>Estado</th> -->
						</tr></thead>
				
					<tbody class="tablaInfo" style="width:80%;">
						<tr class='plantelActual' style="text-align:left;">
							<!--<td class='aceptarDesignacion' style='width:35%;'>Materia</td>
							<td class='aceptarDesignacion' style='width:10%;'>Carrera</td>-->
							
								<td class='aceptarDesignacion' style='width:10%;'>
									<input id="inscriptosM" type="number" min="0" name="M" value="<?php echo $inscriptos['M']; ?>"/>
								</td>
								<td class='aceptarDesignacion' style='width:10%;'>
									<input id="inscriptosN" type="number" min="0" name="N" value="<?php echo $inscriptos['N']; ?>"/>
								</td>
								<td class='aceptarDesignacion' style='width:10%;'>
									<input id="inscriptosT" type="number" min="0" name="T" value="<?php echo $inscriptos['T']; ?>"/>
								</td>
								<td class='aceptarDesignacion' style='width:20%;'>
									<button id="aceptarCambiosInscriptos" type="submit">Modificar</button>
								</td>
							
						</tr></thead>
					</tbody>
				
			</table>
			</form>
		</div>
		
	<script src="./fuentes/funciones.js"></script>
	<script>
		$(document).ready( function() {
			
			$('#modificacionInscriptos').submit(function(event) {
				event.preventDefault();
				
				var formValues =new Object();
				formValues.M = $('#inscriptosM').val();
				formValues.N = $('#inscriptosN').val();
				formValues.T = $('#inscriptosT').val();
				formValues.materia = $('#spanConjunto').text();
				formValues.periodo = $('#spanPeriodo').text();
				formValues.nombre = $('#spanNombre').text();
				console.log(formValues);
				console.log(formValues);
				$.post("./fuentes/AJAX.php?act=cambiarEstimacionInscriptos", formValues, function(data) {
					//console.log(data);
				});
			});
			
			
			
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
	  .resaltar {
		  color:#D4A190;
		  font-weight:bold;
	  }
	  
  </style>

