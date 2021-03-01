<?php
require_once 'Resource/private/checkAdmintoolPassword.php';
require_once 'Resource/private/database.php';

	// Check if admin password is given
	if (!isset($_POST["adm_password"]))
	{
		http_response_code(401);
		die("Please provide authentication via post-parameter adm_password!");
	}

	// If yes, and if it's wrong, abort and dump the password hash to a private file
	$apw = $_POST["adm_password"];
	if (!CheckAdmintoolPassword($apw))
	{
		http_response_code(401);
		die();
	}

	// Admin authenticated successfully

	// Establish database connection
	$conn = ConnectToDatabase();

	// Read sql setup file
	$setup = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/Resource/private/setup.sql');
	
	if (!$conn->multi_query($setup)) {
		http_response_code(500);
		die("Multi query failed :( =><br/>" . $conn->error);
	}

	// Execute sql
	do {
		if ($res = $conn->store_result()) {
			var_dump($res->fetch_all(MYSQLI_ASSOC));
			$res->free();
		}
	} while ($conn->more_results() && $conn->next_result());

	// Close connection
	$conn->close();

	echo("All good :)");
?>
