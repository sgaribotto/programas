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
			<div id="mostrarFormulario" class="comisionesAbiertas">Comisiones Abiertas</div>
			<div id="mostrarFormulario" class="turnos">Turnos</div>
			
			<div id="formularioComisionesAbiertas">
				<fieldset class="">
					<form method="post" class="" action="#" id="formularioCargaComisionesAbiertas">
						
						<label class="formularioLateral" for="periodoComisionAbierta">Periodo:</label>
						<select class="formularioLateral iconCod filtros actualizarTabla" name="periodoComisionAbierta" required="required" id="periodoComisionAbierta" >
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
								echo "<option class='formularioLateral' value='{$anio} - {$cuatrimestre}'>{$anio} - {$cuatrimestre}</option>";
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
						<select class="formularioLateral iconCod filtros " name="horarioComisionAbierta" required="required" id="horarioComisionAbierta" >
						</select>
						
						<br />						
						<button type="submit" class="iconAgregar" id="guardarCargarOtroComsionAbierta">Guardar y cargar otra</button>
					</form>
				</fieldset>
				
				<hr>
			
				<div id="tablaComisionesAbiertas"></div>
			</div>
			
			<div id="formularioTurnos">
				<fieldset class="">
					<form method="post" class="formularioLateral" action="#" id="formularioCargaTurnos">
						
						<label class="formularioLateral" for="letra">Letra:</label>
						<select class="formularioLateral iconTurno" name="letra" required="required" id="letra">
							<option class="formularioLateral" value="A" selected>Sin letra</option>
							<option class="formularioLateral" value="B">B</option>
							<option class="formularioLateral" value="S">S</option>
							<option class="formularioLateral" value="SB">SB</option>
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
							<option class="formularioLateral" value="S">S - 8:30 a 12:30</option>
							<option class="formularioLateral" value="S1">S1 - 8:30 a 10:30</option>
							<option class="formularioLateral" value="S2">S2 - 10:30 a 12:30</option>
						</select>
						<br />
						<label class="formularioLateral" for="periodo">Periodo:</label>
						<select class="formularioLateral iconCod filtros actualizarTabla" name="periodo" required="required" id="periodoTurno" >
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
						
						<label class="formularioLateral" for="observaciones">Observaciones: </label>
						<textarea name="observaciones" class="formularioLateral"   id="observaciones" style="height:40px;"></textarea>
						
						
						<button type="submit" class="formularioLateral iconAgregar" id="guardarCargarOtro">Guardar y cargar otra</button>
					</form>
				</fieldset>
				
				<hr>
			
				<div id="tablaTurnos"></div>
			</div>
			
				
		
		
			
		</div>
		
	</body>
	<?php require "./fuentes/jqueryScripts.html"; ?>
	<script src="./fuentes/funciones.js"></script>
	
	<script>
		$(document).ready(function() {
			
			var actualizarTablaComisionesAbiertas = function() {
				var periodo = $('#periodoComisionAbierta').val();
				var materia = $('#materiaComisionAbierta').text();
				var periodoTurno = $('#periodoTurno').val();
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
				
				$('#tablaTurnos').load("fuentes/AJAX.php?act=tablaTurnosMateria", {"periodo": periodoTurno, "materia": materia}, function(data) {
					$('.botonEliminarTurno').click(function() {
						if (confirm('Desea Eliminar la comisión?')) {
							var id = $(this).data('id');
							$.post("./fuentes/AJAX.php?act=eliminarTurno", {"id":id, }, function(data) {
								
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
			
			$('select.actualizarTabla').change(function() {
				//alert('actual');
				actualizarTablaComisionesAbiertas();
			});
			
			$('#formularioCargaTurnos').submit( function(event) {
				event.preventDefault();
				values = $(this).serialize();
				var materia = $('#materiaComisionAbierta').text();
				
				$.post("./fuentes/AJAX.php?act=agregarTurno&materia=" + materia, values, function(data) {
					
					console.log(data);
					
					actualizarTablaComisionesAbiertas();
				});
			});
			
			function togglerButtonColor() {
				
				gris = '#f9f9f9';
				
				if ($('div#formularioComisionesAbiertas').is(':visible')) {
					$('#mostrarFormulario.comisionesAbiertas').css('backgroundColor', 'black');
					$('#mostrarFormulario.comisionesAbiertas').css('color', gris);
				} else {
					$('#mostrarFormulario.comisionesAbiertas').css('backgroundColor', gris);
					$('#mostrarFormulario.comisionesAbiertas').css('color', 'black');
				}
				
				if ($('div#formularioTurnos').is(':visible')) {
					$('#mostrarFormulario.turnos').css('backgroundColor', 'black');
					$('#mostrarFormulario.turnos').css('color', gris);
				} else {
					$('#mostrarFormulario.turnos').css('backgroundColor', gris);
					$('#mostrarFormulario.turnos').css('color', 'black');
				}
			}
			
			
			$('#mostrarFormulario.comisionesAbiertas').click(function() {
				$('div#formularioComisionesAbiertas').slideToggle(function() {
					if ($('div#formularioComsionesAbiertas').is(':visible')) {
					$('#mostrarFormulario.comisionesAbiertas').css('backgroundColor', 'black');
					$('#mostrarFormulario.comisionesAbiertas').css('color', gris);
				} else {
					$('#mostrarFormulario.comisionesAbiertas').css('backgroundColor', gris);
					$('#mostrarFormulario.comisionesAbiertas').css('color', 'black');
				}
					
				});
				$('div#formularioTurnos').slideUp();
				
				var gris = '#f9f9f9';
				$('#mostrarFormulario.turnos').css('backgroundColor', gris);
					$('#mostrarFormulario.turnos').css('color', 'black');
				
				
			});
			
			$('#mostrarFormulario.turnos').click(function() {
				$('div#formularioTurnos').slideToggle(function() {
					if ($('div#formularioTurnos').is(':visible')) {
						$('#mostrarFormulario.turnos').css('backgroundColor', 'black');
						$('#mostrarFormulario.turnos').css('color', gris);
					} else {
						$('#mostrarFormulario.turnos').css('backgroundColor', gris);
						$('#mostrarFormulario.turnos').css('color', 'black');
					}
				});
				$('div #formularioComisionesAbiertas').slideUp();
				
				var gris = '#f9f9f9';
				
				$('#mostrarFormulario.comisionesAbiertas').css('backgroundColor', gris);
				$('#mostrarFormulario.comisionesAbiertas').css('color', 'black');
				
				
			});
			$('#mostrarFormulario.comisionesAbiertas').click();
			$('#mostrarFormulario.comisionesAbiertas').click();
			
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
