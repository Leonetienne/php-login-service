<?php
require_once 'database.php';

	// Will grant a user a certain permission level
	// Returns false, if user does not exists
	// $permLevel is expected to be an unsigned integer
	function GrantUserPermissions($userEmail, $permLevel) {
		// Establish database connection
		$conn = ConnectToDatabase();

		// Fetch data
		$results = SecureQuery(
			$conn,
			"SELECT * FROM fe_users WHERE email = ?;",
			"s",
			$userEmail
		)->fetch_all();

		if (count($results) < 1) {
			http_response_code(400);
			echo(json_encode(array(
				'status' => 'failed',
				'errno' => '100436',
				'message' => 'user not found'
			)));
			return false;
		}

		// Clamp perm level to valid range
		$permLevel = max(min($permLevel, 100), 0);

		// Set permission level
		$results = SecureQuery(
			$conn,
			"UPDATE fe_users SET permLevel = ? WHERE email = ?;",
			"is",
			$permLevel,
			$userEmail
		);

		return true;
	}
?>
