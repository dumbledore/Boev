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
	$new_page = section_add('НОВ ПОТРЕБИТЕЛ', '
		<form action="gates.php?page=um_add" method="post" name="um_add">
			<table cellspacing="0" cellpadding="0" class="formlabel">
				<tr>
					<td width="100">
						Име на потребителя:
					</td>
					<td style="width: 10px;"></td>
					<td>
						<input name="um_username" class="formfield" size="54">
					</td>
				</tr>
				<tr style="height: 5px;">
					<td></td>
				</tr>
				<tr>
					<td>
						Е-мейл:
					</td>
					<td></td>
					<td>
						<input name="um_email" class="formfield" size="54">
					</td>
				</tr>
				<tr style="height: 5px;">
					<td></td>
				</tr>
				<tr>
					<td>
						Статут на потребителя
					</td>
					<td></td>
					<td>
						<input type="radio" name="um_type" value="viewer" checked="checked">&nbsp;&nbsp;Зрител
						<input type="radio" name="um_type" value="editor">&nbsp;&nbsp;Редактор
						<input type="radio" name="um_type" value="admin">&nbsp;&nbsp;Администратор
					</td>
				</tr>
			</table>
			<br>
			<center>
				<a href="#" onclick="checkUser()"><img alt="СЪЗДАЙ" src="./gfx/icons/btn_create.png"></a>
				&nbsp;&nbsp;&nbsp;
				<a href="#" onclick="um_add.reset(); myMSG.close();"><img src="./gfx/icons/btn_cancel.png" alt="откажи"></a>
			</center>
		</form>
	');
	
	echo '
		<script type="text/javascript">
			myMSG = new DHTML_modalMessage();
			myMSG.setHtmlContent(\''.str_replace(array("\r", "\n"), '', $new_page).'\');
			myMSG.setSource(false);
			myMSG.setSize(500,220);
		</script>
	';
?>
<div style="display: none;">
	<form name="um_remove" action="gates.php?page=um_remove" method="post">
		<input type="hidden" name="um_selected">
	</form>
	<form name="um_activate" action="gates.php?page=um_activate" method="post">
		<input type="hidden" name="um_selected">
		<input type="hidden" name="um_mode">
	</form>
</div>

<form name="um_selected">
<table align="center" cellpadding="0" cellspacing="0" style="width: 600px; border-width: 1px; background-color: #FFFFFF;" class="cm_table_rows_text">
	<tr class="cm_table_headers">
		<td align="center" width="10" class="cm_cell_pad_1"></td>
		<td width="1" class="cm_div_1"></td>
		<td width="40" class="cm_cell_pad_1">
			Активен
		</td>
		<td width="1" class="cm_div_1"></td>
		<td class="cm_cell_pad_1" width ="120">
			Име
		</td>
		<td width="1" class="cm_div_1"></td>
		<td class="cm_cell_pad_1" width="60">
			Статут
		</td>
		<td width="1" class="cm_div_1"></td>
		<td class="cm_cell_pad_1" width="100">
			Е-мейл
		</td>
		<td width="1" class="cm_div_1"></td>
		<td colspan="3" class="cm_cell_pad_1">
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
		<th colspan="3"></th>
	</tr>