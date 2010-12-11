<?php
	include_once '_connect.php';
	include_once PATH_ENGINE.'func_showmsg.php';
	include_once SYS_USERS;

	$userdata = user_verify($_POST['username'], $_POST['password']);
	
	if ($userdata === FALSE || $userdata['credentials'] != 'admin') {
		jump_to(URL_ADMIN.'unlock.php', 'gt_unlock.php: bad data');
		exit;
	}
	 else
	{
		panel_working(true);
		showmsg('panel_unlocked', URL_ADMIN.'index.php');
	}
?>