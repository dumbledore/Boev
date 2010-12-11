<?php
	include_once '_connect.php';
	@include_once '_cpl_active.php';
	include_once PATH_ENGINE.'func_showmsg.php';
	include_once SYS_USERS;
	include_once SYS_AUTHENTICATION;

	if (
		(defined('SITE_CPL_FREEZE') && SITE_CPL_FREEZE === TRUE) ||
		(!defined('SITE_CPL_ERROR') || SITE_CPL_ERROR === TRUE)
	) {
		//code when CPL is down
		jump_to(URL_ADMIN.'index.php', 'gt_auth.php: site is locked/down');
	}
	 else
	{
		$userdata = user_verify($_POST['username'], $_POST['password']);
		
		if ($userdata === FALSE) {
			showmsg('invalid_user', URL_ADMIN.'index.php');
			exit;
		}

		create_session($userdata);
		
		if ($_GET['goto'] != 'site' && $_GET['goto'] != 'cpl')
			$clear['goto'] = 'cpl';
		else
			$clear['goto'] = $_GET['goto'];

		$pages = array('site' => URL_SITE.'index.php', 'cpl' => URL_ADMIN.'main.php');
		
		jump_to($pages[$clear['goto']]);
	}
?>