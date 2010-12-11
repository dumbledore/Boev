<?php
	include_once '_connect.php';
	define('ACCESS_LEVEL', 'admin');
	include_once SYS_ALLOW_EXECUTION;
	$layout_title = 'редактиране на math страница';
	#$layout_onload = 'document.page.cm_caption_bg.focus();'; #not wanted
	include 'layout_header.php';
	include_once PATH_ENGINE.'func_showmsg.php';
	include_once 'func_layout.php';
	include_once 'sys_cm.php';

	$all_data = page_get_all($_GET['id']);
	
	if ($all_data == CM_INVALID_NAME)
	{
		jump_to(URL_ADMIN.'main.php?page=cm_browse', 'invalid id');
		exit;
	}
	
	if ($all_data == CM_NOT_FOUND)
	{
		showmsg('page_deleted', URL_ADMIN.'main.php?page=cm_browse');
		exit;
	}
	
?>
<form action="gates.php?page=cm_edit_math" name="page" method="post" id="cm_form_page" enctype="multipart/form-data">
<?php
	echo '
		<input type="hidden" name="page_uid" value="'.$_GET['id'].'">
		<input type="hidden" name="parent" value="'.$all_data['parent'].'">
	';
?>
	<div align="center" style="width: 740px;" class="cm_table_rows_text">
		<div style="padding: 10px;">
			<?php
				echo layout_page_edit($all_data['id'], $all_data['caption_bg'], $all_data['caption_en'], $all_data['descr_bg'], $all_data['descr_en'], $all_data['id'] == $homepage, $all_data['type'], $all_data['mode'], $all_data['searchable'], $all_data['keywords_bg'], $all_data['keywords_en'], $all_data['registered_only'], $all_data['can_comment'], $all_data['show_name'], $all_data['show_back_link'], $all_data['show_end_bar'], $all_data['delimiter'], $all_data['lang_visibility']);
				
				echo section_add('Съдържание на български',  '
					<span class="formlabel">
					ZIP архив (MAML):&nbsp;<input type="file" name="cm_zip_bg" size="70"><br>
					<br>
					Оригинален документ (PDF, DOC, ...):&nbsp;<input type="file" name="cm_pdf_bg" size="50">'.
					($all_data['attach_filename_bg'] == '' ? '' : '<br>Има качен файл: <span style="color: #AA5555;">' . $all_data['attach_filename_bg']).'</span>'
				, false, 100, 'text_bulgarian', false);
				
				echo section_add('Готовност на английския текст', '
					<span class="formlabel"><input name="cm_use_text_bg_for_all" type="checkbox"'.($all_data['use_text_bg_for_all'] ? ' checked="checked"' : '').'>&nbsp;Използвай българския текст вместо английския</span><br>
					<br>
					Ако английският превод не е готов, изберете да се показва българският на негово място.
				', false, 100, 'readiness', true);
				
				echo section_add('Съдържание на английски', '
					<span class="formlabel">
					ZIP архив (MAML):&nbsp;<input type="file" name="cm_zip_en" size="70"><br>
					<br>
					Оригинален документ (PDF, DOC, ...):&nbsp;<input type="file" name="cm_pdf_en" size="50">'.
					($all_data['attach_filename_en'] == '' ? '' : '<br>Има качен файл: <span style="color: #AA5555;">' . $all_data['attach_filename_en']).'</span>'
				, false, 100, 'text_english', true);
			?>
		</div>
	</div>
		<image type="img" src="./gfx/icons/btn_edit.png" alt="промени" onclick="page.submit()" style="cursor: pointer;">&nbsp;&nbsp;
		<?php
			echo '<a href="main.php?page=cm_browse&amp;parent='.$all_data['parent'].'"><img src="./gfx/icons/btn_cancel.png" alt="откажи"></a>';
		?>
</form>