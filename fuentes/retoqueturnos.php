<?php
	header('Content-Type: text/html; charset=utf-8');
//Consultas vía AJAX
	//Autoload de la clase.
	session_start();
	require_once '../programas.autoloader.php';
	require './constantes.php';

	

	require "./conexion.php";
	
	$cuatrimestre = 2;
	$anio = 2017;
	$dias = ['', 'domingo', 'lunes', 'martes', 'miércoles',
				'jueves', 'viernes', 'sábado'];
	$conversionTurnos = array(
		"8:30:00 a 10:30:00" => 'M1',
		"10:30:00 a 12:30:00" => 'M2',
		"8:30:00 a 12:30:00" => 'M',
		"08:30:00 a 10:30:00" => 'M1',
		"10:30:00 a 12:30:00" => 'M2',
		"08:30:00 a 12:30:00" => 'M',
		"14:00:00 a 16:00:00" => 'T1',
		"16:00:00 a 18:00:00" => 'T2',
		"14:00:00 a 18:00:00" => 'T',
		"18:30:00 a 20:30:00" => 'N1',
		"20:30:00 a 22:30:00" => 'N2',
		"18:30:00 a 22:30:00" => 'N',
	);
	
	$query = "SELECT m.conjunto, 
				b.turno,       
				MIN(b.comision) AS comision,       
				#GROUP_CONCAT(DISTINCT b.comision ORDER BY comision) AS comsiones,
				   COUNT(DISTINCT b.comision) AS cantidad_comisiones,       
				b.horarios      
			FROM (          
				SELECT materia,            
					LEFT(comision, 1) AS turno,           
					comision,           
					GROUP_CONCAT(DISTINCT CONCAT(dia, ' de ', horario)              
						ORDER BY FIELD(dia, 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado')           
					) AS horarios          
				FROM programas.pre_turnos          
				WHERE anio_academico = 2017            
				AND periodo_lectivo = 2          
			GROUP BY materia, comision, turno
			) AS b      
			LEFT JOIN materia AS m       
				ON m.cod = b.materia      
			GROUP BY b.horarios, m.conjunto, b.turno, b.comision";
	
	$result = $mysqli->query($query);
	
	$horarios = array();
	
	while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
		$turnos = array();
		$turnos = explode(',', $row['horarios']);
		foreach ($turnos as $turno) {
			$conjunto = $row['conjunto'];
			if (!in_array($row['comision'], ['N', 'M', 'T'])) {
				
				$conjunto .= substr($row['comision'], 1, 1);
				//echo $conjunto;
			}
			$horarios[$conjunto][$row['turno']][] = explode(' de ', $turno);
		}
		
	}
	
	foreach ($horarios as $conjunto => $turnos) {
		foreach ($turnos as $turno => $dias) {
			foreach ($dias as $horario) {
				//echo $conjunto . " - " . $horario[0] . " - " . $conversionTurnos[$horario[1]];
				//echo "<br>";
				
				$query = "INSERT INTO turnos_con_conjunto (materia, dia, turno, anio, cuatrimestre) 
					VALUES ('{$conjunto}', '{$horario[0]}', '{$conversionTurnos[$horario[1]]}', 2017, 2);";
				$mysqli->query($query);
					
			}
			
		}
	}
	
	$mysqli->close();

		
	
	
	
?>
