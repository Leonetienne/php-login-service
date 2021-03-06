<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
	function ConnectToDatabase()
	{
		require 'databaseCredentials.php';

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
		  die(json_encode(array(
				'status' => 'failed',
				'errno' => '100500',
				'message' => 'internal server error'
			)));
		}

		return $conn;
	}

	function SecureQuery($conn, $queryTemplate, $paramTypes, ...$params) {

		// Prepare statement
		if (!($stmt = $conn->prepare($queryTemplate))) {
			echo($conn->error);
			$conn->close();
			http_response_code(500);
			die(json_encode(array(
				'status' => 'failed',
				'errno' => '100501',
				'message' => 'internal server error'
			)));
		}

		// Bind params
		if (!$stmt->bind_param($paramTypes, ...$params)) {
			echo($stmt->error);
			$conn->close();
			http_response_code(500);
			die(json_encode(array(
				'status' => 'failed',
				'errno' => '100502',
				'message' => 'internal server error'
			)));
		}

		// Execute
		if (!$stmt->execute()) {
			echo($stmt->error);
			$conn->close();
			http_response_code(500);
			die(json_encode(array(
				'status' => 'failed',
				'errno' => '100503',
				'message' => 'internal server error'
			)));
		}

		// Return results
		return $stmt->get_result();
	}
?>
