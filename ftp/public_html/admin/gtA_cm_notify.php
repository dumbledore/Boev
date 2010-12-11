<?php
	include_once '_connect.php';
	define('ACCESS_LEVEL', 'editor');
	include_once SYS_ALLOW_EXECUTION;
	include_once 'sys_cm.php';
	include_once SYS_USERS;

	if (!id_verify($_POST['cm_selected']) || !isset($_POST['cm_notify_message']))
	{
		jump_to(URL_ADMIN.'main.php?page=cm_browse', 'INVALID POST data');
	}
	
	lock_tables('cm_struct_main', 'cm_struct_view', 'cm_struct_search', 'um_main');

	#if (!element_exists($_POST['cm_selected']))
	if (!is_array($info = element_get($_POST['cm_selected'], array('id', 'caption_bg')))) #i.e. not exists
	{ #page not there for some reason
		if (id_verify($_POST['parent'], true)) #if parent is OK
			jump_to(URL_ADMIN.'main.php?page=cm_browse&parent='.$_POST['parent'], 'page not there?');
		else
			jump_to(URL_ADMIN.'main.php', 'page not there?');
	}
	
	$subscribers = get_subscribed($_POST['cm_selected']);
	
	user_send_mail_to_all($subscribers, $info['caption_bg'], $_POST['cm_notify_message']);
	
	unlock_tables();
	
	if (id_verify($_POST['parent'], true)) #if parent is OK
		jump_to(URL_ADMIN.'main.php?page=cm_browse&parent='.$_POST['parent']);
	else
		jump_to(URL_ADMIN.'main.php');
?>