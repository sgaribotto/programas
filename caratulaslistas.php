<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>Carátulas</title>
		<style>
			
			
			
				div.cuerpo {
					page-break-after: always;
					font-size: 1.3em;
					line-height: 180%;
					width: 100%;
					text-align: justify;
					
				}
				
				p.cuerpo {
					text-indent: 1cm;
				}
				
				@media print {
					div.cuerpo {
						
					}
				}
			
			
			
		</style>
		
		<?php //require_once('./fuentes/meta.html'); ?>
		<?php
			
			include 'fuentes/constantes.php';
			require 'conexion.php';
			
			$periodo = $_REQUEST['periodo'];
			$materia = $_REQUEST['materia'];
			
			if ($materia != 'Todas') {
				$materia = " AND ca.materia = '{$materia}' ";
			} else {
				$materia = "";
			}
			
			
			$query = "SELECT ca.materia, 
					m.nombres, 
					ca.turno, 
					ca.nombre_comision,
					ca.horario, 
					IF(COUNT(DISTINCT aa.aula) > 1, 
					GROUP_CONCAT(DISTINCT CONCAT(aa.dia, ': ', aa.aula) 
									ORDER BY FIELD(aa.dia, 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado')
									SEPARATOR '<br>' 
					), aa.aula) AS aula,
					aa.cantidad_alumnos,
					GROUP_CONCAT(DISTINCT CONCAT(d.apellido, ', ', d.nombres) SEPARATOR '/') AS docentes
				FROM comisiones_abiertas AS ca
				LEFT JOIN vista_materias_por_conjunto AS m
					ON m.conjunto = ca.materia
				LEFT JOIN asignacion_aulas AS aa
					ON aa.anio = ca.anio AND aa.cuatrimestre = ca.cuatrimestre
						AND aa.comision_real = ca.nombre_comision 
						AND aa.materia = CONCAT(ca.materia, IFNULL(ca.observaciones, ''))
				LEFT JOIN asignacion_comisiones_calendario AS ac 
					ON ac.anio = ca.anio AND ac.cuatrimestre = ca.cuatrimestre
						AND ac.materia = ca.materia AND ac.comision = ca.nombre_comision
				LEFT JOIN docente AS d
					ON d.id = ac.docente
				WHERE CONCAT(ca.anio, ' - ', ca.cuatrimestre) = '{$periodo}' {$materia}
				GROUP BY ca.materia, ca.nombre_comision
				#HAVING NOT ISNULL(aula) AND NOT ISNULL(docentes)
				ORDER BY materia";
			
			
			//echo $query;
			$result = $mysqli->query($query);
			echo $mysqli->error;
			$caratulas = array();
			
			while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$caratulas[] = $row;
			}
			
			$texto = "<div class='cuerpo' >
				<img src='images/logo.jpg' />
				<br />
				<br />
			<div class='datos' style='border-style: solid'>
			MATERIA: %s
			<br />
			COMISION: %s
			<br />
			DOCENTES: %s
			<br />
			HORARIO: %s
			<br />
			AULA: %s
			</div>
			<br />
			<br />
			<br />

			<span style='display: block; text-align:right'>San Martín, 04 de Marzo de 2019</span>
			<br />
			<br />
			Señores Profesores:
			<br />
			<p class='cuerpo'>Se comunica que los alumnos que se encuentran inscriptos en el presente listado de asistencia son aquellos que, habiendo realizado la inscripción en tiempo y forma, están habilitados para cursar la asignatura que Ud. dicta.</p>
			<p class='cuerpo'>En el caso de detectar omisiones, se solicita no incluir a los alumnos en el listado de asistencia, sino indicarles que deberán pasar por la oficina de la Dirección de Asuntos Académicos en el horario de atención al público, para efectuar el reclamo de acuerdo a la normativa vigente, y cumpliendo con el mecanismo que se ha implementado para tal fin.</p>
			<p class='cuerpo'>Los alumnos tienen un plazo máximo de 7 (siete) días para realizar el trámite, a contarse a partir del primer día en que se inicia la cursada de la asignatura correspondiente.  Todo alumno que no efectúe el trámite en tiempo y forma y/o que no se notifique de la resolución de esta Dirección, no estará incluido en el Acta de Calificaciones Parciales que le será entregada oportunamente.</p>
			<p class='cuerpo'>La Dirección de Asuntos Académicos le informará a Ud., e incluirá en el Acta de Calificaciones Parciales a aquellos alumnos que, habiendo cumplido con el trámite de reclamo, se encuentran habilitados para continuar cursando la asignatura correspondiente.</p>
			<p class='cuerpo'>Tengan en cuenta que el segundo cuatrimestre finaliza el 22 de junio del presente y <b>las actas de cursada deberán ser entregadas hasta esta fecha.</b></p>
			Gracias por su colaboración.
			<br />
			<br />
			<span style='display: block; text-align:right'><u>Dirección de Asuntos Académicos</u></span>
			</div>";
			
			//echo $texto;
			
			foreach ($caratulas as $detalles) {
				//print_r($detalles);
				printf($texto, $detalles['materia'] . ' ' . mb_strtoupper($detalles['nombres'], 'utf8'), $detalles['nombre_comision'], $detalles['docentes'], $detalles['horario'], $detalles['aula']);
			}
		?>
			
	</body>
</html>

