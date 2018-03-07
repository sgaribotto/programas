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
			
			$mail->Username = "secretariaacademica.eeyn@unsam.edu.ar";
			$mail->Password = "Sa998080";
			$mail->SMTPSecure = "ssl";
			$mail->Port = '465';
			$mail->SMTPAuth = true;
			$mail->AddReplyTo('webmaster.eeyn@unsam.edu.ar', 'Planes EEYN');
			$mail->setFrom('secretariaacademica.eeyn@unsam.edu.ar', 'Secretaría Académica EEYN');
			$mail->isHTML(true);
			
			//$mail->AddAttachment('/var/www/CV - Santiago Lazzati - General 2016.doc');
			
			$mail->addAddress($direccion, $docente);
			$mail->Subject = $asunto;
			$mail->Body = $mensaje;
			
			//print_r($mail);
			print_r($mail->send());
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
	
	$host = "planeseeyn.unsam.edu.ar";
	$usuario = "programas";
	$clave = "TMtrj9rS5di";
	$db = "programas";
	
	$mysqli = new MySQLi($host, $usuario, $clave, $db);
	
	
	//CONSULTA ALUMNOS QUE HAY QUE AVISARLES
	$query = "SELECT DISTINCT i.nombre_alumno, 
				ma.mail
			FROM mail_alumnos AS ma
			LEFT JOIN inscriptos AS i
				ON i.nro_documento + 0 = ma.dni + 0
					AND i.anio_academico = 2018
			WHERE ma.dni IN (42111334 ,42197466 ,44143782 ,41970593 ,42238109 ,41676532 ,42567209 ,30799035 ,42690833 ,40932106 ,41327599 ,40185255 ,39063730 ,42193223 ,29564374 ,42230139 ,35431183 ,40713021 ,94828789 ,42196454 ,95574784 ,38100025 ,42339435 ,42115612 ,42225521 ,42144275 ,42024553 ,42418708 ,42371790 ,42229412 ,42824532 ,42463852 ,42116233 ,42494003 ,42470025 ,42527770 ,42832564 ,40895771 ,42566422 ,41471728 ,37275896 ,40975917 ,41459318 ,42393759 ,41765829 ,42229544 ,42495534 ,42690888 ,40230673 ,42279555 ,38631004 ,41028772 ,41665180 ,41129929 ,39270521 ,42674306 ,42054784 ,42094575 ,41969918 ,42311581 ,42674295 ,41952936 ,41970446 ,41916146 ,41881461)
			ORDER BY nombre_alumno;";
	
	echo $query;
	//TEMPLATE Y ASUNTO
	$template = "<pre><p 'style=text-align:justify;>Estimado <b>%s</b></p>


	Su inscripción al CPU para el próximo cuatrimestre fue aceptada para las siguientes comisiones y horarios:


		1002MB	Lunes 8.30 a 10.30		Introducción a las Carreras de Grado
		1001MB	Lunes y Jueves 10.30 a 12.30	Elementos de Matemática
		1003MB	Martes y Jueves 8.30 a 10.30	Introducción a los Estudios Universitarios
		1000MB	Martes y Viernes 10.30 a 12.30	Elementos de Contabilidad

	 
	 
Saludos Cordiales
Dirección de Asuntos Académicos
Secretaría Académica
EEyN-UNSAM
Caseros 2241 - San Martín - Buenos Aires
Tel: 4580-7250 int. 124/125</pre>";
	
	$asunto = "Comisiones cursada CPU";
	
	// ARMADO DE LA BASE DE DATOS PARA EL ENVÏO DE MAILS
	
	/*$result = $mysqli->query($query);
	echo $mysqli->error;
	while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
		
		//$materia = $row['nombre_materia'];
		$message = sprintf($template, $row['nombre_alumno']);
		
		
		
		$docente = "alumno";
		$tipo = 'alumno';
		$nombre_docente = $mysqli->real_escape_string($row['nombre_alumno']);
		$mail = $mysqli->real_escape_string($row['mail']);
		$asunto = $mysqli->real_escape_string($asunto);
		$message = $mysqli->real_escape_string($message);
		
		$insertQuery = "INSERT INTO envios_por_mail 
			(id_destinatario, tipo_destinatario, destinatario, mail, asunto, mensaje) VALUES
			('$docente', '$tipo', '$nombre_docente', '$mail', '$asunto', '$message');";
		$mysqli->query($insertQuery);
		echo $mysqli->error;
		echo "$row[nombre_alumno] --> $row[mail]";
		
		echo "<hr />";
		
		
		
		
	}*/
	
	//TEST MAIL A planes.eeyn@unsam.edu.ar
	/*$mensaje = sprintf($template, 'Santiago Garibotto');
	echo $mensaje;
	mailAvisoMasCampus('Santiago Garibotto', 'santiagogaribotto@gmail.com', $asunto, $mensaje);*/
	
	
	//ENVIO DE MAILS GUARDADOS EN LA BASE
	$query = "SELECT id, id_destinatario, tipo_destinatario, destinatario, mail, asunto, mensaje 
			FROM envios_por_mail
			WHERE NOT ISNULL(mail) AND mail != '' AND enviado = 0
			LIMIT 22;";
	
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
