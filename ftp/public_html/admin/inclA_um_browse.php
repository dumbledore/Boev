<?php
	include_once '_connect.php';
	define('ACCESS_LEVEL', 'admin');
	include_once SYS_ALLOW_EXECUTION;
	$layout_title = 'потребители';
	$layout_metainfo = '<link rel="stylesheet" href="./scripts/dlg/modal-message.css" type="text/css">';
	include 'layout_header.php';
	include_once 'func_layout.php';
	include_once SYS_DB_CONNECT;
	include_once SYS_USERS;
	
	include 'layout_um_browse_1.php';
	
	$res = query('SELECT * FROM `um_main` ORDER BY `username`');
	if (!$res)
		trigger_error('', E_USER_ERROR);
	
	while ($row = mysql_fetch_assoc($res))
	{
		um_row_add($row['username'], $row['credentials'], $row['email'], $row['active']);
	}
	include 'layout_um_browse_2.php';
?>