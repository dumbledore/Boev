<?php
	include_once '_connect.php';
	define('ACCESS_LEVEL', 'editor');
	include_once SYS_ALLOW_EXECUTION;
	$layout_title = '����������� �� addon';
	$layout_onload = 'document.page.cm_caption_bg.focus();';
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
<form action="gates.php?page=cm_edit_link" name="page" method="post" id="cm_form_page" enctype="multipart/form-data">
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
					echo section_add('��������� �� ��������', '
					<table cellspacing="0" cellpadding="0" class="formlabel">
						<tr>
							<td>
								��������:
							</td>
							<td style="width: 10px;"></td>
							<td>
								<input name="cm_link" class="formfield" size="75" maxlength=256" value="'.htmlspecialchars($all_data['link']).'">
							</td>
						</tr>
						<tr style="height: 5px;">
							<td></td>
						</tr>
						<tr>
							<td>
								���������:
							</td>
							<td></td>
							<td>
								<select name="cm_target" size="2" class="common">
									<option value="internal"'.($all_data['target'] == 'internal' ? ' selected="selected"' : '').'>� �����</option>
									<option value="external"'.($all_data['target'] == 'external' ? ' selected="selected"' : '').'>� ��� ��������</option>
								</select>
							</td>
						</tr>
					</table>
				');
			?>
		</div>
	</div>
		<image type="img" src="./gfx/icons/btn_edit.png" alt="�������" onclick="page.submit()" style="cursor: pointer;">&nbsp;&nbsp;
		<?php
			echo '<a href="main.php?page=cm_browse&amp;parent='.$all_data['parent'].'"><img src="./gfx/icons/btn_cancel.png" alt="������"></a>';
		?>
</form>