<?php
	# uses sys_debug.php!
	function regex_clb($in) {
		echo textarea('PROCESSING...', var_export($in, true));
	}
	
	function regex_test($regex, $txt) {
		textarea('INPUT', $txt);
		
		$res = preg_replace_callback($regex, 'regex_clb', $txt);
		textarea('OUTPUT', $res);
	}

?>