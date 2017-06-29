<!DOCTYPE html>
<html>
	<head>
		
		<title>Turnos</title>
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
		?>
		<div class="formularioLateral">
			<h2 class="formularioLateral">Turnos</h2>
			<div id="mostrarFormulario">Mostrar Formulario</div>
			<div id="formulario">
				<fieldset class="formularioLateral">
					<form method="post" class="formularioLateral" action="#" id="formularioCarga">
						
						<label class="formularioLateral" for="materia">Materia: </label>
						<select name="materia" class="formularioLateral iconMateria"  required="required" id="materia">
							<?php
								require './conexion.php';
								
								$query = "SELECT materia, nombre_materia 
											FROM estimacion
											WHERE es_regular = 0
											
											ORDER BY nombre_materia";
								$result = $mysqli->query($query);
								
								while ($row = $result->fetch_array(MYSQL_ASSOC)) {
									echo "<option class='formularioLateral' value='$row[materia]'>$row[materia] - $row[nombre_materia]</option>";
								}
								
								$result->free();
								$mysqli->close();
							?>
						</select>
						<br />
						<label class="formularioLateral" for="dia">Día:</label>
						<select class="formularioLateral iconTurno" name="dia" required="required" id="dia">
							<option class="formularioLateral" value="lunes">Lunes</option>
							<option class="formularioLateral" value="martes">Martes</option>
							<option class="formularioLateral" value="miercoles">Miércoles</option>
							<option class="formularioLateral" value="jueves">Jueves</option>
							<option class="formularioLateral" value="viernes">Viernes</option>
							<option class="formularioLateral" value="sabado">Sábado</option>
						</select>
						<br />
						<label class="formularioLateral" for="turno">Turno:</label>
						<select class="formularioLateral iconTurno" name="turno" required="required" id="turno">
							<option class="formularioLateral" value="M">M - 8:30 a 12:30</option>
							<option class="formularioLateral" value="M1">M1 - 8:30 a 10:30</option>
							<option class="formularioLateral" value="M2">M2 - 10:30 a 12:30</option>
							<option class="formularioLateral" value="N">N - 18:30 a 22:30</option>
							<option class="formularioLateral" value="N1">N1 - 18:30 a 20:30</option>
							<option class="formularioLateral" value="N2">N2 - 20:30 a 22:30</option>
							<option class="formularioLateral" value="T">T - 14 a 18</option>
							<option class="formularioLateral" value="T1">T1 - 14 a 16</option>
							<option class="formularioLateral" value="T2">T2 - 16 a 18</option>
						</select>
						<br />
						
						<label class="formularioLateral" for="observaciones">Observaciones: </label>
						<textarea name="observaciones" class="formularioLateral"   id="observaciones" style="height:40px;"></textarea>
						
						
						<button type="submit" class="formularioLateral iconAgregar" id="guardarCargarOtro">Guardar y cargar otra</button>
					</form>
				</fieldset>
			</div>
			
		
			<hr>
		
		
			<div id="plantelActual">
				<table class="plantelActual">
					<tr class="plantelActual">
						<th class="plantelActual" style="width:10%;">Cod</th>
						<th class="plantelActual" style="width:45%;">Materia</th>
						<th class="plantelActual" style="width:20%;">Día</th>
						<th class="plantelActual" style="width:10%;">Turno</th>
						<th class="plantelActual" style="width:5%;">Eliminar</th>
					</tr>
					<?php
						require('./conexion.php');
						
						$cantidadResultados = (isset($_GET['cantidadResultados'])) ? $_GET['cantidadResultados'] : 10;
						$pagina = (isset($_GET['pagina'])) ? (($_GET['pagina'] - 1) * $cantidadResultados) : 0;
						$hasta = $pagina + $cantidadResultados;
						
						$result = $mysqli->query("SELECT COUNT(*) FROM
													(SELECT COUNT(*)
													FROM turnos AS t
													LEFT JOIN materia AS m ON m.cod = t.materia
													GROUP BY t.turno, m.conjunto) AS b");
													
						$totalPaginas = $result->fetch_row();
						$result->free();
						
						$totalPaginas = $totalPaginas[0] / $cantidadResultados;
						
						$query = "SELECT t.id, m.conjunto, GROUP_CONCAT(DISTINCT m.nombre  SEPARATOR ' / '), 
										GROUP_CONCAT(DISTINCT t.dia ORDER BY FIELD(dia, 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado') SEPARATOR ', ') AS dia, t.turno 
									FROM turnos as t 
									LEFT JOIN materia as m ON m.cod = t.materia
									GROUP BY t.turno, m.conjunto
									ORDER BY t.turno, t.dia, m.cuatrimestre 
									LIMIT $pagina, $cantidadResultados";
						//echo $query;
						$result = $mysqli->query($query);
						echo $mysqli->error;
						while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
							
							echo "<tr class='formularioLateral plantelActual'>";
							
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
			<div class="dialog resumenMateria"></div>
			
		</div>
		
	</body>
	<?php require "./fuentes/jqueryScripts.html"; ?>
	<script src="./fuentes/funciones.js"></script>
	
	<script>
		$(document).ready(function() {
			
			$('#formularioCarga').submit( function(event) {
				event.preventDefault();
				values = $(this).serialize();
				values += "&act=agregarTurno";
				
				$.get("./fuentes/AJAX.php", values, function(data) {
					location.reload();
				});
			});
			
			
			$('.botonEliminar').click(function() {
				id = $(this).data('id');
				$.post("./fuentes/AJAX.php?act=eliminarTurno", {"id":id, }, function(data) {
					location.reload();
				});
			});
			
			$('#mostrarFormulario').click(function() {
				$('div #formulario').toggle();
			});
			
			$("select").combobox();
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
