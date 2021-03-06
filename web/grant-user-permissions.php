<?php
require_once 'Resource/private/session.php';
require_once 'Resource/private/grantUserPermissions.php';
require_once 'Resource/private/permissions.php';

	// Set header data
	header('Access-Control-Allow-Origin: *');
	header("Content-type: application/json");

	// Do we actually have these values?
	if ((!isset($_POST["sessionId"])) ||
		(!isset($_POST["userEmail"])) ||
		(!isset($_POST["permLevel"])))
	{
		// If not, return error code 400 and an approriate error message
		http_response_code(400);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100430',
			'message' => 'missing form data. Please supply (sessionId, userEmail, permLevel)'
		)));
	}

	// Retrieve form data
	$sessionId = $_POST["sessionId"];
	$userEmail = $_POST["userEmail"];
	$permLevel = $_POST["permLevel"];

	// Check if session id is valid
	if (BumpSession($_POST["sessionId"]) === false) {
		http_response_code(400);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100434',
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
	if (($results[0][0] < $PERMISSIONS['grant_permissions']) || ($results[0][0] <= $permLevel)) {
		http_response_code(401);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100435',
			'message' => 'insufficient permissions'
		)));
	}

	// Attempt grant permissions
	if (GrantUserPermissions($userEmail, $permLevel) === false) {
		http_response_code(400);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100436',
		)));
	}

	http_response_code(200);
	die(json_encode(array(
		'status' => 'success',
		'message' => 'all good :3'
	)));

?>
