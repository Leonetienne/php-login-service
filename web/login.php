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
			'errno' => '400',
			'message' => 'missing email or password'
		)));
	}

	// Retrieve login form data
	$userPassword = $_POST["password"];
	$userEmail = $_POST["email"];

	// Establish database connection
	$conn = ConnectToDatabase();

	// Query user list
	// Prepare statement
	if (!($stmt = $conn->prepare("SELECT password_hash FROM fe_users WHERE email = ?"))) {
		$conn->close();
		http_response_code(500);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '500',
			'message' => 'internal server error'
		)));
	}

	// Bind statement
	if (!$stmt->bind_param("s", $userEmail)) {
		$conn->close();
		http_response_code(500);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '501',
			'message' => 'internal server error'
		)));
	}

	// Execute
	if (!$stmt->execute()) {
		$conn->close();
		http_response_code(500);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '502',
			'message' => 'internal server error'
		)));
	}

	// Get results
	$results = $stmt->get_result()->fetch_all();

	// Was any account found?
	if (count($results) < 1) {
		$conn->close();
		http_response_code(400);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '401',
			'message' => 'Account not found'
		)));
	}

	// Have we indeed just matched one account?
	if (count($results) > 1) {
		$conn->close();
		http_response_code(500);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '503',
			'message' => 'internal server error'
		)));
	}

	// Check if the passwords matched
	if (HashPasswordKDF($userPassword, $userEmail) != $results[0][0]) { // Select the first (and only) result and of that the first (and only) column, which is the password_hash
		$conn->close();
		http_response_code(400);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '402',
			'message' => 'Password invalid'
		)));
	}
	$conn->close();

	// All is Okay! Password matches!
	// Banned and unverified accounts can still log in, but they will be restricted.

	// TODO: Create session-id and return it
	echo("Nice! You're logged in!");
?>
