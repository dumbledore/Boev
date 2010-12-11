<?php
	include_once '_connect.php';
	include_once SYS_ALLOW_EXECUTION;
?>

<!-- Next forms are used to send the neccessary vaibles through POST rather than GET,
as it is cruical for gate pages, viz. deleting / moving / etc.-->
<script type="text/javascript" src="./scripts/dlg/modal-message.js"></script>
<script type="text/javascript" src="./scripts/dlg/ajax.js"></script>
<script type="text/javascript" src="./scripts/dlg/ajax-dynamic-content.js"></script>

<div style="display: none;">
	<form name="cm_remove" action="gates.php?page=cm_shred" method="post">
		<input type="hidden" name="cm_selected">
	</form>

	<form name="cm_restore" action="main.php?page=cm_restore" method="post">
		<input type="hidden" name="cm_selected">
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
		<td colspan="2" class="cm_cell_pad_1">
			---
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
		<th colspan="2"></th>
	</tr>