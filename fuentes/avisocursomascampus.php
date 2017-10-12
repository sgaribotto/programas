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
			$mail->Host = 'smtp.unsam.edu.ar';
			
			$mail->Username = "planes.eeyn@unsam.edu.ar";
			$mail->Password = "Pla2017nes";
			$mail->SMTPSecure = "ssl";
			$mail->Port = '465';
			$mail->SMTPAuth = true;
			$mail->AddReplyTo('secretariaacademica@unsam.edu.ar', 'Secretaría Académica EEYN');
			$mail->setFrom('planes.eeyn@unsam.edu.ar', 'Planes EEYN');
			$mail->isHTML(true);
			
			$mail->AddAttachment('Taller Práctico_Inicial_EEyN2017.pdf');
			
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
	
	
	
	//require 'conexion.php';
	
	$host = "10.1.71.121";
	$usuario = "programas";
	$clave = "TMtrj9rS5di";
	$db = "programas";
	
	$mysqli = new MySQLi($host, $usuario, $clave, $db);
	
	$query = "SELECT dd.docente,
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
			LIMIT 1000;";
	$result = $mysqli->query($query);
	
	$template = "Estimad%s profesor%s <b>%s:</b>\n
		<br />
		Por la presente se invita a participar del taller práctico presencial de Introducción 
		al uso de Aulas Virtuales, el próximo 24 de octubre de 2017 a las 15hs y hasta las 18hs. Se 
		requiere inscripción previa mediante envío de mail a secretariaacademica@unsam.edu.ar
		 <br />
		 <br />
		Saluda a Ud. Cordialmente,  
		<br /> 
		<br /> 
		Marcelo Estayno
		<br />
		Secretaria Académico EEyN.";
	
	$asunto = "Más Campus: Invitación a Taller Práctico - Inicial";	
	
	
	//ARMADO DE LOS MAILS A ENVIAR
	/*while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
		
		//$materia = $row['nombre_materia'];
		$message = sprintf($template, $row['genero'], $row['profesor'], $row['nombre_docente']);
		
		
		
		$docente = $row['docente'];
		$tipo = 'docente';
		$nombre_docente = $mysqli->real_escape_string($row['nombre_docente']);
		$mail = $mysqli->real_escape_string($row['mail']);
		$asunto = $mysqli->real_escape_string($asunto);
		$message = $mysqli->real_escape_string($message);
		
		$insertQuery = "INSERT INTO envios_por_mail 
			(id_destinatario, tipo_destinatario, destinatario, mail, asunto, mensaje) VALUES
			($docente, '$tipo', '$nombre_docente', '$mail', '$asunto', '$message');";
		$mysqli->query($insertQuery);
		echo $mysqli->error;
		echo "$row[docente] --> $row[mail]";
		
		echo "<hr />";
		
		
		
		
	}*/
	

	
	$query = "SELECT id, id_destinatario, tipo_destinatario, destinatario, mail, asunto, mensaje 
			FROM envios_por_mail
			WHERE NOT ISNULL(mail) AND mail != '' AND enviado < 1
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
	}
	
	$result->free();
	$mysqli->close();
	
	
	
	
?>
