<?php
	include_once '_connect.php';
	define('ACCESS_LEVEL', 'editor');
	include_once SYS_ALLOW_EXECUTION;
	$layout_title = 'кошче';
	$layout_metainfo = '<link rel="stylesheet" href="./scripts/dlg/modal-message.css" type="text/css">';
	include 'layout_header.php';
	include_once 'func_layout.php';
	include_once SYS_DB_CONNECT;
	include_once 'sys_cm.php';
	
	include 'layout_cm_trash_1.php';

	$res = query('SELECT * FROM `cm_struct_main` WHERE `mode` = \'deleted\' ORDER BY `modified` DESC');
	if (!$res)
		trigger_error('', E_USER_ERROR);
		
	define('CM_NUM_ROWS', mysql_num_rows($res));
	
	if (CM_NUM_ROWS == 0)
			cm_row_msg('Няма изтрити страници.', true);
	else
	 {
		while ($row = mysql_fetch_assoc($res))
		{
			cm_row_add2($row['id'], $row['caption_bg'], $row['type'], $row['size'], $row['modified']);
		}
	}

	include 'layout_cm_trash_2.php';
?>