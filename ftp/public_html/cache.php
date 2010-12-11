<?php
	include '_connect.php';
	error_reporting(E_ALL);
	
	$mf = PATH_SITE.$_GET['id'];

	if (is_file($_GET['id']) && is_file($mf) && file_exists($mf))
	{
		$file = fopen($mf, "r");
		$content = fread($file, filesize($mf));
		fclose($file);
		
		header('Cache-Control: max-age='.(3600 * 24 * 365).', must-revalidate');
		//header('Expires: Tue, 30 Oct 2018 14:19:41 GMT');

		header('Content-Length: '.filesize($mf));
		header('Content-Type: '.mime_content_type($mf));
		echo $content;
	}
?>