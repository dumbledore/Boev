<?php
	include_once '_connect.php';
	define('ACCESS_LEVEL', 'editor');
	include_once SYS_ALLOW_EXECUTION;
	$layout_title = 'карта на сайта';
	include 'layout_header.php';
	include_once 'func_layout.php';
	include_once 'sys_cm.php';
?>
<div align="center" style="width: 600px;" class="cm_table_rows_text">
	<div style="padding: 10px;">
	<center>
	<?php
		echo section_add('Карта на сайта', '
			<br>
			Следва карта на сайта:<br><br>
			<table cellspacing="0" cellpadding="0" align="center">
			<tr>
			<td class="common" style="color: #000000; border-left: 1px solid #FF3300; padding-left: 10px;">
			'.
			tree_get_for_sitemap()
			.'
			</td></tr></table>
			<br>
		', 70);
	?>
	</center>
	</div>
</div>