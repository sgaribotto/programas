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
			$mail->AddReplyTo('verrecart@unsam.edu.ar', 'Valeria Errecart');
			$mail->AddReplyTo('secretariaacademica.eeyn@unsam.edu.ar', 'Secretaría Académica EEYN');
			$mail->setFrom('planes.eeyn@unsam.edu.ar', 'Planes EEYN');
			$mail->isHTML(true);
			
			//$mail->AddAttachment('Taller Práctico_Inicial_EEyN2017.pdf');
			
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
	
	
	//DOCENTES MAIL CONEAU APORTADO POR VALE
	$query = "SELECT docente as nombre, mail
					
				FROM mails_coneau
				;";
			
	$result = $mysqli->query($query);
	
	//print_r($docentes);
	
$template = "<p>Estimado Profesor/a %s, avanzada esta primer etapa del proceso de acreditación de 
				la carrera de Contador Público y verificando que usted se encuentra en la base del sistema CVar, 
				es necesario que continuemos hacia la segunda etapa.  Para dar inicio a la misma, 
				es necesario generar una ficha docente “Currículum docente” en el sistema CONEAU Global. 
				Puede suceder que ya posea una, por favor háganos saber por esta vía de la existencia de 
				la misma; como así también en ese caso asegúrese de tener activos su usuario y contraseña correspondiente.</p>

 

<p>Si no posee la ficha CONEAU docente, los pasos para gestionar la misma son:</p>

 
<br>
<p>a)      Ingresar al link: <a href='http://209.13.179.3/coneauglobal/usuarios/crear/'>http://209.13.179.3/coneauglobal/usuarios/crear/</a></p>

<p>b)      Marcar la opción “Docente Universitario” y presionar el botón continuar</p>

<p>c)       Completar los datos que la ficha le solicita. <b>IMPORTANTE:</b> no olvidar de marcar la opción, 
	<b>“Tengo una ficha creada en CVar y deseo utilizar esa ficha”</b>, ubicada en el margen inferior izquierdo.</p>

 
<br>
<p>Finalizados estos pasos recibirá en su cuenta de mail declarado una notificación de CONEAU Global, 
que le indica el usuario y contraseña para activar su cuenta. Por favor realice la activación de la misma, 
para así darse de alta y finalizar el trámite.</p>
<br />
 

<p>Tenga presente que la fecha límite es el 9 de abril de 2018.</p>

<br>

<p>Esperamos recibir su confirmación de creación con éxito de su ficha CONEAU docente.</p>

 <br>

<p>Ante cualquier duda no deje de comunicarse con Valeria Errecart al 4580-7250 int 142 
o bien vía mail a la dirección <a href='mailto:valeria.errecart@unsam.edu.ar'>valeria.errecart@unsam.edu.ar</a> como así también con Patricia 
Rieger al 4580-7250 int 111  por mail a <a href='mailto:patricia.rieger@unsam.edu.ar'>patricia.rieger@unsam.edu.ar</a></p>

<br>

 

<p>Desde ya muchas gracias</p>

 

<p>Cordiales saludos</p>

 ";
	
	$asunto = "Solicitud ficha docente CONEAU Acreditación carrera de Contador Público";	
	
	
	//TEST MAIL A planes.eeyn@unsam.edu.ar
	/*$mensaje = sprintf($template, 'Santiago Garibotto');
	echo $mensaje;
	mailAvisoMasCampus('Santiago Garibotto', 'santiagogaribotto@gmail.com', 'TEST' . $asunto, $mensaje);*/
	
	/*$mensaje = sprintf($template, 'Santiago Garibotto');
	echo $mensaje;
	mailAvisoMasCampus('Valeria Errecart', 'valeria.errecart@unsam.edu.ar', 'TEST ' . $asunto, $mensaje);*/
	//mailAvisoMasCampus('Patricia Rieger', 'prieger@unsam.edu.ar', 'TEST ' . $asunto, $mensaje);
	
	
	//ARMADO DE LOS MAILS A ENVIAR
	/*while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
		
		//$materia = $row['nombre_materia'];
		$message = sprintf($template, $row['nombre']);
		
		
		
		$docente = NULL;
		$tipo = 'docente';
		$nombre_docente = $mysqli->real_escape_string($row['nombre']);
		$mail = $mysqli->real_escape_string($row['mail']);
		$asunto = $mysqli->real_escape_string($asunto);
		$message = $mysqli->real_escape_string($message);
		
		$insertQuery = "INSERT INTO envios_por_mail 
			(id_destinatario, tipo_destinatario, destinatario, mail, asunto, mensaje) VALUES
			('NULL', '$tipo', '$nombre_docente', '$mail', '$asunto', '$message');";
		$mysqli->query($insertQuery);
		echo $mysqli->error;
		echo "$row[nombre] --> $row[mail]";
		//echo $message;
		echo "<hr />";
		
		
		
	
	}*/
	

	
	$query = "SELECT id, id_destinatario, tipo_destinatario, destinatario, mail, asunto, mensaje 
			FROM envios_por_mail
			WHERE NOT ISNULL(mail) AND mail != '' AND enviado = 0
			LIMIT 26;";
	
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
