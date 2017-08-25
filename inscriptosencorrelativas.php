<!DOCTYPE html>
<html>
	<head>
		
		<title>Inscriptos en correlativas</title>
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
			
			$PERIODO = $ANIO . ' - ' . $CUATRIMESTRE;
		?>
		<div class="formularioLateral">
			<h2 class="formularioLateral">Inscripciones en las correlativas</h2>
			<div id="mostrarFormulario">Mostrar Formulario</div>
			<div id="formulario">
				<fieldset class="formularioLateral">
					<form method="post" class="formularioLateral" action="#" id="formularioCarga" >
						
						<label class="formularioLateral" for="periodo">Periodo: </label>
						<select name="periodo" class="formularioLateral">
							<?php
								require 'fuentes/conexion.php';
								
								$query = "SELECT DISTINCT CONCAT(anio_academico, ' - ', periodo_lectivo + 0) AS periodo
											FROM inscriptos
											ORDER BY anio_academico DESC, periodo_lectivo DESC
											LIMIT 10";
								$result = $mysqli->query($query);
								
								while ($row = $result->fetch_array(MYSQL_ASSOC)) {
									$selected = "";
									
									if ($row['periodo'] == $PERIODO) {
										$selected = 'selected';
									}
									echo "<option value='{$row['periodo']}' $selected>{$row['periodo']}</option>";
								}
								
								$mysqli->close();
							?>
						</select>
						<br />
						<label for="reporte" class="formularioLateral">Reporte: </label>
						<select name="conjunto" class="formularioLateral">
							<?php
								require 'fuentes/conexion.php';
								
								$query = "SELECT DISTINCT conjunto, nombres
											FROM vista_materias_por_conjunto
											ORDER BY conjunto";
								$result = $mysqli->query($query);
								
								while ($row = $result->fetch_array(MYSQL_ASSOC)) {
									echo "<option value='{$row['conjunto']}' $selected>{$row['conjunto']} {$row['nombres']}</option>";
								}
							?>
						</select>
						<br />
						
						
						<button type="submit" class="formularioLateral" id="guardarCargarOtro">Mostrar resultado</button>
					</form>
				</fieldset>
			</div>
			
		
			<hr>
			
			<div class="tabla"></div>
		
			
		
	</body>
	<?php require "./fuentes/jqueryScripts.html"; ?>
	<script src="./fuentes/funciones.js"></script>
	
	<script>
		$(document).ready(function() {
			
			$('#mostrarFormulario').click(function() {
				$('div #formulario').toggle();
			});
			
			$('#formularioCarga').submit(function(event) {
				event.preventDefault();
				var values = $('#formularioCarga').serialize();
				values += '&act=inscriptosEnCorrelativas';
				$('div.tabla').load("./fuentes/AJAX.php?" + values, function(data) {
					
				});
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
