<?php
	include_once '_connect.php';
	define('ACCESS_LEVEL', 'editor');
	include_once SYS_ALLOW_EXECUTION;
	include_once 'sys_cm.php';
	
	if (!id_verify($_POST['cm_selected']))
	{
		jump_to(URL_ADMIN.'main.php?page=cm_browse', 'INVALID POST data');
	}
	
	lock_tables('cm_struct_main', 'settings');

	if (!element_exists($_POST['cm_selected']))
	{ #page not there for some reason
		if (id_verify($_POST['parent'], true)) #if parent is OK
			jump_to(URL_ADMIN.'main.php?page=cm_browse&parent='.$_POST['parent']);
		else
			jump_to(URL_ADMIN.'main.php');
	}
	
	homepage($_POST['cm_selected']);
	
	unlock_tables();
	
	if (id_verify($_POST['parent'], true)) #if parent is OK
		jump_to(URL_ADMIN.'main.php?page=cm_browse&parent='.$_POST['parent']);
	else
		jump_to(URL_ADMIN.'main.php');
?>