<?php
	# Generic CPL layouting
	function section_add($title, $content, $dialog = false, $width = 100, $div_id = false, $div_closed = false) {
		return
			section_add_begin($title, $dialog, $width, $div_id, $div_closed).$content.section_add_end($dialog, $width);
	}
	
	# add section in two calls
	function section_add_begin($title, $dialog = false, $width = 100, $div_id = false, $div_closed = false) {
		return 
			($div_id !== false ? section_add_js() : '').
		'
			<div class="sec_title" style="width: '.$width.'%;'.($dialog ? ' color: #FFCC00;' : '').'">&nbsp;'.mb_strtoupper($title,'CP1251').'&nbsp;'.($div_id === false ? '' : '<img id="cm_div_'.$div_id.'i" '.($div_closed ? 'alt="покажи" title="покажи"' : 'alt="скрий" title="скрий"').' style="cursor: pointer;" src="./gfx/icons/'.($div_closed ? 'show.gif' : 'hide.gif').'" style="" onclick="displaySection(\''.$div_id.'\');">').'</div>
			<div style="width: '.$width.'%; '.($div_id === false ? '' : ($div_closed ? ' display: none;' : '').'" id="cm_div_'.$div_id).'">
			<div style="background-image: url(./gfx/bkg/bkg-grad-600px.png); width: '.$width.'%;">
				<div style="border-width: 1px; border-style: solid;">
					<div style="padding: 10px;">
		';
	}
	
	function section_add_end($dialog = false, $width = 100) {
		return '
					</div>
				</div>
			</div></div>'.
			(!$dialog ? '<div style="width: '.$width.'%;" class="cm_shadow"></div>' : '')
			.
			($dialog ? '' : '<br>');
	}
	
	function section_add_js() {
		if (!defined('SECTION_ADDED_BEFORE'))
		{
			define('SECTION_ADDED_BEFORE');
			return '
				<script type="text/javascript">
					function displaySection(id) {
						if (document.getElementById(\'cm_div_\' + id).style.display == "none") //i.e. HIDDEN
						{ //show
							document.getElementById(\'cm_div_\' + id + \'i\').src = "./gfx/icons/hide.gif";
							document.getElementById(\'cm_div_\' + id + \'i\').title = "скрий";
							document.getElementById(\'cm_div_\' + id + \'i\').alt = "скрий";
							document.getElementById(\'cm_div_\' + id).style.display = "block"
						}
						 else
						{ //hide
							document.getElementById(\'cm_div_\' + id + \'i\').src = "./gfx/icons/show.gif";
							document.getElementById(\'cm_div_\' + id + \'i\').title = "покажи";
							document.getElementById(\'cm_div_\' + id + \'i\').alt = "покажи";
							document.getElementById(\'cm_div_\' + id).style.display = "none"
						}
					}
				</script>
			';
		}
		 else
		{
			return '';
		}
	}
	
	//Content Management Functions
	
	#Editing form common for all pages
	function layout_page_edit($id = '', $caption_bg = 'неозаглавена', $caption_en = 'untitled', $descr_bg = 'няма описание', $descr_en = 'no description', $homepage = false, $type = 'page', $mode = '', $searchable = 1, $keywords_bg = 'дума', $keywords_en = 'keyword', $registered_only = 0, $can_comment = 0, $show_name = 1, $show_back_link = 1, $show_end_bar, $delimiter = 0, $lang_visibility = '') {
		return '
			<script type="text/javascript" src="cm_page_check.js"></script>'.
				section_add('Заглавия',
				'
					<table cellspacing="0" cellpadding="0" class="formlabel" title="Тъй като заглавията ефективно се включват при търсене в сайта, добре е те да бъдат конкретно по въпроса. Ако това е невъзможно, попълнете полетата за ключови думи.">
						<tr>
							<td>
								Заглавие на български:
							</td>
							<td style="width: 10px;"></td>
							<td>
								<input name="cm_caption_bg" class="formfield" size="75" maxlength=256" value="'.htmlspecialchars($caption_bg).'">
							</td>
						</tr>
						<tr style="height: 5px;">
							<td></td>
						</tr>
						<tr>
							<td>
								Заглавие на английски:
							</td>
							<td></td>
							<td>
								<input name="cm_caption_en" class="formfield" size="75" maxlength=256" value="'.htmlspecialchars($caption_en).'">
							</td>
						</tr>
						<tr style="height: 15px;">
							<td></td>
						</tr>
						<tr>
							<th colspan="3" class="common">
								Използвайте <span style="color: #FF3300;">|</span> и <span style="color: #FF3300;">~</span>, за да въведете принудително нов ред,<br>
								когато заглавието се показва респективно в менютата и над текста.
						</td>
					</table>
				')
				.
				section_add('Описание',
				'
					<table width="100%" cellspacing="0" cellpadding="0" class="formlabel">
						<tr>
							<td width="200" align="center">
								<table class="img_preview" style="width: 64px; height: 64px;" cellspacing="0" cellpadding="0">
									<tr valign="middle">
										<td align="center">'.
										($type != 'image'
										?
											(
												file_exists(PATH_PAGES.$id.'/preview.jpg')
												? '<img src="'.URL_ADMIN.'image.php?id='.$id.'">'
												: '<img src="./gfx/icons/no-image.gif">'
											)
										:
											(
												file_exists(PATH_PAGES.$id.'/thumb.jpg')
												? '<a href="'.URL_ADMIN.'image.php?id='.$id.'&amp;type=i" target="_blank"><img src="'.URL_ADMIN.'image.php?id='.$id.'&amp;type=t"></a>'
												: '<img src="./gfx/icons/no-image.gif">'
											)
										)
										.'
										</td>
									</tr>
								</table>
								<br>
								<span class="common">'.
									($type != 'image'
									? '
										Изберете картинка,<br>
										описваща страницата.<br>
										(максимално '.round(IMG_MAX_SIZE / (1024 * 1024), 2).' MB)<br>
										</span><br>
										<input type="file" name="cm_image" size="1" onchange="filterFile(this, new Array('.IMG_SUPPORTED_TYPES.'), \'картинка\');">
									'
									: '
										Изберете снимка<br>
										за добавяне в албума.<br>
										(максимално '.round(IMG_MAX_SIZE / (1024 * 1024), 2).' MB)<br>
										</span><br>
										<input type="checkbox" name="cm_auto_image" checked="checked" onchange="hideFile();">Автоматично оразмеряване<br><br>
										
										<div style="display: none" id="cm_image_caption">
											оригинал ('.IMG_RES.' px):<br>
										</div>
										<input type="file" name="cm_image" size="1" onchange="filterFile(this, new Array('.IMG_SUPPORTED_TYPES.'), \'картинка\');">
										<div style="display: none" id="cm_image_manual">
											<br>
											умалена ('.IMG_SMALL_RES.' px):<br>
											<input type="file" name="cm_image_small" size="1" onchange="filterFile(this, new Array('.IMG_SUPPORTED_TYPES.'), \'картинка\');"><br><br>
											slideshow ('.IMG_SLIDE_RES.' px):<br>
											<input type="file" name="cm_image_slide" size="1" onchange="filterFile(this, new Array('.IMG_SUPPORTED_TYPES.'), \'картинка\');"><br><br>
											thumbnail ('.IMG_THUMB_RES.'x'.IMG_THUMB_RES.' px):<br>
											<input type="file" name="cm_image_thumb" size="1" onchange="filterFile(this, new Array('.IMG_SUPPORTED_TYPES.'), \'картинка\');">
										</div>
									'
									)
								.
								($type != 'image'
								?
									(
										file_exists(PATH_PAGES.$id.'/preview.jpg')
										? '<br><br><input type="checkbox" name="cm_del_image" onchange="disableFile(false);"> Изтрийте картинката'
										: ''
									)
								:
									(
										file_exists(PATH_PAGES.$id.'/thumb.jpg')
										? '<br><br><input type="checkbox" name="cm_del_image" onchange="disableFile(true);"> Изтрийте картинката'
										: ''
									)
								)
								.'
							</td>
							<td width="10"></td>
							<td>
								Кратко описание на български (~4 реда):<br>
								<textarea name="cm_descr_bg" class="formfield" cols="64" rows="4">'.$descr_bg.'</textarea>
								<br><br>
								Кратко описание на английски (~4 реда):<br>
								<textarea name="cm_descr_en" class="formfield" cols="64" rows="4">'.$descr_en.'</textarea>
							</td>
						<tr>
					</table>
				', false, 100, 'description', $type !== 'image')
				.
				section_add('Начална страница','
				<span class="formlabel">
					<input type="checkbox" name="cm_homepage"> Задай за начална
				</span><br>
				<br>
				Изберете, ако желаете тази да е началната страница при отварянето на сайта.<br>
				Разбира се, може да има само една начална страница.
				', false, 100, 'homepage', true)
				.
				section_add('Видимост',
				'
					<table width="100%" cellspacing="0" cellpadding="0" title="Моля, изберете режим. Ако не сте уверени, изберете `нормална`.">
						<tr>
							<td>
								<select id="cm_mode" name="cm_mode" size="5" style="width: 145px" class="common" onchange="showInfo();">
									<option value="inactive"'.($mode == 'inactive' ? ' selected="selected"' : '').'>Неактивна</option>
									<option value="invisible"'.($mode == 'invisible' ? ' selected="selected"' : '').'>Невидима</option>
									<option value="normal"'.($mode == 'normal' ? ' selected="selected"' : '').'>Нормална</option>
									<option value="under_upgrade"'.($mode == 'under_upgrade' ? ' selected="selected"' : '').'>В разработка</option>
									<option value="under_upgrade_smart"'.($mode == 'under_upgrade_smart' ? ' selected="selected"' : '').'>В разработка (smart)</option>
								</select>
							</td>
							<td style="width: 15px;"></td>
							<td align="left">
								<div id="cm_mode_unset" class="common" style="display: '.($mode == '' ? 'block' : 'none').';">
									Моля, изберете режим отляво.<br>
									Ако не сте уверени, изберете режим `нормална`.
									<br><br>
								</div>
								
								<div id="cm_mode_inactive" class="common" style="display: '.($mode == 'inactive' ? 'block' : 'none').';">
									Неактивните страници не могат да бъдат видяни или отворени в сайта.
								</div>
								
								<div id="cm_mode_invisible" class="common" style="display: '.($mode == 'invisible' ? 'block' : 'none').';">
									Невидимите страници не се показват в менютата, но могат<br>
									да бъдат отворени, ако е посочен директен адрес.<br>
									<br>
									Удобни са за демонстриране на съдържанието пред колеги,<br>
									без страницата да попада в официалното меню.
								</div>
								
								<div id="cm_mode_normal" class="common" style="display: '.($mode == 'normal' ? 'block' : 'none').';">
									Нормалният режим е обичайният режим на работа.<br>
									<br>
									Използва се за страници, които се очаква да бъдат<br>
									разглеждани от посетителите на сайта.
								</div>
								
								<div id="cm_mode_under_upgrade" class="common" style="display: '.($mode == 'under_upgrade' ? 'block' : 'none').';">
									Страниците в `режим на разработка` се включват в менютата<br>
									и могат да бъдат разглеждани от потребителите, но последните<br>
									биват уведомявани, че страницата не е завършена.<br>
									<br>
									Примерна употреба е уеб-страница, която е необходимо да бъде<br>
									видима, но едновременно и да се обозначи, че се очаква редакция<br>
									на съдържанието.
								</div>
								
								<div id="cm_mode_under_upgrade_smart" class="common" style="display: '.($mode == 'under_upgrade_smart' ? 'block' : 'none').';">
									Същото като `режим на разработка`, но се прилага автоматично (предвид дали липсва текст).
									<br>
									Примерна употреба е уеб-страница, която има текст на български,<br>
									но няма текст на английски<br>
								</div>
								<br>
							</td>
						</tr>
						<tr>
							<th colspan="3" class="common">
							<br>
								Видимост при активен език:
								<span class="formlabel">
									<input type="checkbox" name="cm_lang_visibility_bg"'.((strpos($lang_visibility, 'bg') !== false) ? ' checked="checked"' : '').'>BG&nbsp;<input type="checkbox" name="cm_lang_visibility_en"'.((strpos($lang_visibility, 'en') !== false) ? ' checked="checked"' : '').'>EN
								</span>
							</th>
						</tr>
					</table>
				', false, 100, 'visibility', true)
				.
				section_add('Потребителски права',
				'
					<span class="formlabel" title="Ако е избрано, страницата ще може да бъде разглеждана само от регистрирани потребители. Нерегистрираните такива ще бъдат уведомявани за този факт."><input name="cm_registered_only" type="checkbox"'.($registered_only ? ' checked="checked"' : '').'>&nbsp;Само за регистрирани</span>
					&nbsp;
					<span class="formlabel" title="Ако е избрано, регистрираните потребители ще могат да оставят коментари под съдържанието на страницата."><input name="cm_can_comment" type="checkbox"'.($can_comment ? ' checked="checked"' : '').'>&nbsp;Включи коментаринето</span>
				', false, 100, 'users', true)
				.
				section_add('Търсене',
				'
					<table cellspacing="0" cellpadding="0" class="formlabel" title="Въведете ключови думи, които ще бъдат използвани от търсачката. Разделяйте ги със запетая.">
						<tr>
							<th colspan="3">
								<span title="Ако не е избрано, тази страница ще бъде подминавана от търсачката."><input name="cm_searchable" type="checkbox"'.($searchable ? ' checked="checked"' : '').'>&nbsp;Показвай в търсачката</span>
							</th>
						</tr>
						<tr style="height: 5px;">
							<th colspan="3"></th>
						</tr>
						<tr>
							<td>
								Ключови думи на български (разделени със запетая):
							</td>
							<td style="width: 10px;"></td>
							<td>
								<input name="cm_keywords_bg" class="formfield" size="48" value="'.htmlspecialchars($keywords_bg).'">
							</td>
						</tr>
						<tr style="height: 5px;">
							<th colspan="3"></th>
						</tr>
						<tr>
							<td>
								Ключови думи на английски (разделени със запетая):
							</td>
							<td></td>
							<td>
								<input name="cm_keywords_en" class="formfield" size="48" value="'.htmlspecialchars($keywords_en).'">
							</td>
						</tr>
						<tr style="height: 15px;">
							<th colspan="3"></th>
						</tr>
						<tr>
							<th colspan="3" class="common">
								Резултатите от търсенето се подреждат по следния начин:<br>
								1. Намерени с ключова дума<br>
								2. Намерени със заглавие<br>
								3. Намерени в описателния текст (FULLTEXT)<br>
								3. Намерени в съдържанието на страницата (FULLTEXT).
							</th>
						</tr>
					</table>
				', false, 100, 'search', true)
				.
				section_add('Други','
					<span class="formlabel" title="Автоматично показва заглавието на страницата"><input name="cm_show_name" type="checkbox"'.($show_name ? ' checked="checked"' : '').'>&nbsp;Показвай заглавието</span><br>
					<span class="formlabel" title="Автоматично добавя линк в края на страницата към бащината такава"><input name="cm_show_back_link" type="checkbox"'.($show_back_link ? ' checked="checked"' : '').'>&nbsp;Показвай линк към предишната страница</span><br>
					<span class="formlabel" title="Графически указва последната страница"><input name="cm_show_end_bar" type="checkbox"'.($show_end_bar ? ' checked="checked"' : '').'>&nbsp;Обозначавай последната страница с картинка</span><br>
					<span class="formlabel" title="Добавя разделител в списъците (например в менюта, в автоматично генерираното съдържание и т.н.) след изписването на страницата"><input name="cm_delimiter" type="checkbox"'.($delimiter ? ' checked="checked"' : '').'>&nbsp;Сложи разделител в списъците</span><br>
				', false, 100, 'others', true);
	}
	
	function cm_row_msg($msg, $trash = FALSE) {
		echo '
			<tr class="cm_table_rows" style="background-color: #FFFFFF;'.($error ? ' color: #FF3300;' : '').'">
				<th colspan="'.($trash ? '14' : '17').'" align="center">'.$msg.'</th>
			</tr>
		';
	}
	
	function cm_row_add($id, $pos = 0, $caption = '..', $type = '', $size = 0, $modified = '', $homepage = NULL) {
		global $CM_PAGE_TYPES; #declare page types
		
		if ($homepage === $id) 
			$homepage = '&nbsp;<span style="color: #FF3300;">(начална)</span>';
		else
			$homepage = '';
	
		global $cm_row_number;
		if (!isset($cm_row_number))
			$cm_row_number = 0;
		echo '
		<tr class="cm_table_rows_'.($cm_row_number % 2 == 0 ? '1' : '2').'">
			<td>'.
				($pos != 0 ? '<input type="checkbox" name="'.$id.'">' : '')
			.'</td>
			<td class="cm_div_2"></td>
			<td align="center">'.
				(
				$pos != 0
					?
					$pos
					:
					'<a href="main.php?page=cm_browse&amp;parent='.$id.'"><img src="./gfx/icons/cm_parent.gif"></a>'
				)
			.'</td>
			<td class="cm_div_2"></td>
			<td class="cm_cell_pad_1">'.
				(
				$pos == 0 || $type == 'page'
					?
					'<a href="main.php?page=cm_browse&amp;parent='.$id.'" class="cm_table_link">'.no_tags($caption).'</a>'.$homepage
					:
					'<span title="Страниците от тип `'.$CM_PAGE_TYPES[$type]['caption'].'` не могат да съдържат други в себе си. Моля, изберете моливчето отстрани, ако искате да редактирате страницата.">'.no_tags($caption).'</span>'.$homepage
				)
			.'</td>
			<td class="cm_div_2"></td>
			<td class="cm_cell_pad_1">
				<span title="'.$CM_PAGE_TYPES[$type]['description'].'">'.$CM_PAGE_TYPES[$type]['caption'].'</span>
			</td>
			<td class="cm_div_2"></td>
			<td class="cm_cell_pad_1">'.
				($pos == 0 ? '' : ($size > 1024 ? (round($size / 1024)).' MB' : $size.' KB'))
			.'</td>
			<td class="cm_div_2"></td>
			<td class="cm_cell_pad_1">'.
				$modified
			.'</td>
			<td class="cm_div_2"></td>'.
			(
			($pos != 0)
				?
				'<td width="14" class="cm_cell_pad_1">
						<a href="main.php?page=cm_edit_'.$type.'&amp;id='.$id.'"><img src="./gfx/icons/cm_edit.gif" alt="редактирай" title="редактирай"></a>
				</td>
				<td width="14" class="cm_cell_pad_2">
						<a href="#" onclick="deleteOne(\''.$id.'\', \''.str_replace(array('"','\'', '\\'), '', $caption).'\')"><img src="./gfx/icons/cm_del.gif" alt="изтрий" title="изтрий"></a>
				</td>
				<td width="14" class="cm_cell_pad_2">'.
					(
					$pos != 1
						?
						'<a href="#" onclick="moveUpDnOne(\''.$id.'\', 0)"><img src="./gfx/icons/cm_up.gif" alt="премести нагоре" title="премести нагоре"></a>'
						:
						'<img src="./gfx/icons/cm_up_d.gif" alt="не можете да преместите първата страница по-нагоре" title="не можете да преместите първата страница по-нагоре">'
					)
				.'</td>
				<td width="14" class="cm_cell_pad_2">'.
					(
					$pos != CM_NUM_ROWS
						?
						'<a href="#" onclick="moveUpDnOne(\''.$id.'\', 1)"><img src="./gfx/icons/cm_down.gif" alt="премести надолу" title="премести надолу"></a>'
						:
						'<img src="./gfx/icons/cm_down_d.gif" alt="не можете да преместите последната страница по-надолу" title="не можете да преместите последната страница по-надолу">'
					)
				.'<td width="14" class="cm_cell_pad_2">
					<a href="#" onclick="moveOne(\''.$id.'\')"><img src="./gfx/icons/cm_move.gif" alt="премести в друга папка" title="премести в друга папка"></a>
				</td>
				<td width="14" class="cm_cell_pad_2">
					<a href="#" onclick="homePage(\''.$id.'\')"><img src="./gfx/icons/cm_homepage.gif" alt="задай за начална страница" title="задай за начална страница"></a>
				</td>
				<td width="14" class="cm_cell_pad_2">
					<a href="#" onclick="notifyUsers(\''.$id.'\', \''.str_replace(array('"','\'', '\\'), '', $caption).'\')"><img src="./gfx/icons/cm_notify.gif" alt="извести, че е обновена" title="извести, че е обновена"></a>
				</td>
				'
				:
				'<th colspan="7"></th>'
			)
			.'
		</tr>
		';
		$cm_row_number++;
	}
	
	function cm_row_add2($id, $caption = '..', $type = '', $size = 0, $modified = '') {
		global $cm_row_number;
		if (!isset($cm_row_number))
			$cm_row_number = 0;
		echo '
		<tr class="cm_table_rows_'.($cm_row_number % 2 == 0 ? '1' : '2').'">
			<td>
				<input type="checkbox" name="'.$id.'">
			</td>
			<td class="cm_div_2"></td>
			<td align="center">'.
				($cm_row_number + 1)
			.'</td>
			<td class="cm_div_2"></td>
			<td class="cm_cell_pad_1">'.
				no_tags($caption)
			.'</td>
			<td class="cm_div_2"></td>
			<td class="cm_cell_pad_1">'.
				$type
			.'</td>
			<td class="cm_div_2"></td>
			<td class="cm_cell_pad_1">'.
				($type != 'page' ? '--':
					($size > 1024 ? (round($size / 1024)).' MB' : $size.' KB')
				)
			.'</td>
			<td class="cm_div_2"></td>
			<td class="cm_cell_pad_1">'.
				$modified
			.'</td>
			<td class="cm_div_2"></td>
				<td width="14" class="cm_cell_pad_2">
						<a href="#" onclick="deleteOne(\''.$id.'\', \''.str_replace(array('"','\'', '\\'), '', $caption).'\')"><img src="./gfx/icons/cm_del.gif" alt="изтрий" title="изтрий"></a>
				</td>
				<td width="14" class="cm_cell_pad_2">
						<a href="#" onclick="restoreOne(\''.$id.'\')"><img src="./gfx/icons/um_act.gif" alt="възстанови" title="възстанови"></a>
				</td>
			</tr>
		';
		$cm_row_number++;
	}
	
	//User menagement
	
	function um_row_add($username, $credentials, $email, $active) {
		global $um_row_number;
		if (!isset($um_row_number))
			$um_row_number = 0;
		echo '
		<tr class="cm_table_rows_'.($um_row_number % 2 == 0 ? '1' : '2').'">
			<td><input type="checkbox" name="'.$username.'"></td>
			<td class="cm_div_2"></td>
			<td class="cm_cell_pad_1" align="center">'.($active ? 'да' : 'не').'</td>
			<td class="cm_div_2"></td>
			<td class="cm_cell_pad_1">
				<a href="main.php?page=um_edit&amp;username='.$username.'" class="cm_table_link">'.$username.'</a>
			</td>
			<td class="cm_div_2"></td>
			<td class="cm_cell_pad_1">
				'.$credentials.'
			</td>
			<td class="cm_div_2"></td>
			<td class="cm_cell_pad_1">'.
				$email
			.'</td>
			<td class="cm_div_2"></td>
			<td width="14" class="cm_cell_pad_1">
				<a href="main.php?page=um_edit&amp;username='.$username.'"><img src="./gfx/icons/cm_edit.gif" alt="редактирай" title="редактирай"></a>
			</td>
			<td width="14" class="cm_cell_pad_1">
				<a href="#" onclick="deleteOne(\''.$username.'\')"><img src="./gfx/icons/cm_del.gif" alt="изтрий" title="изтрий"></a>
			</td>
			<td width="14" class="cm_cell_pad_1">
				<a href="#" onclick="activateOne(\''.$username.'\', '.($active ? '0' : '1').')"><img src="./gfx/icons/um_act.gif" alt="'.($active ? 'де' : '').'активирай" title="'.($active ? 'де' : '').'активирай"></a>
			</td>
		</tr>
		';
		$um_row_number++;
	}
	
?>