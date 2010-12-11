<?php
	include_once '_connect.php';
	define('ACCESS_LEVEL', 'editor');
	include_once SYS_ALLOW_EXECUTION;
	include_once SYS_DB_CONNECT;
	include_once 'sys_cm.php';
	
	if (!isset($_POST['cm_selected']) || $_POST['cm_selected'] == '')
	{
		jump_to(URL_ADMIN.'main.php?page=cm_browse', 'Invalid POST data');
		exit;
	}
	
	if (page_shred(explode(';', $_POST['cm_selected'])) != CM_OK)
	{
		# page_shred does not complain of invalid pages, i.e. with wrong ids or unexistant ones.
		# It just shreds only valid ones.
		jump_to(URL_ADMIN.'main.php?page=cm_browse', 'Something is wrong.');
		exit;
	}
	
	jump_to(URL_ADMIN.'main.php?page=cm_trash');
?>