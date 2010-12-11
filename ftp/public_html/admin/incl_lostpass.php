<?php
	include_once '_connect.php';
	include_once SYS_ALLOW_EXECUTION;
	$layout_title = 'забравена парола';
	$layout_onload = 'document.gates.username.focus();';
	include 'layout_header.php';
?>

Въведете потребителското име и майла,<br>с който сте се регистрирали и натиснете "прати".<br>Ще ви бъде изпратено писмо с данните за вход.<br><br>
<form action="gt_sendpass.php" method="post" name="gates">
<table style="width: 250px;" align="center" cellspacing="0" cellpadding="0">
<tr valign="middle">
<td class="formlabel">потребител:</td>
<td style="width: 15px;"></td>
<td><input type="text" name="username" class="formfield"></td>
</tr>
<tr style="height: 15px;"><td></td><td></td><td></td></tr>
<tr valign="middle">
<td class="formlabel">e-mail:</td>
<td style="width: 15px;"></td>
<td><input type="text" name="email" class="formfield"></td>
</tr>
</table>
<br><input type="submit" value="ПРАТИ" class="smallbutton">
<br><br><a href="index.php?page=login" class="link">Спомних си паролата!</a>
</form>