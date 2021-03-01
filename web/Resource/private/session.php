<?php
require_once 'database.php';

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
		"INSERT INTO ses_ids (sesId, accountId, time_created, time_expires) VALUES (?, ?, ?, ?)",
		"siii",
		$sesId,
		$uid,
		time(),
		time() + 60*60*24 // Expires after one day
	);

	$conn->close();
	return $sesId;
}

?>
