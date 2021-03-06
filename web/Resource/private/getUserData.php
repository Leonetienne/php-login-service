<?php
require_once 'database.php';

	// Will return an array of all user data
	// Returns false, if this user does not exist
	function GetUserData($userEmail) {
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
				'errno' => '100434',
				'message' => 'user not found'
			)));
			return false;
		}

		// Return data as array
		return array(
			'uid'		=> $results[0][0],
			'email'		=> $results[0][1],
			'firstName' => $results[0][2],
			'lastName'	=> $results[0][3],
			'username'	=> $results[0][4],
			'permLevel'	=> $results[0][6], // No 5, because we exclude the password_hash
			'isBanned'	=> $results[0][7],
			'isVerified'=> $results[0][8]
		);
	}
?>
