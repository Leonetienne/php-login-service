<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
	function ConnectToDatabase()
	{
		require_once 'databaseCredentials.php';

		// Connect to mysql database
		$conn = new mysqli (
			$DB_CREDS["host"],
			$DB_CREDS["user"],
			$DB_CREDS["pass"],
			$DB_CREDS["db"],
			$DB_CREDS["port"]
		);

		// Check if we are connected
		if ($conn->connect_errno) {
		  die("Connection failed. :( =><br/>" . $conn->connect_error);
		}

		return $conn;
	}
?>