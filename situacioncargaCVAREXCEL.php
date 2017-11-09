<!DOCTYPE html>

<html>
	<?php
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");;
		header( "Content-disposition: attachment; filename=SituaciónCVar.xls" );
		require './fuentes/conexion.php';
		require './fuentes/constantes.php';
		
		
		$query = "SELECT DISTINCT dni, apellido, nombres, IF(cvar = 1, 'Sí', 'No') AS cvar, 
					IF(exceptuado_cvar = 1, 'Sí', 'No') AS exceptuado_cvar
					FROM docente
					WHERE activo = 1
					ORDER BY apellido, nombres";
				
		$result = $mysqli->query($query);
		
		if ($mysqli->errno) {
			echo $mysqli->error;
		}
		
		$datosTabla = array();
		while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
			$datosTabla[] = $row;
		}
		
		
		$mysqli->close();
		
	?>
	
	<table>
		<thead>
			<tr>
				<th>DNI</th>
				<th>Apellido</th>
				<th>Nombres</th>
				<th>Cargó Cvar?</th>
				<th>Exceptuado Cvar</th>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach ($datosTabla as $key => $value) {
					echo "<tr style='vertical-align: middle; border: 1px solid black;'>";
					foreach ($value as $k => $v) {
						
						echo "<td>" . $v . "</td>";
					}
					echo "</tr>";
				}	
			?>
		</tbody>
	</table>
<html>
