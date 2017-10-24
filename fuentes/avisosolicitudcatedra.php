<meta charset="utf-8">
<?php
	//COmentar para ejecutar
	//phpinfo();
	require '../libs/PHPMailer/PHPMailerAutoload.php';
	require 'constantes.php';

	function mailAvisoMasCampus($docente, $direccion, $asunto, $mensaje) {
		
		
		try {
			
			 
			$mail = new PHPMailer;
			
			$mail->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				)
			);
			$mail->CharSet = 'utf-8';
			//$mail->SMTPDebug = 2;
			//$mail-­>Encoding = "quoted­printable"; 
			//$mail->IsSMTP();
			$mail->Mailer = 'SMTP';
			$mail->SMTPDebug = 0;
			$mail->Host = 'smtp.unsam.edu.ar';
			
			$mail->Username = "planes.eeyn";//@unsam.edu.ar";
			$mail->Password = "Pla2017nes";
			//$mail->Host = 'smtp.gmail.com';
			
			//$mail->Username = "santiagogaribotto@gmail.com";
			//$mail->Password = "";
			$mail->SMTPSecure = "ssl";
			$mail->Port = '465';
			$mail->SMTPAuth = true;
			$mail->AddReplyTo('webmaster.eeyn@unsam.edu.ar', 'Planes EEYN');
			$mail->setFrom('planes.eeyn@unsam.edu.ar', 'Planes EEYN');
			$mail->isHTML(true);
			
			//$mail->AddAttachment('/var/www/CV - Santiago Lazzati - General 2016.doc');
			
			$mail->addAddress($direccion, $docente);
			$mail->Subject = $asunto;
			$mail->Body = $mensaje;
			
			//print_r($mail);
			//print_r($mail->send());
			if (!$mail->send()) {
				echo 'Mail not sent<br />';
				echo "error: " . $mail->ErrorInfo;
			} else {
				echo "Message Sent <br>";
				
				//echo "error: " . $mail->ErrorInfo;
			}
		
		} catch (phpmailerException $e) {
		echo $e->errorMessage(); //Pretty error messages from PHPMailer

		} catch (Exception $e) {
		echo $e->getMessage(); //Boring error messages from anything else!
		}
	
	}
	
	
	
	//require 'conexion.php';
	
	$host = "10.1.71.121";
	$usuario = "programas";
	$clave = "TMtrj9rS5di";
	$db = "programas";
	
	$mysqli = new MySQLi($host, $usuario, $clave, $db);
	
	
	//CONSULTA DOCENTES NO RESPONSABLES DE MATERIA
	/*$query = "SELECT DISTINCT CONCAT_WS(', ', d.apellido, d.nombres) AS nombre_docente,
					d.id AS docente, dd.valor AS mail
				FROM asignacion_comisiones AS ac
				LEFT JOIN docente AS d ON d.id = ac.docente
				LEFT JOIN personal AS p ON p.dni = d.dni
				LEFT JOIN responsable AS r ON r.usuario = p.id AND r.activo = 1
				LEFT JOIN datos_docentes AS dd ON dd.docente = d.id AND tipo = 'mail'
				WHERE ISNull(r.id) AND NOT isNULL(dd.valor);";*/
	
	//CONSULTA PARA LOS RESPONSABLES  1 ADMIN, 2 ECO, 3 LITUR y 4 ECO+ADMIN 
	//6 CPU; 5 CCCP
	/*$query = "SELECT DISTINCT d.id AS docente, 
		CONCAT(p.apellido, ', ', p.nombres) AS nombre_docente,
		dd.valor AS mail
   
		FROM responsable AS r
		LEFT JOIN personal AS p ON p.id = r.usuario
		LEFT JOIN materia AS m ON m.cod = r.materia
		LEFT JOIN docente AS d ON d.dni = p.dni
		LEFT JOIN datos_docentes AS dd ON dd.docente = d.id AND dd.tipo = 'mail'
		WHERE r.activo = 1 AND m.carrera IN (1, 2, 3, 4)";*/
	
	//CONSULTA DOCENTES CON MATERIA DE PERTENENCIA Y COMISIONES
	/*$query = "SELECT CONCAT_WS(', ', d.apellido, d.nombres) AS nombre_docente, ac.materia, dd.valor AS mail, ac.docente,
				GROUP_CONCAT(DISTINCT m.nombre ORDER BY m.cod SEPARATOR ' | ') AS nombre_materia, 
				GROUP_CONCAT(DISTINCT ac.comision ORDER BY ac.comision SEPARATOR ' | ') AS comision
			FROM asignacion_comisiones AS ac
			LEFT JOIN materia AS m ON m.conjunto = ac.materia
			LEFT JOIN docente AS d ON d.id = ac.docente
			LEFT JOIN datos_docentes AS dd ON dd.docente = d.id AND dd.tipo = 'mail'
			WHERE ac.anio = 2016 AND ac.cuatrimestre = 1
			GROUP BY ac.materia, ac.docente
			HAVING NOT ISNULL(comision) AND comision != ''
			ORDER BY docente
			LIMIT 1000;";*/
	
	//CONSULTA CON LOS RESPONSABLES DE MATERIAS CON COMISIONES ABIERTAS
	$query = "SELECT DISTINCT CONCAT(p.apellido, ', ', p.nombres) AS nombre_docente, d.id AS docente, p.id, dd.valor AS mail,
				GROUP_CONCAT(DISTINCT ca.materia) AS materias
			FROM responsable AS r
			LEFT JOIN personal AS p ON p.id = r.usuario
			LEFT JOIN docente AS d ON d.dni = p.dni
			LEFT JOIN datos_docentes AS dd ON dd.docente = d.id AND dd.tipo = 'mail'
			LEFT JOIN materia AS m ON r.materia = m.cod
			LEFT JOIN comisiones_abiertas AS ca ON m.conjunto = ca.materia
			WHERE r.activo = 1 AND ca.anio = {$ANIO} AND ca.cuatrimestre = {$CUATRIMESTRE}
			GROUP BY d.id
			HAVING NOT ISNULL(materias);";
	
	//TEMPLATE Y ASUNTO
	$template = "<p 'style=text-align:justify;'>Estimado Responsable de Cátedra <b>%s</b>:
<br />
<br />
A efectos de poder realizar las designaciones de docentes para el primer cuatrimestre 
del ciclo lectivo 2018​ y con el fin de lograr una adecuada coordinación 
de cursos y​ alumnos le solicitamos realice la asignación de docentes a través de la 
aplicación web de Solicitud de Cátedra. En la misma pantalla, deberá indicar qué docentes 
utilizarán el aula virtual de MásCampus. Ésta será la única vía para solicitar el aula virtual y
deberá realizarse en el lapso indicado debajo. <br />
<br />
La distribución  de comisiones de la materia que usted tiene a cargo estará disponible ingresando a la 
dirección: <a href='http://planeseeyn.unsam.edu.ar/programas'>http://planeseeyn.unsam.edu.ar/programas</a> 
con el usuario y contraseña que se le otorgó el cuatrimestre anterior y accediendo a la opción
asignar comisiones. El sistema estará habilitado para ello el lunes 23 de octubre hasta el 
miércoles 8 de noviembre de 2017.
<br />
Una vez terminada la inscripción por parte de los alumnos, de acuerdo a la cantidad de inscriptos, haremos los ajustes necesarios. 
<br />

<br />
Cualquier duda o consulta sobre la plataforma no dude en comunicarse a la Dirección de Asuntos Académicos.
<br />
<br />
Saluda Cordialmente.<br />
Dirección de Asuntos Académicos.<br />
Secretaría Académica<br />
EEYN - UNSAM </p>";
	
	$asunto = "Solicitud de cátedra 1er cuatrimestre 2018";
	
	// ARMADO DE LA BASE DE DATOS PARA EL ENVÏO DE MAILS
	
	/*$result = $mysqli->query($query);
	while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
		
		//$materia = $row['nombre_materia'];
		$message = sprintf($template, $row['nombre_docente']);
		
		
		
		$docente = $row['docente'];
		$tipo = 'responsable';
		$nombre_docente = $mysqli->real_escape_string($row['nombre_docente']);
		$mail = $mysqli->real_escape_string($row['mail']);
		$asunto = $mysqli->real_escape_string($asunto);
		$message = $mysqli->real_escape_string($message);
		
		$insertQuery = "INSERT INTO envios_por_mail 
			(id_destinatario, tipo_destinatario, destinatario, mail, asunto, mensaje) VALUES
			($docente, '$tipo', '$nombre_docente', '$mail', '$asunto', '$message');";
		$mysqli->query($insertQuery);
		echo $mysqli->error;
		echo "$row[docente] / $nombre_docente --> $row[mail]";
		
		echo "<hr />";
		
		
		
		
	}*/
	
	//TEST MAIL A planes.eeyn@unsam.edu.ar
	//$mensaje = sprintf($template, 'Santiago Garibotto');
	//mailAvisoMasCampus('Santiago Garibotto', 'planes.eeyn@unsam.edu.ar', 'TEST' . $asunto, $mensaje);
	//mailAvisoMasCampus('Matías López', 'matias.lopez@unsam.edu.ar', 'TEST' . $asunto, $mensaje);
	//mailAvisoMasCampus('Lorena Penna', 'lpenna@unsam.edu.ar', 'TEST' . $asunto, $mensaje);
	//mailAvisoMasCampus('Marcelo Estayno', 'mestayno@gmail.com', 'TEST' . $asunto, $mensaje);*/
	
	
	//ENVIO DE MAILS GUARDADOS EN LA BASE
	$query = "SELECT id, id_destinatario, tipo_destinatario, destinatario, mail, asunto, mensaje 
			FROM envios_por_mail
			WHERE NOT ISNULL(mail) AND mail != '' AND enviado = 0
			LIMIT 30;";
	
	$result = $mysqli->query($query);
	echo $mysqli->error;
	
	while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
		mailAvisoMasCampus($row['destinatario'], $row['mail'], $row['asunto'], $row['mensaje']);
		echo $row['mail'];
		echo $row['mensaje'] . "<hr>";
		$updateQuery = "UPDATE envios_por_mail 
							SET enviado = 1 
							WHERE id = $row[id];";
		$mysqli->query($updateQuery);
	}
	
	$result->free();
	$mysqli->close();
	
	
	
	
?>
