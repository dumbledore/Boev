<?php
	include_once '_connect.php';
	define('ACCESS_LEVEL', 'editor');
	include_once SYS_ALLOW_EXECUTION;
	$layout_title = '����������� �� ��������';
	#$layout_onload = 'document.page.cm_caption_bg.focus();'; #not wanted
	include 'layout_header.php';
	include_once PATH_ENGINE.'func_showmsg.php';
	include_once 'func_layout.php';
	include_once URL_EDITOR.'fckeditor.php';
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
<script type="text/javascript">
function showInfoAutoGen() {
	var myopts = document.getElementById("cm_auto_generated").options;
	var mysel = "unset";
	
	for (i = 0; i < myopts.length; i++)
	{
		if (myopts[i].selected === true)
		{
			mysel = myopts[i].value;
		}
		document.getElementById("cm_auto_generated_" + myopts[i].value).style.display = "none";
	}
	
	document.getElementById("cm_auto_generated_unset").style.display = "none";
	document.getElementById("cm_auto_generated_" + mysel).style.display = "block";
}
</script>
<form action="gates.php?page=cm_edit_page" name="page" method="post" id="cm_form_page" enctype="multipart/form-data">
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
				echo section_add('����������� ���������� �� ������������', '
					��� ���������� ������� ����� � ���� ��, ������ � ������������ � �� ���� ����������� ���������� � ����������.<br>
					����������� ������������ ���������� �� ������� ���� ������ �� ���������� (��� ��� �����).<br>
					<br>
					<table width="100%" cellspacing="0" cellpadding="0" title="����, �������� �����. ��� �� ��� �������, �������� `���`.">
						<tr>
							<td>
								<select id="cm_auto_generated" name="cm_auto_generated" size="5" style="width: 110px" class="common" onchange="showInfoAutoGen();">
									<option value="none"'.($all_data['auto_generated'] == 'none' ? ' selected="selected"' : '').'>���</option>
									<option value="brief"'.($all_data['auto_generated'] == 'brief' ? ' selected="selected"' : '').'>������</option>
									<option value="thumbnails"'.($all_data['auto_generated'] == 'thumbnails' ? ' selected="selected"' : '').'>�����</option>
									<option value="full"'.($all_data['auto_generated'] == 'full' ? ' selected="selected"' : '').'>�����</option>
									<option value="slideshow"'.($all_data['auto_generated'] == 'slideshow' ? ' selected="selected"' : '').'>Slideshow</option>
								</select>
							</td>
							<td style="width: 15px;"></td>
							<td align="left">
								<div id="cm_auto_generated_unset" class="common" style="display: '.($all_data['auto_generated'] == '' ? 'block' : 'none').';">
									����, �������� ����� ������.<br>
									��� �� ��� �������, �������� ����� `���`.
									<br><br>
								</div>
								
								<div id="cm_auto_generated_none" class="common" style="display: '.($all_data['auto_generated'] == 'none' ? 'block' : 'none').';">
									����������, ����� �� �� ��������� �����������, �������� ������, ������� ��-����.<br>
									���� �� ��������, ����� �� �������� ����� ������ ��� ���� ������ ���������� ���.
								</div>
								
								<div id="cm_auto_generated_brief" class="common" style="display: '.($all_data['auto_generated'] == 'brief' ? 'block' : 'none').';">
									������������ �� �������� �����������.<br>
									������� �� �������� ���������� �� ���������� (��� ��� ������)<br>
									� ������ � ������� �� ���������� �� ���-����������.
								</div>
								
								<div id="cm_auto_generated_thumbnails" class="common" style="display: '.($all_data['auto_generated'] == 'thumbnails' ? 'block' : 'none').';">
									������������ �� �������� �����������.<br>
									������� �� ������, �������� ������� � ��������.<br>
									��� ������ �����, ����������� ���� ����� � ��������� ������.
								</div>
								
								<div id="cm_auto_generated_full" class="common" style="display: '.($all_data['auto_generated'] == 'full' ? 'block' : 'none').';">
									������������ �� �������� �����������.<br>
									� ���������� �� ������������ �� ���-���������� �� ������� ������, �������� �������<br>
									�� ����������, ��������, ���������� ������ � �����. �������� �� ����������� ���������.
								</div>
								
								<div id="cm_auto_generated_slideshow" class="common" style="display: '.($all_data['auto_generated'] == 'slideshow' ? 'block' : 'none').';">
									������������ �� �������� �����������.<br>
									���-���������� �� ������ � ����� slideshow - � ������� �� ���������� � ���� ���� �����.
								</div>
							</td>
						</tr>
					</table>
				', false, 100, 'contentgeneration', true);
				
				echo section_add('��������� �� ������������', '
					��� ������������ �� ���������� � ����������� ����������, ��� �� ������� ������� �� ��������� �� ���������.<br>
					<span class="formlabel">
						<br>��� �� �����������:&nbsp;
						<select name="cm_sort_direction" class="common">
							<option value="asc"'.($all_data['sort_direction'] == 'asc' ? ' selected="selected"' : '').'>�������� ���</option>
							<option value="desc"'.($all_data['sort_direction'] == 'desc' ? ' selected="selected"' : '').'>�������� ���</option>
						</select>
						&nbsp;&nbsp;&nbsp;
						��������:&nbsp;
						<select name="cm_sort_by" class="common">
							<option value="position"'.($all_data['sort_by'] == 'position' ? ' selected="selected"' : '').'>�������</option>
							<option value="caption"'.($all_data['sort_by'] == 'caption' ? ' selected="selected"' : '').'>��������</option>
							<option value="date"'.($all_data['sort_by'] == 'date' ? ' selected="selected"' : '').'>����</option>
						</select>
					</span>
				', false, 100, 'sort', true);
				
				echo section_add_begin('���������� �� ���������', false, 100, 'text_bulgarian', false);
			
				$bg_editor = new FCKeditor($_GET['id'].'_bg');
				$bg_editor -> BasePath = URL_EDITOR;
				$bg_editor -> Value = $all_data['text_bg'];
				$bg_editor -> Width = 700;
				$bg_editor -> Height = 400;
				
				if ($_SESSION['credentials'] == 'admin')
					$bg_editor -> ToolbarSet = 'AdminVersion';
				 else
				{
					$bg_editor -> Config['LinkBrowser'] = false;
					$bg_editor -> Config['LinkUpload'] = true;
					$bg_editor -> Config['LinkDlgHideAdvanced'] = true;
					$bg_editor -> Config['ImageBrowser'] = false;
					$bg_editor -> Config['ImageUpload'] = true;
					$bg_editor -> Config['ImageDlgHideLink'] = true;
					$bg_editor -> Config['ImageDlgHideAdvanced'] = true;
					$bg_editor -> Config['FlashBrowser'] = false;
					$bg_editor -> Config['StartupShowBlocks'] = true ;
					$bg_editor -> ToolbarSet = 'EditorVersion';
				}
				$bg_editor -> Config['PageUID'] = $_GET['id'];
				$bg_editor -> Create();
				
				echo section_add_end();
				
				echo section_add('��������� �� ���������� �����', '
					<span class="formlabel"><input name="cm_use_text_bg_for_all" type="checkbox"'.($all_data['use_text_bg_for_all'] ? ' checked="checked"' : '').'>&nbsp;��������� ���������� ����� ������ ����������</span><br>
					<br>
					��� ����������� ������ �� � �����, �������� �� �� ������� ����������� �� ������ �����.
				', false, 100, 'readiness', true);
				
				echo section_add_begin('���������� �� ���������', false, 100, 'text_english', true);
				
				$en_editor = new FCKeditor($_GET['id'].'_en');
				$en_editor -> BasePath = URL_EDITOR;
				$en_editor -> Value = $all_data['text_en'];
				$en_editor -> Width = 700;
				$en_editor -> Height = 400;
				
				if ($_SESSION['credentials'] == 'admin')
					$en_editor -> ToolbarSet = 'AdminVersion';
				 else
				{
					$en_editor -> Config['LinkBrowser'] = false;
					$en_editor -> Config['LinkUpload'] = true;
					$en_editor -> Config['LinkDlgHideAdvanced'] = true;
					$en_editor -> Config['ImageBrowser'] = false;
					$en_editor -> Config['ImageUpload'] = true;
					$en_editor -> Config['ImageDlgHideLink'] = true;
					$en_editor -> Config['ImageDlgHideAdvanced'] = true;
					$en_editor -> Config['FlashBrowser'] = false;
					$en_editor -> Config['StartupShowBlocks'] = true ;
					$en_editor -> ToolbarSet = 'EditorVersion';
				}
				$en_editor -> Config['PageUID'] = $_GET['id'];
				$en_editor -> Create();
				
				echo section_add_end();
				
			?>
		</div>
	</div>
		<image type="img" src="./gfx/icons/btn_edit.png" alt="�������" onclick="page.submit()" style="cursor: pointer;">&nbsp;&nbsp;
		<?php
			echo '<a href="main.php?page=cm_browse&amp;parent='.$all_data['parent'].'"><img src="./gfx/icons/btn_cancel.png" alt="������"></a>';
		?>
</form>