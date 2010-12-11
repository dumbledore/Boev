<?php
	include_once '_connect.php';
	define('ACCESS_LEVEL', 'editor');
	include_once SYS_ALLOW_EXECUTION;
	include_once PATH_ENGINE.'func_showmsg.php';
	include_once 'func_post.php';
	include_once 'sys_cm.php';
	
	validate_page();
	if (
		!isset($_POST[$_POST['page_uid'].'_bg']) ||
		!isset($_POST[$_POST['page_uid'].'_en']) ||
		!isset($_POST['cm_auto_generated']) ||
		!isset($_POST['cm_sort_direction']) ||
		!isset($_POST['cm_sort_by'])
	) {
		jump_to(URL_ADMIN.'main.php?page=cm_browse', 'invalid POST data');
		exit;
	}

	#fix the checkboxes
	fix_post_checkboxes('cm_use_text_bg_for_all');
	
	$res = page_edit($_POST['page_uid'], 'page', $_POST['cm_mode'], $_POST['cm_caption_bg'], $_POST['cm_caption_en'], $_POST['cm_del_image'], $_POST['cm_descr_bg'], $_POST['cm_descr_en'], $_POST['cm_searchable'], $_POST['cm_keywords_bg'], $_POST['cm_keywords_en'], $_POST['cm_registered_only'], $_POST['cm_can_comment'], $_POST['cm_show_name'], $_POST['cm_show_back_link'], $_POST['cm_show_end_bar'], $_POST['cm_delimiter'], $_POST['cm_lang_visibility'], isset($_POST['cm_homepage']));
	
	if ($res == CM_INVALID_NAME)
	{
		jump_to(URL_ADMIN.'main.php?page=cm_browse', 'invalid id');
		exit;
	}
	
	if ($res == CM_INVALID_TYPE)
	{
		jump_to(URL_ADMIN.'main.php?page=cm_browse', 'invalid type');
		exit;
	}
	
	if ($res == CM_NOT_FOUND)
	{
		showmsg('page_deleted', URL_ADMIN.'main.php?page=cm_browse');
		exit;
	}
	
	$res = page_edit_page($_POST['page_uid'], $_POST[$_POST['page_uid'].'_bg'], $_POST[$_POST['page_uid'].'_en'], $_POST['cm_auto_generated'], $_POST['cm_use_text_bg_for_all'], $_POST['cm_sort_direction'], $_POST['cm_sort_by']);
	
	if ($res != CM_OK)
	{
		jump_to(URL_ADMIN.'main.php', 'could not edit page. Error no.: '.$res);
		exit;
	}
	
	jump_to(URL_ADMIN.'main.php?page=cm_browse&parent='.$_POST['parent']);
?>