<?php
	include_once '_connect.php';
	define('ACCESS_LEVEL', 'editor');
	include_once SYS_ALLOW_EXECUTION;
	$layout_title = 'преместване';
	//$layout_onload = 'document.userdata.um_email.focus();';
	include 'layout_header.php';
	include_once 'func_layout.php';
	include_once PATH_ENGINE.'func_showmsg.php';
	include_once 'sys_cm.php';

	if (!id_verify($_POST['parent'], true))
	{
		jump_to(URL_ADMIN.'main.php?page=cm_browse', 'invalid parent `'.$_POST['parent'].'`');
		exit;
	}
	
	$selected = explode(';', $_POST['cm_selected']);
	for ($i = 0; $i < count($selected); $i++)
	{
		if (!id_verify($selected[$i]))
		{
			jump_to(URL_ADMIN.'main.php?page=cm_browse', 'invalid id `'.$selected[$i].'`');
			exit;
		}
		
		$selected_query[$i] = '`id` = \''.$selected[$i].'\'';
	}
	
	if (!($res = query('SELECT `id`, `caption_bg` FROM `cm_struct_main` WHERE `parent` = \''.$_POST['parent'].'\' AND ('.implode(' OR ', $selected_query).');')))
		trigger_error('Could not select pages for moving', E_USER_ERROR);
	
	if (mysql_num_rows($res) == 0)
	{
		showmsg('cannot_move', URL_ADMIN.'main.php?page=cm_browse&parent='.$_POST['parent']);
		exit;
	}
	
	$selected_text = '';
	while ($page = mysql_fetch_assoc($res))
	{
		$selected_text .= '&middot;&nbsp;'.no_tags($page['caption_bg']).'<br>';
	}

?>
<form action="gates.php?page=cm_move" name="cm_move" method="post">
<?php
	echo '<input type="hidden" name="parent" value="'.$_POST['parent'].'">';
	echo '<input type="hidden" name="cm_selected" value="'.$_POST['cm_selected'].'">';
?>
<div align="center" style="width: 600px;" class="cm_table_rows_text">
	<div style="padding: 10px;">
	<center>
	<?php
		echo section_add('Страници за преместване', '
			<br>
			<center>
			Следните страници ще бъдат преместени:<br><br>
			<span style="color: #FF3300; font-size: 15px;">'.
			$selected_text
			.'
			</span>
			</center>
			<br>
		', 70);
		
		echo section_add('Цел на преместването', '
			<br>
			<center>
			Моля, посочете къде искате да преместите страниците.<br>
			Тъй като не можете да премествате избраните страници<br>
			една в друга или в себе си, те не са показани на менюто<br>
			<br>
			<select name="cm_target" size="16" style="width: 350px">'.
			tree_get_for_moving($selected)
			.'</select>
			</center>
			<br>
		', 70);

		echo '
			<br>
			<center>
				<input type="image" alt="ПРЕМЕСТИ" src="./gfx/icons/btn_move.png">
				&nbsp;&nbsp;&nbsp;
				<a href="main.php?page=cm_browse&parent='.$_POST['parent'].'"><img src="./gfx/icons/btn_cancel.png" alt="откажи"></a>
			</center>
		';
	?>
	</center>
	</div>
</div>

</form>