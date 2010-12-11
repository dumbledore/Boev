<?php

	include_once '_connect.php';
	include_once SYS_ALLOW_EXECUTION;
	
	# Predefined functions for various pages
	if (isset($clear['page']))
	{
		switch (substr($clear['page'], 0, 3))
		{
			case 'cm_':
				include_once 'sys_cm.php';
				$homepage = homepage();
				
				include 'func_space.php';
				$used_space = usedspace(600);
				
				break;
		}
	}
	
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>

<?php
	echo '
		<title>Онлайн администрация - '.$layout_title.'</title>
		<link REL="stylesheet" HREF="'.URL_ADMIN.'main.css" TYPE="text/css">
	';
?>


<?php
	echo isset($layout_metainfo) ? $layout_metainfo : '';
?>

<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
</head>

<?php
	echo '<body'.(isset($layout_onload) ? ' onload="'.$layout_onload.'"' : '').' bgcolor="#CCCCCC">';
?>

<table style="width: 100%; height: 70%;" align="center" cellspacing="0" cellpadding="0"><tr valign="middle"><td align="center">

<?php
	echo '<span class="title">&middot;&nbsp;'.$layout_title.'&nbsp;&middot;</span><br>';
?>
<table style="width: 100%; height: 2px;" align="center" bgcolor="#AAAAAA" cellspacing="0" cellpadding="0"><tr><td></td></tr></table>

<?php
	echo '<table style="width: 100%; background-image: url('.URL_ADMIN.'gfx/bkg/diagbkg2.gif); background-attachment: fixed;" align="center" cellspacing="0" cellpadding="0"><tr valign="middle"><td align="center" class="common">';
?>

<br><br>