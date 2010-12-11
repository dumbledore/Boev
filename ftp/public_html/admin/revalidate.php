<?php
	function my_get_to_string() {
		$res = '';
		$keys = array_keys($_GET);
		for ($i = 0; $i < count($keys); $i++)
		{
			if ($keys[$i] != 'page')
				$res .= '&'.$keys[$i].'='.$_GET[$keys[$i]];
		}
		return $res;
	}
	
	function remember_data($page, $isgate) {
		if (!is_bool($isgate))
			return false;
			
		if (
			$_SESSION['rev_isgate'] === TRUE &&
			$_SESSION['rev_page'] === $page &&
			isset($_SESSION['rev_data'])
		)
			$data_already_set = TRUE;
		else
			$data_already_set = FALSE;
		
		$_SESSION['rev_active'] = true;
		$_SESSION['rev_page'] = $page;
		$_SESSION['rev_isgate'] = $isgate;
		
		if (!$data_already_set) //prevent replacing the data with NULL (if something unusual happens)
			$_SESSION['rev_data'] = array('get' => my_get_to_string(), 'post' => $_POST);

		return true;
	}
	
	if (!defined('REV_INLINE')) //i.e. not used for inclusion
	{	
		include_once '_connect.php';
		@include_once '_cpl_active.php';
		include_once SYS_AUTHENTICATION;
		if (
			(defined('SITE_CPL_FREEZE') && SITE_CPL_FREEZE === TRUE) ||
			(!defined('SITE_CPL_ERROR') || SITE_CPL_ERROR === TRUE)
		) {
			//code when CPL is down
			jump_to(URL_ADMIN.'index.php', 'revalidate.php: site locked/down');
		}
		else
		{
			if (!isset($_POST['password']))
			{ //show form

				$sess_state = check_session();
				if ($sess_state == SESS_UNAVAILABLE
					|| $sess_state >= SESS_FALSE_AUTHENCITY) //e.g. SESS_INVALID_PASSWORD, etc.
				{
					kill_session();
					jump_to(URL_ADMIN.'index.php', 'revalidate.php: form view | authentication failed');
					exit();
				}
				
				if ($sess_state == SESS_KILLED_BY_GC)
				{
					exit_session();
					jump_to(URL_ADMIN.'index.php', 'revalidate.php: form view | session killed by the garbage collector');
					exit();
				}
				
				if ($sess_state == SESS_KILLED)
				{
					exit_session();
					jump_to(URL_ADMIN.'index.php', 'revalidate.php: form view | session killed');
					exit();
				}
				
				if (!isset($_SESSION['rev_active']) || $_SESSION['rev_active'] !== TRUE)
				{ //someone is loading the page for their own pleasure
					exit_session();
					jump_to(URL_ADMIN.'index.php', 'revalidate.php: form view | loading page for own pleasure');
					exit();
				}
			
				define('ALLOW_EXECUTION', TRUE);
				
				$layout_title = 'ревалидиране';
				$layout_onload = 'document.gates.password.focus();';
				include 'layout_header.php';

				switch ($sess_state) {
					case SESS_INVALID_BROWSER:
						$sess_err_msg = 'Ползвате браузър, различен от предишния.';
						break;
						
					case SESS_EXPIRED:
						$sess_err_msg =	'Минало е твърде дълго време от<br>последната ви заявка към сървъра.';
						break;
					
					case SESS_NOT_IN_CPL:
						$sess_err_msg = 'Разглеждали сте сайта, като сте оставили административния панел отворен.';
						break;
						
					default:
						$sess_err_msg = '';
						break;
				}
				
				echo '
					<table align="center" cellspacing="0" cellpadding="0">
					<tr valign="middle">
					<td><img src="gfx/icons/msg_warn.png" alt="внимание"></td>
					<td style="width:20px;"></td>
					<td class="common">
					'.$sess_err_msg.'<br><br>
						Моля, въведете отново паролата за вашия акаунт,<br>
						за да сме сигурни, че чужд човек не се възползва от него.
					</td>
					</tr>
					</table>
					<br>
					<form action="revalidate.php" method="post" name="gates">
					<table style="width: 250px;" align="center" cellspacing="0" cellpadding="0">
					<tr valign="middle">
					<td class="formlabel">потребител:</td>
					<td style="width: 15px;"></td>
					<td>'.$_SESSION['username'].'</td>
					</tr>
					<tr style="height: 15px;"><td></td><td></td><td></td></tr>
					<tr valign="middle">
					<td class="formlabel">парола:</td>
					<td></td>
					<td><input type="password" name="password" class="formfield"></td>
					</tr>
					</table>
					<br><input type="image" alt="ВХОД" src="./gfx/icons/btn_enter.png">
					</form>
				';
				include 'layout_footer.php';
			}
			 else
			{	//used for THE revalidation, i.e. as a GATE
				include_once PATH_ENGINE.'func_showmsg.php';
				
				//authenticate user
				$sess_state = regenerate_session($_POST['password']);
				if ($sess_state == SESS_UNAVAILABLE
					|| $sess_state >= SESS_HIJACK_ATTEMPT) //e.g. SESS_INVALID_IP, etc.
				{
					kill_session();
					jump_to(URL_ADMIN.'index.php', 'revalidate.php: gates view | authentication failed');
					exit;
				}
				
				if ($sess_state == SESS_KILLED_BY_GC)
				{
					exit_session();
					jump_to(URL_ADMIN.'index.php', 'revalidate.php: gates view | session killed by the garbage collector');
					exit();
				}
				
				if ($sess_state == SESS_KILLED)
				{
					exit_session();
					jump_to(URL_ADMIN.'index.php', 'revalidate.php: gates view | session killed');
					exit();
				}
				
				if ($sess_state == SESS_INVALID_PASSWORD)
				{
					//try again
					showmsg('invalid_recheck', URL_ADMIN.'revalidate.php');
					exit;
				}

				if (!isset($_SESSION['rev_active']) || $_SESSION['rev_active'] !== TRUE)
				{ //someone is loading the page for their own pleasure
					exit_session();
					jump_to(URL_ADMIN.'index.php', 'revalidate.php: gates view | loading page for own pleasure');
					exit;
				}
				
				//Then session has been verified and regenerated
				unset($_SESSION['rev_active']); //executed ONLY if all is OK
				jump_to(URL_ADMIN.(($_SESSION['rev_isgate']) ? 'gates' : 'main').'.php?page='.$_SESSION['rev_page'].$_SESSION['rev_data']['get'], 'revalidate.php: gates view | revalidation OK');
			}
		}
	}
?>