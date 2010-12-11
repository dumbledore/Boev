<?php
	# Helper functions
	
	# HOMEPAGE
	function homepage() {
		if (!($res = query('SELECT `value` FROM `settings` WHERE `name` = \'homepage\'')))
			return NULL;
		
		$row = mysql_fetch_assoc($res);
		return $row['value'];
	}
	
	# Error reporting
	function render_notify($type, $id = 'n/a') {
		send_mail(MAIL_WEBMASTER_ADDRESS, 'Rendering issue', wordwrap(
"
Rendering Issue
--------------------------
Cannot render $type
ID: $id
MYSQL: ".mysql_error()."
"
		));
	}
	
	# get page data
	function get_page_data($id) {
		# select the info
		$res = query('SELECT * FROM `cm_struct_main` LEFT JOIN `cm_struct_view` ON `cm_struct_main`.`id` = `cm_struct_view`.`id` WHERE `cm_struct_main`.`id` = \''.$id.'\' AND (`lang_visibility` = \''.SITE_LANG.'\' OR `lang_visibility` = 3) AND `mode` > 3;'); # 3 is INACTIVE, e.g. show INVISIBLE and up
		
		if (!$res)
		{
			echo 'Има проблем с базата данни. Работи се по отстраняването му!';
			render_notify('page, common info', $id);
			exit;
		}
		
		if (mysql_num_rows($res) != 1)
			return false;
		
		$data_main = mysql_fetch_assoc($res); # Common info
		
		global $CM_PAGE_TYPES;
		
		$res = query('SELECT * FROM `'.$CM_PAGE_TYPES[$data_main['type']]['table'].'` WHERE `id` = \''.$id.'\';');
		
		if (!$res)
		{
			echo 'Има проблем с базата данни. Работи се по отстраняването му!';
			render_notify('page, sp info', $id);
			exit;
		}
		
		if (mysql_num_rows($res) != 1)
			return false;
		
		$data_spec = mysql_fetch_assoc($res); # Specific page info
		
		return array_merge($data_main, $data_spec);
	}
	
	# Menu builder functions
	function render_menu() {
		
		$res = query('SELECT `id`, `caption_'.SITE_LANG.'`, `delimiter` FROM `cm_struct_main` WHERE `parent` = \'menu\' AND (`lang_visibility` = \''.SITE_LANG.'\' OR `lang_visibility` = 3) AND `mode` > 4 ORDER BY `position`;'); #4 is INVISIBLE
		if (!$res)
		{
			echo (SITE_LANG == 'bg' ? 'Менюто не може да бъде заредено. Работи се по проблема.' : 'Cannot load menu data. The administrator has been notified.');
			render_notify('menu');
			return;
		}
		
		$renres = '<table cellspacing="0" cellpadding="0"><tr valign="top">';
		
		# Include custom rendering functions
		include_once PATH_SITE.'layout/layout_render.php';
		
		for ($i = 0; $i < mysql_num_rows($res); $i++)
		{
			$row = mysql_fetch_assoc($res);
			$renres .= lrender_menu_item($row['id'], str_replace(array('|', '~'), array('<br>&nbsp;&nbsp;', ' '), no_tags($row['caption_'.SITE_LANG])));
			
			if ($i != mysql_num_rows($res)-1)
				$renres .= '<td>&nbsp;&middot;&nbsp;</td>';
			
			if ($row['delimiter'])
				$renres .= lrender_menu_delimiter();
		}
		
		return $renres.'</tr></table>';
	}
	
	# Select the rendering engine
	function render_page($id, $others = NULL) {
		
		# Include custom rendering functions
		include_once PATH_SITE.'layout/layout_render.php';

		# select the id
		if (!id_verify($id))
		{
			$id = homepage();
			$homepage = true;
			if (is_null($id))
				return lrender_homepage_not_set(); # No homepage specified
		}
		 else
		{
			$homepage = false;
		}
		
		$data = get_page_data($id); #get all data
		
		if ($data === false)
		{
			if ($homepage) #If showing the homepage
			{
				return lrender_homepage_not_set(); # No homepage specified
			}
			 else
			{
				jump_to(URL_SITE.'index.php', 'page does not exist, show homepage');
				exit;
			}
		}
		
		# All validity checks are OK.
		
		# Check wheather the page is for registered users only
		if ($data['registered_only'] && !isset($_SESSION['username']))
			return 	lrender_caption($id, $data['subscribed'], str_replace(array('|', '~'), array(' ', '<br>'), no_tags($data['caption_'.SITE_LANG]))).
					lrender_reg_only();
		
		# Start rendering
		$renres = '';
		
		# Rendering commons header
		if ($data['show_name'])
			$renres .= lrender_caption($id, $data['subscribed'], str_replace(array('|', '~'), array(' ', '<br>'), no_tags($data['caption_'.SITE_LANG]))); #tame any possible tags against XSS
		
		# Render page EXCLUSIVELY
		if ($data['type'] == 'page')
		{
			# select language version
			$text = $data['text_'.
					(
						SITE_LANG == 'bg'
						? 'bg'
						: ($data['use_text_bg_for_all']
							? 'bg'
							: 'en'
						)
					)];
			# split into pages
			$pages = explode('<div style="page-break-after: always;"><span style="display: none;">&nbsp;</span></div>', $text);
			
			# if text is empty, use descr
			if (count($pages == 1) && $pages[0] == '')
				$pages[0] = $data['descr_'.SITE_LANG];
			
			if (
				!isset($_GET['pnum']) ||
				!is_numeric($_GET['pnum'])
			) #incorrect data
				$page_num = 1;
			 else #semantically correct
			{
				$page_num = floor($_GET['pnum']);
				
				if ($_GET['pnum'] < 1)
					$page_num = 1;
				
				if ($_GET['pnum'] > count($pages))
				$page_num = count($pages);
			}
			
			# get auto_generated page
			$gen_num = false;
			
			if ($data['auto_generated'] != 'none')
			{
				$gen_num_max = 1;
				
				switch ($data['auto_generated'])
				{
					case 'brief':
						$gen_num_max = ceil($data['subpage_count'] / REND_BRIEF_PER_PAGE);
						break;
					
					case 'thumbnails':
						$gen_num_max = ceil($data['subpage_count'] / REND_THUMB_PER_PAGE);
						break;
					
					case 'full':
						$gen_num_max = ceil($data['subpage_count'] / REND_FULL_PER_PAGE);
						break;
					
					case 'slideshow':
						$gen_num_max = $data['subpage_count'];
						break;
				}
				
				if (
					!isset($_GET['gnum']) ||
					!is_numeric($_GET['gnum'])
				) #incorrect data
					$gen_num = 1;
				 else #semantically correct
				{
					$gen_num = floor($_GET['gnum']);
					
					if ($_GET['gnum'] < 1)
						$gen_num = 1;
					
					if ($_GET['gnum'] > $gen_num_max)
						$gen_num = $gen_num_max;
				}
			}
			
			# render text
			$renres .= lrender_images($pages[$page_num-1]);
			
			# show next/prev page bar
			if (count($pages) > 1)
				$renres .= lrender_page_bar($id, ($gen_num == 1 ? false : $gen_num), ($page_num > 1 ? $page_num -1 : false), ($page_num < count($pages) ? $page_num +1 : false), $page_num, count($pages));
			
			# list to be autogenerated
			
			# default stuff for autogenerated pages.
			$order_string = '`';
			
			switch($data['sort_by'])
			{
				case 'caption':
					$order_string .= 'caption_'.SITE_LANG;
					break;
				
				case 'date':
					$order_string .= 'modified';
					break;
				
				default:
					$order_string .= $data['sort_by'];
			}
			
			$order_string .= '`'.($data['sort_direction'] == 'desc' ? ' DESC' : '');
			
			switch ($data['auto_generated'])
			{
				case 'none':
					break; #nothing is automatically generated
				
				case 'brief':
					if (!($res = query('SELECT `id`, `caption_'.SITE_LANG.'`, `delimiter` FROM `cm_struct_main` WHERE `parent` = \''.$id.'\' AND `mode` > 4 ORDER BY '.$order_string.' LIMIT '.strval(($gen_num -1) * REND_BRIEF_PER_PAGE).', '.REND_BRIEF_PER_PAGE.';'))) #4 is INVISIBLE
					{
						echo (SITE_LANG == 'bg' ? 'Има проблем с рендването на страницата. Работи се по отстраняването му.' : 'A rendering problem occured. It is now being fixed.');
						render_notify('page, rendering brief list', $id);
						return;
					}
					
					while ($row = mysql_fetch_assoc($res))
					{
						$renres .= '<br>'.lrender_list_brief($row['id'], str_replace(array('|', '~'), ' ', no_tags($row['caption_'.SITE_LANG])));
						if ($row['delimiter'])
							$renres .= lrender_list_brief_delimiter();
					}
					
					break;
					
				case 'thumbnails':
					if (!($res = query('SELECT `id`, `caption_'.SITE_LANG.'`, `descr_'.SITE_LANG.'`, `delimiter`, `type` FROM `cm_struct_main` WHERE `parent` = \''.$id.'\' AND `mode` > 4 ORDER BY '.$order_string.' LIMIT '.strval(($gen_num -1) * REND_THUMB_PER_PAGE).', '.REND_THUMB_PER_PAGE.';'))) #4 is INVISIBLE
					{
						echo (SITE_LANG == 'bg' ? 'Има проблем с рендването на страницата. Работи се по отстраняването му.' : 'A rendering problem occured. It is now being fixed.');
						render_notify('page, rendering thumbnail list', $id);
						return;
					}
					
					$renres .= '<br><table cellspacing="10" cellpadding="5" align="center">';
					
					for ($i = 1; $i <= mysql_num_rows($res); $i++)
					{
						$row = mysql_fetch_assoc($res);
						$image_file = ($row['type'] == 'image' ? 'thumb.jpg' : 'preview.jpg');
						
						$renres .= lrender_list_thumbnails($row['id'], str_replace(array('|', '~'), array('<br>', ' '), no_tags($row['caption_'.SITE_LANG])), no_tags($row['descr_'.SITE_LANG]), (file_exists(PATH_PAGES.$row['id'].'/'.$image_file) ? $image_file : false), $i, $i == mysql_num_rows($res));
					}
					
					$renres .= '</table>';
					
					break;
					
				case 'full':
					if (!($res = query('SELECT `id`, `caption_'.SITE_LANG.'`, `delimiter`, `type`, `descr_'.SITE_LANG.'` FROM `cm_struct_main` WHERE `parent` = \''.$id.'\' AND `mode` > 4 ORDER BY '.$order_string.' LIMIT '.strval(($gen_num -1) * REND_FULL_PER_PAGE).', '.REND_FULL_PER_PAGE.';'))) #4 is INVISIBLE
					{
						echo (SITE_LANG == 'bg' ? 'Има проблем с рендването на страницата. Работи се по отстраняването му.' : 'A rendering problem occured. It is now being fixed.');
						render_notify('page, rendering full list', $id);
						return;
					}
					$renres .= '<br>';
					
					while ($row = mysql_fetch_assoc($res))
					{
						$descr = no_tags($row['descr_'.SITE_LANG]);
						$image_file = ($row['type'] == 'image' ? 'thumb.jpg' : 'preview.jpg');
						
						$renres .= lrender_list_full($row['id'], str_replace(array('|', '~'), ' ', no_tags($row['caption_'.SITE_LANG])), (trim($descr) == '' ? false : $descr), (file_exists(PATH_PAGES.$row['id'].'/'.$image_file) ? $image_file : false));
						if ($row['delimiter'])
							$renres .= lrender_list_full_delimiter();
					}
					break;
				
				case 'slideshow':
					if (!($res = query('SELECT `id` FROM `cm_struct_main` WHERE `parent` = \''.$id.'\' AND `mode` > 4 ORDER BY '.$order_string.' LIMIT '.strval($gen_num -1).', 1;')))
					{
						echo (SITE_LANG == 'bg' ? 'Има проблем с рендването на страницата. Работи се по отстраняването му.' : 'A rendering problem occured. It is now being fixed.');
						render_notify('page, rendering slide query', $id);
						return;
					}
					
					// if (mysql_num_rows($res) == 0)
					// {
						// echo (SITE_LANG == 'bg' ? 'Има проблем с рендването на страницата. Работи се по отстраняването му.' : 'A rendering problem occured. It is now being fixed.');
						// render_notify('page, rendering slide: no slides found when user expected to find', $id);
						// return;
					// }
					$current_slide = mysql_fetch_assoc($res); ;
					$renres .= '<br>'.render_page_core(get_page_data($current_slide['id']), true);
					break;
			
			} # End of auto_generated algorythms.
			
			if ($data['auto_generated'] != 'none' && $gen_num_max > 1)
			{
				$renres .= lrender_gen_bar($id, ($page_num == 1 ? false : $page_num), ($gen_num > 1 ? $gen_num -1 : false), ($gen_num < $gen_num_max ? $gen_num +1 : false), $gen_num, $gen_num_max);
			}
			#$renres .= '<br>';
			# End of exclusive page rendering
		}
		 else
			$renres .= render_page_core($data); #Render non-page data
		
		# Render more commons footer
		if ($data['mode'] == 'under_upgrade')
			$renres .= lrender_under_upgrade();
			
		if ($data['mode'] == 'under_upgrade_smart' && $data['type'] == 'page')
			if ($data['use_text_bg_for_all'] && SITE_LANG == 'en')
				$renres .= lrender_under_upgrade();
		
		if ($data['show_back_link'] && $data['parent'] != 'menu') {
			if (!($res = query('SELECT `caption_'.SITE_LANG.'` FROM `cm_struct_main` WHERE `id` = \''.$data['parent'].'\' LIMIT 0, 1;')))
					{
						echo (SITE_LANG == 'bg' ? 'Има проблем с рендването на страницата. Работи се по отстраняването му.' : 'A rendering problem occured. It is now being fixed.');
						render_notify('page, rendering back link - getting parent\'s name', $id);
						return;
					}
			$row = mysql_fetch_assoc($res);
			$renres .= lrender_show_back_link($data['parent'], $row['caption_'.SITE_LANG]);
		}
		/*
		if (
			$data['show_end_bar'] &&
			(
				($gen_num === false && $page_num == count($pages)) ||
				($gen_num !== false && $gen_num == $gen_num_max)
			)
		)
			$renres .= lrender_show_end_bar();
		*/
		
		if ($data['can_comment'])
		{
			# Add commenting functionality here
			# Not available still;
		}
		
		return $renres;
	}
	
	function render_page_core($data, $slideshowed = false) {
		# Pages that are not of type `page` are rendered here
		$renres = '';
		
		if ($data['type'] == 'page')
		{
			# PAGE -----------------------------------------
			# select language version
			$text = $data['text_'.
					(
						SITE_LANG == 'bg'
						? 'bg'
						: ($data['use_text_bg_for_all']
							? 'bg'
							: 'en'
						)
					)];
			
			# remove sub-paging
			$text = str_replace('<div style="page-break-after: always;"><span style="display: none;">&nbsp;</span></div>', '', $text);
			
			# if text is empty, use descr
			if ($text == '')
				$text = $data['descr_'.SITE_LANG];
			
			# render text
			$renres .= '<span class="maintext">'.$text.'</span>';
		}
		 else
		{
			$descr = no_tags($data['descr_'.SITE_LANG]);
			if (trim($descr) != '')
				$renres .= '<p class="maintext">'.$descr.'</p><br>';
			
			switch ($data['type'])
			{
				# IMAGE ----------------------------------------
				case 'image':
					if (file_exists(PATH_PAGES.$data['id'].'/small.jpg'))
						$renres .= lrender_image($data['id'], $slideshowed);
					else
						$renres .= lrender_image_na();
					break;
				
				# ADDON ----------------------------------------
				case 'addon':
					if (!file_exists($data['page']))
					{
						$renres .= (SITE_LANG == 'bg' ? 'Програмата не може да бъде заредена!. Администраторът беше известен.' : 'Cannot load addon. The administrator was notified.');
						render_notify('cannot load addon: file '.$data['page'].' not found', $data['id']);
					}
					 else
					{
						include_once $data['page'];
						if (!function_exists('addon_execute'))
						{
							$renres .= (SITE_LANG == 'bg' ? 'Програмата не може да бъде заредена!. Администраторът беше известен.' : 'Cannot load addon. The administrator was notified.');
							render_notify('cannot load addon: function addon_execute() not found', $data['id']);
						}
						 else
							$renres .= addon_execute($data['options']);
					}
					break;
				
				# LINK -----------------------------------------
				case 'link':
					if (trim(no_tags($data['link'])) == '')
						$renres .= 'Страницата не е настроена: няма зададен линк.<br>';
					 else
					{
						switch($data['target'])
						{
							case 'external':
								$renres .= '
									<script type="text/javascript">
										window.open(\''.no_tags($data['link']).'\',\'_blank\');
									</script>'.
									
									(SITE_LANG == 'bg' ? 'Следната страница беше отворена в нов прозорец.' : 'The following page was opened in a new window').
									'<br><br><a href="'.no_tags($data['link']).'" class="link1" target="_blank">'.no_tags($data['link']).'</a><br>';
								break;
							
							case 'internal':
								$renres .= '
									<center>
										<iframe src="'.no_tags($data['link']).'" width="80%" height="500">'.
											(SITE_LANG == 'bg' ? 'Препратка:' : 'The link is as follows:').
											'<br><br><a href="'.no_tags($data['link']).'" class="link1" target="_blank">'.no_tags($data['link']).'</a><br>
										</iframe>
									</center>
									';
								break;
						}
					}
					break;
				
				case 'math':
					$lang_selector = SITE_LANG;
					if ($data['use_text_bg_for_all'])
						$lang_selector = 'bg';
					if (is_file(PATH_PAGES.$data['id'].'/attach_'.$lang_selector.'/'.$data['attach_filename_'.$lang_selector]))
						//$renres .= '<table align="right" style="margin: 0px 0px 20px 20px;"><tr><td><center><a href="'.URL_PAGES.$data['id'].'/attach_'.$lang_selector.'/'.$data['attach_filename_'.$lang_selector].'" class="system" target="_blank"><img src="./layout/gfx/pdfb.gif" alt="свали текста като PDF" style="margin: 10px 20px 0px 20x; border: none;"><br>Свали текста<br>като PDF.</a></center></td></tr></table>';
						$renres .= '<div align="right" style="z-index: 2; position: absolute;"><center><a href="'.URL_PAGES.$data['id'].'/attach_'.$lang_selector.'/'.$data['attach_filename_'.$lang_selector].'" class="system" target="_blank"><img src="./layout/gfx/pdfb.gif" alt="свали текста като PDF" style="margin: 10px 20px 0px 20x; border: none;"><br>Свали текста<br>като PDF.</a></center></div>';
					if (file_exists(PATH_PAGES.$data['id'].'/text_'.$lang_selector.'/mth_text.php')) {
						$renres .= str_replace('<?PAGE_PATH?>', URL_PAGES.$data['id'].'/text_'.$lang_selector.'/', file_get_contents(PATH_PAGES.$data['id'].'/text_'.$lang_selector.'/mth_text.php'));
						include PATH_PAGES.$lang_selector.'/mth_text.php';
					}
					
					#$renres .= 'MathPage MAML: '.$data['id'];
			}
			
			$renres .= '<br>';
		}
		
		return $renres;
	}
?>