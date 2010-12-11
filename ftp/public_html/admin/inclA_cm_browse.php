<?php
	include_once '_connect.php';
	define('ACCESS_LEVEL', 'editor');
	include_once SYS_ALLOW_EXECUTION;
	$layout_title = 'съдържание';
	$layout_metainfo = '<link rel="stylesheet" href="./scripts/dlg/modal-message.css" type="text/css">';
	include 'layout_header.php';
	include_once 'func_layout.php';
	include_once SYS_DB_CONNECT;
	include_once 'sys_cm.php';
	
	if (!isset($_GET['parent']) || !element_exists($_GET['parent'], true))
		$clear['parent'] = 'menu';
	else
		$clear['parent'] = $_GET['parent'];

	$clear['path'] = path_get($clear['parent']);

	for ($i = 0; $i < count($clear['path'][0]) -1; $i++)
	{
		$clear['path_links'][$i] = '<a class="cm_table_link" href="main.php?page=cm_browse&amp;parent='.$clear['path'][0][$i].'">'.$clear['path'][1][$i].'</a>';
	}
	
	$i = count($clear['path'][0]) -1;
	$clear['path_links'][$i] = '<a class="cm_table_link" href="main.php?page='.
	($clear['path'][0][$i] != 'menu'
	? 'cm_edit_page&amp;id='.$clear['path'][0][$i].'">'.$clear['path'][1][$i].'</a>'
	: 'cm_browse&amp;parent='.$clear['path'][0][$i].'">'.$clear['path'][1][$i].'</a>'
	);
	
	//Path caption
	echo '<div class="title2" style="text-align: left; width: 600px;">'.implode('<img src="./gfx/icons/arrow.gif" alt="">', $clear['path_links']).'</div>';
	
	include 'layout_cm_browse_1.php';
	
	
	$res = query('SELECT * FROM `cm_struct_main` WHERE `parent` = \''.$clear['parent'].'\' AND `mode` > 2 ORDER BY `position`'); //2 == DELETED
	if (!$res)
		trigger_error('', E_USER_ERROR);
		
	define('CM_NUM_ROWS', mysql_num_rows($res));
	
	if ($clear['parent'] != 'menu')
	{		
		cm_row_add($clear['path'][0][count($clear['path'][0])-2]);
	}
	 else
	{
		if (CM_NUM_ROWS == 0)
			cm_row_msg('Няма въведени страници.');
	}
	
	while ($row = mysql_fetch_assoc($res))
	{
		cm_row_add($row['id'], $row['position'], $row['caption_bg'], $row['type'], $row['size'], $row['modified'], $homepage);
	}
	
	include 'layout_cm_browse_2.php';
	# select * from cm_struct_main left join cm_struct_view on cm_struct_main.id = cm_struct_view.id WHERE cm_struct_main.id = '897244ac-8b8d-102c-852c-0015174d3084';
?>