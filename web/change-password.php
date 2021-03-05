<?php
require_once 'Resource/private/session.php';
require_once 'Resource/private/hashPasswordKdf.php';

	// Set header data
	header('Access-Control-Allow-Origin: *');
	header("Content-type: application/json");

	// Do we actually have these values?
	if ((!isset($_POST["sessionId"])) ||
		(!isset($_POST["oldPassword"])) ||
		(!isset($_POST["newPassword"])))
	{
		// If not, return error code 400 and an approriate error message
		http_response_code(400);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100412',
			'message' => 'missing form data. Please supply (sessionId, oldPassword, newPassword)'
		)));
	}

	// Retrieve form data
	$sessionId = $_POST["sessionId"];
	$oldPassword = $_POST["oldPassword"];
	$newPassword = $_POST["newPassword"];

	// Check if session id is valid
	if (BumpSession($_POST["sessionId"]) === false) {
		http_response_code(400);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100413',
			'message' => 'invalid session. session expired'
		)));
	}

	// Establish database connection
	$conn = ConnectToDatabase();

	// Fetch password_hash to check if oldPassword matches
	$results = SecureQuery(
		$conn,
		"SELECT fe_users.password_hash, fe_users.email, fe_users.uid FROM fe_users JOIN ses_ids ON ses_ids.accountId = fe_users.uid WHERE ses_ids.sesId = ?;",
		"s",
		$sessionId
	)->fetch_all();

	// Was any account found? Should never fail, because session validity got checked before
	if (count($results) < 1) {
		$conn->close();
		http_response_code(400);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100414',
			'message' => 'Account not found'
		)));
	}

	$oldPwHash = $results[0][0];
	$userEmail = $results[0][1];
	$accountId = $results[0][2];

	// Check if the old password matches
	if (HashPasswordKDF($oldPassword, $userEmail) != $oldPwHash) { // Select the first (and only) result and of that the first column, which is the password_hash
		$conn->close();
		http_response_code(400);
		die(json_encode(array(
			'status' => 'failed',
			'errno' => '100415',
			'message' => 'Old password invalid'
		)));
	}

	// Password matched. Now, change it
	$results = SecureQuery(
		$conn,
		"UPDATE fe_users SET password_hash = ? WHERE uid = ?;",
		"si",
		HashPasswordKDF($newPassword, $userEmail),
		$accountId
	);

	http_response_code(200);
	die(json_encode(array(
		'status' => 'success',
		'message' => 'all good :3'
	)));

?>
