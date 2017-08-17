<?php
	header('Content-Type: text/html; charset=utf-8');
//Consultas vía AJAX
	//Autoload de la clase.
	session_start();
	require_once '../programas.autoloader.php';
	require './constantes.php';

	
	if (isset($_GET['act'])) {
		
			switch($_GET['act']) {
				
									
				case "ofertaAcademica":
					require "./conexion.php";
					
					$periodo = explode(' - ', $_POST['importarA']);
					$cuatrimestre = $periodo[1];
					$anio = $periodo[0];
					$dias = ['', 'domingo', 'lunes', 'martes', 'miércoles',
							    'jueves', 'viernes', 'sábado'];
					$turnos = array(
					    "8:30:00 a 10:30:00" => 'M1',
					    "10:30:00 a 12:30:00" => 'M2',
					    "8:30:00 a 12:30:00" => 'M',
					    "14:00:00 a 16:00:00" => 'T1',
					    "16:00:00 a 18:00:00" => 'T2',
					    "14:00:00 a 18:00:00" => 'T',
					    "18:30:00 a 20:30:00" => 'N1',
					    "20:30:00 a 22:30:00" => 'N2',
					    "18:30:00 a 22:30:00" => 'N',
					);
					
					//BORRAR TODO LO DEL AÑO EN CUESTION
					$query = "DELETE FROM pre_turnos
						WHERE CONCAT(anio_academico, ' - ', periodo_lectivo) = '{$_POST['importarA']}'";
					$mysqli->query($query);
					
					
					//print_r($_FILES);
					//print_r($_POST);
					$dir = "../importaciones";
					$file = $dir . basename($_FILES['importar']['name']);
					
					$lines = file($_FILES['importar']['tmp_name'], FILE_IGNORE_NEW_LINES);
					$count_lines = count($lines);
					
					for ($i = 0; $i < $count_lines; $i++) {
						$lines[$i] = preg_split("/[\,]/", $lines[$i]);
					}
					//print_r($lines);
					$paraAgregar = array();
					for ($i = 1; $i < $count_lines ; $i++) {
						if ($lines[$i][1] != '' and $lines[$i][14] != ''
						    and $lines[$i][15] != '' and $lines[$i][16] != '') {
								
							$cod = $lines[$i][1];
							//$materia = new clases\Materia($cod);
							//$conjunto = $materia->mostrarConjunto();
							
							$comision = $lines[$i][5];
							
							$dia = $lines[$i][16];
							
							$textoTurno = $lines[$i][14] . ' a ' . $lines[$i][15];
							
							/*$turno = 'otro';
							if (isset($turnos[$textoTurno])) {
								$turno = $turnos[$textoTurno];
							} */
							//$letraTurno = substr($turno, 0, 1);
							/*$paraAgregar[$conjunto][$letraTurno][$comision][] = array(
							                                                       'dia' => $dia,
							                                                       'turno' => $turno
							                                                    );*/
							
							$query = "INSERT INTO pre_turnos
								(materia, comision, dia, horario, periodo_lectivo, anio_academico) 
								VALUES ('{$cod}', '{$comision}', '{$dia}', '{$textoTurno}', $cuatrimestre, $anio);";
							$mysqli->query($query);
							echo $mysqli->error . "<br>";
							//echo $cod . " - " . $conjunto . " - " . $dia . " - " . $turno . "<br>";
							//print_r($lines[$i]);
						}
					}
					
					
					/*$paraQuery = array();
					foreach ($paraAgregar as $conjunto => $dataTurno) {
						foreach ($dataTurno as $letraTurno => $comision) {
							foreach ($comision as $nombreComision => $horarios) {
								//Armar el array sin repeticiones y con las comisiones B
								if (!isset($paraQuery[$conjunto][$letraTurno])) {
									$paraQuery[$conjunto][$letraTurno]['horarios'] = $horarios;
									$paraQuery[$conjunto][$letraTurno]['cantidad'] = 1;
								} else {
									if ($paraQuery[$conjunto][$letraTurno]['horarios'] == $horarios) {
										$paraQuery[$conjunto][$letraTurno]['cantidad']++;
									} else {
										/*print_r($paraQuery[$conjunto][$letraTurno]['horarios']);
										echo " == ";
										print_r($horarios);
										echo "<br><br>";*/
									/*	$paraQuery[$conjunto][$letraTurno . 'B'] = $horarios;
									}
								}
							}
						}
					}*/
					
					//print_r($paraQuery);
					//echo "<br><br>";
					//print_r($paraQuery['(201, 1006, 1505)']);	
					
					$mysqli->close();
					break;
					
				case "inscriptos":
					require 'conexion.php';
					
					//print_r($_FILES);
					//print_r($_REQUEST);
					//BORRAR TODO LO DEL AÑO EN CUESTION
					$query = "DELETE FROM inscriptos
						WHERE CONCAT(anio_academico, ' - ', periodo_lectivo + 0) = '{$_REQUEST['importarA']}'";
					
					//echo $query;
					$mysqli->query($query);
					
					//echo $mysqli->error;
					echo "<p>" . $mysqli->affected_rows . " registros preexistentes borrados.</p>";
					
					
					$file = $_FILES['importar']['tmp_name'];
					
					$lines = file($file, FILE_IGNORE_NEW_LINES);
					$count_lines = count($lines);
					
					
					$paraAgregar = array();
					$errores = array();
					for ($i = 1; $i < $count_lines; $i++) {
						$lines[$i] = preg_split("/\t/", $lines[$i]);
						array_map(array($mysqli, 'real_escape_string'), $lines[$i]);
						$valores = '("' . implode( '", "', $lines[$i]) . '")';
						//echo $valores;
						$query = "INSERT INTO inscriptos VALUES {$valores}";
						$mysqli->query($query);
						//echo $valores;
						if ($mysqli->errno) {
							$errores[] = array(
											'error' => $mysqli->error,
											'query' => $query,
											);
						}
						
					}
					
					$intentos = $count_lines - 1;
					$cantidad_errores = count($errores);
					$agregados = $intentos - $cantidad_errores;
					
					echo "<p>Se intentaron agregar {$intentos} registros.</p>";
					echo "<p>Se agregaron {$agregados} registros con éxito</p>";
					echo "<p>Se encontraron {$cantidad_errores} errores.</p>";
					echo "<br /><a href='../importarinscriptos.php'>Volver</a>";
					
					if ($cantidad_errores > 0) {
						echo "<h2>Errores</h2>";
						print_r($errores);
					}
					
					//echo "<p>" . $mysqli->affected_rows . "INSERTADAS</p>";
					
					$mysqli->close();
					break;
					
					
				default:
					echo "No se realizó la búsqueda";
					
			}
		
	}
	
	
?>
