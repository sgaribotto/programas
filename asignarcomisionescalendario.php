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
			$responsables = $materia->mostrarEquipoDocente($ANIO, $CUATRIMESTRE, true, ['titular', 'adjunto', 'asociado']);
			$coordinador = $materia->mostrarCoordinador($ANIO, $CUATRIMESTRE);
			//print_r($coordinador);
			
		?>
		
	</head>
	
	<body>
		
		<?php
			require_once('./fuentes/botonera.php');
			require("./fuentes/panelNav.php");
		?>
		
		
		
		<div class="formularioLateral">
			<h1 class="nombreMateria"><span class="conjuntoMateria"><?php echo $conjunto; ?></span> <?php echo $nombres?></h1>
			<h2 class="formularioLateral">Asignar comisiones del periodo <?php echo "{$ANIO} - {$CUATRIMESTRE}";?></h2>
			<p class="error"></p>
			<label for="coordinador" name="coordinador">Coordinador de la materia: </label>
			<select name="coordinador" class="formularioLaterial coordinador">
				<option value=''>Seleccionar Coordinador</option>
				<?php
					foreach ($responsables as $posicion => $datos) {
						$selected = "";
						
						if ($datos['id_docente'] == $coordinador['id']) {
							$selected = "selected = 'selected'";
						}
						
						echo "<option value='{$datos['id_docente']}' $selected>{$datos['docente']} - {$datos['tipoafectacion']}</option>";
					}
				?>
			</select>
			<div class="tutorial" style="border: 1px solid black;">
				<h3 class="tutorial" >Tutorial</h3>
				<ul>
					<li>Seleccione el docente en el horario deseado.</li>
					<li>Presione el botón "+" y el docente aparecerá debajo.</li>
					<li>Si desea cambiar al docente seleccionado, presione la "X" a la derecha de su nombre.</li>
					<li>Una vez seleccionados los docentes, indique con un tilde si utilizará Aula Virtual.</li>
				</ul>
				<img src="images/seldocentes.gif" style="width:80%;"/>
			</div>
			<br>
			<div class="comisiones">
			</div>
		</div>
	</body>
	<script src="./fuentes/funciones.js"></script>
	<script>
		$(document).ready(function() {

			var actualizarTabla = function() {
				//formValues = $('form.filtros').serialize();
				//console.log(formValues);
				
				var materia = <?php echo $cod; ?>;
				$('div.comisiones').load("fuentes/AJAX.php?act=tablaComisionesCalendario&materia=" + materia, function(data) {
					
					$('form.asignarDocente').submit(function(event) {
						event.preventDefault();
						
						var values = $(this).serialize();
						
						$.get("fuentes/AJAX.php?act=agregarAsignacionComisionCalendario", values, function(data) {
							actualizarTabla();
							data = JSON.parse(data);
							if (data.error) {
								$('p.error').text(data.error);
							}
							
						});
					});
					
					$('button.eliminarAsignacionCalendario').click(function(event) {
						event.preventDefault();
						var id = $(this).data('id');
						$.get("fuentes/AJAX.php?act=eliminarAsignacionComisionCalendario", {'id': id}, function(data) {
							actualizarTabla();
						});
					});	
					
					$('.aulavirtual').change(function() {
						var comision = $(this).data('comision');
						var materia = $(this).data('materia');
						var check = 0;
						if (this.checked) {
							check = 1;
						}
						$.get("./fuentes/AJAX.php?act=asignarAulaVirtualCalendario", {"comision": comision, "check": check, "materia": materia }, function(data1) {
							//console.log(id);
							
							
						});
					});	
				});
			} 
			actualizarTabla();
			
			$('select.coordinador').change(function(event) {
				//alert('change');
				event.preventDefault();
				
				var docente = $(this).val();
				var comision = 'Coord';
				var dia = 'todos';
				var turno = '';
				
				var values = {'comision': comision, 'dia': dia, 'turno': turno, 'docente': docente};
				console.log(values);
				$.get("fuentes/AJAX.php?act=agregarAsignacionComisionCalendario", values, function(data) {
							
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
  </style>
</html>
