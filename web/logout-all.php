<?php
require 'Resource/private/session.php';
	// Set header data
	header('Access-Control-Allow-Origin: *');
	header("Content-type: application/json");

	// Do we actually have these values?
	if (!isset($_POST["sessionId"]))
	{
		// If not, return error code 400 and an approriate error message
		http_response_code(400);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100409',
			'message' => 'missing sessionId'
		)));
	}

	// Prevent expired sessions from logging out other sessions
	if (BumpSession($_POST["sessionId"]) === false) {
		http_response_code(400);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100410',
			'message' => 'invalid session. session expired'
		)));
	}

	// This should never fail
	if (CloseAllAssociatedSessions($_POST["sessionId"]) === false) {
		http_response_code(400);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100411',
			'message' => 'invalid session'
		)));
	}

	http_response_code(200);
	die(json_encode(array(
		'status' => 'success',
		'message' => 'all good :3'
	)));

?>
