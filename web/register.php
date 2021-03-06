<?php
require_once 'Resource/private/database.php';
require_once 'Resource/private/hashPasswordKdf.php';
require_once 'Resource/private/session.php';
require_once 'Resource/private/emailVerification.php';

// Returns whether or not a given email address is available for registration
function IsEmailAvailable($conn, $email) {
	// Query database
	$results = SecureQuery(
		$conn,
		"SELECT password_hash FROM fe_users WHERE email = ?;",
		"s",
		$email
	)->fetch_all();

	// Was any account found?
	return (count($results) < 1);
}

// Returns whether or not a given user name is available for registration
function IsUsernameAvailable($conn, $userName) {
	// Query database
	$results = SecureQuery(
		$conn,
		"SELECT password_hash FROM fe_users WHERE username = ?;",
		"s",
		$userName
	)->fetch_all();

	// Was any account found?
	return (count($results) < 1);
}



	// Set header data
	header('Access-Control-Allow-Origin: *');
	header("Content-type: application/json");

	// Do we actually have these values?
	if ((!isset($_POST["password"])) ||
		(!isset($_POST["email"])) ||
		(!isset($_POST["username"])))
	{
		// If not, return error code 400 and an approriate error message
		http_response_code(400);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100404',
			'message' => 'incomplete registration form'
		)));
	}

	// Retrieve registration form data
	$userPassword = $_POST["password"];
	$userEmail = $_POST["email"];
	$userName = $_POST["username"];
	$userFirstname = empty($_POST["firstname"]) ? NULL : $_POST["firstname"];
	$userLastname =  empty($_POST["lastname"])  ? NULL : $_POST["lastname"];

	// Establish database connection
	$conn = ConnectToDatabase();

	// Check if email is valid
	if ((strlen($userEmail) > 30) ||
		(strlen($userEmail) < 3) ||
		(!filter_var($userEmail, FILTER_VALIDATE_EMAIL))) {
		$conn->close();
		http_response_code(500);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100405',
			'message' => 'email invalid'
		)));
	}
	
	// Check if username is valid
	if ((strlen($userName) > 30) || (strlen($userName) < 3)) {
		$conn->close();
		http_response_code(500);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100406',
			'message' => 'username invalid'
		)));
	}

	// Check if username/email are already taken
	if ((!IsUsernameAvailable($conn, $userName)) ||
		(!IsEmailAvailable($conn, $userEmail))) {
		$conn->close();
		http_response_code(500);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100407',
			'message' => 'username or email already taken'
		)));
	}

	// Create fe_users entry
	$results = SecureQuery(
		$conn,
		"INSERT INTO fe_users (email, username, firstName, lastName, password_hash) VALUES (?, ?, ?, ?, ?);",
		"sssss",
		$userEmail,
		$userName,
		$userFirstname ?: NULL,
		$userLastname ?: NULL,
		HashPasswordKDF($userPassword, $userEmail)
	);

	$sessionId = CreateSession($userEmail);
	if ($sessionId === false) {
		http_response_code(500);
			echo(json_encode(array(
				'status' => 'failed',
				'errno' => '100508',
				'message' => 'internal server error'
			)));
	}

	if(!SendRegistrationEmail($userEmail)) {
		http_response_code(500);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '0',
			'message' => 'failed'
		)));
	}
	
	$conn->close();
	http_response_code(200);
	die(json_encode(array(
			'status' => 'success',
			'message' => 'Account created successfully. Please confirm your email address.',
			'sessionId' => $sessionId
	)));

?>
