<?php
// This file is for resetting a user-password when the user cannot log in
require_once 'Resource/private/resetPassword.php';
	
	// Set header data
	header('Access-Control-Allow-Origin: *');
	header("Content-type: application/json");

	// Do we actually have these values?
	if (!isset($_POST["email"]))
	{
		// If not, return error code 400 and an approriate error message
		http_response_code(400);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100441',
			'message' => 'missing form data. Please supply (email)'
		)));
	}

	// Grab data
	$userEmail = $_POST['email'];

	// Establish database connection
	$conn = ConnectToDatabase();

	// Fetch user id
	$results = SecureQuery(
		$conn,
		"SELECT uid FROM fe_users WHERE email = ?;",
		"s",
		$userEmail
	)->fetch_all();

	// User not found
	if (count($results) < 1) {
		http_response_code(400);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100442',
			'message' => 'user not found'
		)));
	}

	// Grab uid
	$uid = $results[0][0];

	// Send out email
	if(!SendPasswordResetEmail($uid)) {
		http_response_code(500);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100511',
			'message' => 'internal server error'
		)));
	}

	http_response_code(200);
	die(json_encode(array(
		'status' => 'success',
		'message' => 'all good :3'
	)));
?>