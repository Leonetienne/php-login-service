<?php
require_once 'database.php';
require_once 'Resource/private/hashPasswordKdf.php';
require_once 'sendTemplatedEmail.php';
require_once 'kickUser.php';

// This will send an email to the user with the option to reset his password
function SendPasswordResetEmail($userId) {
	// Establish database connection
	$conn = ConnectToDatabase();

	// Fetch user email
	$results = SecureQuery(
		$conn,
		"SELECT email FROM fe_users WHERE uid = ?;",
		"i",
		$userId
	)->fetch_all();

	// User not found
	if (count($results) < 1) {
		return false;
	}

	$userEmail = $results[0][0];

	// Create action key
	$actionKey = hash('tiger128,3', $userId . $userEmail . random_int(0, PHP_INT_MAX));
	$results = SecureQuery(
		$conn,
		"INSERT INTO action_keys
			(actionKey, accountId, time_created, time_expires, action)
			VALUES
			(?, ?, ?, ?, 'reset_password');",
		"siii",
		$actionKey,
		$userId,
		time(),
		time() + 60*60*24 // Expires after one day
	);

	// Now send the email
	$resetLink = 'https://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/action.php?key=' . urlencode($actionKey);
	if (!SendTemplatedEmail($userEmail, 'Request to reset your password', 'template/mail/password-reset-request.php', array(
			'resetLink' => $resetLink
	))) {
		return false;
	}

	return true;
}

// This file will change a users password to a random one
// It will mail the password to the user
function ResetPassword($userId) {
	// Establish database connection
	$conn = ConnectToDatabase();

	// Fetch user email
	$results = SecureQuery(
		$conn,
		"SELECT email FROM fe_users WHERE uid = ?;",
		"i",
		$userId
	)->fetch_all();

	// User not found
	if (count($results) < 1) {
		return false;
	}

	$userEmail = $results[0][0];
	$newPassword = hash('adler32', $userId . $userEmail . random_int(0, PHP_INT_MAX));

	// All good. Now update the password
	SecureQuery(
		$conn,
		"UPDATE fe_users SET password_hash = ? WHERE uid = ?;",
		"si",
		HashPasswordKDF($newPassword, $userEmail),
		$userId
	);

	// Terminate all sessions associated with the user
	KickUser($userEmail);

	// Now email the new password to the user
	if(!SendTemplatedEmail($userEmail, 'Your password has been reset!', 'template/mail/password-reset-pw.php', array(
			'newPassword' => $newPassword
	))) {
		return false;
	}

	return true;
}

?>
