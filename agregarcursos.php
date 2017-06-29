<!DOCTYPE html>
<html>
	<head>
		
		<title>Agregar Cursos</title>
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
			<h2 class="formularioLateral">Agregar Cursos para las aulas</h2>
			<div id="mostrarFormulario">Mostrar Formulario</div>
			<div id="formulario">
				<fieldset class="formularioLateral">
					<form method="post" class="formularioLateral" action="" id="agregarCursoExtension">
						<label class="formularioLateral" for="cod">Código:</label>
						<input type="text" class="formularioLateral iconCod" name="cod" required="required" id="cod"maxlength="10"/>
						<br />
						<label class="formularioLateral" for="nombre">Nombre: </label>
						<input name="nombre" class="formularioLateral iconNombre"  required="required" id="nombre" type="text" maxlength="100">
						<br />
						<label class="formularioLateral" for="anio">Año:</label>
						<input type="number" class="formularioLateral iconAnio" name="anio" required="required" id="anio" value="<?php echo $ANIO; ?>"/>
						<br />
						<label class="formularioLateral" for="cuatrimestre">Cuatrimestre:</label>
						<input type="number" min="1" max="2" class="formularioLateral iconPeriodo" name="cuatrimestre" required="required" id="anio" value="<?php echo $CUATRIMESTRE; ?>"/>
						<br />
						<label class="formularioLateral" for="cantidad">Cantidad de Inscriptos:</label>
						<input type="number" min="1" class="formularioLateral iconCod" name="cantidad" required="required" id="cantidad" value="1"/>
						<br />
						<label class="formularioLateral" for="turno">Turno: </label>
						<select name="turno" class="formularioLateral iconTurno" id="turno">
							<option class="formularioLateral" value="M">M</option>
							<option class="formularioLateral" value="N">N</option>
							<option class="formularioLateral" value="T">T</option>
						</select> 
						<br />
						<button type="submit" class="formularioLateral iconAgregar" id="guardarCargarOtro">Guardar y cargar otra</button>
					</form>
				</fieldset>
			</div>
			
		
			<hr>
		
		
			<div id="plantelActual">
				<table class="plantelActual">
					<tr class="plantelActual">
						<th class="plantelActual" style="width:10%;">Código</th>
						<th class="plantelActual" style="width:40%;">Nombre</th>
						<th class="plantelActual" style="width:20%;">turno</th>
						<th class="plantelActual" style="width:10%;">Cantidad</th>
						<th class="plantelActual" style="width:10%;">anio</th>
						<th class="plantelActual" style="width:10%;">cuatrimestre</th>
					</tr>
					<?php
						require('./conexion.php');
						
						$cantidadResultados = (isset($_GET['cantidadResultados'])) ? $_GET['cantidadResultados'] : 10;
						$pagina = (isset($_GET['pagina'])) ? (($_GET['pagina'] - 1) * $cantidadResultados) : 0;
						$hasta = $pagina + $cantidadResultados;
						
						$result = $mysqli->query("SELECT COUNT(id) FROM materia WHERE activo = 1");
						$totalPaginas = $result->fetch_row();
						$result->free();
						
						$totalPaginas = $totalPaginas[0] / $cantidadResultados;
						
						$query = "SELECT materia, nombre_materia, turno, cantidad, anio, cuatrimestre 
											FROM estimacion 
											WHERE es_regular = 0 
											ORDER BY anio DESC, cuatrimestre DESC, nombre_materia 
											LIMIT $pagina, $cantidadResultados";
						$result = $mysqli->query($query);
						
						while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
							
							echo "<tr class='formularioLateral plantelActual'>";
							
							foreach ($row as $value) {
								echo "<td class='formularioLateral plantelActual'>$value</td>";
							}
							
							//echo "<td class='formularioLaterial eliminarEnTabla'><button type='button' class='formularioLateral botonEliminar' id='eliminarDocente' data-cod='$row[cod]'>X</button>";
							echo "</tr>";
						}
						
						$result->free();
						$mysqli->close();

					?>
				</table>
				<ul class="linkPagina">
				<?php
					if ($totalPaginas > 1) {
						for ($i = 0; $i < $totalPaginas; $i++) {
							$url = $_SERVER['PHP_SELF'] . "?pagina=" . ($i + 1);
							echo "<li class='linkPagina'>
										<a href = $url>" . ($i + 1) . "</a>
									</li>";
							
						}
					}
				?>
				</ul>
			</div>
			
		</div>
		
	</body>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
	<script src="./fuentes/funciones.js"></script>
	
	<script>
		$(document).ready(function() {
			
			/*$('#unidad').change(function() {
				unidad = $('#unidad').val();
				if (unidad != "" ) {
					$.get("./fuentes/AJAX.php?act=mostrarDescripcionUnidadTematica", {"unidad":unidad}, function(data) {
						$('#descripcion').val(data);
					});
				}
			});*/
			
			$("#agregarCursoExtension").submit(function(event) {
				event.preventDefault();
				var formValues = $(this).serialize();
				
				$.post("./fuentes/AJAX.php?act=agregarCursoExtension", formValues, function(data) {
					
				});
			});
			
			/*$(".datepicker").datepicker({
				changeMonth:true,
				changeYear:true,
				dateFormat:'yy-mm-dd',
				yearRange:'c-80:c',
				
			});*/
			
			$('#cod').change(function() {
				cod = $('#cod').val();
				$.post("./fuentes/AJAX.php?act=buscarCurso", {"cod":cod, }, function(data) {
						/*if (data) {
							datosMateria = data.split(',');
							$('#cod').val(datosMateria[1]);
							$('#nombre').val(datosMateria[2]);
							$('#cuatrimestre').val(datosMateria[3]);
							$('#anio').val(datosMateria[4]);
							$('#cantidad').val(datosMateria[5]);
							$('#turno').val(datosMateria[5]);
						}*/
				});
			});
			
			$('.botonEliminar').click(function() {
				cod = $(this).data('cod');
				$.post("./fuentes/AJAX.php?act=eliminarMateria", {"cod":cod, }, function(data) {
					//alert(data);
					location.reload();
				});
			});
			
			$('#mostrarFormulario').click(function() {
				$('div #formulario').toggle();
			});
			
		});
	</script>
</html>
