<?php
	include_once '_settings.php';
	include_once 'sys_debug.php';
	include_once 'sys_dbc.php';
	include_once 'sys_mail.php';
	include_once 'func_passgen.php';
	
	define('USER_OK', 0);
	define('USER_INVALID_NAME', 1);
	define('USER_INVALID_PASS', 2);
	define('USER_INVALID_MAIL', 3);
	define('USER_INVALID_CREDENTIALS', 4);
	define('USER_INVALID_TITLE', 5);

	define('USER_TITLE_TOO_LONG', 16);

	define('USER_ALREADY_EXISTS', 32);
	define('USER_NOT_FOUND', 33);

	define('USER_CANNOT_SEND_MAIL', 40);
	
	define('USER_ADD_OK', 48);
	define('USER_ADD_FAILED', 49);
	
	define('USER_ACTIVATE_OK', 64);
	define('USER_ACTIVATE_FAILED', 65);
	define('USER_ACTIVATE_ALREADY_SENT', 66); //activation already sent
	define('USER_ACTIVATE_HIJACK', 67);
	
	define('USER_EDIT_OK', 80);
	define('USER_EDIT_FAILED', 81);

	define('USER_NAME_VALIDCHARS', '/[^A-Za-z0-9_]/');
	define('USER_PASS_VALIDCHARS', '/[^A-Za-z0-9_$# ]/');
	
	function user_verify($username, $password, $active_only = true) {
		
		if (!is_string($username) || !is_string($password))
			return false; //i.e. passed bad data
		
		if ($username == '' || $password == '')
			return false; //i.e. passed bad data, in this case: empty username/password
		
		if (strlen($username) > USER_NAME_MAX_LENGTH || strlen($password) > USER_PASS_MAX_LENGTH)
			return false; //i.e. passed bad data, in this case: too long username/password
		
		$res = query('select * from `um_main` where `username` = "'.mysql_real_escape_string($username).'" AND `password` = "'.mysql_real_escape_string($password).'";');
		
		if ($res === false)
			trigger_error('', E_USER_ERROR);
		
		if (mysql_num_rows($res) != 1)
			return false; //i.e. username and pass don't match
		
		$res = mysql_fetch_assoc($res);
		
		if ($active_only && $res['active'] == false)
			return false; //i.e. user not yet activated
		
		return $res; //all is OK
	}
	
	function user_verify_name($username) {
		if (strlen($username) < USER_NAME_MIN_LENGTH || strlen($username) > USER_NAME_MAX_LENGTH)
			return false;
		
		if (preg_match(USER_NAME_VALIDCHARS, $username))
			return false;
		
		return true;
	}
	
	function user_get($username) {
		
		if (!is_string($username))
			return false; //i.e. passed bad data
		
		if ($username == '')
			return false; //i.e. passed bad data, in this case: empty username/mail
		
		if (strlen($username) > USER_NAME_MAX_LENGTH)
			return false; //i.e. passed bad data, in this case: too long username
		
		$res = query('select * from `um_main` where `username` = "'.mysql_real_escape_string($username).'";');
		if ($res === false)
			trigger_error('', E_USER_ERROR);
		
		if (mysql_num_rows($res) != 1)
			return false; //i.e. username and pass don't match
		
		$user_main = mysql_fetch_assoc($res);
		
		$res = query('select * from `um_details` where `username` = "'.mysql_real_escape_string($username).'";');
		if ($res === false)
			trigger_error('', E_USER_ERROR);
		
		if (mysql_num_rows($res) != 1)
			return false; //i.e. username and pass don't match
		
		$user_details = mysql_fetch_assoc($res);
		
		return array_merge($user_main, $user_details); //all is OK
	}
	
	function user_get_by_mail($username, $email) {
		
		if (!is_string($username) || !is_string($email))
			return false; //i.e. passed bad data
		
		if ($username == '' || $email == '')
			return false; //i.e. passed bad data, in this case: empty username/mail
		
		if (strlen($username) > USER_NAME_MAX_LENGTH)
			return false; //i.e. passed bad data, in this case: too long username
		
		if (!check_email_address($email))
			return false;

		$res = query('select * from `um_main` where `username` = "'.mysql_real_escape_string($username).'" AND `email` = "'.mysql_real_escape_string($email).'";');
		if ($res === false)
			trigger_error('', E_USER_ERROR);

		if (mysql_num_rows($res) != 1)
			return false; //i.e. username and pass don't match
		
		return mysql_fetch_assoc($res); //all is OK
	}
	
	function user_add($username, $email, $credentials, $title = 'потребител') {
		if (strlen($username) < USER_NAME_MIN_LENGTH || strlen($username) > USER_NAME_MAX_LENGTH)
			return USER_INVALID_NAME;
		
		if (preg_match(USER_NAME_VALIDCHARS, $username))
			return USER_INVALID_NAME;
		
		if (!check_email_address($email))
			return USER_INVALID_MAIL;
			
		if (
				$credentials != 'admin' &&
				$credentials != 'editor' &&
				$credentials != 'viewer'
			)
			
			return USER_INVALID_CREDENTIALS;
			
		if (strlen($title) > 255)
			return USER_TITLE_TOO_LONG;
			
		if ($title == '')
			$title = 'потребител';
			
		if (!query('insert into `um_main` (`username`, `password`, `email`, `credentials`) VALUES ("'.$username.'", "'.$password.'", "'.$email.'", "'.$credentials.'");'))
		{ # warning: returns an error if username exists!
			return USER_ALREADY_EXISTS;
		}
		
		if (!query('insert into `um_details` (`username`, `title`) VALUES ("'.$username.'", "'.$title.'");'))
		{
			$my_error = 'User `'.$username.'` was added to `um_main` but could not be added to user_details: '.mysql_errno().': '.mysql_error();
			if (!query('DELETE FROM `um_main` WHERE `username` = "'.$username.'";'))
				$my_error .= "\r\n".'Could not delete user `'.$username.'` from `um_main`: '.mysql_errno().': '.mysql_error();
		
			trigger_error($my_error, E_USER_ERROR);
			return USER_ADD_FAILED;
		}
		
		//create necessary dirs
		mkdir(PATH_USERS.$username);
		chmod(PATH_USERS.$username, 0777);
		
		//send activation
		$res = user_send_activation($username, $email);
		if ($res != USER_OK)
			return $res;
		
		return USER_ADD_OK;
	}
	
	function user_send_activation($username, $email) {
		//send mail with pass for activation
		$password = user_make_pass($username); //this also UPDATES the password value for the user!
		$act_id = user_prepare_user_activation($username);
		

		//Send mail message
		$mailmsg = "
		----------------- POTREBITELSKI DANNI ----------------
		
		Potrebitelsko ime: ".$username."
		Parola: ".$password."
		Statut: ".$credentials."
		
		Parolata e sazdadena avtomatichno, ako jelaete,
		mojete da ia smenite ot nastroikite za profila.
		
		Predi da izpolzvate akaunta, e nujno da go activirate.
		Za tazi cel, molia, click-nete varhu dolnia link
		ili go vavedete v saotvetnoto pole na browser-a.
		
		------------------------------------------------------
		
		Link za ACTIVACIA:
		".URL_SITE."activate.php?uuid=".$act_id."
		
		";
	
	
		if (send_mail($email, 'activacia', $mailmsg) != MAIL_SEND_OK)
			return USER_CANNOT_SEND_MAIL;
		
		return USER_OK;
	}
	
	function user_remove($username) {
		lock_tables('um_main', 'um_details');

		$user_list = explode(';', $username);
		$user_valid_list = array();
		
		for ($i = 0; $i < count($user_list); $i++)
		{
			if (strlen($user_list[$i]) < USER_NAME_MIN_LENGTH || strlen($user_list[$i]) > USER_NAME_MAX_LENGTH)
			{
				unlock_tables();
				return USER_INVALID_NAME;
			}
			
			if (preg_match(USER_NAME_VALIDCHARS, $user_list[$i]))
			{
				unlock_tables();
				return USER_INVALID_NAME;
			}
			
			if (!($res = query('SELECT * FROM `um_main` WHERE `username` = \''.$user_list[$i].'\';')))
				trigger_error('Could not retrieve user in `um_main`.', E_USER_ERROR);
			
			if (mysql_num_rows($res) == 1 && $user_list[$i] != $_SESSION['username']) #one can't delete themselves can they?
			{
				#add to list with valid users (existing ones)
				$user_valid_list[] = $user_list[$i];
			}
		}
		
		for ($i = 0; $i < count($user_valid_list); $i++)
		{
			if (!query('
					DELETE FROM `um_main`, `um_details`
						USING `um_main` LEFT JOIN `um_details`
							ON `um_main`.`username` = `um_details`.`username`
					WHERE `um_main`.`username` = \''.$user_valid_list[$i].'\''))
				trigger_error('Could not delete user `'.$user_valid_list[$i].'`', E_USER_ERROR);
			
			# delete files;
			$files = glob(PATH_USERS.$user_valid_list[$i].'/*.*');
			for ($j = 0; $j < count($files); $j++)
				@unlink($files[$j]);
			
			#remove dir
				@rmdir(PATH_USERS.$user_valid_list[$i].'/');
		}
		
		unlock_tables();
		return USER_OK;
	}
	
	function user_edit($username, $password, $email, $credentials = '', $active, $name, $title, $message, $aim) {
		if (strlen($username) < USER_NAME_MIN_LENGTH || strlen($username) > USER_NAME_MAX_LENGTH)
			return USER_INVALID_NAME;
		
		if (preg_match(USER_NAME_VALIDCHARS, $username))
			return USER_INVALID_NAME;
		
		if ($password != '')
		{
			if (strlen($password) < USER_PASS_MIN_LENGTH || strlen($password) > USER_PASS_MAX_LENGTH)
				return USER_INVALID_PASS;
			
			if (preg_match(USER_PASS_VALIDCHARS, $password))
				return USER_INVALID_PASS;
		}
		
		if (!check_email_address($email))
			return USER_INVALID_MAIL;
			
		if (
				$credentials != 'admin' &&
				$credentials != 'editor' &&
				$credentials != 'viewer' &&
				$credentials != ''
			)
			
			return USER_INVALID_CREDENTIALS;
			
		if (strlen($title) > 255)
			return USER_TITLE_TOO_LONG;
			
		if ($title == '')
			$title = 'потребител';
		
		//Lock tables
		lock_tables('um_main', 'um_details');
		
		if (!($res = query('SELECT * FROM `um_main` WHERE `username` = \''.$username.'\';')))
			trigger_error('Could not retrieve user in `um_main`.', E_USER_ERROR);
		
		if (mysql_num_rows($res) != 1)
			return USER_NOT_FOUND;
			
		if (!query('
				update `um_main` SET '.($password != '' ? '`password` = \''.$password.'\', ' : '').'`email` = \''.$email.'\', '.($credentials != '' ? '`credentials` = \''.$credentials.'\',' : '').'
				`active` = \''.($active ? '1' : '0').'\' WHERE `username` = \''.$username.'\';
		'))
			trigger_error('`um_main` could not be updated.', E_USER_ERROR);

		if (!($res = query('SELECT * FROM `um_details` WHERE `username` = \''.$username.'\';')))
			trigger_error('Could not retrieve user in `um_details`', E_USER_ERROR);
		
		if (mysql_num_rows($res) != 1)
			trigger_error('User `'.$username.'` exists in `um_main` but not in `um_details`', E_USER_ERROR);
		
		if (!query('
			update `um_details` SET `name` = \''.$name.'\''.($title != '' ? ', `title` = \''.$title.'\'' : '').', `message` = \''.$message.'\', `aim` = \''.$aim.'\' WHERE `username` = \''.mysql_real_escape_string($username).'\';
		'))
			trigger_error('`um_details` could not be updated.', E_USER_ERROR);
			
		unlock_tables();
		return USER_EDIT_OK;
	}
	
	function user_make_pass($username) {
		if (strlen($username) < USER_NAME_MIN_LENGTH || strlen($username) > USER_NAME_MAX_LENGTH)
			return USER_INVALID_NAME;
		
		if (preg_match(USER_NAME_VALIDCHARS, $username))
			return USER_INVALID_NAME;
		
		$password = generate_password(16, 5);
		
		lock_tables('um_main');
		if (!($res = query('SELECT `username` FROM `um_main` WHERE `username` = \''.$username.'\';')))
			trigger_error('Could not access user`'.$username.'` when trying to update password.', E_USER_ERROR);
		
		if (mysql_num_rows($res) != 1)
			return USER_NOT_FOUND;
		
		$res = query('UPDATE `um_main` SET `password` =  "'.$password.'" WHERE `username` = "'.$username.'";');
		if ($res === FALSE)
			trigger_error('Could not update password for `'.$username.'`.', E_USER_ERROR);
		
		unlock_tables();
		return $password;
	}
	
	function user_prepare_user_activation($username) {
		if (strlen($username) < USER_NAME_MIN_LENGTH || strlen($username) > USER_NAME_MAX_LENGTH)
			return USER_INVALID_NAME;
		
		if (preg_match(USER_NAME_VALIDCHARS, $username))
			return USER_INVALID_NAME;

		$res = query('select * from `um_main` where `username` = "'.$username.'";');
		
		if ($res === FALSE)
			trigger_error('', E_USER_ERROR);
		
		if (mysql_num_rows($res) != 1) //i.e. 0
			return USER_NOT_FOUND;
			
		if (!query('INSERT INTO `um_activation` (`id`, `username`) VALUES (UUID(), "'.$username.'");'))
			return USER_ACTIVATE_ALREADY_SENT; //activation already sent.
		
		$res = query('SELECT * FROM `um_activation` WHERE `username` = "'.$username.'";');
		
		if ($res === FALSE)
			trigger_error('', E_USER_ERROR);
		
		if (mysql_num_rows($res) != 1) //i.e. 0
			return USER_ACTIVATE_FAILED;
		
		$act_id = mysql_fetch_assoc($res);
		$act_id = $act_id['id'];
		
		return $act_id;
	}
	
	function user_activate_by_request($uuid) { //used only by activate.php, i.e. by REQUEST!
		//tame the uuid
		$uuid = mysql_real_escape_string($uuid);
		
		//find corresponding username
		lock_tables('um_main', 'um_activation');
		$res = query('SELECT `username` FROM `um_activation` WHERE `id` = "'.$uuid.'";');
		
		if (!$res)
			trigger_error('Could not select user for activation with the following uuid: '.$uuid, E_USER_ERROR);
		
		if (mysql_num_rows($res) != 1) //i.e 0
			return USER_ACTIVATE_HIJACK; //activation request not there, probably hijack attempt
			
		$row = mysql_fetch_assoc($res);
		$username = $row['username'];
		
		//activate user
		if (!query('UPDATE `um_main` SET `active` = true WHERE `username` = "'.$username.'";'))
			trigger_error('Could not de/activate user `'.$username.'`', E_USER_ERROR);
		
		//delete activation request
		if (!query('DELETE FROM `um_activation` WHERE `id` = "'.$uuid.'";'))
			trigger_error('Could not delete activation request', E_USER_ERROR);
		
		//all done!
		unlock_tables();
		return USER_ACTIVATE_OK;
	}
	
	function user_activate($username, $active) {
		if (strlen($username) < USER_NAME_MIN_LENGTH || strlen($username) > USER_NAME_MAX_LENGTH)
			return USER_INVALID_NAME;
		
		if (preg_match(USER_NAME_VALIDCHARS, $username))
			return USER_INVALID_NAME;
		
		if ($username != $_SESSION['username']) #one can't deactivate themselves, can they?
		{
			lock_tables('um_main');
			
			if (!query('UPDATE `um_main` SET `active` = '.$active.' WHERE `username` = \''.$username.'\';'))
				trigger_error('Could not de/activate user `'.$username.'`.', E_USER_ERROR);
			
			unlock_tables();
		}
		return USER_ACTIVATE_OK;
	}
	
	function user_send_mail_to_all($user_list, $caption, $msg) {
		
		for ($i = 0; $i < count($user_list); $i++)
		{
			if (!user_verify_name($user_list[$i]))
			{
				#echo 'not OK: '.$user_list[$i];
				return USER_INVALID_NAME;
			}
			$user_list[$i] = '`username` = \''.$user_list[$i].'\'';
		}	
		
		# Get user's mail
		if (!($res = query('SELECT `email` FROM `um_main` WHERE `username` = \''.$_SESSION['username'].'\';')))
			trigger_error('Could not select the mail of `'.$_SESSION['username'].'`', E_USER_ERROR);
		
		$row = mysql_fetch_assoc($res);
		
		if (mysql_num_rows($res) != 1)
			trigger_error('Controller\'s mail not found!', E_USER_ERROR);
		
		$user_mail = $row['email'];
		
		#fix message
		$msg = "Izvestie za obnovena informacia v `".$caption."`\r\n------------------------------------------------------------\r\n\r\n".$msg;
		
		#retrieve recepients mails
		if (count($user_list) > 0)
		{
			if (!($res = query('SELECT `email` FROM `um_main` WHERE '.implode(' OR ', $user_list).';')))
				trigger_error('Could not select the mail of the users', E_USER_ERROR);
			
			$mails = array();
			
			while($row = mysql_fetch_assoc($res))
				$mails[] = $row['email'];
			
			#echo 'sending from `'.$user_mail."`:\n";
			for ($i = 0; $i < count($mails); $i++)
				#echo 'to `'.$mails[$i]."`\n";
				if (send_mail($mails[$i], 'Obnovena informacia: '.$caption, $msg, $user_mail) != MAIL_SEND_OK)
					return USER_CANNOT_SEND_MAIL;
		}
		
		return USER_OK;
	}
	
	function flush_requests() {
		
		lock_tables('um_main', 'um_details', 'um_activation');
		
		# get users who have left an activation request
		if (!($res = query('SELECT `username` FROM `um_activation`;')))
			trigger_error('Could not browse activations', E_USER_ERROR);
		
		$users_with_request = array();
		while ($row = mysql_fetch_assoc($res))
			$users_with_request[] = $row['username'];
		
		# flush requests
		if (!query('DELETE FROM `um_activation` WHERE 1;'))
			trigger_error('Could not flush user requests', E_USER_ERROR);
		
		if (!query('OPTIMIZE TABLE `um_activation`'))
			trigger_error('Could not re-optimize table', E_USER_ERROR);
		
		if (count($users_with_request) > 0)
		{
			for ($i = 0; $i < count($users_with_request); $i++)
				$users_with_request[$i] = '`username` = \''.$users_with_request[$i].'\'';
			
			# get users who also are NOT ACTIVE!
			if (!($res = query('
							SELECT `username` FROM `um_main`
							WHERE `active` = 0 AND (
							'.implode(' OR ', $users_with_request).'
							);')))
				trigger_error('Could not filter users for deletion', E_USER_ERROR);
			
			while($row = mysql_fetch_assoc($res))
				user_remove($row['username']);
		}
		
		unlock_tables();
		
		return USER_OK;
	}
?>