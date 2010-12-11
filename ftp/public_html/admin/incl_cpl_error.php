<?php
	include_once '_connect.php';
	include_once SYS_ALLOW_EXECUTION;
	
	$layout_title = 'панелът е затворен';
	include 'layout_header.php';
?>

<table align="center" cellspacing="0" cellpadding="0">
	<tr valign="middle">
		<td><img src="gfx/icons/msg_error.png" alt="грешка"></td>
		<td style="width: 15px;"></td>
		<td class="error">
			Поради технически причини административният панел е затворен.<br>
			Администраторът работи върху отстраняването на проблема.
		</td>
	</tr>
</table>

<br>