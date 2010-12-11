<?php
	include_once '_connect.php';
	include_once SYS_ALLOW_EXECUTION;
	$layout_title = 'главно меню';
	include 'layout_header.php';
	include_once 'sys_cm.php';

?>

<table align="center" cellspacing="0" cellpadding="0">

<?php
	# Admin-accessible pages
	if ($_SESSION['credentials'] == 'admin')
	{
		echo '
			<tr valign="middle">
			<td class="formlabel"><img src="./gfx/icons/group.gif"></td>
			<td style="width: 15px;"></td>
			<td><a href="gates.php?page=optimize" class="link">Оптимизирай таблиците</a></td>
			</tr>
			<tr style="height: 15px;"><td></td><td></td><td></td></tr>
		';
		
		echo '
			<tr valign="middle">
			<td class="formlabel"><img src="./gfx/icons/group.gif"></td>
			<td style="width: 15px;"></td>
			<td><a href="gates.php?page=um_flush_requests" class="link">Изчисти неизползваните потребители.</a></td>
			</tr>
			<tr style="height: 15px;"><td></td><td></td><td></td></tr>
		';
		
		echo '
			<tr valign="middle">
			<td class="formlabel"><img src="./gfx/icons/group.gif"></td>
			<td style="width: 15px;"></td>
			<td><a href="main.php?page=um_browse" class="link">Списък на потребителите</a></td>
			</tr>
			<tr style="height: 15px;"><td></td><td></td><td></td></tr>
		';
	}
?>

<tr valign="middle">
<td class="formlabel"><img src="./gfx/icons/user.gif"></td>
<td style="width: 15px;"></td>
<td><a href="main.php?page=um_edit&goto=checkpoint" class="link">Настройки на потребителя</a></td>
</tr>
<tr style="height: 15px;"><td></td><td></td><td></td></tr>

<?php

	# Editor-accesible pages
	if ($_SESSION['credentials'] == 'admin' || $_SESSION['credentials'] == 'editor')
	{
		echo '
			<tr valign="middle">
			<td class="formlabel"><img src="./gfx/icons/sitemap.gif"></td>
			<td style="width: 15px;"></td>
			<td><a href="main.php?page=cm_sitemap" class="link">Карта на сайта</a></td>
			</tr>
			<tr style="height: 15px;"><td></td><td></td><td></td></tr>
		';
		
		echo '
			<tr valign="middle">
			<td class="formlabel"><img src="./gfx/icons/web.gif"></td>
			<td style="width: 15px;"></td>
			<td><a href="main.php?page=cm_homepage" class="link">Начална страница</a>'.
			(homepage() === NULL ? '<br><span style="color: #FF0000;" class="common">Не е зададена начална страница!</span>' : '' )
			.'</td>
			</tr>
			<tr style="height: 15px;"><td></td><td></td><td></td></tr>
		';
		
		echo '
			<tr valign="middle">
			<td class="formlabel"><img src="./gfx/icons/document.gif"></td>
			<td style="width: 15px;"></td>
			<td><a href="main.php?page=cm_browse" class="link">Съдържание</a></td>
			</tr>
			<tr style="height: 15px;"><td></td><td></td><td></td></tr>
		';
		
		if (($rec_size = recycle_full()) === FALSE)
		{
			echo '
				<tr valign="middle">
				<td class="formlabel"><img src="./gfx/icons/rec_empty_sm.png"></td>
				<td style="width: 15px;"></td>
				<td class="common">Кошчето е празно</td>
				</tr>
				<tr style="height: 15px;"><td></td><td></td><td></td></tr>
			';
		}
		 else
		{
			echo '
				<tr valign="middle">
				<td class="formlabel"><img src="./gfx/icons/rec_full_sm.png"></td>
				<td style="width: 15px;"></td>
				<td><a href="main.php?page=cm_trash" class="link">Кошчето е пълно ('.round($rec_size / 1024, 2).' MB)</a></td>
				</tr>
				<tr style="height: 15px;"><td></td><td></td><td></td></tr>
			';
		}
	}
?>

</table>