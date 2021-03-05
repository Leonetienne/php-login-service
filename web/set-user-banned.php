<?php
require_once 'Resource/private/session.php';
require_once 'Resource/private/setUserBanned.php';
require_once 'Resource/private/permissions.php';

	// Set header data
	header('Access-Control-Allow-Origin: *');
	header("Content-type: application/json");

	// Do we actually have these values?
	if ((!isset($_POST["sessionId"])) ||
		(!isset($_POST["userEmail"])) ||
		(!isset($_POST["banned"])))
	{
		// If not, return error code 400 and an approriate error message
		http_response_code(400);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100412',
			'message' => 'missing form data. Please supply (sessionId, userEmail, banned)'
		)));
	}

	// Retrieve form data
	$sessionId = $_POST["sessionId"];
	$userEmail = $_POST["userEmail"];
	$banned	   = $_POST["banned"];

	// Check if session id is valid
	if (BumpSession($_POST["sessionId"]) === false) {
		http_response_code(400);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100416',
			'message' => 'invalid session. session expired'
		)));
	}

	// Establish database connection
	$conn = ConnectToDatabase();

	// Check if user is permitted to manage bans
	$results = SecureQuery(
		$conn,
		"SELECT fe_users.permLevel FROM fe_users JOIN ses_ids ON ses_ids.accountId = fe_users.uid WHERE ses_ids.sesId = ?;",
		"s",
		$sessionId
	)->fetch_all();

	// Check permissions based on wether we are banning or unbanning
	if ($banned) {
		if ($results[0][0] < $PERMISSIONS['ban_user']) {
			http_response_code(401);
			echo(json_encode(array(
				'status' => 'failed',
				'errno' => '100417',
				'message' => 'insufficient permissions'
			)));
			return false;
		}
	}
	else {
		if ($results[0][0] < $PERMISSIONS['unban_user']) {
			http_response_code(401);
			echo(json_encode(array(
				'status' => 'failed',
				'errno' => '100418',
				'message' => 'insufficient permissions'
			)));
			return false;
		}
	}

	// Attempt to set banned status
	if (!SetUserBanned($userEmail, $banned)) {
		http_response_code(400);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100421',
		)));
	}

	http_response_code(200);
	die(json_encode(array(
		'status' => 'success',
		'message' => 'all good :3'
	)));

?>
