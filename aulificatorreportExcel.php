<!DOCTYPE html>
<html>
	<head>
	</head>
	<body>
	<?php
		//header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
		//header( "Content-disposition: attachment; filename=aulas.xls" );
		
		header("Content-Type:   application/excel; charset=utf-8");
		header( "Content-disposition: attachment; filename=aulas.xls" );
		
		require 'aulificatorreport4.php';
		
	?>
	</body>
<html>
