<?php
require_once 'database.php';
require_once 'sendTemplatedEmail.php';

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

		// Create action key
		$actionKey = hash('tiger128,3', $uid . $userEmail . random_int(0, PHP_INT_MAX));
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

		$confirmationLink = 'https://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/action.php?key=' . urlencode($actionKey);
		if (!SendTemplatedEmail($userEmail, 'Thanks for registering!', 'template/mail/registration.php', array(
			'userEmail' => $userEmail,
			'confirmationLink' => $confirmationLink
		))) {
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

		// Set flag to true
		SecureQuery(
			$conn,
			"UPDATE fe_users SET isVerified = 1 WHERE uid = ?;",
			"i",
			$accountUid
		);

		return true;
	}

?>
