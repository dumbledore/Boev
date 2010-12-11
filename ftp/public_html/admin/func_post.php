<?php
	function fix_post_checkboxes() {
		$fields = func_get_args();
		for ($i = 0; $i < count($fields); $i++)
		{
			$_POST[$fields[$i]] = (isset($_POST[$fields[$i]]) ? '1' : '0');
		}
	}
	
	function validate_page() {
		if (
			!isset($_POST['page_uid']) ||
			!isset($_POST['cm_caption_bg']) ||
			!isset($_POST['cm_caption_en']) ||
			!isset($_POST['cm_descr_bg']) ||
			!isset($_POST['cm_descr_en']) ||
			!isset($_POST['cm_keywords_bg']) ||
			!isset($_POST['cm_keywords_en'])
		) {
			#var_dump($_POST);
			jump_to(URL_ADMIN.'main.php?page=cm_browse', 'func_post.php: invalid POST data');
		}
			if (!isset($_POST['cm_mode']))
				$_POST['cm_mode'] = 'normal';
			
			switch($_POST['cm_mode'])
			{
				case 'inactive':
					break;
				
				case 'invisible':
					break;
					
				case 'normal':
					break;
				
				case 'under_upgrade':
					break;
					
				case 'under_upgrade_smart':
					break;
				
				default:
					$_POST['cm_mode'] = 'normal';
			}
		
		#fix the checkboxes
		fix_post_checkboxes('cm_del_image', 'cm_registered_only', 'cm_can_comment', 'cm_searchable', 'cm_show_name', 'cm_show_back_link', 'cm_show_end_bar', 'cm_delimiter');
		
		#fix SETs
		
			#fix lang_visibility set
			$lang_vis_temp = array();
			if (isset($_POST['cm_lang_visibility_bg']))
				$lang_vis_temp[] = 'bg';
			if (isset($_POST['cm_lang_visibility_en']))
				$lang_vis_temp[] = 'en';
			$_POST['cm_lang_visibility'] = implode(',', $lang_vis_temp);
	}
?>