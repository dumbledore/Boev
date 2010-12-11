<?php
	include_once '_connect.php';
	include_once SYS_ALLOW_EXECUTION;
?>

<br>
</td></tr></table>
<table style="width: 100%; height: 2px;" align="center" bgcolor="#AAAAAA" cellspacing="0" cellpadding="0"><tr><td></td></tr></table>

<?php
	if (defined('LOGGED') && LOGGED === TRUE)
	{
		echo '<br><table cellspacing="0" cellpadding="0" class="common"><tr valign="middle">';

		echo '
				<td><img src="'.URL_ADMIN.'gfx/icons/user.gif" alt=""></td>
				<td style="width: 8px;"></td>
				<td>Потребител: <span style="color: #FF6600;">'.$_SESSION['username'].'</span></td>
				<td style="width: 20px;"></td>
			';
		
		if ($clear['page'] != 'checkpoint')
		{
			echo'
				<td><img src="'.URL_ADMIN.'gfx/icons/back.gif" alt=""></td>
				<td style="width: 8px;"></td>
				<td><a href="main.php" class="link">ГЛАВНО МЕНЮ</a></td>
				<td style="width: 20px;"></td>
			';
		}

		if ($clear['page'] == 'cm_browse')
		{
			if ($used_space[1]) # recycle full?
				echo'
					<td><img src="'.URL_ADMIN.'gfx/icons/rec_full_sm.png" alt=""></td>
					<td style="width: 8px;"></td>
					<td><a href="'.URL_ADMIN.'main.php?page=cm_trash" class="link">КОШЧЕ</a></td>
					<td style="width: 20px;"></td>
				';
			else
				echo'
					<td><img src="'.URL_ADMIN.'gfx/icons/rec_empty_sm.png" alt=""></td>
					<td style="width: 8px;"></td>
					<td><a href="'.URL_ADMIN.'main.php?page=cm_trash" class="link">КОШЧЕ</a></td>
					<td style="width: 20px;"></td>
				';
		}
		
		if ($clear['page'] == 'cm_trash')
		{
			echo'
				<td><img src="'.URL_ADMIN.'gfx/icons/document.gif" alt=""></td>
				<td style="width: 8px;"></td>
				<td><a href="'.URL_ADMIN.'main.php?page=cm_browse" class="link">СЪДЪРЖАНИЕ</a></td>
				<td style="width: 20px;"></td>
			';
		}
		
		if (SITE_ACCOUNTS >= SITE_ACC_ON) //if the CPL must be connected to the site
		{
			echo'
				<td><img src="'.URL_ADMIN.'gfx/icons/down.gif" alt=""></td>
				<td style="width: 8px;"></td>
				<td><a href="'.URL_SITE.'index.php" class="link">ИЗЛЕЗ ОТ ПАНЕЛА</a></td>
			';
		} else {
			echo '
				<td><img src="'.URL_ADMIN.'gfx/icons/down.gif" alt=""></td>
				<td style="width: 8px;"></td>
				<td><a href="'.URL_SITE.'exit.php?goto=cpl" class="link">ИЗЛЕЗ</a></td>
			';
		}
		echo '</tr></table>';
	}
	
	if (isset($clear['page']))
	{
		if (substr($clear['page'], 0, 3) == 'cm_')
		{
			if ($homepage === NULL) #defined in layout_header
				echo '
					<br>
					<span style="color: #FF0000;" class="common">
						Внимание! Не е зададена начална страница!<br>
						Моля, зададете такава от меню <a href="main.php?page=cm_homepage" class="cm_table_link">НАЧАЛНА СТРАНИЦА</a>
					</span>
				';
			
			echo '<br>'.$used_space[0]; #defined in layout_header.php
		}
	}

?>

</td></tr></table>

<?php
	if (DEBUG === TRUE)
		include PATH_ENGINE.'layout_debug.php';
?>

</body>
</html>