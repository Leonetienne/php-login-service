<?php
	http_response_code(400);
	die(json_encode(array(
		'status' => 'failed',
		'errno' => '403',
		'message' => 'Connection not encrypted. You MUST use https!'
	)));
?>
