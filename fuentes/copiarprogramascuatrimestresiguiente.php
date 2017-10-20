<?php
//CONSTANTES PHP
	require 'conexion.php';
	
	$anio = 2017;
	$cuatrimestre = 2;
	
	if ($cuatrimestre == 2) {
		$anioSiguiente = $anio + 1;
		$cuatrimestreSiguiente = 1;
	} else {
		$anio_siguiente = $anio;
		$cuatrimestreSiguiente = 2;
	}
	
	// AFECTACION - - EQUIPO DOCENTE
	$query = "INSERT IGNORE INTO `programas`.`afectacion`
		(`id`, `docente`, `materia`, `anio`, `cuatrimestre`, `activo`, `tipoafectacion`, `estado`)
		SELECT NULL as id, docente, materia, {$anioSiguiente}, {$cuatrimestreSiguiente}, activo,
			tipoafectacion, estado
		FROM afectacion
		WHERE anio = {$anio} AND cuatrimestre = {$cuatrimestre} AND activo = 1;";
	$mysqli->query($query);
	if ($mysqli->errno) {
		echo $mysqli->error;
		echo "<br />";
		echo $query;
		echo "<br />";
	}
	
	//AGREGADOS CRONOGRAMA
	$query = "INSERT IGNORE INTO `programas`.`agregados_cronograma`
			(`id`,`tipo`, `valor`,`activo`,	`materia`, `clase`,	`anio`,	`cuatrimestre`)
			SELECT NULL as id, tipo, valor, activo, materia, clase, {$anioSiguiente}, {$cuatrimestreSiguiente}
			FROM agregados_cronograma
			WHERE anio = {$anio} AND cuatrimestre = {$cuatrimestre}";
	$mysqli->query($query);
	if ($mysqli->errno) {
		echo $mysqli->error;
		echo "<br />";
		echo $query;
		echo "<br />";
	}
	
	//BIBLIOGRAFIA
	
	$query = "INSERT IGNORE INTO `programas`.`bibliografia`
				(`id`,
				`materia`,
				`titulo`,
				`autor`,
				`editorial`,
				`paginas`,
				`activo`,
				`anio`,
				`cuatrimestre`)
			SELECT NULL as id, materia, titulo, autor, editorial, paginas, activo, {$anioSiguiente}, {$cuatrimestreSiguiente}
			FROM bibliografia
			WHERE anio = {$anio} AND cuatrimestre = {$cuatrimestre} AND activo = 1;";
	$mysqli->query($query);
	if ($mysqli->errno) {
		echo $mysqli->error;
		echo "<br />";
		echo $query;
		echo "<br />";
	}
	
	//CRONOGRAMA
	
	$query = "INSERT IGNORE INTO `programas`.`cronograma`
				(`id`,
				`materia`,
				`clase`,
				`fecha`,
				`unidadtematica`,
				`descripcion`,
				`metodo`,
				`bibliografia`,
				`paginas`,
				`activo`,
				`docente`,
				`anio`,
				`cuatrimestre`)
			SELECT NULL, materia, clase, fecha, unidadtematica, descripcion, metodo, bibliografia, 
				paginas, activo, docente, {$anioSiguiente}, {$cuatrimestreSiguiente}
			FROM cronograma
			WHERE anio = {$anio} AND cuatrimestre = {$cuatrimestre};";
	$mysqli->query($query);
	if ($mysqli->errno) {
		echo $mysqli->error;
		echo "<br />";
		echo $query;
		echo "<br />";
	}	
	//PROGRAMA
	
	$query = "INSERT IGNORE INTO `programas`.`programa`
				(`id`,
				`materia`,
				`usuario`,
				`campo`,
				`valor`,
				`fecha`,
				`anio`,
				`cuatrimestre`)
			SELECT NULL, materia, usuario, campo, valor, fecha, {$anioSiguiente}, {$cuatrimestreSiguiente}
			FROM programa
			WHERE anio = {$anio} AND cuatrimestre = {$cuatrimestre};";
	
	$mysqli->query($query);
	if ($mysqli->errno) {
		echo $mysqli->error;
		echo "<br />";
		echo $query;
		echo "<br />";
	}
	
	//UNIDADES TEMATICAS
	$query = "INSERT IGNORE INTO `programas`.`unidad_tematica`
				(`id`,
				`materia`,
				`unidad`,
				`descripcion`,
				`fecha`,
				`activo`,
				`anio`,
				`cuatrimestre`)
				SELECT NULL, materia, unidad, descripcion, fecha, activo, {$anioSiguiente}, {$cuatrimestreSiguiente}
			FROM unidad_tematica
			WHERE anio = {$anio} AND cuatrimestre = {$cuatrimestre} AND activo = 1;";
	$mysqli->query($query);
	if ($mysqli->errno) {
		echo $mysqli->error;
		echo "<br />";
		echo $query;
		echo "<br />";
	}
	
	echo "TERMINADO";


	
?>
