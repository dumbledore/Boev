<?php
	include_once '_connect.php';
	define('ACCESS_LEVEL', 'editor');
	include SYS_ALLOW_EXECUTION;
	include_once SYS_DB_CONNECT;
	include_once 'sys_cm.php';
	
	if (page_add($_POST['parent'], $_POST['cm_type'], $_POST['cm_caption_bg'], $_POST['cm_caption_en']) === CM_OK)
	{
		setcookie('lastpagetype', $_POST['cm_type'], time() + 7200, PATH_MAIN, (COOKIE_DOMAIN ? '.' : '').DOMAIN); //two hours, this is 
		jump_to(URL_ADMIN.'main.php?page=cm_browse&parent='.$_POST['parent']);
	}
	else
		jump_to(URL_ADMIN.'main.php?page=cm_browse', 'Could no add page');
?>