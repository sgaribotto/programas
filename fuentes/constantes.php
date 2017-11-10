<?php
//CONSTANTES PHP
	require 'conexion.php';
	
	$query = "SELECT nombre, valor
				FROM constantes;";
	$result = $mysqli->query($query);
	
	$constantes = array();
	while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
		$constantes[$row['nombre']] = $row['valor'];
	}

	$ANIO = $constantes['anio'];
	$CUATRIMESTRE = $constantes['cuatrimestre'];
	$ASIGNAR_COMISIONES = $constantes['asignar_comisiones'];
	
	$turnos = array(
		'M' => 'Mañana', 
		'T' => 'Tarde', 
		'N' => 'Noche',
		'S' => 'Sábado',
		'Otro' => 'Otro'
	);
	
	$letrasComision = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];
	
	$horasTurno = array(
		"M" => "8:30 a 12:30",
		"M1" => "8:30 a 10:30",
		"M2" => "10:30 a 12:30",
		"N" => "18:30 a 22:30",
		"N1" => "18:30 a 20:30",
		"N2" => "20:30 a 22:30",
		"T" => "14 a 18",
		"T1" => "14 a 16",
		"T2" => "16 a 18",
		"M1 - M2" => "8:30 a 12:30",
		"N1 - N2" => "18:30 a 22:30",
		"T1 - T2" => "14 a 18",
		"S" => "8:30 a 12:30",
		"S1" => "8:30 a 10:30",
		"S2" => "10:30 a 12:30",
		"S1 - S2" => "8:30 a 12:30"
	);
	
	$diasSemana = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
	
?>
