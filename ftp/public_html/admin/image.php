<?php
	
	include '_connect.php';
	
	switch($_GET['type'])
	{
		case 'p': #preview
			$type = 'preview';
			break;
		
		case 't': #thumbnail
			$type = 'thumb';
			break;
		
		case 's': #small
			$type = 'small';
			break;
		
		case 'i': #image
			$type = 'image';
			break;
		
		default:
			$type = 'preview';
			break;
	}
	
	$found = false;
	if (id_verify($_GET['id']))
		if (file_exists(PATH_PAGES.$_GET['id'].'/'.$type.'.jpg'))
			$found = true;
	
	include PATH_ENGINE.'func_sendfile.php';
	if ($found)
		send_file(PATH_PAGES.$_GET['id'].'/'.$type.'.jpg', $type.'.jpg', 'image/jpeg', false, false);
	else
		send_file(PATH_ADMIN.'gfx/icons/no-image.gif', 'no-image.gif', 'image/gif', false, false);
?>