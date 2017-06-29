<!DOCTYPE html>
<html>
	<head>
		
		<title>Asignación de aulas</title>
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
			<h2 class="formularioLateral">Asignación de aulas</h2>
			<div id="mostrarFormulario">Mostrar Formulario</div>
			<div id="formulario">
				<fieldset class="formularioLateral">
					<form method="post" class="formularioLateral" action="procesardatos.php?formulario=equipoDocente" id="formulario_asignacion">
						<label class="formularioLateral" for="aula">Aula:</label>
						<select class="formularioLateral iconCod" name="aula" required="required" id="aula"/>
							<option value=""></option>
							<?php
								require './fuentes/conexion.php';
								
								$query = "SELECT id, cod, capacidad FROM aulas WHERE activo = 1";
								$result = $mysqli->query($query);
								
								while($row = $result->fetch_array(MYSQLI_ASSOC) ) {
									echo "<option value='$row[id]'>$row[cod] ($row[capacidad] Alumnos)</option>";
								}
								
								$result->free();
							?>
						</select>
						<br />
						<label class="formularioLateral" for="conjunto">Materia:</label>
						<select class="formularioLateral iconCod" name="conjunto" required="required" id="conjunto">
						<option value=""></option>
							<?php
								require './fuentes/conexion.php';
								
								$query = "SELECT conjunto, LEFT(CONCAT(conjunto, ' ', GROUP_CONCAT(nombre SEPARATOR '/')), 75) as nombre
											FROM materia 
											GROUP BY conjunto
											ORDER BY plan, carrera, conjunto ";
								$result = $mysqli->query($query);
								
								while($row = $result->fetch_array(MYSQLI_ASSOC) ) {
									echo "<option value='$row[conjunto]' style='width:700px;'>$row[nombre]</option>";
								}
								
								$result->free();
							?>
						</select>
						<br />
						<label class="formularioLateral" for="cantidad">Cantidad: </label>
						<input name="cantidad" class="formularioLateral iconNombre"  required="required" id="cantidad" type="number" min="0" max="200" >
						<br />
						<label for='dia' class='formularioLateral'>Día:</label>
						<select id="dia" class='formularioLateral' name='dia'>
							<option value=""></option>
							<option class='formularioLateral' value='lunes'>Lunes</option>
							<option class='formularioLateral' value='martes'>Martes</option>
							<option class='formularioLateral' value='miércoles'>Miércoles</option>
							<option class='formularioLateral' value='jueves'>Jueves</option>
							<option class='formularioLateral' value='viernes'>Viernes</option>
							<option class='formularioLateral' value='sábado'>Sábado</option>
						</select>
						<br />
						<label for='turno' class='formularioLateral'>Turno:</label>
						<select id='turno' class='formularioLateral' name='turno'>
							<option value=""></option>
							<option class='formularioLateral' value='M'>M</option>
							<option class='formularioLateral' value='M1'>M1</option>
							<option class='formularioLateral' value='M2'>M2</option>
							<option class='formularioLateral' value='T'>T</option>
							<option class='formularioLateral' value='T1'>T1</option>
							<option class='formularioLateral' value='T2'>T2</option>
							<option class='formularioLateral' value='N'>N</option>
							<option class='formularioLateral' value='N1'>N1</option>
							<option class='formularioLateral' value='N2'>N2</option>
							<option class='formularioLateral' value='S'>S</option>
						</select>
						<br />
						<label for='comision' class='formularioLateral'>Comisión:</label>
						<select id='comision' class='formularioLateral' name='comision'>
							<option class='formularioLateral' value='A'>A</option>
							<option class='formularioLateral' value='B'>B</option>
							<option class='formularioLateral' value='C'>C</option>
							<option class='formularioLateral' value='D'>D</option>
							<option class='formularioLateral' value='E'>E</option>
						</select>
						<br />
						<label for='cuatrimestre' class='formularioLateral'>Cuatrimestre:</label>
						<input type='number' max='2' min='1' value='2' id="cuatrimestre" class='formularioLateral' name="cuatrimestre">
						<label for='anio' class='formularioLateral anios'>Año:</label>
						<input type='number' value='2015' id="anio" class='formularioLateral anios' name="anio">
						<br />
						<button type="submit" class="formularioLateral iconAgregar" id="guardarCargarOtro">Guardar y cargar otra</button>
					</form>
				</fieldset>
			</div>
			
		
			<hr>
		
		
			<div id="plantelActual">
				<table class="plantelActual">
					<tr class="plantelActual">
						<th class="plantelActual" style="width:7%;">Aula</th>
						<th class="plantelActual" style="width:35%;">Materia</th>
						<th class="plantelActual" style="width:7%;">Cantidad</th>
						<th class="plantelActual" style="width:16%;">Día</th>
						<th class="plantelActual" style="width:10%;">turno</th>
						<th class="plantelActual" style="width:10%;">comisión</th>
						<th class="plantelActual" style="width:5%;">Eliminar</th>
					</tr>
					<?php
						require('./conexion.php');
						
						$cantidadResultados = (isset($_GET['cantidadResultados'])) ? $_GET['cantidadResultados'] : 10;
						$pagina = (isset($_GET['pagina'])) ? (($_GET['pagina'] - 1) * $cantidadResultados) : 0;
						$hasta = $pagina + $cantidadResultados;
						
						$result = $mysqli->query("SELECT COUNT(id) FROM docente WHERE activo = 1");
						$totalPaginas = $result->fetch_row();
						$result->free();
						
						$totalPaginas = $totalPaginas[0] / $cantidadResultados;
						
						$query = "SELECT aa.aula, CONCAT(aa.materia, '<br/>\n', GROUP_CONCAT(m.nombre ORDER BY m.nombre SEPARATOR '<br />\n')) as materia,
							aa.cantidad_alumnos, aa.dia, aa.turno, aa.comision, aa.id
										
									FROM asignacion_aulas as aa
									LEFT JOIN materia AS m ON m.conjunto = aa.materia
									WHERE aa.activo = 1 
									GROUP BY aa.id
									ORDER BY dia, LEFT(turno, 1), aula + 0
									LIMIT $pagina, $cantidadResultados";
						$result = $mysqli->query($query);
						echo $mysqli->error;
						
						while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
							
							echo "<tr class='formularioLateral plantelActual aulasAsignadas'>";
							
							foreach ($row as $key => $value) {
								if ($key != 'id') {
									echo "<td class='formularioLateral plantelActual'>$value</td>";
								}
							}
							
							echo "<td class='formularioLaterial eliminarEnTabla'><button type='button' class='formularioLateral botonEliminar' id='eliminarDocente' data-id='$row[id]'>X</button>";
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
	<?php require "./fuentes/jqueryScripts.html"; ?>
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
			
			$("#formulario_asignacion").submit(function(event) {
				event.preventDefault();
				formValues = $(this).serialize();
				
				$.post("./fuentes/AJAX.php?act=agregarAsignacionDeAula", formValues, function(data) {
					if (data) {
						alert("Error: " + data);
					} else {
						location.reload();
					}
				});
				
			});
			
			$(".datepicker").datepicker({
				changeMonth:true,
				changeYear:true,
				dateFormat:'yy-mm-dd',
				yearRange:'c-80:c',
				
			});
			
			$('.botonEliminar').click(function() {
				id = $(this).data('id');
				$.post("./fuentes/AJAX.php?act=eliminarAsignacionDeAula", {"id":id }, function(data) {
					//alert(data);
					location.reload();
				});
			});
			
			$('#mostrarFormulario').click(function() {
				$('div #formulario').toggle();
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
		
		$('select.formularioLateral').combobox();
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
  </style>
</html>
