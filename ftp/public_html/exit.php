<?php
	include_once '_connect.php';
	include_once SYS_AUTHENTICATION;
	include_once PATH_ENGINE.'func_showmsg.php';

	if ($_GET['goto'] != 'site' && $_GET['goto'] != 'cpl')
		$clear['goto'] = 'cpl';
	else
		$clear['goto'] = $_GET['goto'];

	$pages = array('site' => URL_SITE.'index.php', 'cpl' => URL_ADMIN.'index.php');
	
	exit_session();
	showmsg('on_exit', $pages[$clear['goto']]);
?>