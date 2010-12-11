<?php
	include_once '_connect.php';
	include_once SYS_ALLOW_EXECUTION;
	
	if (isset($_COOKIE[SESS_NAME]))
	{
		//if there's an open session, redirect without ado
		//if there's something wrong with it though, main.php will now what to do ;-)
		jump_to(URL_ADMIN.'main.php', 'incl_login.php: SESSION is already running, redirect user right away!');
		exit;
	}
	
	$layout_title = 'вход';
	$layout_onload = 'document.gates.username.focus();';
	include 'layout_header.php';

	echo '<form action="gt_auth.php?goto='.(isset($_GET['goto']) ? $_GET['goto'] : '').'" method="post" name="gates">';
?>

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
<br><input type="image" alt="ВХОД" src="./gfx/icons/btn_enter.png">
<br><br><a href="index.php?page=lostpass" class="link">Забравена парола</a>
</form>
