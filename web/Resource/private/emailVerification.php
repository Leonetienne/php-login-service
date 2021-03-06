<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
require_once 'database.php';
use PHPMailer\PHPMailer\PHPMailer;

	// Will set up a verification action end send out an email containing a link to it
	function SendRegistrationEmail($userEmail) {
		
		// Establish database connection
		$conn = ConnectToDatabase();

		// Fetch data
		$results = SecureQuery(
			$conn,
			"SELECT * FROM fe_users WHERE email = ?;",
			"s",
			$userEmail
		)->fetch_all();

		// User not found
		if (count($results) < 1) {
			return false;
		}

		$uid = $results[0][0];
		$actionKey = hash('tiger128,3', $uid . $userEmail . random_int(0, PHP_INT_MAX));

		// Create action key
		$results = SecureQuery(
			$conn,
			"INSERT INTO action_keys
				(actionKey, accountId, time_created, time_expires, action)
				VALUES
				(?, ?, ?, ?, 'verify_account');",
			"siii",
			$actionKey,
			$uid,
			time(),
			time() + 60*60*24 // Expires after one day
		);

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
		$mail->addAddress($userEmail); 

		$mail->Subject = 'Thanks for creating your account!';
		$mail->isHTML(true);

		$confirmationLink = 'https://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/action.php?key=' . urlencode($actionKey);

		ob_start();
		include 'template/mail/registration.php';
		$mail->Body = ob_get_clean();

		if (!$mail->send()) {
			echo(json_encode(array(
				'message' => $mail->ErrorInfo
			)));
			return false;
		}
		return true;
	}
 
	// Will set the 'isVerified' flag to true
	function VerifyAccount($accountUid) {
		// Establish database connection
		$conn = ConnectToDatabase();

		// Check if user exists
		// Fetch data
		$results = SecureQuery(
			$conn,
			"SELECT * FROM fe_users WHERE uid = ?;",
			"s",
			$accountUid
		)->fetch_all();

		// User not found
		if (count($results) < 1) {
			return false;
		}

		SecureQuery(
			$conn,
			"UPDATE fe_users SET isVerified = 1 WHERE uid = ?;",
			"i",
			$accountUid
		);

		return true;
	}

?>
