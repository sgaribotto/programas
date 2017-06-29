<!DOCTYPE html>

<html>
	<body>
	<?php
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");;
		header( "Content-disposition: attachment; filename=asignacion_comisiones.xls" );
		require './fuentes/conexion.php';
		require './fuentes/constantes.php';
		
		/*$query = "SELECT ca.materia,
					ca.nombres,
					ca.nombre_comision,
					
					GROUP_CONCAT(DISTINCT CONCAT(d.apellido, ', ', d.nombres) SEPARATOR ' / ') AS docentes,
					ca.responsable
				FROM programas.vista_comisiones_abiertas_con_responsables AS ca
				LEFT JOIN asignacion_comisiones AS ac
					ON ca.materia = ac.materia  
					AND ca.anio = ac.anio
					AND ca.cuatrimestre = ac.cuatrimestre
					AND ca.nombre_comision = ac.comision OR ca.nombre_comision = CONCAT(ac.comision, 'A')
				LEFT JOIN docente AS d
					ON ac.docente = d.id
				WHERE ca.anio = {$ANIO}
					AND ca.cuatrimestre = {$CUATRIMESTRE}
				GROUP BY ca.materia, ca.nombre_comision";
				
		$result = $mysqli->query($query);
		
		if ($mysqli->errno) {
			echo $mysqli->error;
		}
		
		$datosTabla = array();
		while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
			$datosTabla[] = $row;
		}
		
		
		$mysqli->close();*/
		
	?>
	
	<!--<table>
		<thead>
			<tr>
				<th>Materia</th>
				<th>Nombre Materia</th>
				<th>Comision</th>
				<th>Docentes</th>
				<th>Responsable</th>
			</tr>
		</thead>
		<tbody>
			<?php
				/*foreach ($datosTabla as $key => $value) {
					echo "<tr>";
					foreach ($value as $k => $v) {
						echo "<td>" . mb_convert_encoding($v, 'utf16', 'utf8') . "</td>";
					}
					echo "</tr>";
				}*/	
			?>
		</tbody>
	</table>-->
		<div id="tablaAceptarDesignacion"></div>
		
	</body>
	<?php require "./fuentes/jqueryScripts.html"; ?>
	<script src="./fuentes/funciones.js"></script>
	
	<script>
		$(document).ready(function() {
			
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
			
				
			var actualizarTabla = function() {
				formValues = $('form.filtros').serialize();
				//console.log(formValues);
				$('#tablaAceptarDesignacion').load("fuentes/AJAX.php?act=tablaOfertaAcademica", formValues, function(data) {
				
				});
			} 
			actualizarTabla();
			
			$('#tablaAceptarDesignacion').on('click', 'td.masInfo', function(event) {
				
				var string = event.target.innerHTML;
				string = string.substring(1, 6);
				//console.log(string);
				cod = parseFloat(string);
				$('#dialogResumenMateria').empty();
				
				$('#dialogResumenMateria').load('resumenmateria.php', {"materia":cod});
				
				$('#dialogResumenMateria').dialog(dialogOptions).dialog('open');
			});
			
			
			
			$('#mostrarFormulario').click(function() {
				$('div #formulario').slideToggle();
			});
			$('#mostrarFormulario').click();
			
			$('#materia').focus();
			
			$('input.filterTrigger').on('keyup blur change', function() {
				val = $(this).val();
				
				if (val.length > 2 ) {
					//alert(val);
					actualizarTabla(); 
				}
				
			});
			
			$('select.filterTrigger').change(function(event) {
				actualizarTabla();
			});
			
			function download(data, filename, type) {
				var a = document.createElement("a"),
					file = new Blob([data], {type: type});
				if (window.navigator.msSaveOrOpenBlob) // IE10+
					window.navigator.msSaveOrOpenBlob(file, filename);
				else { // Others
					var url = URL.createObjectURL(file);
					a.href = url;
					a.download = filename;
					document.body.appendChild(a);
					a.click();
					setTimeout(function() {
						document.body.removeChild(a);
						window.URL.revokeObjectURL(url);  
					}, 0); 
				}
			}
			
			
		});
	</script>
	
<html>
