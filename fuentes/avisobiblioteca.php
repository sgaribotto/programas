<meta charset="utf-8">
<?php
	//COmentar para ejecutar
	//phpinfo();
	require '../libs/PHPMailer/PHPMailerAutoload.php';
	

	function mailAvisoMasCampus($docente, $direccion, $asunto, $mensaje) {
		
		
		try {
			$mail = new PHPMailer;
			$mail->CharSet = 'utf-8';
			//$mail-­>Encoding = "quoted­printable"; 
			$mail->Mailer = 'SMTP';
			$mail->SMTPDebug = 0;
			$mail->Host = 'smtp.gmail.com';
			$mail->Username = "sgaribotto@unsam.edu.ar";
			$mail->Password = "unsa2017";
			$mail->SMTPSecure = "ssl";
			$mail->Port = '465';
			$mail->SMTPAuth = true;
			$mail->AddReplyTo('biblioteca@unsam.edu.ar', 'Biblioteca EEYN');
			$mail->setFrom('planes.eeyn@unsam.edu.ar', 'Planes EEYN');
			$mail->isHTML(true);
			
			$mail->AddAttachment('R-BI-01 Planilla de Sugerencia de Compras.pdf');
			
			$mail->addAddress($direccion, $docente);
			$mail->Subject = $asunto;
			$mail->Body = $mensaje;
			
			//print_r($mail);
			//print_r($mail->send());
			if (!$mail->send()) {
				echo 'Mail not sent';
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
	
	
	
	require 'conexion.php';
	
	/*$host = "localhost";
	$usuario = "programas";
	$clave = "TMtrj9rS5di";
	$db = "programas";
	
	$mysqli = new MySQLi($host, $usuario, $clave, $db);*/
	
	
	//TODOS LOS DOCENTES
	/*$query = "SELECT dd.docente,
					dd.valor AS mail,
					CONCAT(d.apellido, ', ', d.nombres) AS nombre_docente,
					d.id AS id_docente,
					GROUP_CONCAT(DISTINCT m.nombres SEPARATOR ' | ') AS materias,
					IF(d.genero = 'F', 'a', 'o') AS genero,
					IF(d.genero = 'F', 'a', '') AS profesor
					
				FROM datos_docentes AS dd
				LEFT JOIN docente AS d
					ON dd.docente = d.id
				LEFT JOIN asignacion_comisiones AS ac
					ON ac.docente = d.id
						AND ac.anio = 2017
				LEFT JOIN vista_materias_por_conjunto AS m
					ON m.conjunto = ac.materia
				WHERE dd.activo = 1
					AND dd.tipo = 'mail'
				GROUP BY d.id

				HAVING NOT ISNULL(materias)
			LIMIT 1000;";*/
			
	//RESPONSABLES
	/*$query = "SELECT DISTINCT d.id AS docente, 
				CONCAT(p.apellido, ', ', p.nombres) AS nombre_docente,
				GROUP_CONCAT(DISTINCT m.nombre ORDER BY m.cod SEPARATOR ' | ') AS materias,
				dd.valor AS mail,
				IF(d.genero = 'F', 'a', 'o') AS genero,
				IF(d.genero = 'F', 'a', '') AS profesor
				
		   
				FROM responsable AS r
				LEFT JOIN personal AS p ON p.id = r.usuario
				LEFT JOIN materia AS m ON m.cod = r.materia
				LEFT JOIN carrera AS c ON m.carrera = c.id
				LEFT JOIN docente AS d ON d.dni = p.dni
				LEFT JOIN datos_docentes AS dd ON dd.docente = d.id AND dd.tipo = 'mail'
				LEFT JOIN comisiones_abiertas AS ca
					ON ca.materia = m.conjunto AND ca.anio = 2018 AND ca.cuatrimestre
				WHERE r.activo = 1 AND m.carrera IN (1, 2, 4)
					AND NOT ISNULL(ca.materia)
		GROUP BY m.conjunto
		ORDER BY c.cod, nombre_docente	";
	$result = $mysqli->query($query);
	echo $mysqli->error;
	$docentes = array();
	while ($row = $result->fetch_array(MYSQL_ASSOC)) {
		$docentes[$row['nombre_docente']]['mail'] = $row['mail'];
		$docentes[$row['nombre_docente']]['id'] = $row['docente'];
		$docentes[$row['nombre_docente']]['materias'][] = $row['materias'];
		$docentes[$row['nombre_docente']]['genero'] = $row['genero'];
		$docentes[$row['nombre_docente']]['profesor'] = $row['profesor'];
	}*/
	
	//print_r($docentes);
	
	$template = "<p>Estimad%s profesor%s <b>%s</b></p>
<br>
<br>
<p>Por la presente, nos dirigimos a Usted para informarle que como parte de la constante actualización del fondo bibliográfico de nuestra Biblioteca, estamos organizando la compra de bibliografía para el ciclo lectivo 2018.
En este sentido, como responsable de las asignaturas:</p> 
* %s
</p>Le solicitamos indique que material considera de interés para incorporar en su Cátedra a fin de poder gestionar la compra, pudiendo solicitar hasta 3 (tres) títulos diferentes. Los datos deben ser consignados en la planilla que se adjunta, pudiendo entregarla personalmente en nuestra Biblioteca, o bien, remitirla vía correo electrónico.
Las sugerencias serán recepcionadas hasta el 20 de abril.</p>
<p>Sin más, me despido de Usted quedando a la espera de vuestras sugerencias. Saludos cordiales,</p>
<br>
<br>
                                                                                                           
<p><b>Lic. Laura M. Favale</b></p>
<p><b>Coordinadora</b></p>
<p><b>Biblioteca Profesor Elías Salama</b></p>
<p><b>EEyN - UNSAM</b></p>";
	
	$asunto = "Solicitud de bibliografía 2018";	
	
	
	//TEST MAIL A planes.eeyn@unsam.edu.ar
	$materias[] = 'Asignatura 1';
	$materias[] = 'Asignatura 2';
	
	$materiasImp = implode('<br /> * ', $materias);
	
	//$mensaje = sprintf($template, 'o', '', 'Santiago Garibotto', $materiasImp);
	$mensaje = sprintf($template, 'a', 'a', 'Laura Favale', $materiasImp);
	echo $mensaje;
	mailAvisoMasCampus('Santiago Garibotto', 'sgaribotto@unsam.edu.ar', 'TEST' . $asunto, $mensaje);
	//mailAvisoMasCampus('Laura Favale', 'lfavale@unsam.edu.ar', 'TEST ' . $asunto, $mensaje);
	
	
	//ARMADO DE LOS MAILS A ENVIAR
	/*foreach ($docentes as $nombre => $datos) {
		
		//$materia = $row['nombre_materia'];
		$materias = implode('<br /> * ', $datos['materias']);
		$message = sprintf($template, $datos['genero'], $datos['profesor'], $nombre, $materias);
		
		
		
		$docente = $datos['id'];
		$tipo = 'responsable';
		$nombre_docente = $mysqli->real_escape_string($docente);
		$mail = $mysqli->real_escape_string($datos['mail']);
		$asunto = $mysqli->real_escape_string($asunto);
		$message = $mysqli->real_escape_string($message);
		
		$insertQuery = "INSERT INTO envios_por_mail 
			(id_destinatario, tipo_destinatario, destinatario, mail, asunto, mensaje) VALUES
			($docente, '$tipo', '$nombre_docente', '$mail', '$asunto', '$message');";
		$mysqli->query($insertQuery);
		echo $mysqli->error;
		echo "$docente --> $datos[mail] <br />";
		echo $message;
		
		echo "<hr />";
		
		
		
		
	}*/
	

	
/*	$query = "SELECT id, id_destinatario, tipo_destinatario, destinatario, mail, asunto, mensaje 
			FROM envios_por_mail
			WHERE NOT ISNULL(mail) AND mail != '' AND enviado = 0
			LIMIT 25;";
	
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
	}*/
	
	$result->free();
	$mysqli->close();
	
	
	
	
?>
