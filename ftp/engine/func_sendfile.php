<?php
	
	function send_file($server_filename, $client_filename, $mime, $send = true, $cacheable = true) {
		$file = fopen($server_filename, "r");
		$content = fread($file, filesize($server_filename));
		fclose($file);
		
		header('Cache-Control: no-cache');
		header('Content-Length: '.filesize($server_filename));
		header('Content-Type: '.$mime);
		if ($send) # Forces save as dialogue
			header('Content-Disposition: attachment; filename="'.$client_filename.'"');
		echo $content;
	}
	
?>