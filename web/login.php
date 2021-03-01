<?php
require_once 'Resource/private/database.php';
require_once 'Resource/private/hashPasswordKdf.php';

	// Set header data
	header('Access-Control-Allow-Origin: *');
	header("Content-type: application/json");	

	// Do we actually have these values?
	if ((!isset($_POST["password"])) ||
		(!isset($_POST["email"])))
	{
		// If not, return error code 400 and an approriate error message
		http_response_code(400);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100400',
			'message' => 'missing email or password'
		)));
	}

	// Retrieve login form data
	$userPassword = $_POST["password"];
	$userEmail = $_POST["email"];

	// Establish database connection
	$conn = ConnectToDatabase();

	// Query database
	$results = SecureQuery(
		$conn,
		"SELECT password_hash FROM fe_users WHERE email = ?",
		"s",
		$userEmail
	)->fetch_all();

	// Was any account found?
	if (count($results) < 1) {
		$conn->close();
		http_response_code(400);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100401',
			'message' => 'Account not found'
		)));
	}

	// Have we indeed just matched one account?
	if (count($results) > 1) {
		$conn->close();
		http_response_code(500);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100504',
			'message' => 'internal server error'
		)));
	}

	// Check if the passwords matched
	if (HashPasswordKDF($userPassword, $userEmail) != $results[0][0]) { // Select the first (and only) result and of that the first (and only) column, which is the password_hash
		$conn->close();
		http_response_code(400);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100402',
			'message' => 'Password invalid'
		)));
	}
	$conn->close();

	// All is Okay! Password matches!
	// Banned and unverified accounts can still log in, but they will be restricted.

	// TODO: Create session-id and return it
	http_response_code(200);
	die(json_encode(array(
			'status' => 'success',
			'message' => 'You\'re in!'
	)));
?>
