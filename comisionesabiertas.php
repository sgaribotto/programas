<!DOCTYPE html>
<html>
	<head>
		
		<title>Turnos</title>
		<?php 
			header('Content-Type: text/html; charset=utf-8');
			require_once('./fuentes/meta.html');
			require_once 'programas.autoloader.php';
			/*function __autoload($class) {
				$classPathAndFileName = "./clases/" . $class . ".class.php";
				require_once($classPathAndFileName);
			}*/
		?>
		
	</head>
	
	<body>

		<?php
			require "./fuentes/constantes.php";
			$materia = new clases\Materia($_REQUEST['materia']);
			$nombre = $materia->mostrarNombresConjunto();
			$conjunto = $materia->mostrarConjunto();
		?>
		<div class="">
			<h2 class="formularioLateral"><span id="materiaComisionAbierta"><?php echo $conjunto; ?></span><?php echo $nombre; ?></h2>
			<div id="mostrarFormulario">Mostrar Formulario</div>
			
			<div id="formulario">
				<fieldset class="">
					<form method="post" class="" action="#" id="formularioCargaComisionesAbiertas">
						
						<label class="formularioLateral" for="periodoComisionAbierta">Periodo:</label>
						<select class="formularioLateral iconCod filtros" name="periodoComisionAbierta" required="required" id="periodoComisionAbierta" >
							<?php
								$anio = $ANIO;
								$cuatrimestre = $CUATRIMESTRE;
								
								
								echo "<option class='formularioLateral' value='{$anio} - {$cuatrimestre}' selected='selected'>{$anio} - {$cuatrimestre}</option>";
								if ($cuatrimestre == 2) {
									$anio++;
									$cuatrimestre--;
								} else {
									$cuatrimestre++;
								}
								echo "<option class='formularioLateral' value='{$anio} - {$cuatrimestre}' >{$anio} - {$cuatrimestre}</option>";
							?>
						</select>
						<br />
						<label class="formularioLateral" for="turnoComisionAbierta">turno:</label>
						<select class="formularioLateral iconTurno" name="turnoComisionAbierta" required="required" id="turnoComisionAbierta">
							<option class="formularioLateral" value="N" selected>Noche</option>
							<option class="formularioLateral" value="M">Mañana</option>
							<option class="formularioLateral" value="T">Tarde</option>
						</select>
						<br />
						<label class="formularioLateral" for="nombreComisionAbierta">Nombre Comsión:</label>
						<input type="text" name="nombreComisionAbierta" maxlength="10" placeholder="Por ej: N, NB, M, MC" required="required"/>
						<br />
						<label class="formularioLateral" for="horarioComisionAbierta">Horario:</label>
						<select class="formularioLateral iconCod filtros" name="horarioComisionAbierta" required="required" id="horarioComisionAbierta" >
							<?php
								
							?>
						</select>
						
						<br />						
						<button type="submit" class="iconAgregar" id="guardarCargarOtroComsionAbierta">Guardar y cargar otra</button>
					</form>
				</fieldset>
			</div>
			
				
		
			<hr>
			
			<div id="tablaComisionesAbiertas"></div>
		
			
		</div>
		
	</body>
	<?php require "./fuentes/jqueryScripts.html"; ?>
	<script src="./fuentes/funciones.js"></script>
	
	<script>
		$(document).ready(function() {
			
			var actualizarTablaComisionesAbiertas = function() {
				var periodo = $('#periodoComisionAbierta').val();
				var materia = $('#materiaComisionAbierta').text();
				$('#tablaComisionesAbiertas').load("fuentes/AJAX.php?act=tablaComisionesAbiertas", {"periodo": periodo, "materia": materia}, function(data) {
					$('.botonEliminar').click(function() {
						if (confirm('Desea Eliminar la comisión?')) {
							var id = $(this).data('id');
							$.post("./fuentes/AJAX.php?act=eliminarComisionAbierta", {"id":id, }, function(data) {
								
								actualizarTablaComisionesAbiertas();
							});
						}
					});
				});
				
				$('#horarioComisionAbierta').load('fuentes/AJAX.php?act=optionsHorariosComisionAbierta', 
					{"periodo": periodo, "materia": materia}, function(data) {
				});
			} 
			actualizarTablaComisionesAbiertas();
			
			
			$('#formularioCargaComisionesAbiertas').submit( function(event) {
				event.preventDefault();
				values = $(this).serialize();
				var materia = $('#materiaComisionAbierta').text();
				
				$.post("./fuentes/AJAX.php?act=agregarComisionAbierta&materia=" + materia, values, function(data) {
					
					console.log(data);
					
					actualizarTablaComisionesAbiertas();
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
				$('div #formulario').slideToggle();
			});
			
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
			
			$('#periodoComisionAbierta').change(function() {
				actualizarTablaComisionesAbiertas();
			});
			
			//$("select").combobox();
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
