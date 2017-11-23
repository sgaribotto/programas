<!DOCTYPE html>

<html>
	<head>
	</head>
	<body>
	<?php
		//print_r($_REQUEST);
		$periodo = $_REQUEST['periodo'];
		$periodo = explode(' - ', $periodo);
		$anio = $periodo[0];
		$cuatrimestre = $periodo[1];
		//$reporte = $_REQUEST['reporte'];
		
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");;
		header( "Content-disposition: attachment; filename=reportedesignaciones{$_REQUEST['periodo']}.xls" );
		require './fuentes/conexion.php';
		require './fuentes/constantes.php';
		
		
		$datos = array();
		
		//DESIGNACIONES
		
		$where = "WHERE (YEAR(fecha_alta) <= {$anio} AND MONTH(fecha_alta) <= 6)
						AND (YEAR(fecha_baja) >= {$anio} OR fecha_baja = 0)";
		if ($cuatrimestre == 2) {
			$where = "WHERE YEAR(fecha_alta) <= {$anio}
						AND ((YEAR(fecha_baja) >= {$anio} AND MONTH(fecha_baja) >= 7) OR fecha_baja = 0)";
		}
		$query = "SELECT docente,
						categoria,
						caracter,
						dedicacion,
						fecha_alta,
						fecha_baja
					FROM designacion
					{$where}";
		$result = $mysqli->query($query);
		while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
			$datos[$row['docente']]['designacion'][] = $row;
		}
		
		
		
		//ASIGNACION COMISIONES
		
		$query = "SELECT DISTINCT ac.docente,
						ac.materia,
						m.nombres,
						ac.comision
					FROM asignacion_comisiones AS ac
					LEFT JOIN vista_materias_por_conjunto AS m
						ON m.conjunto = ac.materia
					WHERE ac.anio = {$anio} AND ac.cuatrimestre = {$cuatrimestre};";
		
		$result = $mysqli->query($query);
		while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
			$datos[$row['docente']]['asignacion'][] = $row;
		}
					
					
		
		//DATOS DEL DOCENTE
		$query = "SELECT d.id AS docente,
						d.apellido,
						d.nombres,
						d.fechanacimiento,
						'DNI' AS tipo,
						dni,
						ddCUIL.valor AS CUIL,
						ddmail.valor AS mail,
						dddomicilio.valor AS domicilio,
						ddtitulo.valor AS titulo
					FROM docente AS d
					LEFT JOIN datos_docentes AS ddCUIL
						ON ddCUIL.tipo = 'CUIL' AND ddCUIL.docente = d.id
					LEFT JOIN datos_docentes AS ddmail
						ON ddmail.tipo = 'mail' AND ddmail.docente = d.id
					LEFT JOIN datos_docentes AS dddomicilio
						ON dddomicilio.tipo = 'domicilio' AND dddomicilio.docente = d.id
					LEFT JOIN datos_docentes AS ddtitulo
						ON ddtitulo.tipo = 'titulo' AND ddtitulo.docente = d.id";
		
		$result = $mysqli->query($query);
		echo $mysqli->error;
		while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
			$datos[$row['docente']]['datos'][] = $row;
		}
		
		$mysqli->close();
		
		
		?>
		<table border="1">
			<thead>
				<tr>
					<th>Apellido</th>
					<th>Nombres</th>
					<th>Tipo</th>
					<th>Documento</th>
					<th>Categoría</th>
					<th>Caracter</th>
					<th>Dedicación</th>
					<th>Cod.</th>
					<th>Materia</th>
					<th>comision</th>
					<th>Alta</th>
					<th>Baja</th>
					<th>CUIL</th>
					<th>Nacimiento</th>
					<th>Título</th>
					<th>Domicilio</th>
					<th>Email</th>
					
				</tr>
			</thead>
			<tbody>
				<?php
					
					foreach ($datos as $docente => $tipos) {
						
						$cantidad_designaciones = 0;
						$cantidad_asignaciones = 0;
						if (isset($tipos['designacion'])) {
							$cantidad_asignaciones = count($tipos['designacion']);
						}
						if (isset($tipos['asignacion'])) {
							$cantidad_asignaciones = count($tipos['asignacion']);
						}
						
						$max = max($cantidad_asignaciones, $cantidad_designaciones);
						
						if ($max > 0) {
							for ($i = 0; $i < $max; $i++) {
								echo "<tr>";
								
								if (isset($tipos['datos'])) {
									echo "<td>{$tipos['datos'][0]['apellido']}</td>";
									echo "<td>{$tipos['datos'][0]['nombres']}</td>";
									echo "<td>{$tipos['datos'][0]['tipo']}</td>";
									echo "<td>{$tipos['datos'][0]['dni']}</td>";
									echo "<td>{$tipos['datos'][0]['apellido']}</td>";
								} else {
									echo "<td colspan='5'>Faltan datos del docente {$docente}</td>";
								}
								
								if (isset($tipos['designacion'][$i])) {
									echo "<td>{$tipos['designacion'][$i]['categoria']}</td>";
									echo "<td>{$tipos['designacion'][$i]['caracter']}</td>";
									echo "<td>{$tipos['designacion'][$i]['dedicacion']}</td>";
								} else {
									echo "<td></td><td></td><td></td>";
								}
								
								if (isset($tipos['asignacion'][$i])) {
									echo "<td>{$tipos['asignacion'][$i]['materia']}</td>";
									echo "<td>{$tipos['asignacion'][$i]['nombres']}</td>";
									echo "<td>{$tipos['asignacion'][$i]['comision']}</td>";
								} else {
									echo "<td></td><td></td><td></td>";
								}
								
								if (isset($tipos['designacion'][$i])) {
									echo "<td>{$tipos['designacion'][$i]['fecha_alta']}</td>";
									echo "<td>{$tipos['designacion'][$i]['fecha_baja']}</td>";
								} else {
									echo "<td></td><td></td>";
								}
								
								if (isset($tipos['datos'])) {
									echo "<td>{$tipos['datos'][0]['CUIL']}</td>";
									echo "<td>{$tipos['datos'][0]['fechanacimiento']}</td>";
									echo "<td>{$tipos['datos'][0]['titulo']}</td>";
									echo "<td>{$tipos['datos'][0]['domicilio']}</td>";
									echo "<td>{$tipos['datos'][0]['mail']}</td>";
								} else {
									echo "<td></td><td></td><td></td><td></td><td></td>";
								}
									
							
								
								echo "</tr>";
							}
						}
							
						
						
						
					}	
					
				?>
			</tbody>
		</table>
					

					
				
	</body>
<html>
