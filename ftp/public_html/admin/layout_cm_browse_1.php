<?php
	include_once '_connect.php';
	include_once SYS_ALLOW_EXECUTION;
?>

<!-- Next forms are used to send the neccessary vaibles through POST rather than GET,
as it is cruical for gate pages, viz. deleting / moving / etc.-->
<script type="text/javascript" src="./scripts/dlg/modal-message.js"></script>
<script type="text/javascript" src="./scripts/dlg/ajax.js"></script>
<script type="text/javascript" src="./scripts/dlg/ajax-dynamic-content.js"></script>

<?php
	$lastpagetype = 0;
	if (isset($_COOKIE['lastpagetype']) && is_numeric($_COOKIE['lastpagetype']))
		if (round($_COOKIE['lastpagetype']) >= 0 && round($_COOKIE['lastpagetype']) < count($CM_PAGE_TYPES_NAMES))
			$lastpagetype = round($_COOKIE['lastpagetype']);
	
	$avail_types = '';
	for ($i = 0; $i < count($CM_PAGE_TYPES_NAMES); $i++)
		$avail_types .= '<span title="'.$CM_PAGE_TYPES[$CM_PAGE_TYPES_NAMES[$i]]['description'].'"><input type="radio" name="cm_type" value="'.$i.'"'.($i == $lastpagetype ? ' checked="checked"' : '').'>&nbsp;&nbsp;'.$CM_PAGE_TYPES_NAMES[$i].'</span>&nbsp;&nbsp;';
		
	$new_page = section_add('НОВА СТРАНИЦА', '
		<form action="gates.php?page=cm_add" method="post" name="cm_add">
			<input type="hidden" name="parent" value="'.$clear['parent'].'">
			<table cellspacing="0" cellpadding="0" class="formlabel">
				<tr>
					<td width="100">
						Заглавие на български:
					</td>
					<td style="width: 10px;"></td>
					<td>
						<input name="cm_caption_bg" class="formfield" size="40">
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
						<input name="cm_caption_en" class="formfield" size="40">
					</td>
				</tr>
				<tr style="height: 5px;">
					<td></td>
				</tr>
				<tr>
					<td>
						Тип на страницата
					</td>
					<td></td>
					<td>'.$avail_types.'</td>
				</tr>
			</table>
			<br>
			<center>
				<a href="#" onclick="checkPage()"><img alt="СЪЗДАЙ" src="./gfx/icons/btn_create.png"></a>
				&nbsp;&nbsp;&nbsp;
				<a href="#" onclick="cm_add.reset(); msg_add_page.close();"><img src="./gfx/icons/btn_cancel.png" alt="откажи"></a>
			</center>
		</form>
	', true);
	
	$notify_users_1 = section_add_begin('ИЗВЕСТИ ЗА ОБНОВЛЕНИЕ', true).'
		<form action="gates.php?page=cm_notify" method="post" name="cm_notify">
			<input type="hidden" name="parent" value="'.$clear['parent'].'">
			<input type="hidden" name="cm_selected" value="';
	
	$notify_users_2 = '">
			<table cellspacing="0" cellpadding="0" class="formlabel">
				<tr>
					<td class="common">
						Използвайки този формуляр, вие автоматично ще информирате<br>
						записалите се потребители за тази страница или страници,<br>
						съдържащи я (т.е. родителски страници), че страницата<br>
						съдържа обновено съдържание.
					</td>
				</tr>
				<tr style="height: 15px;">
					<td></td>
				</tr>
				<tr>
					<td>
						Обновена страница:
							<span style="color: #FF3300;">&nbsp;
	';
	
	$notify_users_3 = '
							</span>
					</td>
				</tr>
				<tr style="height: 15px;">
					<td></td>
				</tr>
				<tr>
					<td>
						Текст на съобщението (препоръчително на латиница):<br>
						<br>
						<textarea name="cm_notify_message" class="formfield" rows="6" cols="50">Sadarzanieto beshe obnoveno.</textarea>
					</td>
				</tr>
			</table>
			<br>
			<center>
				<input type="image" alt="СЪЗДАЙ" src="./gfx/icons/btn_notify.png">
				&nbsp;&nbsp;&nbsp;
				<a href="#" onclick="cm_notify.reset(); msg_notify.close();"><img src="./gfx/icons/btn_cancel.png" alt="откажи"></a>
			</center>
		</form>
	'.section_add_end(true);
	
	echo '
		<script type="text/javascript">
			msg_add_page = new DHTML_modalMessage();
			msg_add_page.setHtmlContent(\''.str_replace(array("\t", "\r", "\n"), '', $new_page).'\');
			msg_add_page.setSource(false);
			msg_add_page.shadowDivVisible = false;
			msg_add_page.setSize(400,220);
			
			var notify_users_1 = \''.str_replace(array("\t", "\r", "\n"), '', $notify_users_1).'\';
			var notify_users_2 = \''.str_replace(array("\t", "\r", "\n"), '', $notify_users_2).'\';
			var notify_users_3 = \''.str_replace(array("\t", "\r", "\n"), '', $notify_users_3).'\';
			msg_notify = new DHTML_modalMessage();
			msg_notify.setSource(false);
			msg_notify.setSize(400,10);
			msg_notify.shadowDivVisible = false;
		</script>
	';
?>

<div style="display: none;">
	<form name="cm_remove" action="gates.php?page=cm_remove" method="post">
		<input type="hidden" name="cm_selected">
		<?php
			echo '<input type="hidden" name="parent" value="'.$clear['parent'].'">';
		?>
	</form>

	<form name="cm_move_updn" action="gates.php?page=cm_move_updn" method="post">
		<input type="hidden" name="cm_selected">
		<input type="hidden" name="cm_direction">
		<?php
			echo '<input type="hidden" name="parent" value="'.$clear['parent'].'">';
		?>
	</form>

	<form name="cm_move" action="main.php?page=cm_move" method="post">
		<input type="hidden" name="cm_selected">
		<?php
			echo '<input type="hidden" name="parent" value="'.$clear['parent'].'">';
		?>
	</form>
	
	<form name="cm_homepage" action="gates.php?page=cm_homepage" method="post">
		<input type="hidden" name="cm_selected">
		<?php
			echo '<input type="hidden" name="parent" value="'.$clear['parent'].'">';
		?>
	</form>
</div>

<form name="cm_selected">
<table align="center" cellpadding="0" cellspacing="0" style="width: 600px; border-width: 1px; background-color: #FFFFFF;" class="cm_table_rows_text">
	<tr class="cm_table_headers">
		<td align="center" width="10" class="cm_cell_pad_1">
			
		</td>
		<td width="1" class="cm_div_1"></td>
		<td align="center" width="20" class="cm_cell_pad_1">
			№
		</td>
		<td width="1" class="cm_div_1"></td>
		<td class="cm_cell_pad_1">
			Име
		</td>
		<td width="1" class="cm_div_1"></td>
		<td width="50" class="cm_cell_pad_1">
			Тип
		</td>
		<td width="1" class="cm_div_1"></td>
		<td width="50" class="cm_cell_pad_1">
			Размер
		</td>
		<td width="1" class="cm_div_1"></td>
		<td width="115" class="cm_cell_pad_1">
			Дата
		</td>
		<td width="1" class="cm_div_1"></td>
		<td colspan="7" class="cm_cell_pad_1">
			Действие
		</td>
	</tr>
	<tr class="cm_shadow">
		<td></td>
		<td width="1" class="cm_div_2"></td>
		<td></td>
		<td width="1" class="cm_div_2"></td>
		<td></td>
		<td width="1" class="cm_div_2"></td>
		<td></td>
		<td width="1" class="cm_div_2"></td>
		<td></td>
		<td width="1" class="cm_div_2"></td>
		<td></td>
		<td width="1" class="cm_div_2"></td>
		<th colspan="7"></th>
	</tr>