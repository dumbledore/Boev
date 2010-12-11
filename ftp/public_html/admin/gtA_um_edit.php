<?php
	include_once '_connect.php';
	include_once SYS_ALLOW_EXECUTION;
	include_once SYS_USERS;
	include_once PATH_ENGINE.'func_showmsg.php';
	
	//Check POST data
	if (
		!isset($_POST['um_username']) ||
		(($_SESSION['credentials'] == 'admin') && !isset($_POST['um_type'])) ||
		!isset($_POST['um_email']) ||
		!isset($_POST['um_new_password']) ||
		!isset($_POST['um_name']) ||
		(($_SESSION['credentials'] == 'admin') && !isset($_POST['um_title'])) ||
		!isset($_POST['um_message']) ||
		!isset($_POST['um_aim']) ||
		!isset($_POST['um_password'])
	) {
		jump_to(URL_ADMIN.'main.php', 'gtA_um_edit.php: invalid POST data');
		exit;
	}
	
	if ($_SESSION['credentials'] == 'admin')
	{
		if (!user_verify($_SESSION['username'], $_POST['um_password'], false))
		{
			showmsg('invalid_user', URL_ADMIN.'main.php'.($_POST['goto'] == 'checkpoint' ? '' : '?page=um_browse'), 'Паролата, която въведохте не съвпада с текущата.');
			exit;
		}
		$res = user_edit($_POST['um_username'], $_POST['um_new_password'], $_POST['um_email'], $_POST['um_type'], (isset($_POST['um_active']) ? true : false), $_POST['um_name'], $_POST['um_title'], $_POST['um_message'], $_POST['um_aim']);
		jump_to(URL_ADMIN.'main.php'.($_POST['goto'] == 'checkpoint' ? '' : '?page=um_browse'), 'User edited by admin: '.$res);
	}
	 else
	{
		if ($_SESSION['username'] != $_POST['um_username'])
		{
			jump_to(URL_ADMIN.'main.php', 'gtA_um_edit.php: a non-admin user is trying to edit other users data');
			exit;
		}
		if (!user_verify($_SESSION['username'], $_POST['um_password'], false))
		{
			showmsg('invalid_user', URL_ADMIN.'main.php', 'Паролата, която въведохте не съвпада с текущата.');
			exit;
		}
		
		$res = user_edit($_POST['um_username'], $_POST['um_new_password'], $_POST['um_email'], $_POST['um_type'], true, $_POST['um_name'], '', $_POST['um_message'], $_POST['um_aim']);
		jump_to(URL_ADMIN.'main.php', 'user edited their details: '.$res);
	}
?>