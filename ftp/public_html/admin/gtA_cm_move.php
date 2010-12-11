<?php
	include_once '_connect.php';
	define('ACCESS_LEVEL', 'editor');
	include_once SYS_ALLOW_EXECUTION;
	include_once PATH_ENGINE.'func_showmsg.php';
	include_once 'sys_cm.php';
	
	$res = page_move(explode(';', $_POST['cm_selected']), $_POST['cm_target']);
	
	if ($res == CM_INVALID_NAME)
	{
		jump_to(URL_ADMIN.'main.php?page=cm_browse', 'invalid id');
		exit;
	}
	
	if ($res == CM_NOT_FOUND)
	{
		showmsg('cannot_move', URL_ADMIN.'main.php?page=cm_browse&parent='.$_POST['parent']);
		exit;
	}
	
	if ($res != CM_OK)
	{
		jump_to(URL_ADMIN.'main.php?page=cm_browse', 'Error occured: '.$res);
		exit;
	}
	
	jump_to(URL_ADMIN.'main.php?page=cm_browse&parent='.$_POST['parent']);
	
?>