<?php
require_once 'database.php';

// Will create a unique session for an account
// Returns either false or a sessionId as a string
function CreateSession($email) {
	// Connect to database
	$conn = ConnectToDatabase();

	// Gather user information
	$results = SecureQuery(
		$conn,
		"SELECT uid FROM fe_users WHERE email = ?",
		"s",
		$email
	)->fetch_all();

	// Was any account found?
	if (count($results) < 1) {
		$conn->close();
		return false;
	}

	$uid = $results[0][0];

	// Generate session-id string
	$sesId = hash('tiger128,3', $uid . $email . random_int(0, PHP_INT_MAX));

	// Create fe_users entry
	$results = SecureQuery(
		$conn,
		"INSERT INTO ses_ids (sesId, accountId, time_created, time_expires) VALUES (?, ?, ?, ?);",
		"siii",
		$sesId,
		$uid,
		time(),
		time() + 60*60*24 // Expires after one day
	);

	$conn->close();
	return $sesId;
}

// Will bump a session
// If the session is still active, it will renew it
// If the session is expired, it will remove it
// Returns true, if the session is still good. Either false.
function BumpSession($sessionId) {
	// Connect to database
	$conn = ConnectToDatabase();

	// Fetch expiry date
	$results = SecureQuery(
		$conn,
		"SELECT time_expires FROM ses_ids WHERE sesId = ?;",
		"s",
		$sessionId
	)->fetch_all();

	// Was any session found?
	if (count($results) < 1) {
		$conn->close();
		return false;
	}

	// If session is expired
	if ($results[0][0] < time()) {
		SecureQuery(
			$conn,
			"DELETE FROM ses_ids WHERE sesId = ?;",
			"s",
			$sessionId
		);
		return false;
	}

	// Renew expiry date
	SecureQuery(
		$conn,
		"UPDATE ses_ids SET time_expires = ? WHERE sesId = ?;",
		"is",
		time() + 60*60*24, // Expires after one day
		$sessionId
	);

	return true;
}
?>
