<?php
require_once 'database.php';

	// Will terminate all sessions associated with a user
	function KickUser($userEmail) {
		// Establish database connection
		$conn = ConnectToDatabase();

		// Check if account exists
		$results = SecureQuery(
			$conn,
			"SELECT uid FROM fe_users WHERE email = ?;",
			"s",
			$userEmail
		)->fetch_all();

		if (count($results) < 1) {
			http_response_code(401);
			echo(json_encode(array(
				'status' => 'failed',
				'errno' => '100422',
				'message' => 'user not found'
			)));
			return false;
		}

		// All good. Now terminate sessions
		$results = SecureQuery(
			$conn,
			"DELETE FROM ses_ids WHERE accountId = ?;",
			"i",
			$results[0][0]
		);

		return true;
	}
?>