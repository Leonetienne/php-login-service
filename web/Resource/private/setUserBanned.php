<?php

	// This function will ban or unban a user
	// $userEmail => the email that you want to ban
	// $banned    => set the user to be banned or unbanned?
	function SetUserBanned($userEmail, $banned)
	{
		// Establish database connection
		$conn = ConnectToDatabase();

		// Check if account exists
		if (count(SecureQuery(
			$conn,
			"SELECT password_hash FROM fe_users WHERE email = ?;",
			"s",
			$userEmail
		)->fetch_all()) < 1) {
			http_response_code(401);
			echo(json_encode(array(
				'status' => 'failed',
				'errno' => '100419',
				'message' => 'user not found'
			)));
			return false;
		}

		// All good. Now set user status
		$results = SecureQuery(
			$conn,
			"UPDATE fe_users SET isBanned = ? WHERE email = ?;",
			"is",
			$banned,
			$userEmail
		);

		return true;
	}
?>
