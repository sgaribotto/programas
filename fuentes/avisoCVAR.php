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
			$mail->AddReplyTo('valeria.errecart@unsam.edu.ar', 'Lic. Valeria Errecart');
			$mail->setFrom('planes.eeyn@unsam.edu.ar', 'Planes EEYN');
			$mail->isHTML(true);
			
			$mail->AddAttachment('adjuntosCVAR/Manual usuario CVAR.pdf');
			$mail->AddAttachment('adjuntosCVAR/Intructivo Importación cvar.pdf');
			$mail->AddAttachment('adjuntosCVAR/instructivo navegación cvar.pdf');
			
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
				IFNULL(dd.valor, '') AS mail,
				CONCAT(d.apellido, ', ', d.nombres) AS nombre_docente,
				d.id AS id_docente,
				IF(d.genero = 'F', 'a', 'o') AS genero,
				IF(d.genero = 'F', 'a', '') AS profesor

				FROM docente AS d
				LEFT JOIN datos_docentes AS dd
				ON dd.docente = d.id AND dd.tipo = 'mail'
				WHERE d.exceptuado_cvar = 0 AND cvar = 0
				#HAVING ISNULL(mail) OR mail = '' OR nombre_docente LIKE '%PE'

				
				LIMIT 1000";
	$result = $mysqli->query($query);
	
	$template = "Estimad%s profesor%s <b>%s:</b>\n
		<br />
		Nos ponemos en contacto con usted para comunicarle que la EEyN 
		se encuentra en proceso de acreditación de la Carrera de Contador Público el cual requiere 
		que todas las partes intervinientes se compromentan para que la misma sea efectiva y así 
		garantizar el cumplimiento de los tiempos pautados por CONEAU. En esta primer etapa que 
		vence el 30 de noviembre, cada uno de los docentes que forman parte de la comunidad de la 
		EEyN debe cargar sus antecedentes en la plataforma del CVAR, cuyo instructivo de navegación 
		se adjunta. Los pasos para la misma son:
		<br />
			a)Si no posee usuario del sistema, ingresando al link (<a href='http://cvar.sicytar.mincyt.gob.ar/auth/newreg.jsp')>
			http://cvar.sicytar.mincyt.gob.ar/auth/newreg.jsp</a>)
			puede gestionar la solicitud del mismo.
			<br />
			b)Cargar los datos en cada campo solicitado por el sistema; a modo de guía se adjunta el manual del usuario del CVAR.
			<br />
			c)Si sus datos ya están cargados en otro sistema como el SIGEVA UNSAM,  SIGEVA CONICET u otros, puede importar 
			los mismos al CVAR. Para guía ver en adjunto instructivo de importación CVAR.
			<br />
		Una vez finalizada la carga, enviar por mail a la dirección <a href='mailto:patricia.rieger@unsam.edu.ar'>patricia.rieger@unsam.edu.ar</a> , 
		el curriculum con los datos cargados en formato PDF; dicho archivo resulta como documento del mismo sistema.
		<br />
		Ante cualquier duda no deje de comunicarse con Valeria Errecart al 4580-7250 int 142 o 
		bien via mail a la dirección <a href='mailto:valeria.errecart@unsam.edu.ar'>valeria.errecart@unsam.edu.ar</a> .
 
		 <br />
		 <br />
		Saluda a Ud. Cordialmente,  
		<br /> 
		<br /> 
		Secretaría Académica
		<br />
		Escuela de Economía y Negocios
		<br />
		UNSAM";
	
	$asunto = "Carrera de Contador - Acreditación CONEAU";	
	
	
	//ARMADO DE LOS MAILS A ENVIAR
	/*while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
		
		//$materia = $row['nombre_materia'];
		$message = sprintf($template, $row['genero'], $row['profesor'], $row['nombre_docente']);
		
		
		
		$docente = $row['id_docente'];
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
	
	//$mensaje = sprintf($template, 'a', 'a', 'Patricia Rieger');
	//mailAvisoMasCampus('Santiago Garibotto', 'planes.eeyn@unsam.edu.ar', 'TEST' . $asunto, $mensaje);
	//mailAvisoMasCampus('Patricia Rieger', 'prieger@unsam.edu.ar', 'TEST ' . $asunto, $mensaje);

	
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
