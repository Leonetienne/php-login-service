<?php
require_once 'Resource/private/session.php';
require_once 'Resource/private/getUserData.php';
require_once 'Resource/private/permissions.php';

	// Set header data
	header('Access-Control-Allow-Origin: *');
	header("Content-type: application/json");

	// Do we actually have these values?
	if ((!isset($_POST["sessionId"])) ||
		(!isset($_POST["userEmail"])))
	{
		// If not, return error code 400 and an approriate error message
		http_response_code(400);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100430',
			'message' => 'missing form data. Please supply (sessionId, userEmail)'
		)));
	}

	// Retrieve form data
	$sessionId = $_POST["sessionId"];
	$userEmail = $_POST["userEmail"];

	// Check if session id is valid
	if (BumpSession($_POST["sessionId"]) === false) {
		http_response_code(400);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100431',
			'message' => 'invalid session. session expired'
		)));
	}

	// Establish database connection
	$conn = ConnectToDatabase();

	// Check if user is permitted to read user data
	$results = SecureQuery(
		$conn,
		"SELECT fe_users.permLevel FROM fe_users JOIN ses_ids ON ses_ids.accountId = fe_users.uid WHERE ses_ids.sesId = ?;",
		"s",
		$sessionId
	)->fetch_all();

	// Check permissions
	if ($results[0][0] < $PERMISSIONS['read_userdata']) {
		http_response_code(401);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100432',
			'message' => 'insufficient permissions'
		)));
	}

	// Attempt to get user data
	$userData = GetUserData($userEmail);
	if ($userData === false) {
		http_response_code(400);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100433',
		)));
	}

	http_response_code(200);
	die(json_encode(array(
		'status' => 'success',
		'message' => 'all good :3',
		'userData' => $userData
	)));

?>
