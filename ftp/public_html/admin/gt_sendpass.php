<?php
	include_once '_connect.php';
	@include_once '_cpl_active.php';
	include_once PATH_ENGINE.'func_showmsg.php';
	include_once SYS_USERS;
	include_once SYS_MAIL;

	if (
		(defined('SITE_CPL_FREEZE') && SITE_CPL_FREEZE === TRUE) ||
		(!defined('SITE_CPL_ERROR') || SITE_CPL_ERROR === TRUE)
	) {
		//code when CPL is down
		jump_to(URL_ADMIN.'index.php', 'gt_sendpass.php: site is locked/down');
	}
	 else
	{
		$userdata = user_get_by_mail($_POST['username'], $_POST['email']);
		if ($userdata === FALSE) {
			showmsg('missing_mail', URL_ADMIN.'index.php?page=lostpass');
			exit();
		}

		//Authentication OK

		//Send mail message
		$mailmsg =
"
-------------------- POTREBITELSKI DANNI -------------------

Potrebitelsko ime: ".$userdata['username']."\n
Parola: ".$userdata['password']."\n
Statut: ".$userdata['credentials']."\n

Tova sa dannite za autentikacia na vashiq akaunt.\n
Molq, pazete (nai-dobre iztriite sled prochitane) tova pismo.

------------------------------------------------------------
";
		
		send_mail($userdata['email'], 'zabravena parola', $mailmsg);
		showmsg('password_sent', URL_ADMIN.'index.php');
	}
?>