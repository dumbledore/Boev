<?php
	include_once '_connect.php';
	define('ACCESS_LEVEL', 'editor');
	include_once SYS_ALLOW_EXECUTION;
	$layout_title = 'редактиране на страница';
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
				echo section_add('Автоматично генериране на съдържанието', '
					Ако страницата съдържа други в себе си, удобно е съдържанието й да бъде автоматично генерирано и обновявано.<br>
					Автоматично генерираното съдържание се показва след текста на страницата (ако има такъв).<br>
					<br>
					<table width="100%" cellspacing="0" cellpadding="0" title="Моля, изберете режим. Ако не сте уверени, изберете `без`.">
						<tr>
							<td>
								<select id="cm_auto_generated" name="cm_auto_generated" size="5" style="width: 110px" class="common" onchange="showInfoAutoGen();">
									<option value="none"'.($all_data['auto_generated'] == 'none' ? ' selected="selected"' : '').'>Без</option>
									<option value="brief"'.($all_data['auto_generated'] == 'brief' ? ' selected="selected"' : '').'>Кратко</option>
									<option value="thumbnails"'.($all_data['auto_generated'] == 'thumbnails' ? ' selected="selected"' : '').'>Албум</option>
									<option value="full"'.($all_data['auto_generated'] == 'full' ? ' selected="selected"' : '').'>Пълно</option>
									<option value="slideshow"'.($all_data['auto_generated'] == 'slideshow' ? ' selected="selected"' : '').'>Slideshow</option>
								</select>
							</td>
							<td style="width: 15px;"></td>
							<td align="left">
								<div id="cm_auto_generated_unset" class="common" style="display: '.($all_data['auto_generated'] == '' ? 'block' : 'none').';">
									Моля, изберете режим отляво.<br>
									Ако не сте уверени, изберете режим `без`.
									<br><br>
								</div>
								
								<div id="cm_auto_generated_none" class="common" style="display: '.($all_data['auto_generated'] == 'none' ? 'block' : 'none').';">
									Страниците, които не се генерират автоматично, показват текста, въведен по-долу.<br>
									Това са страници, които не съдържат други такива или имат твърде специфичен вид.
								</div>
								
								<div id="cm_auto_generated_brief" class="common" style="display: '.($all_data['auto_generated'] == 'brief' ? 'block' : 'none').';">
									Съдържанието се генерира автоматично.<br>
									Изписва се кратката информация за страницата (ако има такава)<br>
									и списък с линкове от заглавията на под-страниците.
								</div>
								
								<div id="cm_auto_generated_thumbnails" class="common" style="display: '.($all_data['auto_generated'] == 'thumbnails' ? 'block' : 'none').';">
									Съдържанието се генерира автоматично.<br>
									Показва се списък, съдържащ линкове и картинки.<br>
									Ако искате албум, използвайте този режим и добавяйте снимки.
								</div>
								
								<div id="cm_auto_generated_full" class="common" style="display: '.($all_data['auto_generated'] == 'full' ? 'block' : 'none').';">
									Съдържанието се генерира автоматично.<br>
									В зависимост от съдържанието на под-страниците се показва списък, съдържащ линкове<br>
									от заглавията, картинки, описателен тексто и други. Използва се автоматично групиране.
								</div>
								
								<div id="cm_auto_generated_slideshow" class="common" style="display: '.($all_data['auto_generated'] == 'slideshow' ? 'block' : 'none').';">
									Съдържанието се генерира автоматично.<br>
									Под-страниците се виждат в режим slideshow - с пълното си съдържание и една след друга.
								</div>
							</td>
						</tr>
					</table>
				', false, 100, 'contentgeneration', true);
				
				echo section_add('Сортиране на съдържанието', '
					Ако съдържанието на страницата е автоматично генерирано, тук се посочва начинът на сортиране на списъците.<br>
					<span class="formlabel">
						<br>Ред на сортирането:&nbsp;
						<select name="cm_sort_direction" class="common">
							<option value="asc"'.($all_data['sort_direction'] == 'asc' ? ' selected="selected"' : '').'>възходящ ред</option>
							<option value="desc"'.($all_data['sort_direction'] == 'desc' ? ' selected="selected"' : '').'>низходящ ред</option>
						</select>
						&nbsp;&nbsp;&nbsp;
						Критерий:&nbsp;
						<select name="cm_sort_by" class="common">
							<option value="position"'.($all_data['sort_by'] == 'position' ? ' selected="selected"' : '').'>позиция</option>
							<option value="caption"'.($all_data['sort_by'] == 'caption' ? ' selected="selected"' : '').'>заглавие</option>
							<option value="date"'.($all_data['sort_by'] == 'date' ? ' selected="selected"' : '').'>дата</option>
						</select>
					</span>
				', false, 100, 'sort', true);
				
				echo section_add_begin('Съдържание на български', false, 100, 'text_bulgarian', false);
			
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
				
				echo section_add('Готовност на английския текст', '
					<span class="formlabel"><input name="cm_use_text_bg_for_all" type="checkbox"'.($all_data['use_text_bg_for_all'] ? ' checked="checked"' : '').'>&nbsp;Използвай българския текст вместо английския</span><br>
					<br>
					Ако английският превод не е готов, изберете да се показва българският на негово място.
				', false, 100, 'readiness', true);
				
				echo section_add_begin('Съдържание на английски', false, 100, 'text_english', true);
				
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
		<image type="img" src="./gfx/icons/btn_edit.png" alt="промени" onclick="page.submit()" style="cursor: pointer;">&nbsp;&nbsp;
		<?php
			echo '<a href="main.php?page=cm_browse&amp;parent='.$all_data['parent'].'"><img src="./gfx/icons/btn_cancel.png" alt="откажи"></a>';
		?>
</form>