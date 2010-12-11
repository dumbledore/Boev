<?php

	# Common functuonality here
	
	# Verify id's validity whitout checking whether it exists
	function id_verify($id, $folder = false) {
		
		if ($folder && $id == 'menu')
			return true;
		
		if (strlen($id) !== CM_ID_LENGTH)
			return false;
		
		if (preg_match(CM_ID_VALIDCHARS, $id))
			return false;
		
		return true;
	}
	
	function jump_to($location, $msg = '') {
		if (DEBUG === true && $msg != '')
		{
			setcookie('jump_loc', $location, 0, PATH_MAIN, (COOKIE_DOMAIN ? '.' : '').DOMAIN);
			setcookie('jump_msg', $msg, 0, PATH_MAIN, (COOKIE_DOMAIN ? '.' : '').DOMAIN);
			header('Location: '.URL_SITE.'jump.php');
		}
		 else
			header('Location: '.$location);
	}
	
	function no_mgc($in) {
	#can't turn off MGC in the INI, lets then effectively unable it.
		if (get_magic_quotes_gpc($in))
			return stripslashes($in);
		else
			return $in;
	}

	function no_tags($str) {
		return str_replace(array('<', '>'), array('&lt;', '&gt;'), $str);
	}
	
	function panel_working($working) {
		if ($working == true || DEBUG !== TRUE)
		{
			if (!($h = fopen(PATH_ADMIN.'_cpl_active.php','w+')))
			{
				@unlink(PATH_ADMIN.'_cpl_active.php');
				return false;
			}
			 else
			{
				if (!fwrite($h, "<?php\r\n\tdefine('SITE_CPL_ERROR', ".(!$working == true ? 'true' : 'false').");\r\n\t# If `true` then an error has occured and the CPL must be CLOSED\r\n?>"))
				{
					@unlink(PATH_ADMIN.'_cpl_active.php');
					return false;
				}
				fclose($h);
				
				if (!chmod(PATH_ADMIN.'_cpl_active.php', 0777))
					return false;
				
				return true;
			}
		}
	}
?>