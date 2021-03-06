<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

function SendTemplatedEmail($recipient, $subject, $templateFile, $templateVars) {
	$mail = new PHPMailer();
	$mail->isSMTP();
	$mail->Host = 'localhost';
	$mail->SMTPAuth = false;
	// Use authentication and encryption if using in production!!!
	//$mail->Username = '';
	//$mail->Password = '';
	//$mail->SMTPSecure = 'tls';
	//$mail->SMTPAutoTLS = false;
	$mail->Port = 1025;

	$mail->setFrom('noreply@example.de', 'loginserver');
	$mail->addAddress($recipient); 

	$mail->Subject = $subject;
	$mail->isHTML(true);

	ob_start();
	include $templateFile;
	$mail->Body = ob_get_clean();

	if (!$mail->send()) {
		echo(json_encode(array(
			'message' => $mail->ErrorInfo
		)));
		return false;
	}
	return true;
}

?>