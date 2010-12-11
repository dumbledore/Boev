<?php
	#Define constants
	define('REND_BRIEF_PER_PAGE', 32);
	
	define('REND_THUMB_PER_LINE', 3);
	define('REND_THUMB_LINES_PER_PAGE', 4);
	define('REND_THUMB_PER_PAGE', REND_THUMB_PER_LINE * REND_THUMB_LINES_PER_PAGE);
	
	define('REND_FULL_PER_PAGE', 16);
	
	define('REND_FULL_IMG_SIZE', 48); //Image size in FULL mode
	
	#include_once PATH_SITE.'layout/_settings.php'; #include layout settings
	
	#temporal global vars
	global $BOTTOM_HR_PRESENT;
	$BOTTOM_HR_PRESENT = false;
	
	# MENU rendering
	function lrender_menu_item($id, $caption) {
		return '<td>
					'.(
						$_GET['page'] == $id
						? '<span class="menu">'.$caption.'</span><br>'
						: '<a class="menu" href="index.php?page='.$id.'">'.$caption.'</a><br>'
					).'
				</td>
		';
	}
	
	function lrender_menu_delimiter() {
		return '';
	}
	
	# PAGE rendering
	function lrender_homepage_not_set() {
		return '<table width="100%" style="height: 100%;" align="center" cellspacing="0" cellpadding="0">
					<tr valign="middle" style="height: 100%;">
						<td align="center" width="100%">
							<img src="logo-small.jpg" alt="'.(SITE_LANG == 'bg' ? 'Биоматематика' : 'Biomathematics').'"><br><br>
							<span class="maintext">'.(SITE_LANG == 'bg' ? 'Сайтът е в момент на разработка' : 'This page is under development').'
						</td>
					</tr>
				</table>
		';
	}

	# Page is only for registered users
	function lrender_reg_only() {
		return (
				SITE_LANG == 'bg'
				? 'Страницата е само за регистрирани потребители!<br>Моля, логнете се или се обърнете към администратора за помощ.'
				: 'This page is for registered users only!<br>Please, login in or contact the administrator for more information.'
			);
	}
	
	function lrender_caption($id, $subscribed, $caption) {
		$subscribed_text = '';
		if (isset($_SESSION['username']))
		{
			if (SITE_SHOW_SUBSCRIBE_BUTTON === true)
			{
				$subscribed_text .= '&nbsp;';
				
				if ($subscribed == '')
					$subscribed = array();
				else
					$subscribed = explode(';', $subscribed);
				
				if (!in_array($_SESSION['username'], $subscribed))
					$subscribed_text .= lrender_subscribe($id);
				else
					$subscribed_text .= lrender_unsubscribe($id);
			}
		}
		return '<h1 class="title">'.$caption.$subscribed_text.'<hr></h1>';
	}
	
	function lrender_subscribe($id) {
		return '<a href="subscribe.php?mode=1&amp;page='.$id.'" title="'.(SITE_LANG == 'bg' ? 'запиши се за автоматично информиране' : 'subscribe to automatic notification').'"><img border="0" src="cache.php?id=layout/gfx/subscribe.gif" alt="'.(SITE_LANG == 'bg' ? 'запиши се за автоматично информиране' : 'subscribe to automatic notification').'"></a>';
	}
	
	function lrender_unsubscribe($id) {
		return '<a href="subscribe.php?mode=0&amp;page='.$id.'" title="'.(SITE_LANG == 'bg' ? 'отпиши информирането' : 'unsubscribe').'"><img src="cache.php?id=layout/gfx/unsubscribe.gif" border="0" alt="'.(SITE_LANG == 'bg' ? 'отпиши информирането' : 'unsubscribe').'"></a>';
	}
	
	function lrender_under_upgrade() {
		global $BOTTOM_HR_PRESENT;
		
		$res =
			(
				$BOTTOM_HR_PRESENT
				? '<br>'
				: '<hr>'
			).
			'<span class="system">'
			.(
			 SITE_LANG == 'bg'
			 ? 'Забележка: Този раздел все още не е официално завършен.'
			 : 'Note: This section is being developed. Thus there is a possibility of inconsistency and/or inaccuracy.'
			).
			'</span>'
		;
		
		$BOTTOM_HR_PRESENT = true;
		return $res;
	}
	
	function lrender_page_bar($page, $gen_num, $prev, $next, $current, $max) {
		global $BOTTOM_HR_PRESENT;
		
		$res =
			'<br>'.(
				$BOTTOM_HR_PRESENT
				? ''
				: '<hr>'
			).'<table width = "100%" cellspacing="0" cellpadding="0">
				<tr valign="middle">
					<td align="left">&nbsp;&nbsp;'.
						($prev === false
						? ''
						: '<a href="index.php?page='.$page.'&amp;pnum=1'.($gen_num === false ? '' : '&amp;gnum='.$gen_num).'" title="първа страница"><img border="0" src="cache.php?id=layout/gfx/p_first.gif" alt="първа страница"></a>&nbsp;&nbsp;<a href="index.php?page='.$page.'&amp;pnum='.$prev.($gen_num === false ? '' : '&amp;gnum='.$gen_num).'" class="system"><img border="0" src="cache.php?id=layout/gfx/p_prev.gif" alt="">&nbsp;&nbsp;'.(SITE_LANG == 'bg' ? 'предишна страница' : 'previous page').'</a>'
						)
					.'</td>
					<td align="center">'.
						(SITE_LANG == 'bg' ? 'страница '.$current.' от '.$max : 'page '.$current.' of '.$max)
					.'</td>
					<td align="right">'.
						($next === false
						? ''
						: '<a href="index.php?page='.$page.'&amp;pnum='.$next.($gen_num === false ? '' : '&amp;gnum='.$gen_num).'" class="system">'.(SITE_LANG == 'bg' ? 'следваща страница' : 'next page').'&nbsp;<img src="cache.php?id=layout/gfx/p_next.gif" border="0" alt=""></a>&nbsp;&nbsp<a href="index.php?page='.$page.'&amp;pnum='.$max.($gen_num === false ? '' : '&amp;gnum='.$gen_num).'" title="последна страница"><img border="0" src="cache.php?id=layout/gfx/p_last.gif" alt="последна страница"></a>'
						)
					.'&nbsp;&nbsp;</td>
				</tr>
			</table>
		';
		$BOTTOM_HR_PRESENT = true;
		return $res;
	}
	
	function lrender_gen_bar($page, $page_num, $prev, $next, $current, $max) {
		global $BOTTOM_HR_PRESENT;
		
		$res =
			'<br>'.(
				$BOTTOM_HR_PRESENT
				? ''
				: '<hr>'
			).'
			<table width = "100%" cellspacing="0" cellpadding="0">
				<tr valign="center">
					<td align="left">&nbsp;&nbsp;'.
						($prev === false
						? ''
						: '<a href="index.php?page='.$page.'&amp;gnum=1'.($page_num === false ? '' : '&amp;pnum='.$page_num).'" title="първа страница"><img border="0" src="cache.php?id=layout/gfx/p_first.gif" alt="първа страница"></a>&nbsp;&nbsp;<a href="index.php?page='.$page.'&amp;gnum='.$prev.($page_num === false ? '' : '&amp;pnum='.$page_num).'" class="system"><img border="0" src="cache.php?id=layout/gfx/p_prev.gif" alt="">&nbsp;&nbsp;'.(SITE_LANG == 'bg' ? 'предишна страница' : 'previous page').'</a>'
						)
					.'</td>
					<td align="right">'.
						($next === false
						? ''
						: '<a href="index.php?page='.$page.'&amp;gnum='.$next.($page_num === false ? '' : '&amp;pnum='.$page_num).'" class="system">'.(SITE_LANG == 'bg' ? 'следваща страница' : 'next page').'&nbsp;<img border="0" src="cache.php?id=layout/gfx/p_next.gif" alt=""></a>&nbsp;&nbsp<a href="index.php?page='.$page.'&amp;gnum='.$max.($page_num === false ? '' : '&amp;pnum='.$page_num).'" title="последна страница"><img border="0" src="cache.php?id=layout/gfx/p_last.gif" alt="последна страница"></a>'
						)
					.'&nbsp;&nbsp;</td>
				</tr>
			</table>
		';
		$BOTTOM_HR_PRESENT = true;
		return $res;
	}

	function lrender_show_back_link($parent, $parent_name) {
		global $BOTTOM_HR_PRESENT;
		
		$res =
			'<br>'.(
				$BOTTOM_HR_PRESENT
				? ''
				: '<hr>'
			).'
			<table cellspacing="0" cellpadding="0"><tr valign="middle"><td>
				<a href="index.php?page='.$parent.'" class="system"><img border="0" src="cache.php?id=layout/gfx/p_up.gif">&nbsp;&nbsp;'.
				(SITE_LANG == 'bg' ? 'върни се в &laquo;'.$parent_name.'&raquo;' : 'back to &laquo;ьяаьяа&raquo;')
				.'</a>
			</td></tr></table>
		';
		$BOTTOM_HR_PRESENT = true;
		return $res;
	}
	
	function lrender_list_brief($id, $caption) {
		return '<a class="caption" href="index.php?page='.$id.'">'.$caption.'</a><br>';
	}
	
	function lrender_list_brief_delimiter() {
		return '<br>';
	}
	
	function lrender_list_thumbnails($id, $caption, $descr, $image_file, $i, $force_close) {
		return 
		($i % REND_THUMB_PER_LINE == 1 ? '<tr valign="top" style="height: '.round(0.25 * SITE_LAYOUT_WIDTH_TEXT).'px;">' : '').
		'
			<td align="center" width="'.round(0.25 * SITE_LAYOUT_WIDTH_TEXT).'" '.(trim($descr) != '' ? ' title="'.htmlspecialchars($descr).'"' : '').'>
				<table style="width: '.(IMG_THUMB_RES+16).'px; height: '.(IMG_THUMB_RES+16).'px;" cellspacing="0" cellpadding="0">
					<tr valign="middle">
						<td align="center" class="imgbox3">
							<a href="index.php?page='.$id.'">
								<img border="0" src="'.($image_file === false ? 'cache.php?id=layout/gfx/no-image.gif' : URL_PAGES.$id.'//'.$image_file).'" alt="" style=" vertical-align: bottom;">
							</a>
						</td>
					</tr>
				</table>
				<a href="index.php?page='.$id.'" class="caption2">'.$caption.'</a><br><br>
			</td>
		'
		.($force_close || $i % REND_THUMB_PER_LINE == 0 ? '</tr>' : '');
	}
	
	function lrender_list_full($id, $caption, $descr, $image_file) {
		if ($image_file !== false)
		{
			$imgOsize = getimagesize(PATH_PAGES.$id.'//'.$image_file);
			$imgsize = array(0,0);
			
			if ($imgOsize[1] > $imgOsize[0])
			{ //portrait
				$imgsize[1] = REND_FULL_IMG_SIZE;
				$imgsize[0] = round(REND_FULL_IMG_SIZE / $imgOsize[1] * $imgOsize[0]);
			}
			 else
			{ //landscape
				$imgsize[0] = REND_FULL_IMG_SIZE;
				$imgsize[1] = round(REND_FULL_IMG_SIZE / $imgOsize[0] *$imgOsize[1]);
			}
		}
		
		return '
		<table width="716" cellspacing="0" cellpadding="0">
			<tr valign="middle">
				'.(
				$descr !== false
				? '
					<td width="200">
						<a href="index.php?page='.$id.'" class="caption">'.$caption.'</a>
					</td>
					<td width="16">
					</td>
					<td width="500">
						<table class="imgbox2" width="100%" style="height: 100%;">
							<tr valign="middle">
									'.($image_file === false ? '' : '<td class="imgbox2"><img width="'.$imgsize[0].'" height="'.$imgsize[1].'" src="'.URL_PAGES.$id.'//'.$image_file.'" alt="" class="imgbox2"></td>').'
								<td class="imgbox2">
									'.$descr.'
								</td>
							</tr>
						</table>
					</td>
				'
				: '
					<th colspan="3" align="left">
						<a href="index.php?page='.$id.'" class="caption">'.$caption.'</a>
					</th>
				'
			).'</tr>
		</table>
		<br><br>
		';
	}
	
	function lrender_list_full_delimiter() {
		return '<br>';
	}
	
	function lrender_image($id, $slideshowed = false) {
		return '<center><a href="'.URL_PAGES.$id.'/image.jpg" target="_blank"><img src="'.URL_PAGES.$id.($slideshowed ? '/slide.jpg' : '/small.jpg').'" alt=""></a></center>';
	}
	
	function lrender_image_na() {
		return '
			<table width="'.IMG_SMALL_RES.'" height="'.IMG_SMALL_RES.'" style="background-color: #FFFFFF; border: 2px solid #EDD6B3;">
				<tr valign="center">
					<td class="maintext" style="text-align: center;">'.
						(SITE_LANG == 'bg'
						? 'КАРТИНКАТА ЛИПСВА'
						: 'IMAGE NOT AVAILABLE'
						)
					.'</td>
				</tr>
			</table>
		';
	}
	
	function lrender_show_end_bar() {
		return '<br><center><img src="cache.php?id=layout/gfx/end_bar.gif" alt=""></center>';
	}
	
	# Render Image Functions
	
	#FILTERS
	function img_filter_1($in)
	{
		# Filters images with ALT tag
		
		# comes in:
		# 0: <img src="cells.jpg" width="200" alt= "From left to right: human erythrocyte, thrombocyte, leukocyte." class="ASD">
		# 1: src="cells.jpg" width="200" 
		# 2: alt="From left to right: human erythrocyte, thrombocyte, leukocyte."
		# 3: From left to right: human erythrocyte, thrombocyte, leukocyte.
		# 4: class="ASD"
			
			return '
				<table class="imgbox" align="right" width="1"><tr><td class="imgbox" align="center"><div><center><@@@ '.$in[1].'alt="'.$in[3].'"'.$in[4].' style="float: none;"></center></div>'.($in[3] != '' ? '<div class="imgbox">'.$in[3].'</div>' : '').'</td></tr></table>'
			;
		}

	function img_filter_2($in)
	{
		# Filters images WITHOUT ALT tag
		
		# comes in:
		# 0: <img src="asdasdasd">
			
			return '
				<table class="imgbox" align="right"><tr><td class="imgbox"><div>
			'.
				$in[0]
			.'
				</div></td></tr></table>
			';
	}
	
	function img_filter_3($in)
	{
		#Restores img w/ ALT inicial tag, i.e. @@@ -> img
		return '<img '.$in[1].'>';
	}

	function lrender_images($text) {
	
		#regex
		$tag_img_alt = '<\<img ([^\>]*)(alt[\s]*=[\s]*["]([^\"]*)["]){1,1}([^\>]*)[/]?\>>is';
		$tag_img_wo_alt = '<\<img [^\>]*[/]?\>>is';
		$tag_img_restore = '<\<@@@ ([^\>]*)[/]?\>>is';
		
		#applying filters
		$text = preg_replace_callback($tag_img_alt, 'img_filter_1', $text);
		$text = preg_replace_callback($tag_img_wo_alt, 'img_filter_2', $text);
		$text = preg_replace_callback($tag_img_restore, 'img_filter_3', $text);
		$text = str_replace('<mathtype', '<img', $text);
		
		return $text;
	}
?>