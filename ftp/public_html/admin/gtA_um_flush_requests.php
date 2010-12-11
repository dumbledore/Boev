<?php
	include_once '_connect.php';
	define('ACCESS_LEVEL', 'admin');
	include_once SYS_ALLOW_EXECUTION;
	include_once SYS_USERS;
	include_once PATH_ENGINE.'func_showmsg.php';
	
	flush_requests();
	
	showmsg('requests_flushed', URL_ADMIN.'main.php');
?>