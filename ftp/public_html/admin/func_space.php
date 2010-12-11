<?php
	include_once '_connect.php'; # connect to engine
	include_once SYS_DB_CONNECT; # connect to DB
	
	function usedspace($barwidth) {
		//space is in bytes
		
		if (!defined('SITE_SPACE'))
			trigger_error('Site free space not specified in _settings.php', E_USER_ERROR);
		
		$maxspace = SITE_SPACE; # in MB
		$staticspace = 4; #in MB; space, occupied by static site-data as scripts, etc.
		
		//count the space used by the DB
		if (!($res = query('select table_schema "db",	sum( data_length + index_length ) "size", sum( data_free ) "free" from information_schema.tables group by table_schema')))
			trigger_error('Could not calc db space', E_USER_ERROR);

		$dbspace= 0;

		while ($row = mysql_fetch_assoc($res))
		{
			$dbspace += $row['size'] + $row['free'];
		}
		
		$dbspace = round($dbspace/(1024*1024)); #in MB now
		
		if (!($res = query('SELECT SUM(size) size FROM `cm_struct_main` WHERE `mode` > 2'))) // 2 == DELETED
			trigger_error('Could not calc pages space', E_USER_ERROR);
		
		$row = mysql_fetch_assoc($res);
		$pgspace = ($row['size'] == NULL ? 0 : $row['size']); #in KB!
		$pgspace = round($pgspace/1024); #in MB now
		
		if (!($res = query('SELECT SUM(size) size FROM `cm_struct_main` WHERE `mode` <= 2'))) // 2 == DELETED
			trigger_error('Could not calc recylcle bin space', E_USER_ERROR);
		
		$row = mysql_fetch_assoc($res);
		$recyclespace = ($row['size'] == NULL ? 0 : $row['size']); # in KB!
		$recyclespace = round($recyclespace/1024); #in MB now
		$recycle_full = ($row['size'] == NULL ? false : true);
		
		$usedspace = $dbspace + $pgspace + $staticspace; #in MB
		
		$leftpart = ceil(($usedspace/$maxspace)*($barwidth -4));
		$used_prc = ceil(($usedspace/$maxspace)*100);
		
		$centerpart = floor(($recyclespace/$maxspace)*($barwidth-4));
		$recycle_prc = ceil(($recyclespace/$maxspace)*100);
		
		$rightpart = ($barwidth-4) - ($leftpart + $centerpart);
		$free_prc = 100 - ($used_prc + $recycle_prc);
		
		$overspace = '';
		if ($free_prc <= 10)
		{
			$overspace = '
				<tr>
				<td align="center" class="common" style="color: #FF3300;">
				ВНИМАНИЕ! Повече от 90% от наличното пространство е заето!<br>
				Препоръчително е освобождаването на място или закупуването на такова!<br>
				Моля, обърнете се към администратора за съвет: <a href="mailto:'.MAIL_WEBMASTER_ADDRESS.'" class="link">'.MAIL_WEBMASTER_ADDRESS.'</a>
			';
		}
		
		return
		array ('
				<table style="width: '.$barwidth.'px;" align="center" cellspacing="0" cellpadding="0" title="Използвано '.($usedspace+$recyclespace).'MB от '.$maxspace.'MB">
					<tr>
						<td align="left">
							<div style="width: '.$leftpart.'px; text-align: right;" class="common">
								'.$used_prc.'%&nbsp;използвано
							</div>
						</td>
					</tr>
					<tr>
						<td align="left">
							<img src="./gfx/icons/spc_greybar.bmp" width="1" height="6"><img src="./gfx/icons/spc_whitebar.bmp" width="1" height="6"><img src="./gfx/icons/spc_bluebar.bmp" width="'.$leftpart.'" height="6"><img src="./gfx/icons/spc_redbar.bmp" width="'.$centerpart.'" height="6"><img src="./gfx/icons/spc_whitebar.bmp" width="'.$rightpart.'" height="6"><img src="./gfx/icons/spc_whitebar.bmp" width="1" height="6"><img src="./gfx/icons/spc_greybar.bmp" width="1" height="6">
						</td>
					<tr>
						<td align="left">
							<div style="width: '.($leftpart + $centerpart).'px; text-align:right;" class="common">
								'.$recycle_prc.'%&nbsp;кошче
							</div>
						</td>
					</tr>
					'.$overspace.'
				</table>
		',
			$recycle_full
		);
	}
?>