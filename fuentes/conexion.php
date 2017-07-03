<?php
	//CONEXIÓN A LA BASE DE DATOS
	
	$mysqli = new mysqli('localhost', 'root', 'gari', 'programas');
	$mysqli->set_charset("utf8");
	
	if ($mysqli->errno) {
		printf("Unable to connect to the database:<br /> %s", $mysqli->error);
		exit();
	}
?>
