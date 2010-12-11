<?php
	include_once '_connect.php';
	define('ACCESS_LEVEL', 'editor');
	include_once SYS_ALLOW_EXECUTION;
	include_once 'sys_cm.php';
	
	if (!id_verify($_POST['parent'], true))
		jump_to(URL_ADMIN.'main.php?page=cm_browse', 'invalid parent');
	
	if (
		!id_verify($_POST['cm_selected']) ||
		isset($_POST['cm_direction'])
	) {
		jump_to(URL_ADMIN.'main.php?page=cm_browse&parent='.$_POST['parent'], 'invalid POST data');
	}
	
	$res = page_move_updn($_POST['cm_selected'], $_POST['cm_direction'] == 0);
	
	jump_to(URL_ADMIN.'main.php?page=cm_browse&parent='.$_POST['parent']);
	
?>