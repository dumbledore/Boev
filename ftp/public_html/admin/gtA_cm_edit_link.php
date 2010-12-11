<?php
	include_once '_connect.php';
	define('ACCESS_LEVEL', 'editor');
	include_once SYS_ALLOW_EXECUTION;
	include_once PATH_ENGINE.'func_showmsg.php';
	include_once 'func_post.php';
	include_once 'sys_cm.php';
	
	validate_page();
	if (
		!isset($_POST['cm_link']) ||
		!isset($_POST['cm_target'])
	) {
		jump_to(URL_ADMIN.'main.php?page=cm_browse', 'invalid POST data');
		exit;
	}

	$res = page_edit($_POST['page_uid'], 'page', $_POST['cm_mode'], $_POST['cm_caption_bg'], $_POST['cm_caption_en'], $_POST['cm_del_image'], $_POST['cm_descr_bg'], $_POST['cm_descr_en'], $_POST['cm_searchable'], $_POST['cm_keywords_bg'], $_POST['cm_keywords_en'], $_POST['cm_registered_only'], $_POST['cm_can_comment'], $_POST['cm_show_name'], $_POST['cm_show_back_link'], $_POST['cm_show_end_bar'], $_POST['cm_delimiter'], $_POST['cm_lang_visibility'], isset($_POST['cm_homepage']));
	
	if ($res == CM_INVALID_NAME)
	{
		jump_to(URL_ADMIN.'main.php?page=cm_browse', 'invalid id');
		exit;
	}
	
	if ($res == CM_INVALID_TYPE)
	{
		jump_to(URL_ADMIN.'main.php?page=cm_browse', 'invalid type says common page');
		exit;
	}
	
	if ($res == CM_NOT_FOUND)
	{
		showmsg('page_deleted', URL_ADMIN.'main.php?page=cm_browse');
		exit;
	}
	
	$res = page_edit_link($_POST['page_uid'], $_POST['cm_link'], $_POST['cm_target']);
	
	if ($res != CM_OK)
	{
		jump_to(URL_ADMIN.'main.php', 'could not edit page. Error no.: '.$res);
		exit;
	}
	
	jump_to(URL_ADMIN.'main.php?page=cm_browse&parent='.$_POST['parent']);
?>