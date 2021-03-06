<?php
require_once 'Resource/private/session.php';
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
			'errno' => '100400',
			'message' => 'missing sessionId'
		)));
	}

	$result = BumpSession($_POST["sessionId"]);

	if ($result === false) {
		http_response_code(400);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100400',
			'message' => 'invalid session'
		)));
	}

	http_response_code(200);
	die(json_encode(array(
		'status' => 'success',
		'message' => 'all good :3'
	)));

?>
