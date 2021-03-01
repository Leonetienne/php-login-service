<?php
require_once 'hashPasswordKdf.php';
	function CheckAdmintoolPassword($pw)
	{
		$storedHash = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/Resource/private/admintool.pw");
		if (HashPasswordKDF($pw, 'install_tool') == $storedHash)
			return true;
		else {
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/private/last_admintool_pwh.txt", HashPasswordKDF($pw, 'install_tool'));
			echo("Invalid admin password!<br/>If you are the admin of this page, you can reset it by updating the password hash in 'Resource/private/admintool.pw'. The hash result of the password you just tried got saved to private/last_admintool_pwh.txt");
			return false;
		}
	}
?>
