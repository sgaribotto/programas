<!DOCTYPE html>
<html>
	<head>
		
		<title>Modificar Cantidad Inscriptos</title>
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
			<h2 class="formularioLateral">Inscriptos</h2>
			<div id="mostrarFormulario">Mostrar Formulario</div>
			<div id="mostrarFiltros">Mostrar Filtros</div>
			
			<div id="formulario">
				<fieldset class="formularioLateral">
					<form method="post" class="formularioLateral" action="#" id="formularioCarga">
						
						<label class="formularioLateral" for="materia">Materia: </label>
						<select name="materia" class="formularioLateral iconMateria"  required="required" id="materia">
							<?php
								require './conexion.php';
								
								$query = "SELECT MAX(cod) AS codigo, GROUP_CONCAT(DISTINCT nombre SEPARATOR ' / ') AS nombre, conjunto 
											FROM materia
											WHERE activo = 1
											GROUP BY conjunto
											ORDER BY cuatrimestre, cod";
								$result = $mysqli->query($query);
								
								while ($row = $result->fetch_array(MYSQL_ASSOC)) {
									echo "<option class='formularioLateral' value='$row[conjunto]'>$row[conjunto] - $row[nombre]</option>";
								}
								
								$result->free();
								$mysqli->close();
							?>
						</select>
						<br />
						<label class="formularioLateral" for="letra">Letra:</label>
						<select class="formularioLateral iconTurno" name="letra" required="required" id="letra">
							<option class="formularioLateral" value="A" selected>Sin letra</option>
							<option class="formularioLateral" value="B">B</option>
							<option class="formularioLateral" value="S">S</option>
							<option class="formularioLateral" value="SB">SB</option>
						</select>
						<br />
						
						<label class="formularioLateral" for="turno">Turno:</label>
						<select class="formularioLateral iconTurno" name="turno" required="required" id="turno">
							<option class="formularioLateral" value="M">M</option>
							
							<option class="formularioLateral" value="N">N</option>
							
							<option class="formularioLateral" value="T">T</option>
					
							<option class="formularioLateral" value="S">S</option>
							
						</select>
						<br />
						<label class="formularioLateral" for="periodo">Periodo:</label>
						<select class="formularioLateral iconCod filtros" name="periodo" required="required" id="periodo" >
							<?php
								$anio = $ANIO;
								$cuatrimestre = $CUATRIMESTRE;
								
								echo "<option class='formularioLateral' value='{$anio} - {$cuatrimestre}' selected>{$anio} - {$cuatrimestre}</option>";
								if ($cuatrimestre == 2) {
									$anio++;
									$cuatrimestre--;
								} else {
									$cuatrimestre++;
								}
								echo "<option class='formularioLateral' value='{$anio} - {$cuatrimestre}'>{$anio} - {$cuatrimestre}</option>";
							?>
						</select>
						<br />
						<label class="formularioLateral" for="periodo">Cantidad:</label>
						<input type="number" class="formularioLateral iconCantidad" min="1" name="cantidad" />
						<br />
						
						
						<button type="submit" class="formularioLateral iconAgregar" id="guardarCargarOtro">Guardar y cargar otra</button>
					</form>
				</fieldset>
			</div>
			
			<div id="filtros" class="desplegable">
				<fieldset class="formularioLateral">
					<form method="post" class="formularioLateral filtros" action="" >
						<label class="formularioLateral" for="periodo">Periodo:</label>
						<select class="formularioLateral iconCod filtros" name="periodo" required="required" id="periodo" >
							<?php
								require "./fuentes/conexion.php";
								
								$query = "SELECT DISTINCT CONCAT(anio, ' - ', cuatrimestre) AS periodo
											FROM estimacion
											ORDER BY anio DESC, periodo DESC";
								$result = $mysqli->query($query);
								
								$periodos = array();
								while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
									$periodos[] = $row['periodo'];
								}
								
								foreach ($periodos as $periodo) {
									echo "<option class='formularioLateral filtros'>$periodo</option>";
								}
							?>
						</select>
						<br />
						<label class="formularioLateral" for="filtro">Buscar:</label>
						<input type="text" class="formularioLateral iconCod filtros" name="filtro" id="filtro" maxlength="15"/>
						
					</form>	
				</fieldset>
			</div>
			
		
			<hr>
			
			<div id="tablaDatos"></div>
		
			<div class="dialog resumenMateria"></div>
			
		</div>
		
	</body>
	<?php require "./fuentes/jqueryScripts.html"; ?>
	<script src="./fuentes/funciones.js"></script>
	
	<script>
		$(document).ready(function() {
			
			var actualizarTabla = function() {
				formValues = $('form.filtros').serialize();
				$('#tablaDatos').load("fuentes/AJAX.php?act=tablaEstimacion", formValues, function(data) {
					//$('.botonEliminar').off();
					$('.botonEliminar').click(function() {
						if (confirm('Desea Eliminar el turno?')) {
							var id = $(this).data('id');
							$.post("./fuentes/AJAX.php?act=eliminarEstimacion", {"id":id, }, function(data) {
								
								actualizarTabla();
							});
						}
					});
					//$('#tablaDatos').click();
				});
			} 
			actualizarTabla();
			
			$('#formularioCarga').submit( function(event) {
				event.preventDefault();
				values = $(this).serialize();
				
				$.post("./fuentes/AJAX.php?act=agregarEstimacion", values, function(data) {
					alert('Se ha agregado el turno');
					actualizarTabla();
				});
			});
			
			function togglerButtonColor() {
				
				gris = '#f9f9f9';
				
				if ($('div#formulario').is(':visible')) {
					$('#mostrarFormulario').css('backgroundColor', 'black');
					$('#mostrarFormulario').css('color', gris);
				} else {
					$('#mostrarFormulario').css('backgroundColor', gris);
					$('#mostrarFormulario').css('color', 'black');
				}
				
				if ($('div#filtros').is(':visible')) {
					$('#mostrarFiltros').css('backgroundColor', 'black');
					$('#mostrarFiltros').css('color', gris);
				} else {
					$('#mostrarFiltros').css('backgroundColor', gris);
					$('#mostrarFiltros').css('color', 'black');
				}
			}
			
			
			$('#mostrarFormulario').click(function() {
				$('div #formulario').slideToggle(function() {
					if ($('div#formulario').is(':visible')) {
					$('#mostrarFormulario').css('backgroundColor', 'black');
					$('#mostrarFormulario').css('color', gris);
				} else {
					$('#mostrarFormulario').css('backgroundColor', gris);
					$('#mostrarFormulario').css('color', 'black');
				}
					
				});
				$('div #filtros').slideUp();
				
				var gris = '#f9f9f9';
				$('#mostrarFiltros').css('backgroundColor', gris);
					$('#mostrarFiltros').css('color', 'black');
				
				
			});
			
			$('#mostrarFiltros').click(function() {
				$('div #filtros').slideToggle(function() {
					if ($('div#filtros').is(':visible')) {
						$('#mostrarFiltros').css('backgroundColor', 'black');
						$('#mostrarFiltros').css('color', gris);
					} else {
						$('#mostrarFiltros').css('backgroundColor', gris);
						$('#mostrarFiltros').css('color', 'black');
					}
				});
				$('div #formulario').slideUp();
				
				var gris = '#f9f9f9';
				
				$('#mostrarFormulario').css('backgroundColor', gris);
				$('#mostrarFormulario').css('color', 'black');
				
				
			});
			$('#mostrarFiltros').click();
			
			$('input.filtros').on('keyup', function(event) {
				//alert('filtro activado');
				actualizarTabla();
				
			});
			
			$('select.filtros').on('blur', function(event) {
				//alert('filtro activado');
				actualizarTabla();
				
			});
			
			$('#filtro').focus();
			
			$.ajaxSetup({
				contentType: "application/x-www-form-urlencoded;charset=UTF-8"
			});
			var dialogOptions = {
				autoOpen: false,
				width:1000,
				height: 600,
				modal: true,
				appendTo: "#Botonera",
				close: function() {
					$('#mostrarFormulario').off('click');
					$('#mostrarFormulario').click(function() {
						$('div #formulario').slideToggle();
					
					});
				},
					
			};
			
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
