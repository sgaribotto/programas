﻿<?php
	header('Content-Type: text/html; charset=utf-8');
//Consultas vía AJAX
	//Autoload de la clase.
	session_start();
	require_once '../programas.autoloader.php';
	require './constantes.php';
	//print_r($_SESSION);
	
	if (isset($_GET['act'])) {
		
			switch($_GET['act']) {
				
				case "errorLogging":
					$errorLog = fopen('errorLog.txt', 'a+');
					
					$error = $_GET['error'];
					$date = date('Y - m - d');
					$sesion = json_encode($_SESSION);
					
					$log = $date . "\t" . $error . "\t" . $sesion . "\n";
					
					fwrite($errorLog, $log);
					fclose($errorLog);
					break;
				
				case "buscarDocente":
					require("./conexion.php");
					$query = "SELECT CONCAT_WS(', ', apellido, nombres) AS docente FROM docente WHERE dni = '$_GET[dni]' ";
					$result = $mysqli->query($query);
					
					if ($result->num_rows == 1) {
						$row = $result->fetch_array(MYSQLI_ASSOC);
						
						echo $row['docente'];
					}
					
					break;
				
				case "agregarAfectacion":
				
					//$ANIO = 2017;
					//$CUATRIMESTRE = 1;
					
					$docente = new clases\Docente($_GET['dni']);
					
					if (isset($_SESSION['materia'])) {
						$materiaSesion = $_SESSION['materia'];
						$materia = $materiaSesion;
					}
					
					if (isset($_GET['materia'])) {
						$materia = $_GET['materia'];
					}
					
					
					if (isset($materia)) {
						echo $docente->agregarAfectacion($materia, $_GET['tipo'], $ANIO, $CUATRIMESTRE);
					}
					break;
					
				case "agregarUnidadTematica":
					
					$materia = new clases\Materia($_SESSION['materia']);
					$materia->agregarUnidadTematica($_POST['unidad'], $_POST['descripcion'], $ANIO, $CUATRIMESTRE);
					echo "<script>location.assign('../unidadestematicas.php')</script>";
					break;
					
				case "mostrarDescripcionUnidadTematica":
					
					$materia = new clases\Materia($_SESSION['materia']);
					$descripcion = $materia->mostrarUnidadesTematicas($_GET['unidad'], $ANIO, $CUATRIMESTRE);
					if (isset($descripcion[$_GET['unidad']])) {
						echo $descripcion[$_GET['unidad']];
					}
					break;
					
				case "agregarBibliografia":
					
					$materia = new clases\Materia($_SESSION['materia']);
					$materia->agregarBibliografia($_POST['titulo'], $_POST['autor'], $_POST['editorial'], $_POST['paginas'], $ANIO, $CUATRIMESTRE);
					echo "<script>location.assign('../bibliografia.php')</script>";
					break;
					
				case "agregarCarrera":
					require "./conexion.php";
					
					$query = "SELECT cod FROM carrera WHERE cod = '$_POST[cod]' ";
					$result = $mysqli->query($query);
					
					if ($result->num_rows == 1) {
						$query = "UPDATE carrera SET nombre = '$_POST[nombre]', activo = 1 WHERE cod = '$_POST[cod]'  ";
					} else {
						$query = "INSERT INTO carrera (cod, nombre) VALUES ('$_POST[cod]', '$_POST[nombre]')";
					}
					$result->free();
					
					echo $query;
					$mysqli->query($query);
					
					
					$mysqli->close();
					break;
					
				case "mostrarNombreCarrera":
					require "./conexion.php";
					
					$query = "SELECT cod, nombre FROM carrera WHERE cod = '$_POST[cod]' ";
					$result = $mysqli->query($query);
					$row = $result->fetch_array();
					
					echo $row['nombre'];
					
					break;
				
				case "agregarDocente":
					require "./conexion.php";
					
					$query = "SELECT dni FROM docente WHERE dni = '$_POST[dni]' ";
					$result = $mysqli->query($query);
					$_POST['apellido'] = $mysqli->real_escape_string($_POST['apellido']);
					if ($result->num_rows == 1) {
						$query = "UPDATE docente SET apellido = '$_POST[apellido]', nombres = '$_POST[nombre]', fechanacimiento = '$_POST[fechanacimiento]', fechaingreso = '$_POST[fechaingreso]', activo = 1 WHERE dni = '$_POST[dni]'  ";
						$result->free();
					} else {
						$query = "INSERT INTO docente SET dni = '$_POST[dni]', apellido = '$_POST[apellido]', nombres = '$_POST[nombre]', fechanacimiento = '$_POST[fechanacimiento]', fechaingreso = '$_POST[fechaingreso]'";
					}
					//echo $query;
					
					if ($mysqli->errno) {
						echo "ERROR: " . $mysqli->error;
					} else {
						echo "Se ha cargado el nuevo docente";
					}
					
					
					$mysqli->query($query);
					$mysqli->close();
					break;
				
				case "agregarConstante":
					require "./conexion.php";
					
					$query = "SELECT nombre FROM constantes WHERE nombre = '$_POST[nombre]' ";
					$result = $mysqli->query($query);
					$_POST['valor'] = $mysqli->real_escape_string($_POST['valor']);
					if ($result->num_rows == 1) {
						$query = "UPDATE constantes 
									SET valor = '$_POST[valor]', nombre = '$_POST[nombre]'
									WHERE nombre = '$_POST[nombre]'  ";
						$result->free();
					} else {
						$query = "INSERT INTO constantes 
									SET valor = '$_POST[valor]', nombre = '$_POST[nombre]'";
					}
					//echo $query;
					
					if ($mysqli->errno) {
						echo "ERROR: " . $mysqli->error;
					} else {
						echo "Se ha cargado una nueva constante";
					}
					
					
					$mysqli->query($query);
					$mysqli->close();
					break;
				
				case "buscarDNI":
					require "./conexion.php";
					
					$query = "SELECT apellido, nombres, fechanacimiento, fechaingreso FROM docente WHERE dni = '$_POST[dni]' ";
					$result = $mysqli->query($query);
					if ($result->num_rows == 1) {
						$row = $result->fetch_row();
						$datosDocente = ",";
						foreach ($row as $value) {
							$datosDocente .= $value . ",";
						} 
						echo $datosDocente;
					} else {
						echo "nuevo";
					}
					
					
					$result->free();
					$mysqli->close();
					break;
					
				case "eliminarDocente":
					require "./conexion.php";
					$id = $_REQUEST['id'];
					
					$query = "UPDATE docente SET activo = 0 WHERE id = {$id}";
					$mysqli->query($query);
					$mysqli->close();
					break;
				
				case "eliminarConstante":
					require "./conexion.php";
					$id = $_REQUEST['id'];
					
					$query = "DELETE FROM constantes WHERE id = {$id}";
					$mysqli->query($query);
					$mysqli->close();
					break;
					
				case "agregarPersonal":
					require "./conexion.php";
					
					$query = "SELECT dni FROM personal WHERE dni = '$_POST[dni]' ";
					$result = $mysqli->query($query);
					$_POST['apellido'] = $mysqli->real_escape_string($_POST['apellido']);
					if ($result->num_rows == 1) {
						$query = "UPDATE personal 
									SET apellido = '$_POST[apellido]', 
										nombres = '$_POST[nombre]', 
										usuario = '$_POST[usuario]',
										password = MD5('$_POST[usuario]'),
										activo = 1
									WHERE dni = '$_POST[dni]'  ";
						
					} else {
						$query = "INSERT INTO personal 
									SET dni = '$_POST[dni]', 
										apellido = '$_POST[apellido]', 
										nombres = '$_POST[nombre]', 
										usuario = '$_POST[usuario]', 
										password = MD5('$_POST[usuario]')";
					}
					echo $query;
					$mysqli->query($query);
					if ($mysqli->errno) {
						echo "ERROR: " . $mysqli->error;
					} else {
						echo "Se ha cargado el nuevo docente";
					}
					
					
					
					$mysqli->close();
					break;
				
				case "buscarPersonal":
					require "./conexion.php";
					
					$query = "SELECT apellido, nombres, usuario FROM personal WHERE dni = '$_POST[dni]' ";
					$result = $mysqli->query($query);
					if ($result->num_rows == 1) {
						$row = $result->fetch_row();
						$datosDocente = ",";
						foreach ($row as $value) {
							$datosDocente .= $value . ",";
						} 
						echo $datosDocente;
					} else {
						echo "nuevo";
					}
					
					
					$result->free();
					$mysqli->close();
					break;
					
				case "eliminarPersonal":
					require "./conexion.php";
					$id = $_REQUEST['id'];
					
					$query = "UPDATE personal SET activo = 0 WHERE id = {$id}";
					$mysqli->query($query);
					$mysqli->close();
					break;
					
				case "agregarTurno":
					require "./conexion.php";
					
					$materia = $_REQUEST['materia'];
					if ($_REQUEST['letra'] != 'A') {
						$materia .= $_REQUEST['letra'];
					}
					
					$periodo = explode(' - ', $_REQUEST['periodo']);
					
					//Verificar si se repiten turnos en ese cuatrimestre.
					
					$cod = trim(trim($materia, '('), ')');
					$conjunto = explode(', ', trim($cod, ')B'));
					$errores = array();
					$errores['hayErrores'] = 0;
					//print_r($conjunto);
					foreach ($conjunto as $materiaVerificar) {
						$materiaObjeto = new clases\Materia($materiaVerificar);
						
						$cod_carrera = $materiaObjeto->datosMateria['cod_carrera'];
						$plan = $materiaObjeto->datosMateria['plan'];
						$cuatrimestreEnPlan = $materiaObjeto->datosMateria['cuatrimestre'];
						
		
						$query = "SELECT m.cod, m.nombre, m.plan, t.turno, t.dia
									FROM turnos_con_conjunto AS t
									LEFT JOIN materia AS m
										ON t.materia = m.conjunto
									LEFT JOIN carrera AS c
										ON m.carrera = c.id
									WHERE (t.turno = '{$_REQUEST['turno']}'
											OR t.turno = LEFT('{$_REQUEST['turno']}', 1)
											OR LEFT(t.turno, 1) = '{$_REQUEST['turno']}')
										AND t.dia = '{$_REQUEST['dia']}'
										AND t.anio = {$periodo[0]}
										AND t.cuatrimestre = {$periodo[1]}
										AND m.plan = '{$plan}'
										AND c.cod = '{$cod_carrera}'
										AND m.cuatrimestre = '{$cuatrimestreEnPlan}';";
					
						
						$result = $mysqli->query($query);
						if($mysqli->errno) {
							echo $mysqli->error;
							echo "<br>" . $query;
						}
						
						$errores['errores'] = array();
						while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
							foreach ($row as $key => $value) {
								$errores['errores'][$row['cod']][$key] = $value;
							}
						}
					}
					
					
					if (!empty($errores['errores'])) {
						$errores['hayErrores'] = 1;
						
					}
					
					
					
				
					$query = "INSERT INTO turnos_con_conjunto SET 
						materia = '{$materia}', 
						dia = '{$_REQUEST['dia']}', 
						turno = '{$_REQUEST['turno']}', 
						observaciones = '{$_REQUEST['observaciones']}',
						anio = {$periodo[0]},
						cuatrimestre = {$periodo[1]}";
					
					$mysqli->query($query);
					if ($mysqli->errno) {
						echo $mysqli->error;
					}
					
					$errores = json_encode($errores);
					echo $errores;
					
					
					$mysqli->close();
					break;
				
				case "eliminarTurno":
					require "./conexion.php";
					$query = "DELETE FROM turnos_con_conjunto WHERE id = $_POST[id]";
					$mysqli->query($query);
					$mysqli->close();
					break;
					
				case "agregarMateria":
					require "./conexion.php";
					
					$query = "SELECT cod FROM materia WHERE cod = '$_POST[cod]' ";
					//echo $query;
					$result = $mysqli->query($query);
					$mensaje['tipo'] = "error";
					
					if ($result->num_rows == 1) {
						$mensaje['tipo'] = "modificacion";
						$query = "UPDATE materia SET nombre = '$_POST[nombre]', 
							carrera = '$_POST[carrera]', plan = '$_POST[plan]', 
							cuatrimestre = '$_POST[cuatrimestre]', 
							contenidosminimos = '$_POST[contenidosminimos]', 
							activo = 1 
							WHERE cod = '$_POST[cod]'  ";
						$result->free();
					} else {
						$mensaje['tipo'] = "agregado";
						$query = "INSERT INTO materia SET cod = '$_POST[cod]', 
							nombre = '$_POST[nombre]', carrera = '$_POST[carrera]', 
							plan = '$_POST[plan]', cuatrimestre = '$_POST[cuatrimestre]', 
							contenidosminimos = '$_POST[contenidosminimos]',
							conjunto = CONCAT('(', {$_POST['cod']}, ')');";
					}
					//echo $query;
					
					$mysqli->query($query);
					$mysqli->close();
					break;
					
				case "buscarMateria":
					require "./conexion.php";
					
					$query = "SELECT nombre, carrera, plan, cuatrimestre, contenidosminimos, conjunto FROM materia WHERE cod = '$_POST[cod]' ";
					
					$result = $mysqli->query($query);
					if ($result->num_rows == 1) {
						$row = $result->fetch_row();
						$datosMateria = "///";
						foreach ($row as $value) {
							$datosMateria .= str_replace("///", "/", $value) . "///";
						} 
						echo $datosMateria;
					}
					
					
					$result->free();
					$mysqli->close();
					break;
				
				case "eliminarMateria":
					require "./conexion.php";
					$query = "UPDATE materia SET activo = 0 WHERE cod = '$_POST[cod]' LIMIT 1";
					$mysqli->query($query);
					$mysqli->close();
					break;
				
				case "agregarResponsable":
					require "./conexion.php";
					
					$query = "SELECT id FROM responsable WHERE usuario = '$_POST[usuario]' AND materia = '$_POST[materia]' ";
					echo $query;
					$result = $mysqli->query($query);
					
					if ($result->num_rows == 1) {
						$query = "UPDATE responsable SET activo = 1 WHERE usuario = '$_POST[usuario]' AND materia = '$_POST[materia]' ";
						$result->free();
					} else {
						$query = "INSERT INTO responsable SET usuario = '$_POST[usuario]', materia = '$_POST[materia]' ";
					}
					echo $query;
					
					$mysqli->query($query);
					$mysqli->close();
					break;
					
				case "eliminarResponsable":
					require "./conexion.php";
					$query = "UPDATE responsable SET activo = 0 WHERE id = '$_POST[id]' LIMIT 1";
					$mysqli->query($query);
					$mysqli->close();
					break;
					
				case "mostrarPlanDeClase";
					$materia = new clases\Materia($_SESSION['materia']);
					$planDeClase = $materia->mostrarPlanDeClase($_POST['clase'], $ANIO, $CUATRIMESTRE);
					
					$datos = "";
					foreach ($planDeClase as $value) {
						$datos .= $value . '|';
					}
					
					echo $datos;
					break;
					
				case "agregarPlanDeClase":
					require "./conexion.php";
					
					$query = "SELECT id FROM cronograma WHERE clase = '$_POST[clase]' AND materia = '$_SESSION[materia]' AND anio = $ANIO AND cuatrimestre = $CUATRIMESTRE ";
					//echo $query . "<br />";
					
					/*$docentes = implode('/ ', $_POST['docente']);
					$bibliografia = implode('/ ', $_POST['bibliografia']);
					$metodo = implode('/ ', $_POST['metodo']);*/
										
					$result = $mysqli->query($query);
					
					if ($result->num_rows == 1) {
						
						$query = "UPDATE cronograma SET fecha = '$_POST[fecha]', unidadtematica = '$_POST[unidadtematica]', descripcion = '$_POST[descripcion]'
										WHERE clase = '$_POST[clase]' AND materia = '$_SESSION[materia]' AND anio = $ANIO AND cuatrimestre = $CUATRIMESTRE ";
						$result->free();
					} else {
						$query = "INSERT INTO cronograma SET fecha = '$_POST[fecha]', unidadtematica = '$_POST[unidadtematica]', descripcion = '$_POST[descripcion]', 
										clase = '$_POST[clase]', materia = '$_SESSION[materia]', anio = $ANIO, cuatrimestre = $CUATRIMESTRE ";
					}
					//echo $query;
					
					$mysqli->query($query);
					$mysqli->close();
					echo "<script>location.assign('../cronograma.php?clase=" . $_POST['clase'] . "');</script>";
					break;
					
				case "mostrarPlanDeClase";
					$materia = new clases\Materia($_SESSION['materia']);
					$planDeClase = $materia->mostrarPlanDeClase($_POST['clase']);
					
					$datos = "";
					foreach ($planDeClase as $value) {
						$datos .= $value . '|';
					}
					
					echo $datos;
					break;
					
				case "agregarAula":
					require "./conexion.php";
					
					$query = "SELECT id FROM aulas WHERE cod = '$_REQUEST[aula]'";
										
					$result = $mysqli->query($query);
					
					if ($result->num_rows == 1) {
						
						$query = "UPDATE aulas SET activo = 1, capacidad = $_REQUEST[capacidad], mas_info = '$_REQUEST[mas_info]'
										WHERE cod = '$_REQUEST[aula]'";
						$result->free();
					} else {
						$query = "INSERT INTO aulas SET activo = 1, capacidad = $_REQUEST[capacidad], mas_info = '$_REQUEST[mas_info]', cod = '$_REQUEST[aula]'";
					}
					//echo $query;
					
					$mysqli->query($query);
					$mysqli->close();
					//echo "<script>location.assign('../cronograma.php?clase=" . $_POST['clase'] . "');</script>";
					break;
					
				case "mostrarAula":
					require "./conexion.php";
					
					$query = "SELECT capacidad, mas_info 
								FROM aulas 
								WHERE cod = '$_REQUEST[aula]'";
					//echo $query;
					$result = $mysqli->query($query);
					$row = array();
					if ($result->num_rows == 1) {
						$row = $result->fetch_array(MYSQLI_ASSOC);
						//print_r($row);
						
					}
					$data = implode(' | ', $row);
					
					$result->free();
					$mysqli->close();
					echo $data;
					break;
					
				case "eliminarAula":
					require "./conexion.php";
					$query = "UPDATE aulas SET activo = 0 WHERE id = $_REQUEST[id] LIMIT 1";
					echo $query;
					$mysqli->query($query);
					$mysqli->close();
					break;
					
				case "eliminarAfectacion":
					require "./conexion.php";
					$id = $mysqli->real_escape_string($_POST['id']);
					$query = "UPDATE afectacion SET activo = 0 WHERE id = {$id} LIMIT 1";
					
					$mysqli->query($query);
					echo $mysqli->error;
					$mysqli->close();
					break;
					
				case "eliminarUnidadTematica":
					require "./conexion.php";
					$query = "UPDATE unidad_tematica SET activo = 0 
							WHERE 
								unidad = '$_POST[unidadtematica]' 
								AND materia = '$_SESSION[materia]' 
								AND anio = $ANIO 
								AND cuatrimestre = $CUATRIMESTRE";
					//echo $query;
					$mysqli->query($query);
					//echo $mysqli->error;
					$mysqli->close();
					break;
					
				case "eliminarBibliografia":
					require "./conexion.php";
					$query = "UPDATE bibliografia SET activo = 0 WHERE id = '$_POST[id]' LIMIT 1";
					echo $query;
					$mysqli->query($query);
					$mysqli->close();
					break;
				
				case "eliminarPlanDeClase":
					require "./conexion.php";
					$query = "DELETE FROM cronograma WHERE id = '$_POST[id]' LIMIT 1";
					echo $query;
					$mysqli->query($query);
					$mysqli->close();
					break;
					
				case "listaDocentes":
					require "./conexion.php";
					$query = "SELECT dni, apellido, nombres FROM docente ORDER BY apellido, nombres";
					$result = $mysqli->query($query);
					
					$listaDocentes = "";
					while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
						$listaDocentes .= $row['dni'] . " -- " . $row['dni'] . " - " . $row['apellido'] . ", " . $row['nombres'] . "***";
					}
					
					echo $listaDocentes;
					$mysqli->close();
					break;
					
				case "agregarAgregadosCronograma":
					require "./conexion.php";
					$tipo = $_POST['tipo'];
					$clase = $_POST['clase'];
					$valor = json_encode($_POST, JSON_UNESCAPED_UNICODE);
					
					$query = "INSERT INTO agregados_cronograma (tipo, materia, clase, valor, anio, cuatrimestre) VALUES
								('$tipo', $_SESSION[materia], $clase, '$valor', $ANIO, $CUATRIMESTRE)";
					echo $query;
					$mysqli->query($query);
					
					$mysqli->close();
					
					break;
					
				case "actualizarTabla":
					require "./conexion.php";
					$clase = $_POST['clase'];
					$materia = $_SESSION['materia'];
					$tabla = $_GET['tabla'];
					
					$query = "SELECT valor, id
										FROM agregados_cronograma
										WHERE clase = $clase AND materia = $materia AND anio = $ANIO AND cuatrimestre = $CUATRIMESTRE AND activo = 1 AND tipo = '$tabla' ";
					$result = $mysqli->query($query);
					switch ($tabla){
						case "bibliografia":
							$valores = array();
							$totalPaginas = 0;
							$tabla = "";
							while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
								$valores[$row['id']] = $row['valor'];
								
							}
							
							foreach ($valores as $id => $value) {
								$valoresTabla = json_decode($value);
								//print_r($valoresTabla);
								$tabla .= '<tr class="agregadosCronograma formularioLateral">';
								$tabla .= '<td class="agregadosCronograma formularioLateral">' . $valoresTabla->titulo . '</td>';
								$tabla .= '<td class="agregadosCronograma formularioLateral">' . $valoresTabla->paginas . '</td>';
								$tabla .= '<td class="formularioLateral correlatividadesTable"><button type="button" class="botonEliminarAgregadoCronograma" data-id="' . $id . '" >X</button></td>';
							$tabla .= '</tr>';
								$totalPaginas += $valoresTabla->paginas;
							}
							
							$tabla .= '<tr class="agregadosCronograma formularioLateral">';
								$tabla .= '<td class="agregadosCronograma formularioLateral">' . '<b>TOTAL DE PÁGINAS</b>' . '</td>';
								$tabla .= '<td class="agregadosCronograma formularioLateral"><b>' . $totalPaginas . '</b></td>';
								$tabla .= '</tr>';
							break;
							
							
							
						case "metodo":
							$valores = array();
							$totalActivo = 0;
							$claseCubierta = 0;
							$tabla = "";
							while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
								$valores[$row['id']] = $row['valor'];
								
								
							}
							
							foreach ($valores as $id => $value) {
								$valoresTabla = json_decode($value);
								//print_r($valoresTabla);
								$tabla .= '<tr class="agregadosCronograma formularioLateral">';
								$tabla .= '<td class="agregadosCronograma formularioLateral">' . $valoresTabla->metodo . '</td>';
								$tabla .= '<td class="agregadosCronograma formularioLateral">' . $valoresTabla->activo . '</td>';
								$tabla .= '<td class="agregadosCronograma formularioLateral">' . (100 - $valoresTabla->activo) . '</td>';
								$tabla .= '<td class="agregadosCronograma formularioLateral">' . $valoresTabla->porcentajeClase . '</td>';
								$tabla .= '<td class="formularioLateral correlatividadesTable"><button type="button" class="botonEliminarAgregadoCronograma" data-id="' . $id . '" >X</button></td>';
								$tabla .= '</tr>';
								$totalActivo += ($valoresTabla->activo * $valoresTabla->porcentajeClase / 100);
								$claseCubierta += $valoresTabla->porcentajeClase;
							}
							
							$tabla .= '<tr class="agregadosCronograma formularioLateral">';
								$tabla .= '<td class="agregadosCronograma formularioLateral">' . '<b>TOTALES</b>' . '</td>';
								$tabla .= '<td class="agregadosCronograma formularioLateral"><b>' . $totalActivo . '</b></td>';
								$tabla .= '<td class="agregadosCronograma formularioLateral"><b>' . (100 - $totalActivo) . '</b></td>';
								$tabla .= '<td class="agregadosCronograma formularioLateral alerta100 porcentajeCubierto"><b>' . $claseCubierta . '</b></td>';
								$tabla .= '</tr>';
							break;
						
						case "docente":
							$valores = array();
							$tabla = "";
							while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
								$valores[$row['id']] = $row['valor'];
							}
							
							foreach ($valores as $id => $value) {
								$valoresTabla = json_decode($value);
								//print_r($valoresTabla);
								$tabla .= '<tr class="agregadosCronograma formularioLateral">';
								$tabla .= '<td class="agregadosCronograma formularioLateral">' . $valoresTabla->docente . '</td>';
								$tabla .= '<td class="agregadosCronograma formularioLateral">' . $valoresTabla->horasClase . '</td>';
								$tabla .= '<td class="formularioLateral correlatividadesTable"><button type="button" class="botonEliminarAgregadoCronograma" data-id="' . $id . '" >X</button></td>';
								$tabla .= '</tr>';
								
							}
							
							/*$tabla .= '<tr class="agregadosCronograma formularioLateral">';
								$tabla .= '<td class="agregadosCronograma formularioLateral">' . '<b>TOTALES</b>' . '</td>';
								$tabla .= '<td class="agregadosCronograma formularioLateral"><b>' . $totalActivo . '</b></td>';
								$tabla .= '<td class="agregadosCronograma formularioLateral"><b>' . (100 - $totalActivo) . '</b></td>';
								$tabla .= '<td class="agregadosCronograma formularioLateral alerta100"><b>' . $claseCubierta . '</b></td>';
								$tabla .= '</tr>';*/
							break;
							
							
					}
					print_r($tabla);
					
					$result->free();
					
					$mysqli->close();
					break;
					
				case "eliminarAgregadoCronograma":
					require "./conexion.php";
					
					$query = "DELETE FROM agregados_cronograma WHERE id = $_POST[id]";
					$mysqli->query($query);
					echo $query;
					$mysqli->close();
					break;
					
				case "toggleMenuAdmin":
					if (isset($_SESSION['admin']) and $_SESSION['admin']) {
						$_SESSION['admin'] = false;
					} else {
						$_SESSION['admin'] = true;
					}
					print_r($_SESSION['admin']);
					break;
					
				case "esAdmin":
					print_r($_SESSION['admin']);
					break;
					
				case "traerProgramaMateria":
					$_SESSION['materiaTemporal'] = $_POST['materia'];
					$_SESSION['cuatrimestreTemporal'] = $_POST['periodo'];
					break;
					
				case "actualizarTablaAceptarDesignaciones":
					require "./conexion.php";
					
					$carreras = array();
								$estados = array();
								
								if (in_array(2, $_SESSION['permiso'])) { //PERMISOS DEL SECRETARIO
									 $carreras[] = 2;
									 $carreras[] = 1;
									 $carreras[] = 3;
									 $carreras[] = 4;
									 $opcionesEstado = array(
										'Pendiente' => 'disabled',
										'AprobadoCOORD' => ['AprobadoSA', 'RechazadoSA'],
										'RechazadoCOORD' => 'disabled',
										'AprobadoSA' => 'disabled',
										'RechazadoSA' => 'disabled',
										'AprobadoADMIN' => 'disabled',
										'RechazadoADMIN' => ['AprobadoSA', 'RechazadoSA'],
										'designado' => 'disabled',
									);
								} 
								if (in_array(3, $_SESSION['permiso'])) { //PERMISOS DEL DIRECTOR DE ADMINISTRACIÓN
									 $carreras[] = 2;
									 $carreras[] = 1;
									 $carreras[] = 3;
									 $carreras[] = 4;
									 $opcionesEstado = array(
										'Pendiente' => 'disabled',
										'AprobadoCOORD' => 'disabled',
										'RechazadoCOORD' => 'disabled',
										'AprobadoSA' => ['AprobadoADMIN', 'RechazadoADMIN'],
										'RechazadoSA' => 'disabled',
										'AprobadoADMIN' => 'disabled',
										'RechazadoADMIN' => 'disabled',
										'designado' => 'disabled',
									);
								} 
								if (in_array(4, $_SESSION['permiso'])) { //Permisos del director de carrera ECONOmiA
									 $carreras[] = 2;
									 $carreras[] = 4;
									 $opcionesEstado = array(
										'Pendiente' => ['AprobadoCOORD', 'RechazadoCOORD'],
										'AprobadoCOORD' => 'disabled',
										'RechazadoCOORD' => 'disabled',
										'AprobadoSA' => 'disabled',
										'RechazadoSA' => ['AprobadoCOORD', 'RechazadoCOORD'],
										'AprobadoADMIN' => 'disabled',
										'RechazadoADMIN' => 'disabled',
										'designado' => 'disabled',
									);
								} 
								if (in_array(5, $_SESSION['permiso'])) { //Permisos del director de carrera ADMIN
									 $carreras[] = 1;
									 $carreras[] = 4;
									 $opcionesEstado = array(
										'Pendiente' => ['AprobadoCOORD', 'RechazadoCOORD'],
										'AprobadoCOORD' => 'disabled',
										'RechazadoCOORD' => 'disabled',
										'AprobadoSA' => 'disabled',
										'RechazadoSA' => ['AprobadoCOORD', 'RechazadoCOORD'],
										'AprobadoADMIN' => 'disabled',
										'RechazadoADMIN' => 'disabled',
										'designado' => 'disabled',
									);
								} 
								if (in_array(6, $_SESSION['permiso'])) { //Permisos del director de carrera TURISMO
									 $carreras[] = 3;
									 $opcionesEstado = array(
										'Pendiente' => ['AprobadoCOORD', 'RechazadoCOORD'],
										'AprobadoCOORD' => 'disabled',
										'RechazadoCOORD' => 'disabled',
										'AprobadoSA' => 'disabled',
										'RechazadoSA' => ['AprobadoCOORD', 'RechazadoCOORD'],
										'AprobadoADMIN' => 'disabled',
										'RechazadoADMIN' => 'disabled',
										'designado' => 'disabled',
									);
								} 
								
							$inCarreras = "(" . implode(', ', $carreras) . ")";			
					
					$where = " WHERE a.activo = 1 AND c.id in $inCarreras";
					
					foreach ($_POST as $key => $value) {
						if ($value != "") {
							$where .= " AND " . str_replace('*', '.', $key) . " LIKE '%$value%' ";
						}
					}
					
					$query = "SELECT a.id, CONCAT(d.apellido, ', ', d.nombres) AS docente, m.nombre, m.cod as cod_materia, c.cod, 
								CONCAT(a.anio, '-', a.cuatrimestre) as periodo, a.tipoafectacion, a.estado
							FROM afectacion AS a
							LEFT JOIN docente AS d ON a.docente = d.id
							LEFT JOIN materia AS m ON a.materia = m.cod
							LEFT JOIN carrera AS c ON m.carrera = c.id
								$where";
								
					///echo $query;
					echo "<br />";
					
					
					$result = $mysqli->query($query);
					/*if ($mysqli->errno) {
						printf("Database error:<br /> %s", $mysqli->error);
						exit();
					}*/ //ERRORES DE MYSQL
					
					echo "<table class='aceptarDesignacion'><thead class='aceptarDesignacion'>
						<tr class='plantelActual'>
							<th class='aceptarDesignacion' style='width:35%;'>Docente</th>
							<th class='aceptarDesignacion' style='width:35%;'>Materia</th>
							<!--<th class='aceptarDesignacion' style='width:10%;'>Carrera</th>
							<th class='aceptarDesignacion' style='width:10%;'>Periodo</th>-->
							<th class='aceptarDesignacion' style='width:15%;'>Cargo</th>
							<th class='aceptarDesignacion' style='width:15%;'>Estado</th>
						</tr></thead>";
					echo "<tbody class='aceptarDesignacion'>";
					while ($row = $result->fetch_array(MYSQL_ASSOC)) {
						echo "<tr class='aceptarDesignacion'>
								<td class='aceptarDesignacion'>$row[docente]</td>
								<td class='aceptarDesignacion linkResumenMateria' data-cod='$row[cod_materia]'>$row[nombre]</td>
								<!--<td class='aceptarDesignacion'>$row[cod]</td>
								<td class='aceptarDesignacion'>$row[periodo]</td>-->
								<td class='aceptarDesignacion'>$row[tipoafectacion]</td>";
								
								
								$estado = "<select class='aceptarDesignacion' data-id='$row[id]'>
											<option class='aceptarDesignacion' value='$row[estado]' selected='selected'>$row[estado]</option>";
									
									if ($opcionesEstado[$row['estado']] != 'disabled') {
										
										foreach ($opcionesEstado[$row['estado']] as $opcion) {
											$estado .= "<option class='aceptarDesignacion'>$opcion</option>";
										}
									}
								$estado .= "</select>";
								
								echo "<td class='aceptarDesignacion'>$estado</td>
							</tr>";
					}
					
					
					
					echo "</tbody></table>";
					
					$result->free();
					$mysqli->close();
					break;
					
				case "cambiarEstadoDesignacion":
					require "conexion.php";
					$id = $_POST['id'];
					$estado = $_POST['estado'];
					
					$query = "UPDATE afectacion SET estado = '$estado' WHERE id = $id ";
					//echo $query;
					$mysqli->query($query);
					
					$mysqli->close();
					break;
					
				case "signacionDeAula":
					require 'conexion.php';
					//print_r($_REQUEST);
					$query = "INSERT INTO asignacion_aulas (aula, materia, cantidad_alumnos, dia, turno, comision, anio, cuatrimestre)
								VALUES ('$_REQUEST[aula]', '$_REQUEST[conjunto]', $_REQUEST[cantidad], '$_REQUEST[dia]', '$_REQUEST[turno]', 
								'$_REQUEST[comision]', $_REQUEST[anio], $_REQUEST[cuatrimestre])";
					$mysqli->query($query);
					$error = $mysqli->error;
					
					if ($mysqli->errno) {
						$error = strtolower($mysqli->error);
						//echo strpos($error, 'duplicate') . "<br />";
						if (!(strpos($error, 'duplicate') === false)) {
							if (!(strpos($error, 'aula') === false)) {
								echo "El aula ya está asignada en el turno seleccionado";
							} else {
								echo "La materia y comisión seleccionada ya tiene un aula asignada";
							}
						} else {
							echo "Error desconocido en la base de datos, por favor comuniquese con Santiago";
						}
					}
							
					$mysqli->close();
								
					break;
				
				case "eliminarAsignacionDeAula":
					require "./conexion.php";
					//print_r ($_REQUEST);
					$query = "DELETE FROM asignacion_aulas WHERE id = $_REQUEST[id]";
					$mysqli->query($query);
					echo $mysqli->error;
					$mysqli->close();
					break;
					
				case "listarMaterias":
					require 'conexion.php';
					
					$filtro['m.nombre'] = $_REQUEST['materia'];
					$filtro['m.cuatrimestre'] = $_REQUEST['cuatrimestre'];
					$filtro['m.carrera'] = $_REQUEST['carrera'];
					
					$tipos = array();
					$carreras = '()';
					$tipos['designados'] = "('designados')";
					
					if (in_array(2, $_SESSION['permiso']) ) {
						$tipos['pendientes'] = "('AprobadoCOORD', 'RechazadoADMIN')";
						$tipos['aceptados'] = "('AprobadoSA', 'AprobadoADMIN')";
						$tipos['rechazados'] = "('RechazadoCOORD', 'RechazadoSA')";
						$tipos['propuestos'] = "('Pendiente')";
						$carreras = '(1, 2, 3, 4, 5)';
					} elseif (in_array(3, $_SESSION['permiso'])  ) {
						$tipos['pendientes'] = "('AprobadoSA')";
						$tipos['aceptados'] = "('AprobadoADMIN')";
						$tipos['rechazados'] = "('RechazadoCOORD', 'RechazadoSA', 'RechazadoADMIN')";
						$tipos['propuestos'] = "('Pendiente', 'AprobadoCOORD')";
						$carreras = '(1, 2, 3, 4, 5)';
					} elseif (in_array(4, $_SESSION['permiso']) or in_array(5, $_SESSION['permiso']) or in_array(6, $_SESSION['permiso']) ) {
						$tipos['pendientes'] = "('RechazadoSA', 'Pendiente')";
						$tipos['aceptados'] = "('AprobadoCOORD', 'AprobadoSA', 'AprobadoADMIN')";
						$tipos['rechazados'] = "('RechazadoCOORD','RechazadoADMIN')";
						$tipos['propuestos'] = "('')";
						if (in_array(4, $_SESSION['permiso'])) {
							$carreras = '(2, 4)';
						}
						if (in_array(5, $_SESSION['permiso'])) {
							$carreras = '(1, 4)';
						}
						if (in_array(6, $_SESSION['permiso'])) {
							$carreras = '(3)';
						}
					}
					
					$where = "1 = 1 AND c.id IN $carreras ";
					foreach ($filtro as $key => $value) {
						if ($value != "") {
							$where .= "AND $key = '$value' ";
						}
					}
					
					
					$query = "SELECT m.cuatrimestre, m.conjunto, m.nombre, m.plan, c.nombre AS carrera,
									SUM(IF (a.estado IN $tipos[pendientes] AND a.cuatrimestre = 2 AND a.anio = 2015 AND a.activo = 1, 1, 0)) AS pendiente,
									SUM(IF (a.estado IN $tipos[aceptados] AND a.cuatrimestre = 2 AND a.anio = 2015 AND a.activo = 1, 1, 0)) AS aceptado,
									SUM(IF (a.estado IN $tipos[rechazados] AND a.cuatrimestre = 2 AND a.anio = 2015 AND a.activo = 1, 1, 0)) AS rechazado,
									SUM(IF (a.estado IN $tipos[propuestos] AND a.cuatrimestre = 2 AND a.anio = 2015 AND a.activo = 1, 1, 0)) AS propuesto,
									SUM(IF (a.estado IN $tipos[designados] AND a.cuatrimestre = 2 AND a.anio = 2015 AND a.activo = 1, 1, 0)) AS designado
								FROM materia AS m
								LEFT JOIN carrera AS c ON m.carrera = c.id
								LEFT JOIN afectacion AS a ON a.materia = m.cod
								WHERE $where
								GROUP BY conjunto
								ORDER BY m.carrera, m.cuatrimestre";
								
								//echo $query;
					
					$result = $mysqli->query($query);
					//echo $mysqli->error;
					$materias = array();
					
					while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
						//$materias[$row['carrera']][$row['cuatrimestre']][$row['conjunto'] . $row['nombre']]['turnos'][] = $row['turno'];
						$materias[$row['carrera']][$row['cuatrimestre']][$row['conjunto'] . $row['nombre']]['pendiente'] = $row['pendiente'];
						$materias[$row['carrera']][$row['cuatrimestre']][$row['conjunto'] . $row['nombre']]['propuesto'] = $row['propuesto'];
						$materias[$row['carrera']][$row['cuatrimestre']][$row['conjunto'] . $row['nombre']]['aceptado'] = $row['aceptado'];
						$materias[$row['carrera']][$row['cuatrimestre']][$row['conjunto'] . $row['nombre']]['rechazado'] = $row['rechazado'];
						$materias[$row['carrera']][$row['cuatrimestre']][$row['conjunto'] . $row['nombre']]['designado'] = $row['designado'];
					}
					echo "<table class='materias'>";
						foreach ($materias AS $carrera => $materia) {
							echo "<tr class='subtitulo'>
									<th colspan='6' style='text-align:center;font-size:1.2em;'>Carrera: $carrera</th>
								</tr>";
							foreach ($materia AS $cuatrimestre => $datos) {
								echo "<tr class='subtitulo'>
									<th class='materias'>Cuatrimestre $cuatrimestre</th>
									<th class='materias'>Pendientes</th>
									<th class='materias'>Propuestos</th>
									<th class='materias'>Aceptados</th>
									<th class='materias'>Rechazados</th>
									<th class='materias'>Designados</th>
								</tr>";
								foreach ($datos AS $nombre => $turno) {
									
									echo "<tr class='info'>
											<td class='info masInfo'>$nombre</td>";
									echo "<td class='materia' style='text-align:center;'>$turno[pendiente]</td>";
									echo "<td class='materia' style='text-align:center;'>$turno[propuesto]</td>";
									echo "<td class='materia' style='text-align:center;'>$turno[aceptado]</td>";
									echo "<td class='materia' style='text-align:center;'>$turno[rechazado]</td>";
									echo "<td class='materia' style='text-align:center;'>$turno[designado]</td>";
											//<td class='info'>". implode(', ', $turno['turnos']) . "</td>";
									echo "</tr>";
								}
							}
						}
					echo "</table>";
					break;
					
					case "buscarConjunto":
					require "./conexion.php";
					
					$query = "SELECT conjunto FROM materia WHERE cod = '$_POST[cod]' LIMIT 1";
					$result = $mysqli->query($query);
					
					if ($result->num_rows == 1) {
						$conjunto = $result->fetch_array(MYSQL_ASSOC);
						$result->free();
						
						$query = "SELECT cod, nombre FROM materia WHERE conjunto = '$conjunto[conjunto]'";
						//echo $query;
						$result = $mysqli->query($query);
						
						$asociadas = array();
						while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
							$asociadas[] = $row;
						}
						
						$asociadas = json_encode($asociadas);
						echo $asociadas;
						$result->free();
						
					} else {
						$error['error'] = "No se encontró la materia";
						echo json_encode($error);
					}
					
					$mysqli->close();
					break;
					
				case "agregarConjunto":
					require 'conexion.php';
					//print_r($_REQUEST);
					$query = "SELECT conjunto FROM materia WHERE cod = '$_REQUEST[cod]'";
					
					$result = $mysqli->query($query);
					
					while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
						$conjunto[0] = $row['conjunto'];
					}
					//echo $conjunto[0];
					$result->free();
					echo $mysqli->error;
					
					$query = "SELECT conjunto FROM materia WHERE cod = '$_REQUEST[agregar]'";
					$result = $mysqli->query($query);
					
					if ($result->num_rows) {
					
						if (!preg_match('/[\(\s]' . $_REQUEST['agregar'] . '[,\)]/', $conjunto[0])) {
							$conjunto[1] = preg_replace('/\(|\)/', '', $conjunto[0]);
							
							$conjunto[1] = explode(', ', $conjunto[1]);
							
							$conjunto[1][] = $_REQUEST['agregar'];
							sort($conjunto[1]);
							$conjunto[1] = "(" . implode(', ', $conjunto[1]) . ")";
						
							//echo $conjunto[1];
							$query = "UPDATE materia SET conjunto = '$conjunto[1]' WHERE conjunto = '$conjunto[0]' OR cod = '$_REQUEST[agregar]'";
							$mysqli->query($query);
							echo $mysqli->error;
							
							$mensaje['exito'] = 1;
						}
					} else {
						$mensaje['exito'] = 0;
						$mensaje['error'] = "No se encontró la materia a agregar";
						
					}
					
					echo json_encode($mensaje);		
					$mysqli->close();
								
					break;
					
				case "eliminarConjunto":
					require "./conexion.php";
					
					$retirar = $_REQUEST['cod'];
					
					$query = "SELECT conjunto FROM materia WHERE cod = '$_REQUEST[cod]'";
					$result = $mysqli->query($query);
					while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
						$conjuntoOriginal = $row['conjunto'];
					}
					
					$conjunto = preg_replace('/([\(\s])' . $retirar . '([,\)])/', '$1$2', $conjuntoOriginal);
					$conjunto = str_replace(', )', ')', $conjunto);
					$conjunto = str_replace('(, ', '(', $conjunto);
					$conjunto = str_replace(', ,', ',', $conjunto); 
					//print_r ($_REQUEST);
					$query = "UPDATE materia SET conjunto = '$conjunto' WHERE conjunto = '$conjuntoOriginal'";
					$mysqli->query($query);
					echo $mysqli->error;
					
					$query = "UPDATE materia SET conjunto = '($retirar)' WHERE cod = '$retirar'";
					$mysqli->query($query);
					echo $mysqli->error;
					$mysqli->close();
					break;
					
				case "buscarCorrelativa":
					require "./conexion.php";
					
						$materia = $_REQUEST['cod'];
					
						$query = "SELECT c.requisito, c.tipo, m.nombre
							FROM correlatividad AS c
							LEFT JOIN materia AS m
								ON m.cod = c.requisito
							WHERE materia = '{$materia}'
							ORDER BY c.tipo, c.requisito";
						//echo $query;
						$result = $mysqli->query($query);
						
						$correlativas = array();
						while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
							$correlativas[] = $row;
						}
						
						$correlativas = json_encode($correlativas);
						echo $correlativas;
						$result->free();
						
					
					
					$mysqli->close();
					break;
					
				case "agregarCorrelativa":
					require 'conexion.php';
					$materia = $_REQUEST['cod'];
					$requisito = $_REQUEST['requisito'];
					$tipo = $_REQUEST['tipo'];
					
					if ($tipo == "Ambas") {
						$query = "INSERT INTO correlatividad (materia, requisito, tipo)
							VALUES ({$materia}, {$requisito}, 'Total'),
							({$materia}, {$requisito}, 'Cursada');";
						$mysqli->query($query);
					} else {
						
						$query = "INSERT INTO correlatividad (materia, requisito, tipo)
							VALUES ({$materia}, {$requisito}, '{$tipo}');";
						$mysqli->query($query);
					}
					
					$mensaje['exito'] = 1;
					if ($mysqli->errno) {
						$mensaje['exito'] = 0;
						$mensaje['error'] = "ERROR: " . $mysqli->error;
					}
					
					echo json_encode($mensaje);		
					$mysqli->close();
								
					break;
					
				case "eliminarCorrelativa":
					require 'conexion.php';
					$materia = $_REQUEST['cod'];
					$requisito = $_REQUEST['requisito'];
					$tipo = $_REQUEST['tipo'];
					print_r($_REQUEST);
					$query = "DELETE FROM correlatividad
								WHERE materia = {$materia}
									AND requisito = {$requisito}
									AND tipo = '{$tipo}';";
					echo $query;
					
					$mysqli->query($query);
					
					$mensaje['exito'] = 1;
					if ($mysqli->errno) {
						$mensaje['exito'] = 0;
						$mensaje['error'] = "ERROR: " . $mysqli->error;
					}
					
					echo json_encode($mensaje);		
					$mysqli->close();
					break;
					
				case "agregarAsignacionComision":
					
					
					//$ANIO = 2017;
					//$CUATRIMESTRE = 1;
					
					if ($ASIGNAR_COMISIONES) {
						require "./conexion.php";
						$materia = new clases\Materia($_SESSION['materia']);
						$conjunto = $materia->datosMateria['conjunto'];
						$id = $_REQUEST['comision'];
						$usuario = $_SESSION['usuario'];
						
						$query = "SELECT turno, horario, nombre_comision, dependencia
							FROM comisiones_abiertas 
							WHERE id = {$id};";
						
						$result = $mysqli->query($query);
						$datos = $result->fetch_array(MYSQLI_ASSOC);
						
						
						
						$result->free();
						
						
						
						//Prevención de duplicados
						$query = "SELECT COUNT(*) AS cantidad FROM asignacion_comisiones
									WHERE turno = '{$datos['turno']}'
										AND materia = '{$conjunto}'
										AND comision = '{$datos['nombre_comision']}'
										AND dependencia = '{$datos['dependencia']}'
										AND anio = {$ANIO}
										AND cuatrimestre = {$CUATRIMESTRE};";
						$result = $mysqli->query($query);
						//echo '{error:MYSQL-> ' . $mysqli->error . '}'; 
						$cantidad = $result->fetch_array(MYSQL_ASSOC)['cantidad'];
						$result->free();
						
						if (!$cantidad) {
							$query = "INSERT INTO asignacion_comisiones (docente, materia, turno, comision, dependencia, usuario_ultima_modificacion, anio, cuatrimestre)
										VALUES ({$_REQUEST['docente']}, '{$conjunto}', '{$datos['turno']}', '{$datos['nombre_comision']}',
													'{$datos['dependencia']}','{$usuario}', {$ANIO}, {$CUATRIMESTRE})";
							$mysqli->query($query);
							$mensajes['exito'] = 'true';
						} else {
							//NO DUPLICAR
							/*$mensajes['error'] = "Solo puede agregar un docente por comisión en esta etapa";*/
							
							//DUPLICAR HABILITADO
							$query = "INSERT INTO asignacion_comisiones (docente, materia, turno, comision, dependencia, usuario_ultima_modificacion, anio, cuatrimestre)
										VALUES ({$_REQUEST['docente']}, '{$conjunto}', '{$datos['turno']}', '{$datos['nombre_comision']}',
													'{$datos['dependencia']}', '{$usuario}', {$ANIO}, {$CUATRIMESTRE})";
							$mysqli->query($query);
							$mensajes['exito'] = 'true';
							
						}
						echo json_encode($mensajes);			
						$mysqli->close();
					} else {
						$mensajes['exito'] = 'false';
						$mensajes['error'] = 'El periodo de asignación de comisiones está cerrado';
						
						echo json_encode($mensajes);
					}
					break;
				case "agregarAsignacionComisionCalendario":
					
					
					//NO LO EMPECÉ
					
					if ($ASIGNAR_COMISIONES) {
						require "./conexion.php";
						$materia = new clases\Materia($_SESSION['materia']);
						$docente = $_REQUEST['docente'];
						$conjunto = $materia->datosMateria['conjunto'];
						$dia = $_REQUEST['dia'];
						$turno = $_REQUEST['turno'];
						$comision = $_REQUEST['comision'];
						$anio = $ANIO;
						$cuatrimestre = $CUATRIMESTRE;
						$usuario = $_SESSION['usuario'];
					
						$query = "INSERT INTO asignacion_comisiones_calendario 
							(docente, materia, turno, comision, dependencia, usuario_ultima_modificacion, anio, cuatrimestre, dia)
									VALUES ({$docente}, '{$conjunto}', '{$turno}', '{$comision}',
												'EEYN', '{$usuario}', {$ANIO}, {$CUATRIMESTRE}, '$dia')";
						$mysqli->query($query);
						$mensajes['exito'] = 'true';
							
					
						echo json_encode($mensajes);			
						$mysqli->close();
					} else {
						$mensajes['exito'] = 'false';
						$mensajes['error'] = 'El periodo de asignación de comisiones está cerrado';
						
						echo json_encode($mensajes);
					}
					break;
					
				case "eliminarComisionAsignada":
					require "./conexion.php";
					
					$id = $_REQUEST['id'];
					$query = "DELETE FROM asignacion_comisiones WHERE id = $id";
					echo $query;
					$mysqli->query($query);
					
					$mysqli->close();
					
					break;
				case "tablaAsignacionComisiones":
					$materia = new clases\Materia($_SESSION['materia']);
					$conjunto = $materia->mostrarConjunto();
					require 'conexion.php';
					//print_r($materia);
					$anio = $ANIO;
					$cuatrimestre = $CUATRIMESTRE;
					
					//$anio = 2017;
					//$cuatrimestre = 2;
					
					$query = "SELECT IFNULL(a.id, 'empty') AS id, c.nombre_comision, c.horario, 
									CONCAT(d.apellido, ', ', d.nombres) AS docente,
									aula_virtual
								FROM comisiones_abiertas AS c
								LEFT JOIN asignacion_comisiones AS a
									ON a.anio = c.anio AND a.cuatrimestre = c.cuatrimestre
										AND c.nombre_comision = a.comision AND c.materia = a.materia
								LEFT JOIN docente AS d
									ON d.id = a.docente
								WHERE c.materia = '{$conjunto}'
									AND c.anio = {$anio}
									AND c.cuatrimestre = {$cuatrimestre}
								GROUP BY nombre_comision, d.id
								ORDER BY nombre_comision";
					$result = $mysqli->query($query);
					while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
						$empty = false;
						if ($row['id'] == 'empty') {
							$empty = true;
						}
						$resaltar = "";
						if ($empty) {
							$resaltar = "resaltar";
						}
						
						$checked = '';
						if ($row['aula_virtual'] == 1) {
							$checked = 'checked';
						}
						
						$disabled = "";
						$eliminar = "botonEliminar";
						if (!$ASIGNAR_COMISIONES) {
							$disabled = "disabled";
							$eliminar = "periodoCerrado";
						}
						
						echo "<tr class='formularioLateral correlatividadesTable'>
								
								
								<td class='formularioLateral correlatividadesTable'>$row[nombre_comision]</td>
								<td class='formularioLateral correlatividadesTable'>$row[horario]</td>
								<td class='formularioLateral correlatividadesTable {$resaltar}'>$row[docente]</td>";
								
						if (!$empty) {
							echo "<td class='formularioLateral correlatividadesTable' data-id='{$row['id']}'>
									<input type='checkbox' class='formularioLateral aulaVirtual' value='1' {$checked} {$disabled} data-id='$row[id]'/>
								</td>";
							
							echo "<td class='formularioLateral correlatividadesTable'>
									<button type='button' class='{$eliminar}' data-id='$row[id]' >X</button>
								</td>";
						} else {
							echo "<td></td>";
						}
						echo "</tr>";
						
					}
					break;
					
				case "asignarAulaVirtual":
					require 'conexion.php';
					$id = $_REQUEST['id'];
					$check = $_REQUEST['check'];
					$usuario = $_SESSION['usuario'];
					//$usuario = 'test';
					
					$query = "UPDATE asignacion_comisiones
								SET aula_virtual = {$check},
								usuario_ultima_modificacion = '{$usuario}'
								WHERE id = {$id}";
					$result = $mysqli->query($query);
					//echo $mysqli->error;
					echo 'ok';
					
					$mysqli->close();
					break;
					
				case "asignarCargaCVAR":
					require 'conexion.php';
					$id = $_REQUEST['id'];
					$check = $_REQUEST['check'];
					
					//$usuario = 'test';
					
					$query = "UPDATE docente
								SET cvar = {$check}
								WHERE id = {$id}";
					$result = $mysqli->query($query);
					echo $mysqli->error;
					echo 'ok';
					
					$mysqli->close();
					break;
				
				case "asignarExceptuadoCVAR":
					require 'conexion.php';
					$id = $_REQUEST['id'];
					$check = $_REQUEST['check'];
					
					//$usuario = 'test';
					
					$query = "UPDATE docente
								SET exceptuado_cvar = {$check}
								WHERE id = {$id}";
					$result = $mysqli->query($query);
					echo $mysqli->error;
					echo 'ok';
					
					$mysqli->close();
					break;
					
				case "tablaEquipoDocente":
				
					//$ANIO = 2017;
					//$CUATRIMESTRE = 1;
					
					$materia = new clases\Materia($_SESSION['materia']);
					$equipoDocente = $materia->mostrarEquipoDocente('*', $ANIO, $CUATRIMESTRE);
					
					if (empty($equipoDocente)) {
						echo "<tr><td colspan='2'>No hay docentes cargados</td></tr>";
					} else {
					
						foreach ($equipoDocente as $row) {
							echo "<tr class='formularioLateral correlatividadesTable'>
									<td class='formularioLateral correlatividadesTable'>$row[docente]</td>
									<td class='formularioLateral correlatividadesTable'>$row[tipoafectacion]</td>
									<td class='formularioLateral correlatividadesTable'><button type='button' class='botonEliminar' data-id='$row[id]' >X</button></td>
								</tr>";
						}
					}
					
					break;
				
				case "tablaUnidadesTematicas":
					$materia = new clases\Materia($_SESSION['materia']);
					$unidadesTematicas = $materia->mostrarUnidadesTematicas('*', $ANIO, $CUATRIMESTRE);
					
					if (empty($unidadesTematicas)) {
						echo "<tr><td colspan='2'>No hay unidades cargadas</td></tr>";
					} else {
					
						foreach ($unidadesTematicas as $key => $value) {
							echo "<tr class='formularioLateral correlatividadesTable'>
									<td class='formularioLateral correlatividadesTable'>{$key}</td>
									<td class='formularioLateral correlatividadesTable'>{$value}</td>
									<td class='formularioLateral correlatividadesTable'><button type='button' class='botonEliminar' data-unidadtematica='{$key}' >X</button></td>
								</tr>";
						}
					}
					
					break;
				
					
				case "tablaControlComisiones":
					require 'conexion.php';
					
					$filtro['m.nombre'] = $_REQUEST['materia'];
					$filtro['m.cuatrimestre'] = $_REQUEST['cuatrimestre'];
					$filtro['m.carrera'] = $_REQUEST['carrera'];
					
					$tipos = array();
					$carreras = '()';
					
					$where = "1 = 1 ";
					foreach ($filtro as $key => $value) {
						if ($value != "") {
							if ($key == 'm.nombre') {
								$where .= "AND $key LIKE '%$value%' ";
							} else {
								$where .= "AND $key = '$value' ";
							}
						}
					}
					
					
					$query = "SELECT m.cuatrimestre, m.conjunto, m.nombre, m.plan, c.nombre AS carrera, 
								GROUP_CONCAT(DISTINCT CONCAT(p.apellido, ', ', p.nombres) SEPARATOR ' / ') AS responsable, 
								GROUP_CONCAT(DISTINCT CONCAT(cc.turno, ':', cc.cantidad) SEPARATOR '--') AS cantidad, 
								COUNT(DISTINCT ac.comision) AS comisiones_pobladas, 
								COUNT(DISTINCT ac.docente) AS docentes_asignados, 
								COUNT(DISTINCT a.docente) AS docentes_equipo 
							FROM materia AS m 
							LEFT JOIN carrera AS c ON m.carrera = c.id 
							LEFT JOIN cantidad_comisiones AS cc ON cc.materia = m.conjunto AND cc.anio = $ANIO AND cc.cuatrimestre = $CUATRIMESTRE
							LEFT JOIN asignacion_comisiones AS ac ON ac.materia = m.conjunto AND ac.anio = $ANIO AND ac.cuatrimestre = $CUATRIMESTRE
							LEFT JOIN materia AS om ON om.conjunto = m.conjunto
							LEFT JOIN responsable AS r ON r.materia = om.cod 
							LEFT JOIN personal AS p ON r.usuario = p.id 
							LEFT JOIN afectacion AS a ON a.materia = m.cod AND a.anio = $ANIO AND a.cuatrimestre = $CUATRIMESTRE AND a.activo = 1
							WHERE $where
							GROUP BY conjunto 
							ORDER BY m.carrera, m.cuatrimestre;";
					//echo $query;
					$result = $mysqli->query($query);
					echo $mysqli->error;
					$materias = array();
					
					while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
						$preCantidad = explode('--', $row['cantidad']);
						$preCantidad = array_map(function($item) {
							return explode(':', $item);
						}, $preCantidad);
						
						$cantidad = 0;
						//print_r($preCantidad);
						foreach ($preCantidad as $key => $value) {
							if (isset($value[1])) {
								$cantidad += $value[1];
							}
						}
						
						$materias[$row['carrera']][$row['cuatrimestre']][$row['conjunto'] . $row['nombre']]['cantidad'] = $cantidad;
						$materias[$row['carrera']][$row['cuatrimestre']][$row['conjunto'] . $row['nombre']]['comisiones_pobladas'] = $row['comisiones_pobladas'];
						$materias[$row['carrera']][$row['cuatrimestre']][$row['conjunto'] . $row['nombre']]['docentes_asignados'] = $row['docentes_asignados'];
						$materias[$row['carrera']][$row['cuatrimestre']][$row['conjunto'] . $row['nombre']]['responsable'] = $row['responsable'];
						$materias[$row['carrera']][$row['cuatrimestre']][$row['conjunto'] . $row['nombre']]['docentes_equipo'] = $row['docentes_equipo'];
					}
					echo "<table class='materias'>";
						foreach ($materias AS $carrera => $materia) {
							echo "<tr class='subtitulo'>
									<th colspan='8' style='text-align:center;font-size:1.2em;'>Carrera: $carrera</th>
								</tr>";
							foreach ($materia AS $cuatrimestre => $datos) {
								echo "<tr class='subtitulo'>
									<th class='materias'>Cuatrimestre $cuatrimestre</th>
									<th class='materias'>Responsable</th>
									<th class='materias'>Comisiones</th>
									<th class='materias'>Ocupadas</th>
									<th class='materias'>Libres</th>
									<th class='materias'>Docentes</th>
									<th class='materias'>Asignados</th>
									<th class='materias'>Libres</th>
								</tr>";
								foreach ($datos AS $nombre => $turno) {
									
									echo "<tr class='info'>
											<td class='info masInfo'>$nombre</td>";
									echo "<td class='materia' style='text-align:center;'>$turno[responsable]</td>";
									echo "<td class='materia' style='text-align:center;'>$turno[cantidad]</td>";
									echo "<td class='materia' style='text-align:center;'>$turno[comisiones_pobladas]</td>";
									echo "<td class='materia comisionesLibres' style='text-align:center;'>" . ($turno['cantidad'] - $turno['comisiones_pobladas']) ."</td>";
									echo "<td class='materia' style='text-align:center;'>$turno[docentes_equipo]</td>";
									echo "<td class='materia' style='text-align:center;'>$turno[docentes_asignados]</td>";
									echo "<td class='materia' style='text-align:center;'>" . ($turno['docentes_equipo'] - $turno['docentes_asignados']) ."</td>";
											//<td class='info'>". implode(', ', $turno['turnos']) . "</td>";
									echo "</tr>";
								}
							}
						}
					echo "</table>";
					break;
					
				case "tablaConsultaComisiones":
					require 'conexion.php';
					
					$filtro['m.nombre'] = $_REQUEST['materia'];
					$filtro['m.cuatrimestre'] = $_REQUEST['cuatrimestre'];
					$filtro['m.carrera'] = $_REQUEST['carrera'];
					$filtro['m.plan'] = $_REQUEST['plan'];
					
					$tipos = array();
					$carreras = '()';
					
					$where = "1 = 1 ";
					foreach ($filtro as $key => $value) {
						if ($value != "") {
							if ($key == 'm.nombre') {
								$where .= "AND CONCAT(m.nombre, m.conjunto) LIKE '%$value%' ";
							} else {
								$where .= "AND $key = '$value' ";
							}
						}
					}
					
					$query = "SELECT m.cuatrimestre, m.conjunto, 
								GROUP_CONCAT(DISTINCT m.nombre ORDER BY m.cod + 0
									SEPARATOR ' / ') AS nombre, m.plan, 
								c.nombre AS carrera, 
								GROUP_CONCAT(DISTINCT CONCAT(p.apellido, ', ', p.nombres) 
									SEPARATOR ' / ') AS responsable
							FROM materia AS m 
							LEFT JOIN carrera AS c ON m.carrera = c.id 
							LEFT JOIN materia AS om ON om.conjunto = m.conjunto
							LEFT JOIN responsable AS r ON r.materia = om.cod AND r.activo = 1
							LEFT JOIN personal AS p ON r.usuario = p.id 
							WHERE $where
							GROUP BY m.conjunto 
							ORDER BY FIELD (m.carrera, 6, 7, 4, 1 , 2 , 3, 5), m.cuatrimestre";
								
					$result = $mysqli->query($query);
					echo $mysqli->error;
					$materias = array();
					
					while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
						$materias[$row['carrera']][$row['cuatrimestre']][$row['conjunto'] . " " . $row['nombre']]['responsable'] = $row['responsable'];
					}
					echo "<table class='materias'>";
						foreach ($materias AS $carrera => $materia) {
							echo "<tr class='subtitulo'>
									<th colspan='2' style='text-align:center;font-size:1.2em;'>Carrera: $carrera</th>
								</tr>";
							foreach ($materia AS $cuatrimestre => $datos) {
								echo "<tr class='subtitulo'>
									<th class='materias'>Cuatrimestre $cuatrimestre</th>
									<th class='materias'>Responsable</th>
								</tr>";
								foreach ($datos AS $nombre => $turno) {
									
									echo "<tr class='info {$carrera}'>
											<td class='info masInfo'>$nombre</td>
											<td class='materia' style='text-align:left;'>$turno[responsable]</td>
										</tr>";
								}
							}
						}
					echo "</table>";
					break;
					
				case "tablaOfertaAcademica":
					require 'conexion.php';
					
					$carrera = $_REQUEST['carrera'];
					switch ($carrera) {
						case "1":
							$carrera = "(1, 7, 4)";
							$nombre_carrera = "Lic. Administración y Gestión Empresarial";
							break;
						case "2":
							$carrera = "(2, 7, 4)";
							$nombre_carrera = "Lic. Economía";
							break;
						case "3":
							$carrera = "(3, 7)";
							$nombre_carrera = "Lic. Turismo";
							break;
						default: 
							$carrera = "(" . $carrera . ")";
							$nombre_carrera = "Selección personalizada de carreras";
							break;
					}
					
					//echo $carrera;
							
					$filtro['m.nombre'] = $_REQUEST['materia'];
					$filtro['m.carrera'] = $carrera;
					$filtro['m.plan'] = $_REQUEST['plan'];
					$filtro['LEFT(t.turno, 1)'] = $_REQUEST['turno'];
					
					$periodo = $_REQUEST['periodo'];
					
					$turno = $_REQUEST['turno'];
					switch ($turno) {
						case 'N':
							$turno = 'noche';
							break;
						case 'M':
							$turno = 'mañana';
							break;
						case 'T':
							$turno = 'tarde';
							break;
						default:
							$turnos = 'todos los turnos';
							break;
					}
					
					$plan = $_REQUEST['plan'];
					
					$tipos = array();
					$carreras = '()';
					
					$where = "CONCAT(t.anio, ' - ', t.cuatrimestre) = '$periodo' ";
					foreach ($filtro as $key => $value) {
						if ($value != "" and $value != '()') {
							if ($key == 'm.nombre') {
								$where .= "AND CONCAT(m.nombre, m.cod) LIKE '%$value%' ";
							} else if ($key == 'm.carrera') {
								$where .= " AND m.carrera IN {$value} " ;
							} else if ($key == 'LEFT(t.turno, 1)' and ($value == 'N' or $value =='M')) {
								$where .= " AND ({$key} = '{$value}' OR {$key} = 'S') ";
							} else {
								$where .= "AND $key = '$value' ";
							}
						}
					}
					
					
					$query = "SELECT c.cod, m.plan, m.cuatrimestre, 
								IF(RIGHT(t.materia,1) != ')', 
									CONCAT(m.cod, LEFT(t.turno,1), RIGHT(t.materia, 1)), 
									CONCAT(m.cod, LEFT(t.turno, 1))
								) AS materia, 
								m.nombre, t.turno,
								IF (RIGHT(t.turno, 1) IN (1, 2) , CONCAT('S', RIGHT(t.turno, 1)), 'S') AS turno_sabado,
								GROUP_CONCAT(DISTINCT CONCAT(t.dia, '-', t.turno)) AS horarios,
								MAX(IF(t.dia = 'lunes', 
									IF(RIGHT(t.materia,1) != ')', 
									CONCAT(m.cod, LEFT(t.turno,1), RIGHT(t.materia, 1)), 
									CONCAT(m.cod, LEFT(t.turno, 1))
								), 
									'')) AS lunes,
								MAX(IF(t.dia = 'martes', 
									IF(RIGHT(t.materia,1) != ')', 
									CONCAT(m.cod, LEFT(t.turno,1), RIGHT(t.materia, 1)), 
									CONCAT(m.cod, LEFT(t.turno, 1))
								), 
									'')) AS martes,
								MAX(IF(t.dia = 'miercoles', 
									IF(RIGHT(t.materia,1) != ')', 
									CONCAT(m.cod, LEFT(t.turno,1), RIGHT(t.materia, 1)), 
									CONCAT(m.cod, LEFT(t.turno, 1))
								), 
									'')) AS miercoles,
								MAX(IF(t.dia = 'jueves', 
									IF(RIGHT(t.materia,1) != ')', 
									CONCAT(m.cod, LEFT(t.turno,1), RIGHT(t.materia, 1)), 
									CONCAT(m.cod, LEFT(t.turno, 1))
								), 
									'')) AS jueves,
								MAX(IF(t.dia = 'viernes', 
									IF(RIGHT(t.materia,1) != ')', 
									CONCAT(m.cod, LEFT(t.turno,1), RIGHT(t.materia, 1)), 
									CONCAT(m.cod, LEFT(t.turno, 1))
								), 
									'')) AS viernes,
								MAX(IF(t.dia = 'sabado', 
									IF(t.materia LIKE '%)S%',
										CONCAT(m.cod, REPLACE(t.materia, m.conjunto, '')),
										IF(RIGHT(t.materia,1) != ')', 
											CONCAT(m.cod, LEFT(t.turno,1), RIGHT(t.materia, 1)), 
											CONCAT(m.cod, LEFT(t.turno, 1))
										)
											
								), 
									'')) AS sabado,
								IF(cor.nombre_correlativas LIKE 'Elementos de%', 'CPU completo', cor.nombre_correlativas) AS nombre_correlativas,
								IF(cor.correlativas LIKE '1000 + %', 'CPU completo', cor.correlativas) AS correlativas
								
							FROM turnos_con_conjunto AS t
							LEFT JOIN materia AS m
								ON m.conjunto = t.materia OR t.materia LIKE CONCAT(m.conjunto, '%')
							LEFT JOIN carrera AS c 
								ON c.id = m.carrera
							LEFT JOIN (SELECT c.materia, GROUP_CONCAT(DISTINCT m1.nombre ORDER BY m1.cod SEPARATOR ' + ') AS nombre_correlativas,
								GROUP_CONCAT(DISTINCT m1.cod ORDER BY m1.cod SEPARATOR ' + ') AS correlativas
								FROM correlatividad AS c
								LEFT JOIN materia AS m1 
									ON m1.cod = c.requisito
								GROUP BY c.materia
							) AS cor ON cor.materia = m.cod
							WHERE {$where}
							GROUP BY t.materia, t.turno
							ORDER BY LEFT(t.turno, 1), m.cuatrimestre, t.materia, t.turno";
					$result = $mysqli->query($query);
					
					
					echo $query;
					if ($mysqli->errno) {
						echo $mysqli->error;
						echo "<br>";
						echo $query;
					}
					
					$materias = array();
					
					while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
						$materias[$row['cuatrimestre']][$row['materia'] . " " . $row['nombre']][$row['turno']]['lunes'] = $row['lunes'];
						$materias[$row['cuatrimestre']][$row['materia'] . " " . $row['nombre']][$row['turno']]['martes'] = $row['martes'];
						$materias[$row['cuatrimestre']][$row['materia'] . " " . $row['nombre']][$row['turno']]['miercoles'] = $row['miercoles'];
						$materias[$row['cuatrimestre']][$row['materia'] . " " . $row['nombre']][$row['turno']]['jueves'] = $row['jueves'];
						$materias[$row['cuatrimestre']][$row['materia'] . " " . $row['nombre']][$row['turno']]['viernes'] = $row['viernes'];
						
						$materias[$row['cuatrimestre']][$row['materia'] . " " . $row['nombre']][$row['turno']]['sabado'] = $row['sabado'];
						$materias[$row['cuatrimestre']][$row['materia'] . " " . $row['nombre']][$row['turno']]['nombre_correlativas'] = $row['nombre_correlativas'];
						$materias[$row['cuatrimestre']][$row['materia'] . " " . $row['nombre']][$row['turno']]['correlativas'] = $row['correlativas'];
						
					}
					
					
					
					echo "<h2 class='tituloTabla Requerimientos'>Requerimiento de aulas para el turno {$turno}</h2>";
					echo "<h3 class='tituloTabla carrera'>{$nombre_carrera} - Plan: {$plan}</h3>";
					echo "<table class='materias' border='1'>";
					/*foreach ($materias AS $turno => $detalleMaterias) {
						echo "<tr class='subitutlo'>
								<th class='subtitulo' style='text-align:center;font-size:1.1em;' colspan='8'> $turno </th>
							</tr>";*/
						
						foreach ($materias AS $cuatrimestre => $materia) {
							echo "<tr class='subtitulo'>
									<th style='text-align:center;font-size:1em;color:white; background-color:black;' width='42%'>{$cuatrimestre} º Cuatrimestre</th>
									<th style='text-align:center;font-size:1em;color:white; background-color:black;' width='10%'>Horario</th>
									<th style='text-align:center;font-size:1em;color:white; background-color:black;' width='8%' >Lunes</th>
									<th style='text-align:center;font-size:1em;color:white; background-color:black;' width='8%' >Martes</th>
									<th style='text-align:center;font-size:1em;color:white; background-color:black;' width='8%' >Miércoles</th>
									<th style='text-align:center;font-size:1em;color:white; background-color:black;' width='8%' >Jueves</th>
									<th style='text-align:center;font-size:1em;color:white; background-color:black;' width='8%' >Viernes</th>
									<th style='text-align:center;font-size:1em;color:white; background-color:black;' width='8%' >Sábado</th>
									<th style='text-align:left;font-size:1em;color:white; background-color:black;' width='8%' >Correlativas mínimas - Para Cursar</th>
									<th style='text-align:left;font-size:1em;color:white; background-color:black;' width='8%' >Correlativas Totales</th>
								</tr>";
							
							foreach ($materia AS $nombre => $turno) {
								if (isset($materiaAnterior) and $materiaAnterior != $nombre) {
									echo "<tr class='salto' style='height:.5em'>
											<td class='materia' style='text-align:center;font-size:.8em;'></td>
											<td class='materia' style='text-align:center;font-size:.8em'></td>
											<td class='materia' style='text-align:center;font-size:.8em'></td>
											<td class='materia' style='text-align:center;font-size:.8em'></td>
											<td class='materia' style='text-align:center;font-size:.8em'></td>
											<td class='materia' style='text-align:center;font-size:.8em'></td>
											<td class='materia' style='text-align:center;font-size:.8em'></td>
											<td class='materia' style='text-align:center;font-size:.8em'></td>
											<td class='materia' style='text-align:center;font-size:.8em'></td>
										</tr>";
								}
								foreach ($turno AS $turno => $comision) {
									echo "<tr class=''>
											<td class='info masInfo'>$nombre</td>";
									if (!in_array($turno, ['M1', 'M2', 'M']) and
										$comision['sabado'] != '') { 
									$turno_sabado = "S";
									switch ($turno) {
										case 'N1':
											$turno_sabado = 'S1';
											break;
										case 'N2':
											$turno_sabado = 'S2';
											break;
										case 'N':
											$turno_sabado = 'S';
											break;
										case 'T1':
											$turno_sabado = 'S1';
											break;
										case 'T2':
											$turno_sabado = 'S2';
											break;
										case 'T':
											$turno_sabado = 'S';
											break;
									}
									echo "<td class='materia' style='text-align:center;font-size:.8em;'>{$horasTurno[$turno_sabado]}</td>";
									echo "<td class='materia' style='text-align:center;font-size:.8em'></td>
											<td class='materia' style='text-align:center;font-size:.8em'></td>
											<td class='materia' style='text-align:center;font-size:.8em'></td>
											<td class='materia' style='text-align:center;font-size:.8em'></td>
											<td class='materia' style='text-align:center;font-size:.8em'></td>
											<td class='materia' style='text-align:center;font-size:.8em'>$comision[sabado]</td>
											<td class='materia' style='text-align:left;font-size:.8em'>$comision[nombre_correlativas]</td>
											<td class='materia' style='text-align:left;font-size:.8em'>$comision[correlativas]</td>
										
										</tr>";
									} else {
										echo "<td class='materia' style='text-align:center;font-size:.8em;'>{$horasTurno[$turno]}</td>";
										echo "<td class='materia' style='text-align:center;font-size:.8em'>$comision[lunes]</td>
											<td class='materia' style='text-align:center;font-size:.8em'>$comision[martes]</td>
											<td class='materia' style='text-align:center;font-size:.8em'>$comision[miercoles]</td>
											<td class='materia' style='text-align:center;font-size:.8em'>$comision[jueves]</td>
											<td class='materia' style='text-align:center;font-size:.8em'>$comision[viernes]</td>
											<td class='materia' style='text-align:center;font-size:.8em'>$comision[sabado]</td>
											<td class='materia' style='text-align:left;font-size:.8em'>$comision[nombre_correlativas]</td>
											<td class='materia' style='text-align:left;font-size:.8em'>$comision[correlativas]</td>
										
										</tr>";
									}
									
								}
								
								$materiaAnterior = $nombre;
							}
							
							
						}
					
					echo "</table>";
					break;
					
				case "tablaAnalisisOferta":
					require 'conexion.php';
					
					$dias = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
					
					
					$carrera = $_REQUEST['carrera'];
					switch ($carrera) {
						case "1":
							$carrera = "(1, 7, 4)";
							$nombre_carrera = "Lic. Administración y Gestión Empresarial";
							break;
						case "2":
							$carrera = "(2, 7, 4)";
							$nombre_carrera = "Lic. Economía";
							break;
						case "3":
							$carrera = "(3, 7)";
							$nombre_carrera = "Lic. Turismo";
							break;
						default: 
							$carrera = "(" . $carrera . ")";
							$nombre_carrera = "Selección personalizada de carreras";
							break;
					}
					
					//echo $carrera;
							
					$filtro['m.nombre'] = $_REQUEST['materia'];
					$filtro['m.carrera'] = $carrera;
					$filtro['m.plan'] = $_REQUEST['plan'];
					$filtro['LEFT(t.turno, 1)'] = $_REQUEST['turno'];
					$filtro['m.cuatrimestre'] = $_REQUEST['cuatrimestre'];
					
					$periodo = $_REQUEST['periodo'];
					
					$turno = $_REQUEST['turno'];
					switch ($turno) {
						case 'N':
							$turno = 'noche';
							break;
						case 'M':
							$turno = 'mañana';
							break;
						case 'T':
							$turno = 'tarde';
							break;
						default:
							$turnos = 'todos los turnos';
							break;
					}
					
					$plan = $_REQUEST['plan'];
					
					$tipos = array();
					$carreras = '()';
					
					$where = "WHERE 1 = 1 ";
					foreach ($filtro as $key => $value) {
						if ($value != "" and $value != '()') {
							if ($key == 'm.nombre') {
								$where .= "AND CONCAT(m.nombre, m.cod) LIKE '%$value%' ";
							} else if ($key == 'm.carrera') {
								$where .= " AND m.carrera IN {$value} " ;
								
							} else {
								$where .= "AND $key = '$value' ";
							}
						}
					}
					
					$query = "SELECT m.cod, IFNULL(t.materia, m.conjunto) AS materia, 
								m.nombre, e.turno AS turno, t.dia, t.turno AS horario, e.cantidad,
								CONCAT(e.turno, REPLACE(e.materia, m.conjunto, '')) AS alternativa_turno,
								COUNT(DISTINCT aa.nombre_comision) AS comisiones, m.cuatrimestre, m.plan, c.cod AS carrera,
								c.nombre AS nombre_carrera
								
							FROM materia AS m
							LEFT JOIN estimacion AS e
								ON e.materia LIKE CONCAT(m.conjunto, '%')
									AND CONCAT(e.anio , ' - ', e.cuatrimestre) = '{$periodo}'
							LEFT JOIN comisiones_abiertas AS aa
								ON aa.materia = m.conjunto
									AND aa.turno = e.turno
									AND aa.anio = e.anio AND aa.cuatrimestre = e.cuatrimestre
							LEFT JOIN carrera AS c
								ON m.carrera = c.id
							LEFT JOIN turnos_con_conjunto AS t
								ON t.materia LIKE CONCAT(m.conjunto, '%')
									AND e.turno = LEFT(t.turno, 1)
									AND t.anio = e.anio AND t.cuatrimestre = e.cuatrimestre
							{$where}
							GROUP BY m.carrera, m.plan,  m.cod, t.dia, alternativa_turno, e.turno, t.turno
							ORDER BY t.turno";
					$result = $mysqli->query($query);
					
					
					echo $query;
					if ($mysqli->errno) {
						echo $mysqli->error;
						echo "<br>";
						echo $query;
					}
					
					$materias = array();
					$cantidades = array();
					
					while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
						//print_r($row);
						if (!isset($materias[$row['plan']][$row['alternativa_turno']][$row['horario']][$row['dia']])) {
							$materias[$row['plan']][$row['alternativa_turno']][$row['horario']][$row['dia']] = $row['cod'];
						} else {
							$materias[$row['plan']][$row['alternativa_turno']][$row['horario']][$row['dia']] .= ' + ' . $row['cod'];
						}
						$cantidades[$row['alternativa_turno']]['inscriptos'] = $row['cantidad'];
						$cantidades[$row['alternativa_turno']]['comisiones'] = $row['comisiones'];
						
					}
					
					print_r($materias);
					
					
					echo "<h2 class='tituloTabla Requerimientos'>Análisis del turno {$turno}</h2>";
					
					foreach ($materias as $plan => $alternativas) {
						foreach ($alternativas as $alternativa => $horarios) {
							echo "<h3 class='tituloTabla carrera'>{$alternativa} - Plan: {$plan}</h3>";
							echo "<table class='materias' border='1'>";

							echo "<tr class='subtitulo'>
									<th style='text-align:center;font-size:1em;color:white; background-color:black;' width='10%'>Horario</th>";
							foreach ($dias as $dia) {
								echo "<th style='text-align:center;font-size:1em;color:white; background-color:black;' width='8%' >{$dia}</th>";
							}
									
							echo "</tr>";
							
							foreach ($horarios as $horario => $dias_cargados) {
								echo "<tr class='salto' style='height:.5em'>
										<td class='materia' style='text-align:center;font-size:.8em;'>{$horario}</td>";
								foreach ($dias as $dia) {
									if (isset($dias_cargados[$dia])) {
										echo "<td class='materia' style='text-align:center;font-size:.8em;'>{$dias_cargados[$dia]}</td>";
									} else {
										echo "<td></td>";
									}
								}
										
								echo "</tr>";
							}
								
					
						echo "</table>";
						}
					}
					
					break;
					
				case "tablaInscriptosParaAsignar":
					require 'conexion.php';
					
					$filtro['periodo'] = $ANIO . ' - ' . $CUATRIMESTRE;
					if (isset($_REQUEST['periodo'])) {
						$filtro['periodo'] = $_REQUEST['periodo'];
					}
					
					$filtro['nombre_materia'] = $_REQUEST['materia'];
					
					$tipos = array();
					$carreras = '()';
					
					$where = "1 = 1 ";
					foreach ($filtro as $key => $value) {
						if ($value != "") {
							if ($key == 'nombre_materia') {
								$where .= "AND CONCAT(nombre_materia, materia) LIKE '%$value%' ";
							} else {
								$where .= "AND CONCAT(anio, ' - ', cuatrimestre)  = '$value' ";
							}
						}
					}
					
					$query = "SELECT materia, nombre_materia, 
								turno, cantidad, 
								id, anio, cuatrimestre 
								
							FROM estimacion 
							WHERE $where
							ORDER BY materia
							LIMIT 100";
					
					$result = $mysqli->query($query);
					echo $mysqli->error;
					$materias = array();
					
					while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
						$materias[$row['materia']]['nombre'] = $row['nombre_materia'];
						$materias[$row['materia']][$row['turno']] = $row['cantidad'];
					}
					echo "<table class='materias'>";
					echo "<tr class='subtitulo'>
						<th class='materias'>Cod</th>
						<th class='materias'>Materia</th>
						<th class='materias'>M</th>
						<th class='materias'>N</th>
						<th class='materias'>T</th>
					</tr>";
					
					foreach ($materias AS $cod => $datos) {
						if (!isset($datos['M'])) {
							$datos['M'] = "";
						}
						if (!isset($datos['N'])) {
							$datos['N'] = "";
						}
						if (!isset($datos['T'])) {
							$datos['T'] = "";
						}
						
						echo "<tr class='info'>
								<td class='info masInfo'>$cod</td>
								<td class='materia' style='text-align:left;'>$datos[nombre]</td>
								<td class='materia' style='text-align:left;'>$datos[M]</td>
								<td class='materia' style='text-align:left;'>$datos[N]</td>
								<td class='materia' style='text-align:left;'>$datos[T]</td>
							</tr>";
					}
						
						
					echo "</table>";
					break;
					
				case "buscarDatosContacto":
					
					$docente = new clases\Docente($_POST['id']);
					$datosDocente = $docente->mostrarDatosContacto();
					
					if (empty($datosDocentes)) {
						$datosDocentes['vacio'] = true;
					}
					echo json_encode($datosDocente);
					break;
				
				case "agregarDatosContacto":
					$docente = new clases\Docente($_POST['dni']);
					//print_r($_POST);
					foreach ($_POST as $tipo => $valor) {
						if ($tipo != 'dni') {
							$docente->agregarDatoContacto($tipo, $valor);
						}
					}
					
					
					break;
					
				case "agregarDesignacion":
					require 'conexion.php';
					
					$dedicacion = $mysqli->real_escape_string($_REQUEST['dedicacion']);
					$categoria = $mysqli->real_escape_string($_REQUEST['categoria']);
					$caracter = $mysqli->real_escape_string($_REQUEST['caracter']);
					$observaciones = $mysqli->real_escape_string($_REQUEST['observaciones']);
					$alta = $_REQUEST['fecha_alta'];
					$baja = $_REQUEST['fecha_baja'];
					
					$docente = new clases\Docente($_REQUEST['dni']);
					
					
					$docente->agregarDesignacion($dedicacion, $categoria, $caracter, $alta, $baja, $observaciones);
					
					
					
					break;
					
				case "tablaContactosDocentes":
					require 'conexion.php';
					
					$filtro = '';
					if (isset($_GET['filtro'])) {
						$filtro = $_GET['filtro'];
					}
					
					$campos = ["d.dni", "dc1.valor", "dc2.valor", 
						"d.apellido", "d.nombres", 
						"CONCAT_WS(', ', d.apellido, d.nombres)"];
					
					$where = "1 = 0 ";
					if ($filtro != '') {
						foreach ($campos as $campo) {
							$where .= " OR $campo LIKE '%$filtro%' ";
						}
					}
					
					
					$query = "SELECT d.id, CONCAT_WS(', ', d.apellido, 
							d.nombres) AS docente, dc1.valor AS telefono, 
							dc2.valor AS mail
						FROM docente AS d
						LEFT JOIN datos_docentes AS dc1 ON dc1.docente = d.id
							AND dc1.tipo = 'telefono'
						LEFT JOIN datos_docentes AS dc2 ON dc2.docente = d.id
							AND dc2.tipo = 'mail'
						WHERE $where
						ORDER BY d.apellido, d.nombres
						LIMIT 25;";
					//echo $query;
					$result = $mysqli->query($query);
					echo $mysqli->error;
					$docentes = array();
					
					while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
						$docentes[] = $row;
					}
					
					if (sizeof($docentes)) {
						
						echo "<table class='docentes' style='width:98%;'>";
						echo "<tr class='subtitulo'>
								<th class='subtitulo'style='width:40%;'>Docente</th>
								<th class='subtitulo' style='width:20%;'>Telefono</th>
								<th class='subtitulo'style='width:40%;'>Mail</th>
							</tr>";
						foreach ($docentes AS $valores) {
							echo "<tr class='info'>
									<td class='info masInfo' data-id='$valores[id]'>$valores[docente]</td>";
							echo "<td class='materia' style='text-align:left;'>$valores[telefono]</td>";
							echo "<td class='materia' style='text-align:left;'><a class='mail' href='mailto:$valores[mail]'>$valores[mail]</a></td>";
							echo "</tr>";
						}
						echo "</table>";
					} else {
						echo "<p>No se encontraron docentes</p>";
					}
					break;
					
				case "tablaDocentesAsignados":
					require 'conexion.php';
					
					$filtro = '';
					if (isset($_GET['filtro'])) {
						$filtro = $_GET['filtro'];
					}
					
					$campos = ["m.nombre", "m.cod",
						"d.apellido", "d.nombres", 
						"CONCAT_WS(', ', d.apellido, d.nombres)"];
					
					$where = " (ac.anio = $ANIO AND ac.cuatrimestre = $CUATRIMESTRE) AND (1 = 0 ";
					if ($filtro != '') {
						foreach ($campos as $campo) {
							$where .= " OR $campo LIKE '%$filtro%' ";
						}
					}
					$where .= " )";
					
					
					$query = "SELECT ac.docente AS id, ac.materia, 
								GROUP_CONCAT(DISTINCT m.nombre ORDER BY m.cod SEPARATOR '/ ') AS nombre,
								CONCAT_WS(', ', d.apellido, d.nombres) AS docente,
								GROUP_CONCAT(DISTINCT ac.comision ORDER BY ac.comision SEPARATOR ', ') AS comisiones
							FROM asignacion_comisiones as ac
							LEFT JOIN materia AS m ON m.conjunto = ac.materia
							LEFT JOIN docente AS d ON d.id = ac.docente
							WHERE $where
								
							GROUP BY ac.materia, ac.docente
							ORDER BY ac.materia
							LIMIT 50";
					//echo $query;
					$result = $mysqli->query($query);
					echo $mysqli->error;
					$docentes = array();
					
					while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
						$docentes[] = $row;
					}
					
					if (sizeof($docentes)) {
						
						echo "<table class='docentes' style='width:98%;'>";
						echo "<tr class='subtitulo'>
								<th class='subtitulo'style='width:40%;'>Docente</th>
								<th class='subtitulo' style='width:40%;'>Materia</th>
								<th class='subtitulo'style='width:40%;'>Comisiones</th>
							</tr>";
						foreach ($docentes AS $valores) {
							echo "<tr class='info'>
									<td class='info masInfo' data-id='$valores[id]'>$valores[docente]</td>";
							echo "<td class='materia' style='text-align:left;'>$valores[materia] $valores[nombre]</td>";
							echo "<td class='materia' style='text-align:left;'>$valores[comisiones]</td>";
							echo "</tr>";
						}
						echo "</table>";
					} else {
						echo "<p>No se encontraron docentes</p>";
					}
					break;
					
				case "tablaDocentes":
					require 'conexion.php';
					
					$where = '';
					
					if (isset($_REQUEST['filtro']) AND $_REQUEST['filtro'] != '') {
						$where = " AND CONCAT(apellido, nombres, dni) LIKE '%{$_REQUEST['filtro']}%' ";
					}
					
					$query = "SELECT id, dni, CONCAT(apellido, ', ', nombres) AS docente, fechanacimiento, fechaingreso 
									FROM docente 
									WHERE activo = 1 {$where} 
									ORDER BY apellido, nombres
									LIMIT 20";
					//echo $query;
					$result = $mysqli->query($query);
					echo $mysqli->error;
					$docentes = array();
					
					while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
						$docentes[] = $row;
					}
					
					if (sizeof($docentes)) {
						
						echo "<table class='docentes' style='width:98%;'>";
						echo "<tr class='subtitulo'>
								<th class='subtitulo'style='width:15%;'>DNI</th>
								<th class='subtitulo'style='width:55%;'>Docente</th>
								<th class='subtitulo' style='width:20%;'>Fecha de ingreso</th>
								<th class='subtitulo' style='width:10%;'>Eliminar</th>
							</tr>";
						foreach ($docentes AS $valores) {
							echo "<tr class='info'>
									<td class='info masInfo' data-id='$valores[id]'>$valores[dni]</td>
									<td class='info masInfo' data-id='$valores[id]'>$valores[docente]</td>";
							echo "<td class='materia' style='text-align:left;'>$valores[fechaingreso]</td>";
							echo "<td class='formularioLaterial eliminarEnTabla'><button type='button' 
									class='formularioLateral botonEliminar' id='eliminarDocente' data-id='$valores[id]'>
								X</button>";
							echo "</tr>";
						}
						echo "</table>";
					} else {
						echo "<p>No se encontraron docentes</p>";
					}
					break;
					
				case "tablaSituacionCVAR":
					require 'conexion.php';
					
					$where = '';
					
					if (isset($_REQUEST['docente']) AND $_REQUEST['docente'] != '') {
						$where = " AND CONCAT(apellido, nombres, dni) LIKE '%{$_REQUEST['docente']}%' ";
					}
					
					$query = "SELECT id, dni, CONCAT(apellido, ', ', nombres) AS docente, exceptuado_cvar, cvar
									FROM docente 
									WHERE 1 = 1 {$where} 
									ORDER BY apellido, nombres
									LIMIT 20";
					//echo $query;
					$result = $mysqli->query($query);
					echo $mysqli->error;
					$docentes = array();
					
					while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
						$docentes[] = $row;
					}
					
					if (sizeof($docentes)) {
						
						echo "<table class='docentes' style='width:98%;'>";
						echo "<tr class='subtitulo'>
								<th class='subtitulo'style='width:15%;'>DNI</th>
								<th class='subtitulo'style='width:55%;'>Docente</th>
								<th class='subtitulo' style='width:15%;'>CVar Cargado</th>
								<th class='subtitulo' style='width:15%;'>Exceptuado CVAR</th>
							</tr>";
						foreach ($docentes AS $valores) {
							echo "<tr class='info'>
									<td class='info masInfo' data-id='$valores[id]'>$valores[dni]</td>
									<td class='info masInfo' data-id='$valores[id]'>$valores[docente]</td>";
							$checked = '';
							if ($valores['cvar'] == 1) {
								$checked = 'checked';
							}
							echo "<td class='materia' style='text-align:left;'>
									<input type='checkbox' name='cvar' id='cvar' class='cvar' data-id='{$valores['id']}' $checked>
							</td>";
							$checked = '';
							if ($valores['exceptuado_cvar'] == 1) {
								$checked = 'checked';
							}
							echo "<td class='materia' style='text-align:left;'>
									<input type='checkbox' name='exceptuado_cvar' id='exceptuado_cvar' class='exceptuado_cvar' data-id='{$valores['id']}' $checked>
							</td>";
							echo "</tr>";
						}
						echo "</table>";
					} else {
						echo "<p>No se encontraron docentes</p>";
					}
					break;
					
				case "tablaConstantes":
					require 'conexion.php';
					
					$where = '';
					
					if (isset($_REQUEST['filtro']) AND $_REQUEST['filtro'] != '') {
						$where = " AND nombre LIKE '%{$_REQUEST['filtro']}%' ";
					}
					
					$query = "SELECT id, nombre, valor 
									FROM constantes 
									WHERE 1 = 1 {$where} 
									ORDER BY nombre
									LIMIT 20";
					//echo $query;
					$result = $mysqli->query($query);
					echo $mysqli->error;
					$constantes = array();
					
					while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
						$constantes[] = $row;
					}
					
					if (sizeof($constantes)) {
						
						echo "<table class='docentes' style='width:98%;'>";
						echo "<tr class='subtitulo'>
								<th class='subtitulo'style='width:10%;'>Id</th>
								<th class='subtitulo'style='width:40%;'>Nombre</th>
								<th class='subtitulo' style='width:40%;'>Valor</th>
								<th class='subtitulo' style='width:10%;'>Eliminar</th>
							</tr>";
						foreach ($constantes AS $valores) {
							echo "<tr class='info'>
									<td class='info masInfo' data-id='$valores[id]'>$valores[id]</td>
									<td class='info masInfo' data-id='$valores[id]'>$valores[nombre]</td>";
							echo "<td class='materia' data-id='$valores[id]'>$valores[valor]</td>";
							echo "<td class='formularioLaterial eliminarEnTabla'><button type='button' 
									class='formularioLateral botonEliminar' id='eliminarConstante' data-id='$valores[id]'>
								X</button>";
							echo "</tr>";
						}
						echo "</table>";
					} else {
						echo "<p>No hay resultados.</p>";
					}
					break;
				
				case "tablaPersonal":
					require 'conexion.php';
					
					$where = '';
					
					if (isset($_REQUEST['filtro']) AND $_REQUEST['filtro'] != '') {
						$where = " AND CONCAT(apellido, nombres, dni) LIKE '%{$_REQUEST['filtro']}%' ";
					}
					
					$query = "SELECT id, dni, CONCAT(apellido, ', ', nombres) AS personal, usuario 
									FROM personal
									WHERE activo = 1 {$where} 
									ORDER BY apellido, nombres
									LIMIT 20";
					//echo $query;
					$result = $mysqli->query($query);
					echo $mysqli->error;
					$docentes = array();
					
					while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
						$docentes[] = $row;
					}
					
					if (sizeof($docentes)) {
						
						echo "<table class='docentes' style='width:98%;'>";
						echo "<tr class='subtitulo'>
								<th class='subtitulo'style='width:15%;'>DNI</th>
								<th class='subtitulo'style='width:55%;'>Nombre</th>
								<th class='subtitulo' style='width:20%;'>Usuario</th>
								<th class='subtitulo' style='width:10%;'>Eliminar</th>
							</tr>";
						foreach ($docentes AS $valores) {
							echo "<tr class='info'>
									<td class='info masInfo' data-id='$valores[id]'>$valores[dni]</td>
									<td class='info masInfo' data-id='$valores[id]'>$valores[personal]</td>";
							echo "<td class='materia' style='text-align:left;'>$valores[usuario]</td>";
							echo "<td class='formularioLaterial eliminarEnTabla'><button type='button' 
									class='formularioLateral botonEliminar' id='eliminarPersonal' data-id='$valores[id]'>
								X</button>";
							echo "</tr>";
						}
						echo "</table>";
					} else {
						echo "<p>No hay resultados</p>";
					}
					break;
						
				case "tablaTurnos":
					require 'conexion.php';
					
					$where = '';
					
					//print_r($_REQUEST);
					
					if (isset($_REQUEST['filtro']) AND $_REQUEST['filtro'] != '') {
						$where .= " AND CONCAT(t.materia, m.nombres) LIKE '%{$_REQUEST['filtro']}%' ";
					}
					
					if (isset($_REQUEST['periodo']) AND $_REQUEST['periodo'] != '') {
						$where .= " AND CONCAT(t.anio, ' - ',  t.cuatrimestre) = '{$_REQUEST['periodo']}' ";
					}
					
					$query = "SELECT t.id, t.materia, m.nombres, t.dia, t.turno, t.observaciones 
									FROM turnos_con_conjunto AS t
									LEFT JOIN vista_materias_por_conjunto AS m
										ON m.conjunto = t.materia OR t.materia LIKE CONCAT(m.conjunto, '_')
									WHERE 1 = 1 {$where} 
									ORDER BY LEFT(t.turno, 1), t.materia, 
										FIELD(t.dia, 'lunes', 'martes', 
											'miércoles', 'jueves', 
											'viernes', 'sábado')
									#LIMIT 30";
					//echo $query;
					$result = $mysqli->query($query);
					echo $mysqli->error;
					$docentes = array();
					
					while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
						$docentes[] = $row;
					}
					
					if (sizeof($docentes)) {
						
						echo "<table class='docentes' style='width:98%;'>";
						echo "<tr class='subtitulo'>
								<th class='subtitulo'style='width:20%;'>Cod</th>
								<th class='subtitulo'style='width:50%;'>Nombre</th>
								<th class='subtitulo' style='width:15%;'>Día</th>
								<th class='subtitulo' style='width:8%;'>Horario</th>
								<th class='subtitulo' style='width:7%;'>Eliminar</th>
							</tr>";
						foreach ($docentes AS $valores) {
							echo "<tr class='info'>
									<td class='info masInfo' data-id='$valores[id]'>$valores[materia]</td>
									<td class='info masInfo' data-id='$valores[id]'>$valores[nombres]</td>";
							echo "<td class='materia' style='text-align:left;'>$valores[dia]</td>";
							echo "<td class='materia' style='text-align:left;'>$valores[turno]</td>";
							echo "<td class='formularioLaterial eliminarEnTabla'><button type='button' 
									class='formularioLateral botonEliminar' id='eliminarTurno' data-id='$valores[id]'>
								X</button>";
							echo "</tr>";
						}
						echo "</table>";
					} else {
						echo "<p>No se encontraron materias</p>";
					}
					break;
				
				case "tablaComisionesPorDia":
					require 'conexion.php';
					require 'constantes.php';
					
					$periodo = $_REQUEST['periodo'];
					
					$turnosConsiderados = array('M1' => 'M', 
												'M2' => 'M', 
												'N1' => 'N', 
												'N2' => 'N', 
												'T1' => 'T', 
												'T2' => 'T', 
												'S1' => 'S', 
												'S2' => 'S'
											);
					$turnosCompletos = ['M', 'N', 'T', 'S'];
					
					
					$query = "SELECT t.dia, t.turno, 
								COUNT(DISTINCT CONCAT(t.materia, ca.nombre_comision)) AS cantidad_comisiones
							FROM turnos_con_conjunto AS t
							LEFT JOIN comisiones_abiertas AS ca
								ON t.materia = CONCAT(ca.materia, IFNULL(ca.observaciones, ''))
									AND ca.turno = LEFT(t.turno, 1)
									AND ca.anio = t.anio
									AND ca.cuatrimestre = t.cuatrimestre
							WHERE CONCAT(t.anio, ' - ', t.cuatrimestre) = '{$periodo}'
							GROUP BY t.turno, t.dia#, t.materia
							ORDER BY ca.turno, t.dia, t.turno, t.materia";
					//echo $query;
					$result = $mysqli->query($query);
					echo $mysqli->error;
					$cantidad_comisiones = array();
					
					while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
						$cantidad_comisiones[$row['turno']][$row['dia']] = $row['cantidad_comisiones'];
					}
					
					
					if (sizeof($cantidad_comisiones)) {
						
						echo "<table class='docentes' style='width:98%;'>";
						echo "<tr class='subtitulo'>
								<th class='subtitulo'style='width:10%;text-align:left;'>Turno</th>
								<th class='subtitulo'style='width:15%;text-align:center;'>Lunes</th>
								<th class='subtitulo' style='width:15%;text-align:center;'>Martes</th>
								<th class='subtitulo' style='width:15%;text-align:center;'>Miércoles</th>
								<th class='subtitulo' style='width:15%;text-align:center;'>Jueves</th>
								<th class='subtitulo' style='width:15%;text-align:center;'>Viernes</th>
								<th class='subtitulo' style='width:15%;text-align:center;'>Sábado</th>
							</tr>";
						foreach ($turnosConsiderados as $horario => $turno) {
							echo "<tr class='info'>
									<td class='turno' >{$horario}</td>";
							foreach ($diasSemana as $dia) {
								$cantidad = 0;
								if (isset($cantidad_comisiones[$horario][$dia])) {
									$cantidad += $cantidad_comisiones[$horario][$dia];
								}
								if (isset($cantidad_comisiones[$turno][$dia])) {
									$cantidad += $cantidad_comisiones[$turno][$dia];
									//$cantidad += 100;
								}
								
								$mark = '';
								if ($cantidad > 21) {
									$mark = "background-color: yellow;";
								}
								echo "<td class='dia' style='text-align:center;{$mark}' >{$cantidad}</td>";
							}
							echo "</tr>";
						}
						echo "</table>";
					} else {
						echo "<p>No se encontraron comisiones abiertas</p>";
					}
					break;
					
				case "tablaTurnosMateria":
					require 'conexion.php';
					
					$materia = $_REQUEST['materia'];
					$periodo = $_REQUEST['periodo'];
					
										
					$query = "SELECT t.id, t.materia, m.nombres, t.dia, t.turno, t.observaciones 
									FROM turnos_con_conjunto AS t
									LEFT JOIN vista_materias_por_conjunto AS m
										ON m.conjunto = t.materia OR t.materia LIKE CONCAT(m.conjunto, '_')
									WHERE (t.materia = '{$materia}' OR t.materia LIKE '{$materia}_')
										AND CONCAT(t.anio, ' - ', t.cuatrimestre) = '{$periodo}'
									ORDER BY LEFT(t.turno, 1), t.materia, 
										FIELD(t.dia, 'lunes', 'martes', 
											'miércoles', 'jueves', 
											'viernes', 'sábado')
									#LIMIT 30";
					//echo $query;
					$result = $mysqli->query($query);
					echo $mysqli->error;
					$turnos = array();
					
					while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
						$turnos[] = $row;
					}
					
					if (sizeof($turnos)) {
						
						echo "<table class='docentes' style='width:98%;'>";
						echo "<tr class='subtitulo'>
								<th class='subtitulo'style='width:20%;'>Cod</th>
								<th class='subtitulo'style='width:50%;'>Nombre</th>
								<th class='subtitulo' style='width:15%;'>Día</th>
								<th class='subtitulo' style='width:8%;'>Horario</th>
								<th class='subtitulo' style='width:7%;'>Eliminar</th>
							</tr>";
						foreach ($turnos AS $valores) {
							echo "<tr class='info'>
									<td class='info masInfo' data-id='$valores[id]'>$valores[materia]</td>
									<td class='info masInfo' data-id='$valores[id]'>$valores[nombres]</td>";
							echo "<td class='materia' style='text-align:left;'>$valores[dia]</td>";
							echo "<td class='materia' style='text-align:left;'>$valores[turno]</td>";
							echo "<td class='formularioLaterial eliminarEnTabla'><button type='button' 
									class='formularioLateral botonEliminarTurno' id='eliminarTurno' data-id='$valores[id]'>
								X</button>";
							echo "</tr>";
						}
						echo "</table>";
					} else {
						echo "<p>No se encontraron horarios</p>";
					}
					break;
					
				case "tablaMaterias":
					require 'conexion.php';
					
					$where = '';
					
					if (isset($_REQUEST['filtro']) AND $_REQUEST['filtro'] != '') {
						$where = " AND CONCAT(m.nombre, m.cod, m.conjunto, m.plan) LIKE '%{$_REQUEST['filtro']}%' ";
					}
					
					$query = "SELECT m.id, m.cod, m.conjunto, m.nombre, c.cod AS carrera, m.plan, m.cuatrimestre 
									FROM materia AS m
									LEFT JOIN carrera AS c
										ON c.id = m.carrera
									WHERE m.activo = 1 {$where} 
									ORDER BY m.carrera, m.plan, m.cuatrimestre, m.cod
									LIMIT 20";
					//echo $query;
					$result = $mysqli->query($query);
					echo $mysqli->error;
					$materias = array();
					
					while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
						$materias[] = $row;
					}
					
					if (sizeof($materias)) {
						
						echo "<table class='docentes' style='width:98%;'>";
						echo "<tr class='subtitulo'>
								<th class='subtitulo'style='width:5%;'>Cod</th>
								<th class='subtitulo'style='width:15%;'>Conjunto</th>
								<th class='subtitulo'style='width:40%;'>Materia</th>
								<th class='subtitulo' style='width:10%;'>Carrera</th>
								<th class='subtitulo' style='width:10%;'>Plan</th>
								<th class='subtitulo' style='width:10%;'>Cuatrimestre</th>
								<!--<th class='subtitulo' style='width:10%;'>Eliminar</th>-->
							</tr>";
						foreach ($materias AS $valores) {
							echo "<tr class='info'>
									<td class='info masInfo' data-id='$valores[id]'>$valores[cod]</td>
									<td class='info masInfo' data-id='$valores[id]'>$valores[conjunto]</td>
									<td class='info masInfo' data-id='$valores[id]'>$valores[nombre]</td>";
							echo "<td class='materia' style='text-align:left;'>$valores[carrera]</td>";
							echo "<td class='materia' style='text-align:left;'>$valores[plan]</td>";
							echo "<td class='materia' style='text-align:left;'>$valores[cuatrimestre]</td>";
							/*echo "<td class='formularioLaterial eliminarEnTabla'><button type='button' 
									class='formularioLateral botonEliminar' id='eliminarDocente' data-id='$valores[id]'>
								X</button>";*/
							echo "</tr>";
						}
						echo "</table>";
					} else {
						echo "<p>No se encontraron materias</p>";
					}
					break;
					
				case "tablaResponsables":
					require 'conexion.php';
					
					$where = '';
					
					if (isset($_REQUEST['filtro']) AND $_REQUEST['filtro'] != '') {
						$where = " WHERE CONCAT(responsable, materia) LIKE '%{$_REQUEST['filtro']}%' ";
					}
					
					$query = "SELECT id, responsable, materia FROM vista_responsable
									{$where}
									ORDER BY materia
									LIMIT 20";
					//echo $query;
					$result = $mysqli->query($query);
					echo $mysqli->error;
					$docentes = array();
					
					while ($row = $result->fetch_array(MYSQLI_ASSOC) ) {
						$docentes[] = $row;
					}
					
					if (sizeof($docentes)) {
						
						echo "<table class='docentes' style='width:98%;'>";
						echo "<tr class='subtitulo'>
								
								<th class='subtitulo'style='width:45%;'>Docente</th>
								<th class='subtitulo' style='width:45%;'>Materia</th>
								<th class='subtitulo' style='width:10%;'>Eliminar</th>
							</tr>";
						foreach ($docentes AS $valores) {
							echo "<tr class='info'>
									<td class='info masInfo' data-id='$valores[id]'>$valores[responsable]</td>
									<td class='info masInfo' data-id='$valores[id]'>$valores[materia]</td>";
							echo "<td class='formularioLaterial eliminarEnTabla'><button type='button' 
									class='formularioLateral botonEliminar' id='eliminarResponsable' data-id='$valores[id]'>
								X</button>";
							echo "</tr>";
						}
						echo "</table>";
					} else {
						echo "<p>No se encontraron docentes</p>";
					}
					break;
					
				case "generarInscriptos";
					require 'conexion.php';
					$periodo = $_REQUEST['periodo'];
					
					$query = "REPLACE INTO estimacion (materia, nombre_materia, turno, cantidad, anio, cuatrimestre)
							SELECT conjunto, nombre_materia, turno, cantidad, anio_academico, LEFT(periodo_lectivo, 1)
							FROM vista_inscriptos_por_conjunto
							WHERE CONCAT(anio_academico, ' - ', LEFT(periodo_lectivo, 1)) = '{$periodo}'
								AND NOT ISNULL(conjunto)";	
								
					$mysqli->query($query);
					
					$mysqli->close();
					
					break;
				
				case "cambiarEstimacionInscriptos";
					require 'conexion.php';
					$periodo = $_REQUEST['periodo'];
					$periodo = explode(' - ', $periodo);
					$anio = $periodo[0];
					$cuatrimestre = $periodo[1];
					$materia = $_REQUEST['materia'];
					$nombre_materia = $_REQUEST['nombre'];
					$inscriptos['M'] = $_REQUEST['M'];
					$inscriptos['N'] = $_REQUEST['N'];
					$inscriptos['T'] = $_REQUEST['T'];
					
					
					$query = "REPLACE INTO estimacion (materia, nombre_materia, turno, cantidad, anio, cuatrimestre)
							VALUES ('{$materia}', '{$nombre_materia}', 'M', {$inscriptos['M']}, {$anio}, {$cuatrimestre}), 
								('{$materia}', '{$nombre_materia}', 'N', {$inscriptos['N']}, {$anio}, {$cuatrimestre}), 
								('{$materia}', '{$nombre_materia}', 'T', {$inscriptos['T']}, {$anio}, {$cuatrimestre})";	
					
					echo $query;
					
					$mysqli->query($query);
					
					$mysqli->close();
					
					break;
				
				case "agregarCurso";
					require 'conexion.php';
					$anio = $ANIO;
					$cuatrimestre = $CUATRIMESTRE;
					
					$materia = $_REQUEST['codigo'];
					$nombre_materia = $_REQUEST['nombre'];
					$inscriptos['M'] = $_REQUEST['M'];
					$inscriptos['N'] = $_REQUEST['N'];
					$inscriptos['T'] = $_REQUEST['T'];
					
					
					$query = "INSERT INTO estimacion (materia, nombre_materia, turno, cantidad, anio, cuatrimestre)
							VALUES ('{$materia}', '{$nombre_materia}', 'M', {$inscriptos['M']}, {$anio}, {$cuatrimestre}), 
								('{$materia}', '{$nombre_materia}', 'N', {$inscriptos['N']}, {$anio}, {$cuatrimestre}), 
								('{$materia}', '{$nombre_materia}', 'T', {$inscriptos['T']}, {$anio}, {$cuatrimestre})";	
					
					//echo $query;
					
					$mysqli->query($query);
					
					$mysqli->close();
					
					break;
					
				case "agregarCursoExtension";
					require 'conexion.php';
					$anio = $_REQUEST['anio'];
					$cuatrimestre = $_REQUEST['cuatrimestre'];
					
					$materia = $_REQUEST['cod'];
					$nombre_materia = $_REQUEST['nombre'];
					$turno = $_REQUEST['turno'];
					$cantidad = $_REQUEST['cantidad'];
					$esRegular = 0;
					
					
					$query = "INSERT INTO estimacion (materia, nombre_materia, turno, cantidad, anio, cuatrimestre, es_regular)
							VALUES ('{$materia}', '{$nombre_materia}', '{$turno}', {$cantidad}, {$anio}, {$cuatrimestre}, {$esRegular})";	
					
					//echo $query;
					
					$mysqli->query($query);
					
					$mysqli->close();
					
					break;
					
				case "buscarCurso":
					require "./conexion.php";
					
					$query = "SELECT materia, nombre_materia, turno, 
								cantidad, cuatrimestre, anio
							FROM estimacion 
							WHERE es_regular = 0 ";
					
					$result = $mysqli->query($query);
					if ($result->num_rows == 1) {
						$row = $result->fetch_row();
						$datosMateria = "/*/";
						foreach ($row as $value) {
							$datosMateria .= $value . "/*/";
						} 
						echo $datosMateria;
					}
					
					
					$result->free();
					$mysqli->close();
					break;
					
				case "copiarOferta":
					require "./conexion.php";
					
					$copiarDe = $_REQUEST['copiarDe'];
					/*$copiarDe = explode(' - ', $copiarDe);
					$anio = $copiarDe[0];
					$cuatrimestre = $copiarDe[1];*/
					
					$copiarAOriginal = $_REQUEST['copiarA'];
					$copiarA = explode(' - ', $_REQUEST['copiarA']);
					$anio = $copiarA[0];
					$cuatrimestre = $copiarA[1];
					
					
					
					$query = "DELETE FROM turnos_con_conjunto
						WHERE CONCAT(anio, ' - ', cuatrimestre) = '{$copiarAOriginal}';";
					
					$mysqli->query($query);
					
					$query = "INSERT INTO turnos_con_conjunto
							(materia, dia, turno, observaciones, anio, cuatrimestre)
						SELECT materia, dia, turno, observaciones, {$anio}, {$cuatrimestre}
						FROM turnos_con_conjunto
						WHERE CONCAT(anio, ' - ', cuatrimestre) = '{$copiarDe}';";
					$mysqli->query($query);
					
					$query = "DELETE FROM comisiones_abiertas
						WHERE CONCAT(anio, ' - ', cuatrimestre) = '{$copiarAOriginal}';";
					
					$mysqli->query($query);
					
					$query = "INSERT INTO comisiones_abiertas
						(materia, horario, nombre_comision, turno, dependencia, observaciones, anio, cuatrimestre, nombre_horario)
						SELECT materia, horario, nombre_comision, turno, dependencia, observaciones,
							{$anio}, {$cuatrimestre}, nombre_horario
						FROM comisiones_abiertas
						WHERE CONCAT(anio, ' - ', cuatrimestre) = '{$copiarDe}';";
					$mysqli->query($query);
					
					echo $query;
					if ($mysqli->error) {
						echo $mysqli->error;
					}
					
					$mysqli->close();
					break;
					
				case "tablaEstimacionPreliminar":
					require 'conexion.php';
					
					$filtro = $_REQUEST['filtro'];
					//echo $filtro;
					
					$where = '';
					if ($filtro != '') {
						$where = "WHERE CONCAT(m.conjunto, m.nombre) LIKE '%{$filtro}%'";
					}
	
					$query = "SELECT m.cod, 
							m.nombre,
							m.conjunto
						FROM materia AS m
						$where
						ORDER BY m.cuatrimestre, conjunto";
						
					//echo $query;
					$result = $mysqli->query($query);
					$materias = array();
					
					while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
						$materias[$row['cod']] = $row;
					}
					
					$inscriptosEstimados = array();
					foreach ($materias as $cod => $info) {
						$materia = new clases\Materia($cod);
						
						$nombre = $materia->mostrarNombresConjunto();
						$carrera = $materia->datosMateria['cod_carrera'];
						$conjunto = $materia->mostrarConjunto();
						$estimados = $materia->mostrarEstimacionPreliminarPorCod($ANIO, $CUATRIMESTRE, $carrera);
						
						
						foreach ($estimados as $turno => $datos) {
							if ($turno == 'N') {
								$turno = 'Noche';
							} elseif($turno = 'M') {
								$turno = 'Mañana';
							}
							
							if (isset($inscriptosEstimados[$turno][$conjunto])) {
								if (isset($datos['nuevos'])) {
									$inscriptosEstimados[$turno][$conjunto]['nuevos'] += $datos['nuevos'];
								} else {
									$inscriptosEstimados[$turno][$conjunto]['nuevos'] += 0;
								}
								
								if (isset($datos['recursantes'])) {
									$inscriptosEstimados[$turno][$conjunto]['recursantes'] += $datos['recursantes'];
								} else {
									$inscriptosEstimados[$turno][$conjunto]['recursantes'] += 0;
								}
							} else {
								if (isset($datos['nuevos'])) {
									$inscriptosEstimados[$turno][$conjunto]['nuevos'] = $datos['nuevos'];
								} else {
									$inscriptosEstimados[$turno][$conjunto]['nuevos'] = 0;
								}
								
								if (isset($datos['recursantes'])) {
									$inscriptosEstimados[$turno][$conjunto]['recursantes'] = $datos['recursantes'];
								} else {
									$inscriptosEstimados[$turno][$conjunto]['recursantes'] = 0;
								}
							}
							
							if(!isset($inscriptosEstimados[$turno][$conjunto]['nombre'])) {
								$inscriptosEstimados[$turno][$conjunto]['nombre'] = $nombre;
							}
						}
						
						
					}
					
					foreach ($inscriptosEstimados as $turno => $info) {	
						echo "<h1>Turno: {$turno}</h1>
						<table>
							<thead>
								<th class='info subtitulo' style='width:15%;'>Cod</th>
								<th class='info subtitulo' style='width:55%;'>Materia</th>
								<th class='info subtitulo' style='width:10%;text-align:center;'>Nuevos</th>
								<th class='info subtitulo' style='width:10%;text-align:center;'>Recursantes</th>
								<th class='info subtitulo' style='width:10%;text-align:center;'>Total</th>
							</thead>
							<tbody>";
						
						foreach ($info as $conjunto => $inscriptos) {
							echo "<tr class='info'>";
						
							echo "<td class='info'>{$conjunto}</td>";
							echo "<td class='info'>" . substr($inscriptos['nombre'], 0, 35) . "</td>";
							
							//echo "<td class='info'>{$info['cuatrimestre']}</td>";
							//echo "<td class='info'>{$info['carrera']}</td>";
							
							echo "<td class='info' style='text-align:center;'>". $inscriptos['nuevos'] . "</td>";
							echo "<td class='info' style='text-align:center;'>". $inscriptos['recursantes'] . "</td>";
							$inscriptos['total'] = $inscriptos['nuevos'] + $inscriptos['recursantes'];
							echo "<td class='info' style='text-align:center;'>". $inscriptos['total'] . "</td>";
							echo "</tr>";
						}
						
						echo "</tbody></table>";
						
						
						
					}
					
					break;
					
				case "armarEstimacionBaseDeDatos":
					require 'conexion.php';
					
					$cuatrimestre = 1;
					$anio = $ANIO + 1;
					if ($CUATRIMESTRE == 1) {
						$cuatrimestre = 2;
						$anio = $ANIO;
					}
					
					//Borrar la estimación previa
					$query = "DELETE FROM estimacion
								WHERE anio = {$ANIO} 
									AND cuatrimestre = {$CUATRIMESTRE}";
					$mysqli->query($query);
					
					$filtro = $_REQUEST['filtro'];
					//echo $filtro;
					
					
					$where = '';
					if ($filtro != '') {
						$where = "WHERE CONCAT(m.conjunto, m.nombre) LIKE '%{$filtro}%'";
					}
	
					$query = "SELECT m.cod, 
							m.nombre,
							m.conjunto
						FROM materia AS m
						$where
						ORDER BY m.cuatrimestre, conjunto";
						
					//echo $query;
					$result = $mysqli->query($query);
					$materias = array();
					
					while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
						$materias[$row['cod']] = $row;
					}
					
					$inscriptosEstimados = array();
					foreach ($materias as $cod => $info) {
						$materia = new clases\Materia($cod);
						
						$nombre = $materia->mostrarNombresConjunto();
						$carrera = $materia->datosMateria['cod_carrera'];
						$conjunto = $materia->mostrarConjunto();
						$estimados = $materia->mostrarEstimacionPreliminarPorCod($ANIO, $CUATRIMESTRE, $carrera);
						
						
						foreach ($estimados as $turno => $datos) {
							
							if (isset($inscriptosEstimados[$turno][$conjunto])) {
								if (isset($datos['nuevos'])) {
									$inscriptosEstimados[$turno][$conjunto]['nuevos'] += $datos['nuevos'];
								} else {
									$inscriptosEstimados[$turno][$conjunto]['nuevos'] += 0;
								}
								
								if (isset($datos['recursantes'])) {
									$inscriptosEstimados[$turno][$conjunto]['recursantes'] += $datos['recursantes'];
								} else {
									$inscriptosEstimados[$turno][$conjunto]['recursantes'] += 0;
								}
							} else {
								if (isset($datos['nuevos'])) {
									$inscriptosEstimados[$turno][$conjunto]['nuevos'] = $datos['nuevos'];
								} else {
									$inscriptosEstimados[$turno][$conjunto]['nuevos'] = 0;
								}
								
								if (isset($datos['recursantes'])) {
									$inscriptosEstimados[$turno][$conjunto]['recursantes'] = $datos['recursantes'];
								} else {
									$inscriptosEstimados[$turno][$conjunto]['recursantes'] = 0;
								}
							}
							
							if(!isset($inscriptosEstimados[$turno][$conjunto]['nombre'])) {
								$inscriptosEstimados[$turno][$conjunto]['nombre'] = $nombre;
							}
						}
						
						
					}
					
					foreach ($inscriptosEstimados as $turno => $info) {	
						
						foreach ($info as $conjunto => $inscriptos) {
							
							$inscriptos['total'] = $inscriptos['nuevos'] + $inscriptos['recursantes'];
							
							$query = "INSERT INTO estimacion (materia, nombre_materia, turno, cantidad, cuatrimestre, es_regular)
								VALUES ('{$conjunto}', '{$inscriptos['nombre']}', '{$turno}', {$inscriptos['total']}, {$anio}, {$cuatrimestre}, 1)";
							$mysqli->query($query);
						}
					}
					
					break;
					
				case "tablaAperturaComisiones":
					require 'conexion.php';
					
					$filtro = $_REQUEST['filtro'];
					//echo $filtro;
					
					$where = '';
					if ($filtro != '') {
						$where = "WHERE CONCAT(m.conjunto, m.nombre) LIKE '%{$filtro}%'";
					}
	
					$query = "SELECT Min(m.cod), 
							m.conjunto
						FROM materia AS m
						$where
						GROUP BY m.conjunto
						ORDER BY m.cuatrimestre, conjunto";
						
					//echo $query;
					$result = $mysqli->query($query);
					$materias = array();
					
					while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
						$materias[$row['cod']] = $row;
					}
					
					$inscriptosEstimados = array();
					foreach ($materias as $cod => $info) {
						$materia = new clases\Materia($cod);
						
						$nombre = $materia->mostrarNombresConjunto();
						$carrera = $materia->datosMateria['cod_carrera'];
						$conjunto = $materia->mostrarConjunto();
						$inscriptosEstimados = $materia->mostrarCantidadComisionesDesdeActas($ANIO - 1, $CUATRIMESTRE);
						
					}
					
					foreach ($inscriptosEstimados as $turno => $info) {	
						echo "<h1>Turno: {$turno}</h1>
						<table>
							<thead>
								<th class='info subtitulo' style='width:15%;'>Cod</th>
								<th class='info subtitulo' style='width:55%;'>Materia</th>
								<th class='info subtitulo' style='width:10%;text-align:center;'>Nuevos</th>
								<th class='info subtitulo' style='width:10%;text-align:center;'>Recursantes</th>
								<th class='info subtitulo' style='width:10%;text-align:center;'>Total</th>
							</thead>
							<tbody>";
						
						foreach ($info as $conjunto => $inscriptos) {
							echo "<tr class='info'>";
						
							echo "<td class='info'>{$conjunto}</td>";
							echo "<td class='info'>" . substr($inscriptos['nombre'], 0, 35) . "</td>";
							
							//echo "<td class='info'>{$info['cuatrimestre']}</td>";
							//echo "<td class='info'>{$info['carrera']}</td>";
							
							echo "<td class='info' style='text-align:center;'>". $inscriptos['nuevos'] . "</td>";
							echo "<td class='info' style='text-align:center;'>". $inscriptos['recursantes'] . "</td>";
							$inscriptos['total'] = $inscriptos['nuevos'] + $inscriptos['recursantes'];
							echo "<td class='info' style='text-align:center;'>". $inscriptos['total'] . "</td>";
							echo "</tr>";
						}
						
						echo "</tbody></table>";
						
						
						
					}
					
					break;
					
				case "tablaComisionesAbiertas":
					require 'conexion.php';
					
					$query = "SELECT id, CONCAT(materia, nombre_comision) AS nombre, horario, turno
								FROM comisiones_abiertas
								WHERE materia = '{$_REQUEST['materia']}'
								    AND CONCAT(anio, ' - ', cuatrimestre) = '{$_REQUEST['periodo']}'
								ORDER BY turno, nombre_comision";
								
					//echo $query;
					$result = $mysqli->query($query);
					
					$comisiones = array();
					while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
						$comisiones[] = $row;
					}
					
					if (sizeof($comisiones)) {
						
						echo "<table class='docentes' style='width:98%;'>";
						echo "<tr class='subtitulo'>
								<th class='subtitulo'style='width:10%;'>Turno</th>
								<th class='subtitulo'style='width:20%;'>Nombre</th>
								<th class='subtitulo' style='width:60%;'>horario</th>
								<th class='subtitulo' style='width:10%;'>Eliminar</th>
							</tr>";
						foreach ($comisiones AS $valores) {
							echo "<tr class='info'>
									<td class='info masInfo' data-id='$valores[id]'>$valores[turno]</td>
									<td class='info masInfo' data-id='$valores[id]'>$valores[nombre]</td>";
							echo "<td class='materia' style='text-align:left;'>$valores[horario]</td>";
							echo "<td class='formularioLaterial eliminarEnTabla'><button type='button' 
									class='formularioLateral botonEliminar' id='eliminarComisionAbierta' data-id='$valores[id]'>
								X</button>";
							echo "</tr>";
						}
						echo "</table>";
					} else {
						echo "<p>No se encontraron comisiones abiertas en el periodo</p>";
					}
					
					break;
					
				case "agregarComisionAbierta":
					require "./conexion.php";
					
					$periodo = explode(' - ', $_REQUEST['periodoComisionAbierta']);
					$anio = $periodo[0];
					$cuatrimestre = $periodo[1];
					$letra = "";
					
					$turno = $_REQUEST['turnoComisionAbierta'];
					$nombre_comision = $_REQUEST['nombreComisionAbierta'];
					
					if (strpos($nombre_comision, 'S')) {
						$turno = 'S';
					}
					
					
					if (strpos($_REQUEST['horarioComisionAbierta'], '(')) {
						//echo $_REQUEST['horarioComisionAbierta'];
						$letra = explode(' (', $_REQUEST['horarioComisionAbierta']);
						//print_r($letra);
						$letra = trim($letra[1], ')');
						//echo $letra . " - ";
						$letra = substr($letra, 1, 1);
						//echo $letra . " - ";
					}
					
					
					$query = "INSERT INTO comisiones_abiertas 
								SET turno = '{$turno}', 
								nombre_comision = '{$nombre_comision}', 
								horario = '{$_REQUEST['horarioComisionAbierta']}', 
								materia = '{$_REQUEST['materia']}', 
								anio = {$anio},
								cuatrimestre = {$cuatrimestre},
								observaciones = '{$letra}'";
					//echo $query;
					$mysqli->query($query);
					if ($mysqli->errno) {
						echo "ERROR: " . $mysqli->error;
					} else {
						//echo "Se ha cargado el nuevo docente";
					}
					
					$mysqli->close();
				  
					break;
					
				case "eliminarComisionAbierta":
					require 'conexion.php';
				
					$id = $_REQUEST['id'];
					
					$query = "DELETE FROM comisiones_abiertas
								WHERE id = {$id};";
					$mysqli->query($query);
					
					$mysqli->close();
					break;
				
				case "optionsHorariosComisionAbierta":
					require 'conexion.php';
					
					$materia = $_REQUEST['materia'];
					$periodo = $_REQUEST['periodo'];
					
					$query = "SELECT DISTINCT materia, dia, turno AS horario, LEFT(turno, 1) AS turno
								FROM turnos_con_conjunto
								WHERE materia LIKE '{$materia}%'
									AND CONCAT(anio, ' - ', cuatrimestre) = '{$periodo}'
								ORDER BY materia, dia, horario";
					$result = $mysqli->query($query);
					
					$horarios = array();
					while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
						if($row['dia'] == 'sábado') {
							$row['horario'] = str_replace($row['turno'], 'S', $row['horario']);
						} 
						$horarios[$row['materia']][$row['turno']][$row['dia']][] = $horasTurno[$row['horario']];
					}
					
					$imprimir = "";
					print_r($horarios);
					foreach ($horarios as $materia => $turnos) {
						
						foreach ($turnos as $turno => $dias) {
							$option = "";
							foreach ($dias as $dia => $horas) {
								foreach ($horas as $hora) {
									$option .= $dia . $hora . ", ";
									//$imprimir .= "<option value='{$option}'>{$option}</option>";
								}
								
							}
							$option = trim($option, ', ');
							$letra = substr($materia, -1, 1);
							$letraOption = "";
							if ($letra != ')') {
								$option .= " (" . $turno . $letra . ")";
								$letraOption = "data-letra='" . $letra . "'" ;
							}
							
							$imprimir .= "<option value='{$option}' $letraOption>{$option}</option>";
							
						}
						
						
						
					}
					
					echo $imprimir;			
				
					break;
					
					case "inscriptosEnCorrelativas":
					require 'conexion.php';
					//print_r($_REQUEST);
					$conjunto = $_REQUEST['conjunto'];
					$periodo = $_REQUEST['periodo'];
					
					$query = "SELECT COUNT(DISTINCT i.nro_documento) AS inscriptos, 
								m.conjunto,
								GROUP_CONCAT(DISTINCT m.nombre ORDER BY m.cod SEPARATOR '/') AS nombres,
								REPLACE(nombre_comision, nombre_comision + 0, '') AS comision_agrupada
							FROM inscriptos AS i
							LEFT JOIN materia AS m
								ON m.cod = i.materia
							WHERE CONCAT(i.anio_academico,' - ', i.periodo_lectivo + 0) = '{$periodo}'
								AND materia IN (
									SELECT requisito FROM correlatividad WHERE materia IN {$conjunto}
								)
							GROUP BY m.conjunto, comision_agrupada
							ORDER BY m.conjunto, comision_agrupada";
								
					//echo $query;
					$result = $mysqli->query($query);
					
					echo $mysqli->error;
					$comisiones = array();
					while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
						$comisiones[] = $row;
					}
					
					if (sizeof($comisiones)) {
						
						echo "<table class='docentes' style='width:98%;'>";
						echo "<tr class='subtitulo'>
								<th class='subtitulo'style='width:20%;'>Códigos</th>
								<th class='subtitulo'style='width:60%;'>Nombre</th>
								<th class='subtitulo' style='width:10%;'>Comision</th>
								<th class='subtitulo' style='width:10%;'>Inscriptos</th>
							</tr>";
						foreach ($comisiones AS $valores) {
							echo "<tr class='info'>
									<td class='info masInfo'>$valores[conjunto]</td>
									<td class='info masInfo'>$valores[nombres]</td>";
							echo "<td class='materia' style='text-align:left;'>$valores[comision_agrupada]</td>";
							echo "<td class='materia' style='text-align:left;'>$valores[inscriptos]</td>";
							echo "</tr>";
						}
						echo "</table>";
					} else {
						echo "<p>Sin resultados</p>";
					}
					
					break;
					
				case "tablaComisionesCalendario":
					require 'conexion.php';
					require 'constantes.php';
					
					$materia = new clases\Materia($_REQUEST['materia']);
					
					$anio = $ANIO;
					$cuatrimestre = $CUATRIMESTRE;
					
					$situacion = $materia->mostrarSituacionAsignacion($anio, $cuatrimestre);
					$equipoDocente = $materia->mostrarEquipoDocenteConRestricciones($anio, $cuatrimestre);
					$conjunto = $materia->mostrarConjunto();
					$cod = $materia->mostrarCod();
					
					//print_r($equipoDocente);
					
					$comisiones = array();
					foreach ($situacion as $detalle) {
						if (!in_array($detalle['turno'], ['N', 'T', 'M', 'S'])) {
							$comisiones[$detalle['materia'] . $detalle['nombre_comision']][$detalle['turno']][$detalle['dia']] = $detalle;
						} else {
							$comisiones[$detalle['materia'] . $detalle['nombre_comision']][$detalle['turno'] . '1'][$detalle['dia']] = $detalle;
							$comisiones[$detalle['materia'] . $detalle['nombre_comision']][$detalle['turno'] . '2'][$detalle['dia']] = $detalle;
						}
					}
					//print_r($comisiones);
					foreach ($comisiones as $nombre => $turnos) {
						$comision = str_replace($conjunto, '', $nombre);
						echo "<div class='comision calendario'>";
						echo "<h3 class='comision'>Comisión {$nombre}</h3>";
						echo "<table class='comision' border='1'>";
						echo "<thead class='comision'>
							<tr class='comision'>
								
								<th class='calendario' style='width:10%;'>Horario</th>
								<th class='calendario' style='width:15%;'>Lunes</th>
								<th class='calendario' style='width:15%;'>Martes</th>
								<th class='calendario' style='width:15%;'>Miércoles</th>
								<th class='calendario' style='width:15%;'>Jueves</th>
								<th class='calendario' style='width:15%;'>Viernes</th>
								<th class='calendario' style='width:15%;'>Sábado</th>
								
							</tr>
							</thead>
							<tbody class='calendario' id='comisionesAsignadas'>";
							
							foreach ($turnos as $turno => $dias) {
								echo "<tr class='calendario turno{$turno}'>";
								echo "<td class='calendario horario'>{$horasTurno[$turno]}</td>";
								
								foreach ($diasSemana as $dia) {
									echo "<td class='calendario {$dia}'>";
									
									if (isset($turnos[$turno][$dia])) {
										echo "<form class='asignarDocente {$turno}{$dia}' method='POST' action=''>";
										echo "<input type='hidden' name='dia' value='{$dia}' />";
										echo "<input type='hidden' name='turno' value='{$turno}' />";
										echo "<input type='hidden' name='comision' value='{$comision}' />";
										echo "<input type='hidden' name='conjunto' value='{$conjunto}' />";
										echo "<input type='hidden' name='materia' value='{$cod}' />";
										echo "<select class='docente' name='docente' required>";
										echo "<option value=''>Seleccionar docente</option>";
										foreach ($equipoDocente as $idDocente => $datos) {
											if (!isset($datos[$turno][$dia])) {
												
												echo "<option value='{$idDocente}'>{$datos['nombre_docente']}</option>";
											}
										}
										echo "</select>";
										echo "<button type='submit' class='agregarDocente'>+</button>";
										echo "</form>";
									}
									
									
									echo "</td>";
									
								}
							}

							echo "</tbody>
						</table>
					</div>";
				}
					
					break;
					
				default:
					echo "No se realizó la búsqueda";
					
			}
		
	}
	
	
?>
