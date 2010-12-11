<?php
	include_once SYS_DB_CONNECT;
	
	function showmsg($msgid, $returnpage = 'index.php', $msginfo = '')  {
		unlock_tables();
		if ($returnpage == '')
			$returnpage = 'index.php';

		setcookie('aim_msgid', $msgid, 0, PATH_MAIN, (COOKIE_DOMAIN ? '.' : '').DOMAIN);
		setcookie('aim_returnpage', $returnpage, 0, PATH_MAIN, (COOKIE_DOMAIN ? '.' : '').DOMAIN);
		setcookie('aim_msginfo', $msginfo, 0, PATH_MAIN, (COOKIE_DOMAIN ? '.' : '').DOMAIN);
		jump_to(URL_SITE.'inform.php');
	}
?>