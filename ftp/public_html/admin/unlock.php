<?php
	include_once '_connect.php';
	@include_once '_cpl_active.php';
	
	define('ALLOW_EXECUTION', TRUE);
	$layout_title = 'Отключване на панела';
	$layout_onload = 'document.gates.username.focus();';
	include 'layout_header.php';

	if (SITE_CPL_ERROR === FALSE) //i.e. no problem whatsoever
	{
		jump_to(URL_ADMIN.'index.php', 'unlock.php: no necessity for unlocking so lets pretend it does not exist');
	}
	 else
	{
		echo '
			За отключването на панела необходимо е<br>администратор да въведе своето име и парола.<br><br>
			<form action="gt_unlock.php" method="post" name="gates">
			<table style="width: 250px;" align="center" cellspacing="0" cellpadding="0">
			<tr valign="middle">
			<td class="formlabel">потребител:</td>
			<td style="width: 15px;"></td>
			<td><input type="text" name="username" class="formfield"></td>
			</tr>
			<tr style="height: 15px;"><td></td><td></td><td></td></tr>
			<tr valign="middle">
			<td class="formlabel">парола:</td>
			<td></td>
			<td><input type="password" name="password" class="formfield"></td>
			</tr>
			</table>
			<br><input type="image" alt="ОТКЛЮЧИ ПАНЕЛА" src="./gfx/icons/btn_enter.png">
			</form>
		';
	}
	include 'layout_footer.php';
?>