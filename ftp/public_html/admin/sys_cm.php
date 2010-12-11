<?php
	include_once '_connect.php';
	include_once SYS_DB_CONNECT;

	#CONTENT MANAGEMENT SECTION
	define('CM_OK', 0);
	define('CM_NOT_FOUND', 1);
	define('CM_INVALID_NAME', 2);
	define('CM_NOT_A_PARENT', 3);
	define('CM_MISSING_DATA', 4);
	define('CM_MISSING_PARENT', 5);
	define('CM_INVALID_INPUT', 6);
	define('CM_PAGE_NOT_EMPTY', 7);
	define('CM_INVALID_TYPE', 8);
	
	#CM_ID_LENGTH is defined in _settings.php
	
	# Raw functions for common elements
	
	# Check if a PAGE/FOLDER exists
	function element_exists($id, $parent = false, $deleted = false) {
		if ($parent && $id == 'menu')
			return true;
			
		if (!id_verify($id))
		{
			# echo 'invalid id';
			return false;
		}
			
		if (!($res = query('SELECT `type` FROM `cm_struct_main` WHERE `id` = \''.$id.'\' and `mode` '.($deleted ? '=' : '>').' 2 LIMIT 1;'))) # 2 == DELETED
			trigger_error('Could not check `'.$id.'` for existence', E_USER_ERROR);
		
		if (mysql_num_rows($res) != 1)
		{
			 # echo 'not found';
			return false;
		}
		
		$row = mysql_fetch_assoc($res);
		if ($parent && $row['type'] != 'page')
		{
			# echo 'cannot be a parent';
			return false;
		}
		return true;
	}
	
	# Get common PAGE data, i.e. from cm_struct_main
	function element_get($id, $fields = NULL, $all_tables = false, $deleted = false) {
		if (!id_verify($id))
			return CM_INVALID_NAME;
		
		$res = query('SELECT '.(is_null($fields) ? '*' : (is_array($fields) ? implode(', ', $fields) : $fields)).' FROM `cm_struct_main` '.
			($all_tables ? '
				LEFT JOIN `cm_struct_search` ON `cm_struct_main`.`id` = `cm_struct_search`.`id`
				LEFT JOIN `cm_struct_view` ON `cm_struct_main`.`id` = `cm_struct_view`.`id`' : '')
		.'WHERE `cm_struct_main`.`id` = \''.$id.'\' AND `mode` '.($deleted ? '=' : '>').' 2;'); //2 == DELETED
		
		if ($res === false)
			trigger_error('Could not select element `'.$id.'` with fields: '.(is_null($fields) ? '*' : (is_array($fields) ? implode(', ', $fields) : $fields)), E_USER_ERROR);
		
		if (mysql_num_rows($res) != 1)
			return CM_NOT_FOUND;
		
		$row = mysql_fetch_assoc($res);
		
		if (!is_null($fields) && !is_array($fields))
			return $row[$fields];
		else
			return $row;
	}
	
	
	# Get the path to a FOLDER/PAGE
	function path_get($id) {
		if ($id == 'menu')
			return array(array('menu'), array('Главно меню'));
		
		$ids = array();
		$names = array();
		
		$working = true;
		$current_id = $id;
		
		while ($working)
		{
			$current = element_get($current_id, array('id', 'caption_bg', 'parent'));
			
			if ($current === CM_INVALID_NAME)
				return CM_INVALID_NAME;
				
			if ($current === CM_NOT_FOUND)
				return CM_NOT_FOUND;
			
			$ids[] = $current['id'];
			$names[] = no_tags($current['caption_bg']);
			$current_id = $current['parent'];
			if ($current_id == 'menu')
			{
				$ids[] =  'menu';
				$names[] = 'Главно меню';
				$working = false;
			}
		}
		
		return array(array_reverse($ids), array_reverse($names));
	}
	
		function get_subscribed($id) {
		if (!id_verify($id))
			return CM_INVALID_NAME;
		
		$subscribers = array();
		
		$working = true;
		$current_id = $id;
		
		while ($working)
		{
			$current = element_get($current_id, array('parent', 'subscribed'), true);
			
			if ($current === CM_INVALID_NAME)
				return CM_INVALID_NAME;
				
			if ($current === CM_NOT_FOUND)
				return CM_NOT_FOUND;
			
			if ($current['subscribed'] != '')
			{
				$current_subscribers = explode(';', $current['subscribed']);
				for ($i = 0; $i < count($current_subscribers); $i++)
				{
					if (!in_array($current_subscribers[$i], $subscribers))
						$subscribers[] = $current_subscribers[$i];
				}
			}
			$current_id = $current['parent'];
			if ($current_id == 'menu')
				$working = false;
		}
		return $subscribers;
	}
	
	# Get last page position in folder
	function parent_get_last_position($id, $deleted = false) {
		if (!($res = query('SELECT MAX(`position`) `position` FROM `cm_struct_main` WHERE `mode` '.($deleted ? '=' : '>').' 2 AND `parent` = \''.$id.'\';')))
			trigger_error('Could not get last page position.', E_USER_ERROR);
		
		$row = mysql_fetch_assoc($res);
		
		if ($row['position'] === NULL)
			return 0;
		
		return $row['position'];
	}
	
	#TREE for moving
	function tree_get_for_moving($exclude = array()) {
		$my_tree_text = '';
		$my_tree_array = array();
		

		if (!($res = query('SELECT `id`, `caption_bg`, `parent` FROM `cm_struct_main` WHERE `mode` > 2 AND `type` = \'page\' ORDER BY `parent`, `position`'))) //2 == DELETED
			trigger_error('Could not select all pages`, E_USER_ERROR');
		
		while($page = mysql_fetch_assoc($res))
		{
			$my_tree_array[$page['parent']][] = array($page['id'], $page['caption_bg']);
		}

		tree_get_for_moving_private($my_tree_array, $my_tree_text, $exclude);

		return $my_tree_text;
	}
	
	function tree_get_for_moving_private(&$from, &$to, $exclude = '', $parent = 'menu', $caption = 'Главно меню', $level = 0, $selected = true) {
		$to .= '<option value="'.$parent.'"'.($selected ? ' selected="selected"' : '').'>'.str_repeat('&nbsp;', $level * 3).'&gt;&nbsp;'.no_tags($caption).'</option>';

		if (isset($from[$parent])) #i.e. the page HAS children
		{
			for ($i = 0; $i < count($from[$parent]); $i++)
			{
				if (!in_array($from[$parent][$i][0], $exclude))
					tree_get_for_moving_private($from, $to, $exclude, $from[$parent][$i][0], $from[$parent][$i][1], $level + 1, false);
			}
		}
	}
	
	#Tree for Sitemap
	function tree_get_for_sitemap() {
		$my_tree_text = '';
		$my_tree_array = array();
		

		if (!($res = query('SELECT `id`, `caption_bg`, `parent`, `type`, `position` FROM `cm_struct_main` WHERE `mode` > 2 ORDER BY `parent`, `position`'))) //2 == DELETED
			trigger_error('Could not select all pages`, E_USER_ERROR');
		
		while($page = mysql_fetch_assoc($res))
		{
			$my_tree_array[$page['parent']][] = array($page['id'], $page['caption_bg'], $page['type'], $page['position']);
		}

		tree_get_for_sitemap_private($my_tree_array, $my_tree_text);

		return $my_tree_text;
	}
	
	function tree_get_for_sitemap_private(&$from, &$to, $parent = 'menu', $caption = 'Главно меню', $type = NULL, $position = NULL, $level = 0) {
		if ($parent == 'menu')
			$to .= '<a href="main.php?page=cm_browse" class="cm_table_link">'.str_repeat('&nbsp;', $level * 3).'&middot;&nbsp;'.no_tags($caption).'</a><br>';
		else
		{
			global $homepage;
			$to .= str_repeat('&nbsp;', $level * 3).'<a href="main.php?page=cm_edit_'.$type.'&id='.$parent.'" class="cm_table_link">&middot;&nbsp;'.$position.'&nbsp;'.no_tags($caption).'</a>&nbsp;('.$type.($homepage == $parent ? ', <span style="color: #FF3300;">начална</span>' : '').')<br>';
		}

		for ($i = 0; $i < count($from[$parent]); $i++)
		{
			if (!in_array($from[$parent][$i][0], $exclude))
				tree_get_for_sitemap_private($from, $to, $from[$parent][$i][0], $from[$parent][$i][1], $from[$parent][$i][2], $from[$parent][$i][3], $level + 1);
		}
	}
	
	# PAGE funcionality
	
	# Get all data for a page (incl. special data)
	function page_get_all($id) {
		global $CM_PAGE_TYPES, $CM_PAGE_TYPES_TABLES;
		
		# fast check for id validity
		if (!id_verify($id))
			return CM_INVALID_NAME;
		
		lock_tables('cm_struct_main', 'cm_struct_search', 'cm_struct_view', $CM_PAGE_TYPES_TABLES);
		#Lock all tables as data is spread around

		#try to see if page is there and if so get type
		if (($type = element_get($id, array('id', 'type'))) == CM_NOT_FOUND)
			return $type; #use array('id','type') to have an array returned. thus one would be sure of the result
		
		#join select
		if (!($res = query('
					SELECT * FROM `cm_struct_main`
						LEFT JOIN `cm_struct_search` ON `cm_struct_main`.`id` = `cm_struct_search`.`id`
						LEFT JOIN `cm_struct_view` ON `cm_struct_main`.`id` = `cm_struct_view`.`id`
						LEFT JOIN `'.$CM_PAGE_TYPES[$type['type']]['table'].'` ON `cm_struct_main`.`id` = `'.$CM_PAGE_TYPES[$type['type']]['table'].'`.`id`
							WHERE `cm_struct_main`.`id` = \''.$id.'\' AND `mode` > 2
				'))) #2 == DELETED
					trigger_error('Cannot join table info for `'.$id.'`', E_USER_ERROR);
		$row = mysql_fetch_assoc($res);
		unlock_tables();
		return $row;
	}
	
	function image_upload($postname, $filename, $maxsize, $restrict_y = true) {
		if (isset($_FILES[$postname])) #If image is uploaded
		{
			if ($_FILES['cm_image']['size'] <= IMG_MAX_SIZE)
			{
				if (($img_size = getimagesize($_FILES[$postname]['tmp_name'])) != false)
				{
					@unlink($filename);
					if ($img_size[0] <= $maxsize && (!$restrict_y || $img_size[1] <= $maxsize) && $img_size[2] == IMAGETYPE_JPEG)
					{
						# no resize or convertion is necessary
						copy($_FILES[$postname]['tmp_name'], $filename);
					}
					 else
					{
						if ($img_size[0] <= $maxsize && (!$restrict_y || $img_size[1] <= $maxsize))
						{
							# no resize necessary, but must convert
							imagejpeg(imagecreatefromstring(file_get_contents($_FILES[$postname]['tmp_name'])), $filename, IMG_QUALITY);
						}
						 else
						{
							# resize
							if (!$restrict_y) # restrict only x
								$ratio = $maxsize / $img_size[0];
							 else
							{
								if ($img_size[0] > $img_size[1])
									$ratio = $maxsize / $img_size[0];
								else
									$ratio = $maxsize / $img_size[1];
							}
							
							$myimg_orig = imagecreatefromstring(file_get_contents($_FILES[$postname]['tmp_name']));
							$myimg_resized = imagecreatetruecolor(round($ratio * $img_size[0]), round($ratio * $img_size[1]));
							imagecopyresampled($myimg_resized, $myimg_orig, 0, 0, 0, 0, round($ratio * $img_size[0]), round($ratio * $img_size[1]), $img_size[0], $img_size[1]);
							imagejpeg($myimg_resized, $filename, IMG_QUALITY);
						}
					}
					chmod($filename, 0777);
				}
			}
		}
	}
	
	# Edit common page data
	function page_edit($id, $type, $mode = 'normal', $caption_bg = 'без заглавие', $caption_en = 'untitled', $del_image = false, $descr_bg = 'няма описание', $descr_en = 'no description', $searchable = 1, $keywords_bg = '', $keywords_en = '', $registered_only = 0, $can_comment = 0, $show_name = 1, $show_back_link = 1, $show_end_bar = 1, $delimiter = 0, $lang_visibility = '', $set_as_homepage = false) {
		
		global $CM_PAGE_TYPES;
		
		if (!id_verify($id))
			return CM_INVALID_NAME;
		
		if (!array_key_exists($type, $CM_PAGE_TYPES))
			return CM_INVALID_TYPE;
		
		#Lock all tables as data is spread around
		lock_tables('cm_struct_main', 'cm_struct_search', 'cm_struct_view', $CM_PAGE_TYPES[$type]['table'], 'settings');

		#try to see if page is there and if so get type
		if (($type_check = element_get($id, array('id', 'type'))) == CM_NOT_FOUND)
			return $type_check; #use array('id','type') to have an array returned. thus, one would be sure of the result
		
		if ($type != $type_check['type'])
			return CM_INVALID_TYPE;
		
		$image_uploaded = false;
		
		# delete image
		if ($type != 'image')
		{
			if ($del_image == 1)
				@unlink(PATH_PAGES.$id.'/preview.jpg');
			else
				image_upload('cm_image', PATH_PAGES.$id.'/preview.jpg', IMG_THUMB_RES);
		}
		 else
		{
			if ($del_image == 1)
			{
				@unlink(PATH_PAGES.$id.'/thumb.jpg');
				@unlink(PATH_PAGES.$id.'/small.jpg');
				@unlink(PATH_PAGES.$id.'/slide.jpg');
				@unlink(PATH_PAGES.$id.'/image.jpg');
			}
			 else
			{
				if ($_FILES['cm_image']['name'] != '') #image uploaded
				{
					$image_filename = $_FILES['cm_image']['name'];
					if (($dotpos = strrpos($image_filename, '.')) !== false)
						$image_filename = substr($image_filename, 0, $dotpos -1);
					
					image_upload('cm_image', PATH_PAGES.$id.'/thumb.jpg', IMG_THUMB_RES);
					image_upload('cm_image', PATH_PAGES.$id.'/small.jpg', IMG_SMALL_RES);
					image_upload('cm_image', PATH_PAGES.$id.'/slide.jpg', IMG_SLIDE_RES, false);
					image_upload('cm_image', PATH_PAGES.$id.'/image.jpg', IMG_RES);
					$image_uploaded = true;
				}
			}
		}
		
		include_once PATH_ENGINE.'func_dirsize.php';
		function page_get_size($id) {
			$size = recursive_directory_size(PATH_PAGES.$id);
			return ($size < 0 ? 0 : round($size / 1024)); #in KB
		}
		
		$size = page_get_size($id, $type['type']);
		
		if (!($res = query('
					UPDATE `cm_struct_main`
						LEFT JOIN `cm_struct_search` ON `cm_struct_main`.`id` = `cm_struct_search`.`id`
						LEFT JOIN `cm_struct_view` ON `cm_struct_main`.`id` = `cm_struct_view`.`id`
						'.(($type == 'image' && $image_uploaded) ? 'LEFT JOIN `'.$CM_PAGE_TYPES['image']['table'].'` ON `cm_struct_main`.`id` = `'.$CM_PAGE_TYPES['image']['table'].'`.`id`' : '').'
							SET
							`cm_struct_main`.`mode` = \''.$mode.'\',
							`cm_struct_main`.`caption_bg` = \''.mysql_real_escape_string(no_mgc($caption_bg)).'\',
							`cm_struct_main`.`caption_en` = \''.mysql_real_escape_string(no_mgc($caption_en)).'\',
							`cm_struct_main`.`descr_bg` = \''.mysql_real_escape_string(no_mgc($descr_bg)).'\',
							`cm_struct_main`.`descr_en` = \''.mysql_real_escape_string(no_mgc($descr_en)).'\',
							`cm_struct_main`.`size` = '.$size.',
							`cm_struct_main`.`delimiter` = '.$delimiter.',
							`cm_struct_main`.`lang_visibility` = \''.$lang_visibility.'\',
							`cm_struct_search`.`searchable` = '.$searchable.',
							`cm_struct_search`.`keywords_bg` = \''.mysql_real_escape_string(no_mgc($keywords_bg)).'\',
							`cm_struct_search`.`keywords_en` = \''.mysql_real_escape_string(no_mgc($keywords_en)).'\',
							`cm_struct_view`.`registered_only` = '.$registered_only.',
							`cm_struct_view`.`can_comment` = '.$can_comment.',
							`cm_struct_view`.`show_name` = '.$show_name.',
							`cm_struct_view`.`show_back_link` = '.$show_back_link.',
							`cm_struct_view`.`show_end_bar` = '.$show_end_bar.
							(($type == 'image' && $image_uploaded) ? ', `'.$CM_PAGE_TYPES['image']['table'].'`.`filename` = \''.$image_filename.'\'' : '').'
							
							WHERE `cm_struct_main`.`id` = \''.$id.'\' AND `mode` > 2
				'))) #2 == DELETED
					trigger_error('Cannot UPDATE table info for `'.$id.'`', E_USER_ERROR);
		
		if ($set_as_homepage)
			homepage($id);
		# we do not unlock tables here as they must remain locked until the page-specific 
		# editing function has finished.
		return CM_OK;
	}
	
	#edit page of type `page`
	function page_edit_page($id, $text_bg, $text_en, $auto_generated = 'none', $use_text_bg_for_all = 0, $sort_direction = 'asc', $sort_by = 'position') {
		# WARNING!
		# All necessary validity checks are made in `page_edit`
		# so none are made here!
		
		# as used in a function, the following variables
		# shell be declared GLOBAL!
		global $page_uid, $images, $files;
		$page_uid = $id;
		
		
		# remove magic quotes
		$text_bg = no_mgc($text_bg);
		$text_en = no_mgc($text_en);
		
		# load file lists
		
		# images
		$images = glob(PATH_PAGES.$page_uid.'/images/*.*');
		for ($i = 0; $i < count($images); $i++)
		{
			$images[$i] = substr($images[$i], strrpos($images[$i], "/") +1);
		}
		
		# files
		$files = glob(PATH_PAGES.$page_uid.'/files/*.*');
		for ($i = 0; $i < count($files); $i++)
		{
			$files[$i] = substr($files[$i], strrpos($files[$i], "/") +1);
		}
		
		#follows a line of new code
		if ($_SESSION['credentials'] != 'admin') {

			# custom replace callback for the SRC attribute
			# If the file is not on the domain, its SRC attribute's content
			# is replaced with the string `prohibited`
			#
			# If the file is on the domain, then its filename is removed
			# from the list of files/images, effectively keeping it on
			# the server and preventing our GC to delete it.
			function my_filter_src ($in) {
				
				global $page_uid, $images, $files;
				$folder = ($in[1] == 'img' ? 'images' : 'files');
				
				if (substr($in[3], 0, strlen(URL_PAGES) + 37 + strlen($folder)) != URL_PAGES.$page_uid.'/'.$folder) #37 as 1 + 36 as pageid is 36 chars long
					return '<'.$in[1].$in[2].' src = "prohibited"'.$in[4].'>';
				 else
				{
					# remove from GC collections
					if ($in[1] == 'img')
					{
						$pos = array_search(substr($in[3], strrpos($in[3], "/") +1), $images);
						if ($pos !== false)
							unset($images[$pos]);
					}
					 else
					{
						$pos = array_search(substr($in[3], strrpos($in[3], "/") +1), $files);
						if ($pos !== false)
							unset($files[$pos]);
					}
					
					return '<'.$in[1].$in[2].' src = "'.$in[3].'"'.$in[4].'>';
				}
			}
			
			# regex for the script search
			$tags_script = '<\<script[^\>]*\>.*\</script\>>is';
			
			# Remove scripts
			$text_bg = preg_replace($tags_script, '<!-- Using scripts in pages is considered illegal! -->', $text_bg);
			$text_en = preg_replace($tags_script, '<!-- Using scripts in pages is considered illegal! -->', $text_en);
			
			# regex for the src search
			$tags_src = '<[<]([^\s\>]+)([^\>]*)[\s]{1,}src[\s]{0,}=[\s]{0,}"([^"]*)"([^\>]*)[>]>is';
			
			# Remove external src
			$text_bg = preg_replace_callback($tags_src, 'my_filter_src', $text_bg);
			$text_en = preg_replace_callback($tags_src, 'my_filter_src', $text_en);

			# regex for event attributes
			$tag_events = '<[<]([^\s\>]+)([^\>]*)[\s]{1,}on[\w]{0,}[\s]{0,}=[\s]{0,}"(\\\"|[^"]){0,}"([^\>]*)[>]>is';		
			
			# filter function
			function my_filter_events($in) {
			# comes in:
			# 0: <img src="asd" onload="dothis(\"lala\", 3);" alt="qwe">
			# 1: img
			# 2:  src="asd"
			# 3: ; (last char of JScode
			# 4:  alt="qwe"
				return '<'.$in[1].$in[2].$in[4].'>';
			}
			
			# Remove event attributes
			$text_bg = preg_replace_callback($tag_events, 'my_filter_events', $text_bg);
			$text_en = preg_replace_callback($tag_events, 'my_filter_events', $text_en);
		}
		
		# regex for the anchor search
		$tags_links = '<[<]a[^\>]*[\s]{1,}href[\s]{0,}=[\s]{0,}"([^"]*)"[^\>]*[>]>is';
		
		# search in BG text
		$found = array();
		preg_match_all($tags_links, $text_bg, $found);
		if (count($found) == 2)
		{
			for ($i = 0; $i < count($found[1]); $i++)
			{
				$pos = array_search(substr($found[1][$i], strrpos($found[1][$i], "/") +1), $images);
				if ($pos !== false)
					unset($images[$pos]);
				
				$pos = array_search(substr($found[1][$i], strrpos($found[1][$i], "/") +1), $files);
				if ($pos !== false)
					unset($files[$pos]);
			}
		}
		
		# search in EN text
		$found = array();
		preg_match_all($tags_links, $text_en, $found);
		if (count($found) == 2)
		{
			for ($i = 0; $i < count($found[1]); $i++)
			{
				$pos = array_search(substr($found[1][$i], strrpos($found[1][$i], "/") +1), $images);
				if ($pos !== false)
					unset($images[$pos]);
				
				$pos = array_search(substr($found[1][$i], strrpos($found[1][$i], "/") +1), $files);
				if ($pos !== false)
					unset($files[$pos]);
			}
		}

		# reindex the arrays !
		$images = array_values($images);
		$files = array_values($files);
		
		# GC: delete unnecessary files
		for ($i = 0; $i < count($images); $i++)
			@unlink(PATH_PAGES.$page_uid.'/images/'.$images[$i]);
		
		for ($i = 0; $i < count($files); $i++)
			@unlink(PATH_PAGES.$page_uid.'/files/'.$files[$i]);
		
		#Update DB
		global $CM_PAGE_TYPES;
		
		if (!($res = query('
						UPDATE `'.$CM_PAGE_TYPES['page']['table'].'`
						SET
							`sort_direction` = \''.$sort_direction.'\',
							`sort_by` = \''.$sort_by.'\',
							`auto_generated` = \''.$auto_generated.'\',
							`use_text_bg_for_all` = '.$use_text_bg_for_all.',
							`text_bg` = \''.mysql_real_escape_string($text_bg).'\',
							`text_en` = \''.mysql_real_escape_string($text_en).'\'
						WHERE `id` = \''.$page_uid.'\'
					')))
			trigger_error('Could not update page info for `'.$page_uid.'`', E_USER_ERROR);
		
		#unlock tables AT LAST! ;-)
		unlock_tables();
	}
	
	# Edit page of type `addon`
	function page_edit_addon($id, $page = '', $options = ''){
		#Update DB
		global $CM_PAGE_TYPES;
		
		if (!($res = query('
						UPDATE `'.$CM_PAGE_TYPES['addon']['table'].'`
						SET
							`page` = \''.mysql_real_escape_string($page).'\',
							`options` = \''.mysql_real_escape_string($options).'\'
						
						WHERE `id` = \''.$id.'\'
					')))
			trigger_error('Could not update page info for `'.$id.'`', E_USER_ERROR);
		
		#unlock tables AT LAST! ;-)
		unlock_tables();
	}
	
	# Edit page of type `link`
	function page_edit_link($id, $link = '', $target = 'internal'){
		switch($target) {
			case 'internal':
			case 'external':
			
			break;
			
			default:
				$target = 'external';
		}
		
		#Update DB
		global $CM_PAGE_TYPES;
		
		if (!($res = query('
						UPDATE `'.$CM_PAGE_TYPES['link']['table'].'`
						SET
							`link` = \''.mysql_real_escape_string($link).'\',
							`target` = \''.$target.'\'
						
						WHERE `id` = \''.$id.'\'
					')))
			trigger_error('Could not update page info for `'.$id.'`', E_USER_ERROR);
		
		#unlock tables AT LAST! ;-)
		unlock_tables();
	}

	function page_edit_math($id, $zip_bg = false, $zip_en = false, $pdf_bg = false, $pdf_en = false, $use_text_bg_for_all = true){
		function proc_name($name) {
			$s_find = array(
				'а','б','в', 'г', 'д', 'е', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ь', 'ю', 'я',
				'А','Б','В', 'Г', 'Д', 'Е', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ь', 'Ю', 'Я',
				' '
			);

			$s_replace = array(
				'a','b','v','g','d','e','j','z','i','i','k','l','m','n','o','p','r','s','t','u','f','h','c','ch','sh','sht','a', '', 'iu','ia',
				'A','B','V','G','D','E','J','Z','I','I','K','L','M','N','O','P','R','S','T','U','F','H','C','CH','SH','SHT','A', '', 'IU','IA',
				'_'
			);
			
			$name = str_replace($s_find, $s_replace, $name);
			
			$s_correct = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM~1234567890-_.';
			$res = '';
			for ($i = 0; $i < "".strlen($name); $i++) {
				$c = substr($name, $i, 1);
				if (strstr($s_correct, $c) !== false)
					$res .= $c;
			}
			
			if ($res == '')
				$res = 'untitled.txt';
			
			return $res;
		}
		
		$pdf_bg_name = '';
		$pdf_en_name = '';
		
		$z = new ZipArchive();
		
		if ($zip_bg !== false) {
			if ($z->open($zip_bg['tmp_name']) === true) {
				$files = glob(PATH_PAGES.$id.'/text_bg/*.*');
				for ($i = 0; $i < count($files); $i++)
					@unlink($files[$i]);
				
				$z->extractTo(PATH_PAGES . $id . '/text_bg/');
				$z->close();
				
				$files = glob(PATH_PAGES.$id.'/text_bg/*.*');
				for ($i = 0; $i < count($files); $i++)
					@chmod($files[$i], 0777);
			}
		}
		
		if ($zip_en !== false) {
			if ($z->open($zip_en['tmp_name']) === true) {
				$files = glob(PATH_PAGES.$id.'/text_en/*.*');
				for ($i = 0; $i < count($files); $i++)
					@unlink($files[$i]);
				
				$z->extractTo(PATH_PAGES . $id . '/text_en/');
				$z->close();
				
				$files = glob(PATH_PAGES.$id.'/text_en/*.*');
				for ($i = 0; $i < count($files); $i++)
					@chmod($files[$i], 0777);
			}
		}
		
		if ($pdf_bg !== false) {
			$files = glob(PATH_PAGES.$id.'/attach_bg/*.*');
			for ($i = 0; $i < count($files); $i++)
				@unlink($files[$i]);
			
			$pdf_bg_name = proc_name($pdf_bg['name']);
			move_uploaded_file($pdf_bg['tmp_name'], PATH_PAGES . $id . '/attach_bg/' . $pdf_bg_name);
			
			$files = glob(PATH_PAGES.$id.'/attach_bg/*.*');
			for ($i = 0; $i < count($files); $i++)
				@chmod($files[$i], 0777);
		}
		
		if ($pdf_en !== false) {
			$files = glob(PATH_PAGES.$id.'/attach_en/*.*');
			for ($i = 0; $i < count($files); $i++)
				@unlink($files[$i]);
			
			$pdf_en_name = proc_name($pdf_en['name']);
			move_uploaded_file($pdf_en['tmp_name'], PATH_PAGES . $id . '/attach_en/' . $pdf_en_name);
			
			$files = glob(PATH_PAGES.$id.'/attach_en/*.*');
			for ($i = 0; $i < count($files); $i++)
				@chmod($files[$i], 0777);
		}
		
		#Update DB
		global $CM_PAGE_TYPES;
		
		if (!($res = query('
						UPDATE `'.$CM_PAGE_TYPES['math']['table'].'`
						SET'.
							($pdf_bg_name != '' ? '`attach_filename_bg` = \''.mysql_real_escape_string($pdf_bg_name).'\',' : '').
							($pdf_en_name != '' ? '`attach_filename_en` = \''.mysql_real_escape_string($pdf_en_name).'\',' : '').'
							`use_text_bg_for_all` = \''.($use_text_bg_for_all ? '1' : '0').'\'
						
						WHERE `id` = \''.$id.'\'
					')))
			trigger_error('Could not update page info for `'.$id.'`', E_USER_ERROR);
		
		#unlock tables AT LAST! ;-)
		unlock_tables();
	}
	
	# Add page
	function page_add($parent, $type_id, $caption_bg, $caption_en) {
		global $CM_PAGE_TYPES;
		global $CM_PAGE_TYPES_NAMES;
		
		
		# Check if type is valid
		if (!is_numeric($type_id))
			return CM_INVALID_INPUT;
		
		if (round($type_id) < 0 || round($type_id) >= count($CM_PAGE_TYPES))
			return CM_INVALID_INPUT;
		
		#if (!array_key_exists($type, $CM_PAGE_TYPES)) //not used when type_id is used instead
		#	return CM_INVALID_INPUT;
		
		$type = $CM_PAGE_TYPES_NAMES[$type_id];
		
		# Lock tables, incl. special one
		if ($type != 'page')
			lock_tables('cm_struct_main', 'cm_struct_search', 'cm_struct_view', $CM_PAGE_TYPES['page']['table'], $CM_PAGE_TYPES[$type]['table']);
		else
			lock_tables('cm_struct_main', 'cm_struct_search', 'cm_struct_view', $CM_PAGE_TYPES[$type]['table']);
		
		# Check if parent is there
		if (!element_exists($parent, true))
			return CM_MISSING_PARENT;
		
		$position = parent_get_last_position($parent) +1;
		
		if (!($res = query('SELECT UUID() id;')))
			trigger_error('Could not create UUID', E_USER_ERROR);
		
		if (mysql_num_rows($res) != 1)
			trigger_error('No UUID returned', E_USER_ERROR);
			
		$row = mysql_fetch_assoc($res);
		
		if ($row['id'] === NULL)
			trigger_error('mySQL returns null as ID', E_USER_ERROR);
		
		$id = $row['id'];
		
		if ($caption_bg == '')
			$caption_bg = 'Няма заглавие';
		
		if ($caption_en == '')
			$caption_en = 'Untitled';
		
		# Insert common page data into DB
		
		if (!query('INSERT INTO `cm_struct_main` (`id`, `parent`, `position`, `type`, `caption_bg`, `caption_en`) VALUES'.
		'(\''.$id.'\', \''.$parent.'\', \''.$position.'\', \''.$type.'\', \''.$caption_bg.'\', \''.$caption_en.'\')'))
			trigger_error('Could not insert page `'.$id.'` into `cm_struct_main`', E_USER_ERROR);
		
		if (!query('INSERT INTO `cm_struct_search` (`id`, `keywords_bg`, `keywords_en`) VALUES (\''.$id.'\', \'\', \'\');'))
			trigger_error('Could not instert page `'.$id.'` into `cm_struct_search`', E_USER_ERROR);
			
		if (!query('INSERT INTO `cm_struct_view` (`id`) VALUES (\''.$id.'\');'))
			trigger_error('Could not insert page `'.$id.'` into `cm_struct_view`', E_USER_ERROR);
			
		if ($parent != 'menu') #update subpage_count
			if (!query('UPDATE `'.$CM_PAGE_TYPES['page']['table'].'` SET `subpage_count` = `subpage_count` + 1 WHERE `id` = \''.$parent.'\';'))
				trigger_error('Could not increase counter of page `'.$parent.'`', E_USER_ERROR);
		
		# Insert special page data into DB
		
		switch($type) {
			
			case 'page':
				if (!query('INSERT INTO `'.$CM_PAGE_TYPES[$type]['table'].'` (`id`, `text_bg`, `text_en`) VALUES (\''.$id.'\', \'\', \'\');'))
					trigger_error('Could not insert page `'.$id.'` into pages table', E_USER_ERROR);
				break;
				
			case 'image':
				if (!query('INSERT INTO `'.$CM_PAGE_TYPES[$type]['table'].'` (`id`, `filename`) VALUES (\''.$id.'\', \'\');'))
					trigger_error('Could not insert page `'.$id.'` into images table', E_USER_ERROR);
				break;
			
			case 'addon':
				if (!query('INSERT INTO `'.$CM_PAGE_TYPES[$type]['table'].'` (`id`, `page`, `options`) VALUES (\''.$id.'\', \'\', \'\');'))
					trigger_error('Could not insert page `'.id.'` into addons table', E_USER_ERROR);
				break;
				
			case 'link':
				if (!query('INSERT INTO `'.$CM_PAGE_TYPES[$type]['table'].'` (`id`, `link`) VALUES (\''.$id.'\', \'\');'))
					trigger_error('Could not insert page `'.$id.'` into links table', E_USER_ERROR);
				break;
				
			case 'math':
				if(!query('INSERT INTO `'.$CM_PAGE_TYPES[$type]['table'].'` (`id`) VALUES (\''.$id.'\');'))
					trigger_error('Could not insert page `'.$id.'` into MATHS table', E_USER_ERROR);
				break;
			
		}
		
		unlock_tables();
		
		# Create common folders
		if (!mkdir(PATH_PAGES.$id))
			trigger_error('Could not create page directory: '.PATH_PAGES.$id, E_USER_ERROR);
		
		if (!chmod(PATH_PAGES.$id, 0777))
			trigger_error('Could not chmod page directory: '.PATH_PAGES.$id, E_USER_ERROR);
		
		# Create special folders
		for ($i = 0; $i < count($CM_PAGE_TYPES[$type]['files']); $i++) # If the page type does not use special folders then this will not execute as count(array()) is zero
		{
			if (!mkdir(PATH_PAGES.$id.'/'.$CM_PAGE_TYPES[$type]['files'][$i]))
				trigger_error('Could not create page directory: '.PATH_PAGES.$id.'/'.$CM_PAGE_TYPES[$type]['files'][$i], E_USER_ERROR);
			
			if (!chmod(PATH_PAGES.$id.'/'.$CM_PAGE_TYPES[$type]['files'][$i], 0777))
				trigger_error('Could not chmod page directory: '.PATH_PAGES.$id.'/'.$CM_PAGE_TYPES[$type]['files'][$i], E_USER_ERROR);
		}
		return CM_OK;
	}
	
	# Move
	function page_move($from, $to) {
		for ($i = 0; $i < count($from); $i++)
		{
			if (!id_verify($from[$i]))
			{
				return CM_INVALID_NAME;
			}
		}
		
		global $CM_PAGE_TYPES;
		lock_tables('cm_struct_main', $CM_PAGE_TYPES['page']['table']); //must be BEFORE the following check, or it would not be sure that the check result is still valid

		if (!element_exists($to, true))
		{
			unlock_tables();
			return CM_NOT_FOUND;
		}
		
		#get new target position
		$newposition = parent_get_last_position($to) +1;
		
		#move pages
		for ($i = 0; $i < count($from); $i++)
		{
			#get position in from
			$old_data = element_get($from[$i], array('position', 'parent'));
			
			if ($old_data['parent'] != $to)
			{ #move
				
				#fix position of from parents
				if (!query('UPDATE `cm_struct_main` SET `position` = `position` -1 WHERE `parent` = \''.$old_data['parent'].'\' AND `position` > '.$old_data['position'].';'))
					trigger_error('Could not fix positions in `'.$from.'`', E_USER_ERROR);
				
				#decrease counter of old parent if a page
				if ($old_data['parent'] != 'menu')
					if (!query('UPDATE `'.$CM_PAGE_TYPES['page']['table'].'` SET `subpage_count` = `subpage_count` - 1 WHERE `id` = \''.$old_data['parent'].'\';'))
						trigger_error('Could not decrease counter of page `'.$old_data['parent'].'`', E_USER_ERROR);
				
				#move element
				if (!query('UPDATE `cm_struct_main` SET `position` = '.$newposition.', `parent` = \''.$to.'\' WHERE `id` = \''.$from[$i].'\';'))
					trigger_error('Could not move pages to `'.$to.'`', E_USER_ERROR);
			
				$newposition += 1; #if moved only ;-)
			}
		}
		
		if ($to != 'menu') #increase counter of new parent if a page
			if (!query('UPDATE `'.$CM_PAGE_TYPES['page']['table'].'` SET `subpage_count` = `subpage_count` + '.count($from).' WHERE `id` = \''.$to.'\';'))
				trigger_error('Could not increase counter of page `'.$to.'`', E_USER_ERROR);
				
		unlock_tables();
		return CM_OK;
	}
	
	# Restore
	function page_restore($from, $to) {
	
		for ($i = 0; $i < count($from); $i++)
		{
			if (!id_verify($from[$i]))
			{
				return CM_INVALID_NAME;
			}
		}
		
		global $CM_PAGE_TYPES;
		lock_tables('cm_struct_main', $CM_PAGE_TYPES['page']['table']); //must be BEFORE the following check, or it would not be sure that the check result is still valid
		
		if (!element_exists($to, true))
		{
			unlock_tables();
			return CM_NOT_FOUND;
		}
		
		$newposition = parent_get_last_position($to) +1;
		
		for ($i = 0; $i < count($from); $i++)
		{
			#move element
			if (!($res = query('UPDATE `cm_struct_main` SET `mode` = \'normal\', `parent` = \''.$to.'\', `position` = '.strval($newposition + $i).' WHERE `mode` = \'deleted\' AND `id` = \''.$from[$i].'\'')))
				trigger_error('Could not move pages', E_USER_ERROR);
			
			#fix deleted_follow elements
			if (!($res = query('UPDATE `cm_struct_main` SET `mode` = \'normal\', `del_parent` = NULL WHERE `mode` = \'deleted_follow\' AND `del_parent` = \''.$from[$i].'\';')))
				trigger_error('Could not move pages', E_USER_ERROR);
		}
		
		#increase counter of targeted parent if a page
		if ($to != 'menu')
			if (!query('UPDATE `'.$CM_PAGE_TYPES['page']['table'].'` SET `subpage_count` = `subpage_count` + '.count($from).' WHERE `id` = \''.$to.'\';'))
				trigger_error('Could not decrease counter of page `'.$to.'`', E_USER_ERROR);
		
		unlock_tables();
		return CM_OK;
	}
	
	function page_trash($pages) {
		
		global $CM_PAGE_TYPES;
		
		lock_tables('cm_struct_main', $CM_PAGE_TYPES['page']['table'], 'settings');
		
		$valid_pages = array();
		
		for ($i = 0; $i < count($pages); $i++)
		{
			if (element_exists($pages[$i]))
				$valid_pages[] = $pages[$i];
		}
		
		for ($i = 0; $i < count($valid_pages); $i++)
				page_trash_private($valid_pages[$i], homepage());
		
		unlock_tables();
		return CM_OK;
	}
	
	# Private iterator
	function page_trash_private($page, $homepage, $first = true, $del_parent = '') {
		if ($page == $homepage)
			homepage(''); #i.e. unset homepage tag ;-)
		
		if (!($res = query('SELECT `id` FROM `cm_struct_main` WHERE `parent` = \''.$page.'\';')))
			trigger_error('Could not select children of `'.$page.'`', E_USER_ERROR);
		
		if (!$first)
		{
			if (!query('UPDATE `cm_struct_main` SET `mode` = \'deleted_follow\', `del_parent` = \''.$del_parent.'\' WHERE `id` = \''.$page.'\';'))
				trigger_error('Could not mark page `'.$page.'` as deleted', E_USER_ERROR);
		}
		 else
		{
			# it is the first one
			$data = element_get($page, array('parent', 'position'));
			
			if (!query('UPDATE `cm_struct_main` SET `mode` = \'deleted\' WHERE `id` = \''.$page.'\';'))
				trigger_error('Could not mark page `'.$page.'` as deleted', E_USER_ERROR);
			
			#fix position
			if (!query('UPDATE `cm_struct_main` SET `position` = `position` -1 WHERE `parent` = \''.$data['parent'].'\' AND `position` > '.$data['position'].' AND `mode` > 2;')) //2 == DELETED
				trigger_error('Could not set positions after `'.$page.'`', E_USER_ERROR);
			
			#decrease counter of old parent if a page
			if ($data['parent'] != 'menu')
			{
				global $CM_PAGE_TYPES;
				
				if (!query('UPDATE `'.$CM_PAGE_TYPES['page']['table'].'` SET `subpage_count` = `subpage_count` - 1 WHERE `id` = \''.$data['parent'].'\';'))
					trigger_error('Could not decrease counter of page `'.$data['parent'].'`', E_USER_ERROR);
			}
			
			$del_parent = $page;
		}
		
		while($row = mysql_fetch_assoc($res))
		{
			page_trash_private($row['id'], $homepage, false, $del_parent);
		}
	}
	
	# Move Up & Down
	function page_move_updn($id, $up) {
		lock_tables('cm_struct_main');
		
		$data = element_get($id, array('parent', 'position'));
		if (is_numeric($data)) #i.e. error (but if only one numeric field was selected the output would have been ambigous)
			return $data;
		
		if ($up && $data['position'] > 1)
		{
			if (!($res = query('SELECT `id` FROM `cm_struct_main` WHERE `position` = '.($data['position'] -1).' AND `parent` = \''.$data['parent'].'\' AND `mode` > 2;'))) #2 is deleted
				trigger_error('Could not select element no. '.($data['position'] -1).' in `'.$data['parent'].'`',  E_USER_ERROR);
				
			if (!($row = mysql_fetch_assoc($res)))
				trigger_error('Could not find element no. '.($data['position'] -1).' in `'.$data['parent'].'`',  E_USER_ERROR);
			
			$swap_id = $row['id'];
			
			if (!query('UPDATE `cm_struct_main` SET `position` = '.$data['position'].' WHERE `id` = \''.$swap_id.'\';'))
				trigger_error('Could not set position of `'.$swap_id.'` to '.$data['position'], E_USER_ERROR);
			if (!query('UPDATE `cm_struct_main` SET `position` = '.($data['position'] - 1).' WHERE `id` = \''.$id.'\';'))
				trigger_error('Could not set position of `'.$id.'` to '.($data['position'] -1), E_USER_ERROR);
		}
		
		if (!$up)
		{
			$last_position = parent_get_last_position($data['parent']);
			
			if ($last_position == 0)
				trigger_error('No elements found, but id is here?');
				
			if ($last_position > $data['position'])
			{
				if (!($res = query('SELECT `id` FROM `cm_struct_main` WHERE `position` = '.($data['position'] +1).' AND `parent` = \''.$data['parent'].'\' AND `mode` > 2;'))) # 2 is deleted
					trigger_error('Could not select element no. '.($data['position'] +1).' in `'.$data['parent'].'`',  E_USER_ERROR);
					
				if (!($row = mysql_fetch_assoc($res)))
					trigger_error('Could not find element no. '.($data['position'] +1).' in `'.$data['parent'].'`',  E_USER_ERROR);
				
				$swap_id = $row['id'];
				
				if (!query('UPDATE `cm_struct_main` SET `position` = '.$data['position'].' WHERE `id` = \''.$swap_id.'\';'))
					trigger_error('Could not set position of `'.$swap_id.'` to '.$data['position'], E_USER_ERROR);
				if (!query('UPDATE `cm_struct_main` SET `position` = '.($data['position'] +1).' WHERE `id` = \''.$id.'\';'))
					trigger_error('Could not set position of `'.$id.'` to '.($data['position'] +1), E_USER_ERROR);
			}
		}
		
		unlock_tables();
		
		return CM_OK;
	}
	
	function page_shred($pages, $debug = false) {
		#tables, corresponding to all new page types (as NEWS, FILES, etc.) must be added in the lock_table query!
		
		global $CM_PAGE_TYPES, $CM_PAGE_TYPES_TABLES;
		
		$valid_pages = array();
		
		lock_tables('cm_struct_main', 'cm_struct_search', 'cm_struct_view', $CM_PAGE_TYPES_TABLES);
		for ($i = 0; $i < count($pages); $i++)
		{
			if (element_exists($pages[$i], false, true))
				$valid_pages[] = $pages[$i];
		}
		
		for ($i = 0; $i < count($valid_pages); $i++)
			page_shred_private($valid_pages[$i], $debug);
			
		unlock_tables();
		
		return CM_OK;
	}
	
	# Shred one page at a time
	function page_shred_private($page, $debug = false) {
		global $CM_PAGE_TYPES, $CM_PAGE_TYPES_TABLES;
		
		# select children of page
		if (!($res = query('SELECT `id`, `type` FROM `cm_struct_main` WHERE `mode` = \'deleted_follow\' AND `del_parent` = \''.$page.'\'')))
			trigger_error('Could not select deleted children of `'.$page.'`', E_USER_ERROR);
		
		$child_num = mysql_num_rows($res);
		
		# delete files of children, i.e. deleted_follow of $page
		while ($row = mysql_fetch_assoc($res))
		{
			page_shred_from_fs($row['id'], $row['type'], $debug);
		}

		if ($child_num > 0) # if there are children to delete
		{
			$tbls_join = array();
			for ($i = 0; $i < count($CM_PAGE_TYPES_TABLES); $i++)
				$tbls_join[$i] = 'LEFT JOIN `'.$CM_PAGE_TYPES_TABLES[$i].'` ON `cm_struct_main`.`id` = `'.$CM_PAGE_TYPES_TABLES[$i].'`.`id`';
			
			# delete children from DB in one query
			if (!query('DELETE FROM `cm_struct_main`, `cm_struct_view`, `cm_struct_search`, `'.implode('` , `', $CM_PAGE_TYPES_TABLES).'` USING `cm_struct_main` LEFT JOIN `cm_struct_search` ON `cm_struct_main`.`id` = `cm_struct_search`.`id` LEFT JOIN `cm_struct_view` ON `cm_struct_main`.`id` = `cm_struct_view`.`id` '.implode(' ', $tbls_join).' WHERE `cm_struct_main`.`mode` = \'deleted_follow\' AND `cm_struct_main`.`del_parent` = \''.$page.'\';', $debug))
				trigger_error('Could not delete children pages from DB!', E_USER_ERROR);
		}
		
		# get type of parent
		$parent_type = element_get($page, 'type', false, true); #i.e. get only type filed and check if parent is indeed deleted!

		# delete files of parent
		page_shred_from_fs($page, $parent_type, $debug);
		
		# delete db records of parent
		if (!query('DELETE FROM `cm_struct_main`, `cm_struct_view`, `cm_struct_search`, `'.$CM_PAGE_TYPES[$parent_type]['table'].'` USING `cm_struct_main` LEFT JOIN `cm_struct_search` ON `cm_struct_main`.`id` = `cm_struct_search`.`id` LEFT JOIN `cm_struct_view` ON `cm_struct_main`.`id` = `cm_struct_view`.`id` LEFT JOIN `'.$CM_PAGE_TYPES[$parent_type]['table'].'` ON `cm_struct_main`.`id` = `'.$CM_PAGE_TYPES[$parent_type]['table'].'`.`id` WHERE `cm_struct_main`.`id` = \''.$page.'\' AND `mode` = \'deleted\';', $debug))
			trigger_error('Cannot delete parent', E_USER_ERROR);
		
		if ($debug)
			echo str_repeat('-', 128).'<br><br>';
	}
	
	function page_shred_from_fs($page, $type, $debug = false) {
	# shred files
		if ($debug)
			echo 'Deleting info for '.$page.' of type '.$type.'...';
		global $CM_PAGE_TYPES;
		
		$dirs = $CM_PAGE_TYPES[$type]['files']; #if the page type is not using using special folders, then it will be just an empty array
		$dirs[] = ''; # add root dir
		
		for ($i = 0; $i < count($dirs); $i++)
		{
			#remove files
			$files = glob(PATH_PAGES.$page.'/'.$dirs[$i].'/*.*');
			for ($j = 0; $j < count($files); $j++)
				if (!$debug)
					@unlink($files[$j]);
				else
					echo('file: '.$files[$j]).'<br>';
			
			#remove dir
			if (!$debug)
				@rmdir(PATH_PAGES.$page.'/'.$dirs[$i].'/');
			else
				echo('dir: '.PATH_PAGES.$page.'/'.$dirs[$i].'/<br>');
		}
	}
	
	#RECYCLE BIN
	function recycle_full() {
		if (!($res = query('SELECT SUM(size) size FROM `cm_struct_main` WHERE `mode` <= 2'))) // 2 == DELETED
			trigger_error('Could not check recycle bin', E_USER_ERROR);
		
		$row = mysql_fetch_assoc($res);
		if (!$row)
			return false;
		
		if ($row['size'] == NULL)
			return false;
		
		return $row['size'];
	}
	
	#HOMEPAGE
	function homepage($id = NULL) {
		if (is_null($id)) //i.e. no parameters passed, i.e. user wants to read, not to write
		{
			if (!($res = query('SELECT `value` FROM `settings` WHERE `name` = \'homepage\'')))
				trigger_error('Could not select homepage', E_USER_ERROR);
			
			$row = mysql_fetch_assoc($res);
			return $row['value'];
		}
		 else
		{
			if ($id != '' && !id_verify($id))
				return false;
			# WARNING
			#
			# The check for pages existance is done in gtA_cm_homepage.php
			# as it requires table locking which cannot be initiated if tables are already locked
			# which is the case when unsetting the homepage from page_trash()
			if (!($res = query('UPDATE `settings` SET `value` = '.($id == '' ? 'NULL' : '\''.$id.'\'').' WHERE `name` = \'homepage\'')))
				trigger_error('Could not set homepage to `'.$id.'`', E_USER_ERROR);
			
			return true;
		}
	}
?>