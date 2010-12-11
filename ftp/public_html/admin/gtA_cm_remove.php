<?php
	include_once '_connect.php';
	define('ACCESS_LEVEL', 'editor');
	include_once SYS_ALLOW_EXECUTION;
	include_once 'sys_cm.php';
	include_once SYS_DB_CONNECT;
	
	if (
	!isset($_POST['parent']) || $_POST['parent'] == '' ||
	!isset($_POST['cm_selected']) || $_POST['cm_selected'] == ''
	)
		jump_to(URL_ADMIN.'main.php?page=cm_browse', 'Invalid POST data');
	
	if (page_trash(explode(';', $_POST['cm_selected'])) != CM_OK)
	{
		jump_to(URL_ADMIN.'main.php?page=cm_browse', 'Something is wrong.');
		exit;
	}
	
	jump_to(URL_ADMIN.'main.php?page=cm_browse&parent='.$_POST['parent']);
?>