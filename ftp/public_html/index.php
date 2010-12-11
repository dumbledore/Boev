<?php

	include '_connect.php';
	include_once SYS_DB_CONNECT;
	include_once SYS_MAIL;
	include_once PATH_SITE.'_std.php';
	include_once './layout/_settings.php';
	
	define('ALLOW_EXECUTION', TRUE);
	# prevents running inclusions witout beeing
	# called from the RIGHT page
	
	# Set language
	define('SITE_LANG', language($_COOKIE['lang']));

	//include LOGIN support
	ob_start();
	
	include 'sys_login.php';
		
	include PATH_ENGINE.'sys_render.php';
	
	//html starts from here
	include './layout/layout_header.php';
	echo render_menu();
	include './layout/layout_login.php';
	include './layout/layout_middle.php';
	echo render_page($_GET['page']);
	include './layout/layout_footer.php';
	
	if (DEBUG === TRUE)
		include PATH_ENGINE.'layout_debug.php';
	ob_end_flush();
?>