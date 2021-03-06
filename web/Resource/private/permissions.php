<?php
	// This array provides permission presets
	$PERM_LEVELS = array(
		'user'			=> 0,
		'moderator'		=> 1,
		'administrator' => 2,
	);
	
	// This array describes the minimum permission level an account has to have in order to do specific functions
	$PERMISSIONS = array(
		'ban_user'			=> $PERM_LEVELS['moderator'],
		'unban_user'		=> $PERM_LEVELS['moderator'],
		'kick_user'			=> $PERM_LEVELS['moderator'],
		'read_userdata'		=> $PERM_LEVELS['moderator'],
		'grant_permissions'	=> $PERM_LEVELS['administrator']
	);
?>
