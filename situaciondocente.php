		<?php
			header('Content-Type: text/html; charset=utf-8');
			require_once 'programas.autoloader.php';
			include './fuentes/constantes.php';
			$docente = new clases\Docente($_REQUEST['docente']);
			//$ANIO = 2017;
			
			
	
			$datosDocente = array(
				'personales' => $docente->mostrarDatosDocente(),
				'contacto' => $docente->mostrarDatosContacto(),
				'afectaciones' => $docente->mostrarComisionesAsignadas($ANIO),
				'designaciones' => $docente->mostrarDesignaciones($ANIO - 1, $CUATRIMESTRE),
				'renovacion' => $docente->mostrarDesignaciones($ANIO, $CUATRIMESTRE),
			);
			$dni = $datosDocente['personales']['dni'];
			session_start();
			
			
		?>

		<div class="">
			<h2 class="formularioLateral tituloMateria">
				<?php 
					echo $datosDocente['personales']['apellido'];
					echo ", ";
					echo $datosDocente['personales']['nombres']; 
					echo "  (DNI: <span class='dni'>{$dni}</span>)";
				?>
			</h2>
			<br />
			
			<h3 class="formularioLateral tablaInfo" style="margin:0;">Asignaciones <?php echo $ANIO; ?></h3>	
			
			<table class='aceptarDesignacion'><thead class='aceptarDesignacion'>
						<tr class='plantelActual'>
							<th class='aceptarDesignacion' style='width:15%;text-align: left;'>Periodo</th>
							<th class='aceptarDesignacion' style='width:55%;text-align: left;'>Materia</th>
							<!--<th class='aceptarDesignacion' style='width:10%;'>Carrera</th>-->
							
							<th class='aceptarDesignacion' style='width:20%;text-align: left;'>Cargo</th>
							<th class='aceptarDesignacion' style='width:10%;text-align: left;'>Comision</th>
							<!-- <th class='aceptarDesignacion' style='width:25%;'>Estado</th> -->
						</tr></thead>
				<tbody class="tablaInfo" style="width:100%;">
				<?php 
					foreach ($datosDocente['afectaciones'] as $afectacion) {
						
						echo "<tr class='aceptarDesignacion'>
						<td class='aceptarDesignacion'>{$afectacion['anio']} - {$afectacion['cuatrimestre']}</td>
								<td class='aceptarDesignacion'>$afectacion[conjunto] $afectacion[materia]</td>
								<td class='aceptarDesignacion'>$afectacion[tipoafectacion]</td>
								<td class='aceptarDesignacion'>$afectacion[comision]</td>
							</tr>";
					} ?>
				</tbody>
			</table>
			
			
			
		</div>
		
		<?php if (in_array(2, $_SESSION['permiso'])) { //SOLO SA ?>
			<hr />
			<div class="infoDesignacion">
				<h2 class="subtitulo">Designaciones <?php echo ($ANIO - 1); ?> </h2>
				<table class="designacion">
				<tr>
					<th>Dedicación</th>
					<th>Categoría</th>
					<th>Caracter</th>
					<th>Alta</th>
					<th>Baja</th>
					<th>Horas</th>
					<th>Cuatrimestres</th>
				</tr>
				<?php
					$totalHoras = array(
						'1' => 0,
						'2' => 0,
						);
						
					foreach ($datosDocente['designaciones'] as $designacion) {
						if ($designacion['cuatrimestres'] == 'primero' or
								$designacion['cuatrimestres'] == 'ambos') {
							$totalHoras[1] += $designacion['horas_requeridas'];
						}
						
						if ($designacion['cuatrimestres'] == 'segundo' or
								$designacion['cuatrimestres'] == 'ambos') {
							$totalHoras[2] += $designacion['horas_requeridas'];
						}
						
						
						echo "<tr class='designacion'>";
						echo "<td>{$designacion['dedicacion']}</td>"; 
						echo "<td>{$designacion['categoria']}</td>"; 
						echo "<td>{$designacion['caracter']}</td>"; 
						echo "<td>{$designacion['fecha_alta']}</td>"; 
						echo "<td>{$designacion['fecha_baja']}</td>"; 
						echo "<td>{$designacion['horas_requeridas']}</td>";
						echo "<td>{$designacion['cuatrimestres']}</td>";
						echo "</tr>";
					}
					
					echo "<tr><td colspan='5' style='text-align:right;'>Total Horas Primer Cuatrimestre</td>
							<td>{$totalHoras[1]}</td></tr>";
					echo "<tr><td colspan='5' style='text-align:right;'>Total Horas Segundo Cuatrimestre</td>
							<td>{$totalHoras[2]}</td></tr>";
				?>
			</table>
			</div>
			<hr />
			
			<div class="infoRenovacion">
				<h2 class="subtitulo">Renovacion - Alta - Baja - Modificación <?php echo ($ANIO); ?> </h2>
				<table class="designacion">
				<tr>
					<th>Dedicación</th>
					<th>Categoría</th>
					<th>Caracter</th>
					<th>Alta</th>
					<th>Baja</th>
					<th>Horas</th>
					<th>Cuatrimestres</th>
				</tr>
				<?php
					$totalHoras = array(
						'1' => 0,
						'2' => 0,
						);
						
					foreach ($datosDocente['renovacion'] as $designacion) {
						if ($designacion['cuatrimestres'] == 'primero' or
								$designacion['cuatrimestres'] == 'ambos') {
							$totalHoras[1] += $designacion['horas_requeridas'];
						}
						
						if ($designacion['cuatrimestres'] == 'segundo' or
								$designacion['cuatrimestres'] == 'ambos') {
							$totalHoras[2] += $designacion['horas_requeridas'];
						}
						
						
						echo "<tr class='designacion'>";
						echo "<td>{$designacion['dedicacion']}</td>"; 
						echo "<td>{$designacion['categoria']}</td>"; 
						echo "<td>{$designacion['caracter']}</td>"; 
						echo "<td>{$designacion['fecha_alta']}</td>"; 
						echo "<td>{$designacion['fecha_baja']}</td>"; 
						echo "<td>{$designacion['horas_requeridas']}</td>";
						echo "<td>{$designacion['cuatrimestres']}</td>";
						echo "<td ><button type='button' data-id='{$designacion['id']}' class='eliminarDesignacion'>X</button></td>";
						echo "</tr>";
					}
					
					echo "<tr><td colspan='5' style='text-align:right;'>Total Horas Primer Cuatrimestre</td>
							<td>{$totalHoras[1]}</td></tr>";
					echo "<tr><td colspan='5' style='text-align:right;'>Total Horas Segundo Cuatrimestre</td>
							<td>{$totalHoras[2]}</td></tr>";
				?>
				<tr>
					<form class="renovacion" method="post" action="fuentes/AJAX.php?act=AgregarDesignacion">
						<td>
							<select name='dedicacion'>
								<?php
									$query = "SELECT DISTINCT dedicacion
												FROM dedicacion
												ORDER BY dedicacion;";
									$result = $mysqli->query($query);
									while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
										echo "<option value='{$row['dedicacion']}'>{$row['dedicacion']}</option>";
									}
								?>
							</select>
						</td>
						<td>
							<select name='categoria'>
								<?php
									$query = "SELECT DISTINCT categoria AS value
												FROM designacion
												ORDER BY value;";
									$result = $mysqli->query($query);
									while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
										echo "<option value='{$row['value']}'>{$row['value']}</option>";
									}
								?>
							</select>
						</td>
						<td>
							<select name='caracter'>
								<?php
									$query = "SELECT DISTINCT caracter AS value
												FROM designacion
												ORDER BY value;";
									$result = $mysqli->query($query);
									while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
										echo "<option value='{$row['value']}'>{$row['value']}</option>";
									}
								?>
							</select>
						</td>
						<td>
							<input type="date" id="fecha_alta" name="fecha_alta" required pattern="[0-9]{4}/[0-9]{2}/[0-9]{2}" placeholder='aaaa/mm/dd'>
						</td>
						<td>
							<input type="date" id="fecha_baja" name="fecha_baja" required pattern="[0-9]{4}/[0-9]{2}/[0-9]{2}" placeholder='aaaa/mm/dd'>
						</td>
						
						<td colspan='2'><textarea name='observaciones' placeholder='observaciones...'></textarea></td>
						<td><button type='submit'>+</button></td>
					</form>
				</tr>
			</table>
			<button class='renovarAnioAnterior'>Renovar todo el año anterior</button>
			
			</div>
			
			<hr />
			<h3 class="formularioLateral tablaInfo" style="margin:0;">Información personal</h3>			
			<ul class="datosContacto">
				<?php
					
					foreach ($datosDocente['contacto'] as $datos) {
						
						
						$anchor[0] = "";
						$anchor[1] = "";
						if (strpos($datos['tipo'], 'ail')) {
							$anchor[0] = "<a href='mailto:$datos[valor]'>";
							$anchor[1] = "</a>"; 
						} elseif (strpos($datos['tipo'], 'elefono')) {
							$string = (string)$datos['valor'];
							//var_dump($string);
							if (strlen($string) == 10) {
								$formato = "";
								$formato .= substr($string, 0, 3);
								$formato .= ' - ';
								$formato .= substr($string, 3, 3);
								$formato .= ' - ';
								$formato .= substr($string, 6, 4);
								$datos['valor'] = $formato;
							} else if (strlen($string) == 8) {
								$formato = "";
								$formato .= substr($string, 0, 4);
								$formato .= ' - ';
								$formato .= substr($string, 4, 7);
								$datos['valor'] = $formato;
							}
						}
								
						echo "<li class='datosContacto'>";
						echo $datos['tipo'] . ": ";
						echo $anchor[0];
						echo $datos['valor'];
						echo $anchor[1];
						echo "</li>";
					}
				?>
			</ul>
		<?php } ?> 
	<script src="./fuentes/funciones.js"></script>
	
	<script>
		$(document).ready(function() {
			$('form.renovacion').submit(function(event) {
				event.preventDefault();
				
				var values = $(this).serialize();
				var dni = $('span.dni').text();
				
				$.get("./fuentes/AJAX.php?act=agregarDesignacion&dni=" + dni, values, function(data) {
					
				});
			});
		});
	</script>
	
	
	<script>
		
	//COMBOBOX CON BÚSQUEDA
	  (function( $ ) {
		$.widget( "custom.combobox", {
		  _create: function() {
			this.wrapper = $( "<span>" )
			  .addClass( "custom-combobox" )
			  .insertAfter( this.element );
	 
			this.element.hide();
			this._createAutocomplete();
			this._createShowAllButton();
		  },
	 
		  _createAutocomplete: function() {
			var selected = this.element.children( ":selected" ),
			  value = selected.val() ? selected.text() : "";
	 
			this.input = $( "<input>" )
			  .appendTo( this.wrapper )
			  .val( value )
			  .attr( "title", "" )
			  .addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
			  .autocomplete({
				delay: 0,
				minLength: 0,
				source: $.proxy( this, "_source" )
			  })
			  .tooltip({
				tooltipClass: "ui-state-highlight"
			  });
	 
			this._on( this.input, {
			  autocompleteselect: function( event, ui ) {
				ui.item.option.selected = true;
				this._trigger( "select", event, {
				  item: ui.item.option
				});
			  },
	 
			  autocompletechange: "_removeIfInvalid"
			});
		  },
	 
		  _createShowAllButton: function() {
			var input = this.input,
			  wasOpen = false;
	 
			$( "<a>" )
			  .attr( "tabIndex", -1 )
			  .attr( "title", "Show All Items" )
			  .tooltip()
			  .appendTo( this.wrapper )
			  .button({
				icons: {
				  primary: "ui-icon-triangle-1-s"
				},
				text: false
			  })
			  .removeClass( "ui-corner-all" )
			  .addClass( "custom-combobox-toggle ui-corner-right" )
			  .mousedown(function() {
				wasOpen = input.autocomplete( "widget" ).is( ":visible" );
			  })
			  .click(function() {
				input.focus();
	 
				// Close if already visible
				if ( wasOpen ) {
				  return;
				}
	 
				// Pass empty string as value to search for, displaying all results
				input.autocomplete( "search", "" );
			  });
		  },
	 
		  _source: function( request, response ) {
			var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
			response( this.element.children( "option" ).map(function() {
			  var text = $( this ).text();
			  if ( this.value && ( !request.term || matcher.test(text) ) )
				return {
				  label: text,
				  value: text,
				  option: this
				};
			}) );
		  },
	 
		  _removeIfInvalid: function( event, ui ) {
	 
			// Selected an item, nothing to do
			if ( ui.item ) {
			  return;
			}
	 
			// Search for a match (case-insensitive)
			var value = this.input.val(),
			  valueLowerCase = value.toLowerCase(),
			  valid = false;
			this.element.children( "option" ).each(function() {
			  if ( $( this ).text().toLowerCase() === valueLowerCase ) {
				this.selected = valid = true;
				return false;
			  }
			});
	 
			// Found a match, nothing to do
			if ( valid ) {
			  return;
			}
	 
			// Remove invalid value
			this.input
			  .val( "" )
			  .attr( "title", value + " didn't match any item" )
			  .tooltip( "open" );
			this.element.val( "" );
			this._delay(function() {
			  this.input.tooltip( "close" ).attr( "title", "" );
			}, 2500 );
			this.input.autocomplete( "instance" ).term = "";
		  },
	 
		  _destroy: function() {
			this.wrapper.remove();
			this.element.show();
		  }
		});
		$("select.combo").combobox();
	  })( jQuery );
 

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

