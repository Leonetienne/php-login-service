<?php
// This file is responsible to handle actions, hidden from users
require_once 'Resource/private/emailVerification.php';

// Private function to delete key
$private_DeleteKey = function($conn, $key) {
	SecureQuery(
		$conn,
		"DELETE FROM action_keys WHERE actionKey = ?",
		"s",
		$key
	);
	return;
};

	// Set header data
	header('Access-Control-Allow-Origin: *');
	header("Content-type: application/json");

	// Do we actually have these values?
	if ((!isset($_GET["key"])))
	{
		// If not, return error code 400 and an approriate error message
		http_response_code(400);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100437',
			'message' => 'missing get data. Please supply (key)'
		)));
	}

	$key = $_GET['key'];

	// Look up corresponding action

	// Establish database connection
	$conn = ConnectToDatabase();

	// Fetch data
	$results = SecureQuery(
		$conn,
		"SELECT time_expires, action, accountId FROM action_keys WHERE actionKey = ?;",
		"s",
		$key
	)->fetch_all();

	// No key with that id? :(
	if (count($results) < 1) {
		http_response_code(400);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100438',
			'message' => 'invalid action key'
		)));
	}

	// Is key expired?
	if ($results[0][0] < time()) {
		http_response_code(400);
		echo(json_encode(array(
			'status' => 'failed',
			'errno' => '100439',
			'message' => 'key expired'
		)));
		$private_DeleteKey($conn, $key);
		unset($private_DeleteKey);
		die();
	}

	// Collect data
	$actionId  = $results[0][1];
	$accountId = $results[0][2];

	// Attempt action
	if ($actionId == 'verify_account') {
		if (!VerifyAccount($accountId)) {
			http_response_code(500);
			echo(json_encode(array(
				'status' => 'failed',
				'errno' => '100507',
				'message' => 'internal server error'
			)));
			$private_DeleteKey($conn, $key);
			unset($private_DeleteKey);
			die();
		}
	}
	else {
		http_response_code(400);
		echo(json_encode(array(
			'status' => 'failed',
			'errno' => '100440',
			'message' => 'invalid action'
		)));
		$private_DeleteKey($conn, $key);
		unset($private_DeleteKey);
		die();
	}

	$private_DeleteKey($conn, $key);
	unset($private_DeleteKey);

	http_response_code(200);
	die(json_encode(array(
		'status' => 'success',
		'message' => 'all good :3'
	)));
?>
