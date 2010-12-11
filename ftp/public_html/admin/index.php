<?php
	include_once '_connect.php';
	@include_once '_cpl_active.php';
	
	
	if (
		(defined('SITE_CPL_FREEZE') && SITE_CPL_FREEZE === TRUE) ||
		(!defined('SITE_CPL_ERROR') || SITE_CPL_ERROR === TRUE) # if there is error
	) {
		//code when CPL is down
		define('ALLOW_EXECUTION', TRUE);
		include 'incl_cpl_error.php';
		include 'layout_footer.php';
	}
	 else
	{
		if (!isset($_GET['page']) || !file_exists('incl_'.$_GET['page'].'.php'))
			$clear['page'] = 'login';
		else
			$clear['page'] = $_GET['page'];
		
		// allow execution of included files
		// and prevent their execution from outside
		define('ALLOW_EXECUTION', TRUE);
		
		//Start output buffering
		ob_start();
		
		// INCLUSION
			// layout_header is included in incl_page
			include 'incl_'.$clear['page'].'.php';
			include 'layout_footer.php';
		// INCLUSION
		
		ob_end_flush();
		
	}
?>