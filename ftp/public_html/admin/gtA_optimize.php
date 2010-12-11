<?php
	include_once '_connect.php';
	define('ACCESS_LEVEL', 'admin');
	include_once SYS_ALLOW_EXECUTION;
	include_once SYS_DB_CONNECT;
	include_once PATH_ENGINE.'func_showmsg.php';
	
	$tbls_join = '';
	for ($i = 0; $i < count($CM_PAGE_TYPES_TABLES); $i++)
		$tbls_join .= '`'.$CM_PAGE_TYPES_TABLES[$i].'`, ';
	
	if (!query('OPTIMIZE TABLE
					`cm_struct_main`, `cm_struct_view`, `cm_struct_search`,
					'.$tbls_join.'
					`cm_comments`,
					`um_main`, `um_details`, `um_activation`,
					`settings`;
	'))
		trigger_error('Could not optimize tables!');
	
	showmsg('tables_optimized', URL_ADMIN.'main.php');
?>