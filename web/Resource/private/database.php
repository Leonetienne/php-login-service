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
			$conn->close();
			http_response_code(500);
			die(json_encode(array(
				'status' => 'failed',
				'errno' => '100501',
				'message' => 'internal server error'
			)));
		}

		// Bind params
		//foreach ($params as &$pair) {
		//	if (!$stmt->bind_param($pair['type'], $pair['value'])) {
		//		$conn->close();
		//		http_response_code(500);
		//		die(json_encode(array(
		//			'status' => 'failed',
		//			'errno' => '100502',
		//			'message' => 'internal server error'
		//		)));
		//	}
		//}

		if (!$stmt->bind_param($paramTypes, ...$params)) {
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
