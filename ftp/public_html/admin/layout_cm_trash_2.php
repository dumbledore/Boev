<?php
	include_once '_connect.php';
	include_once SYS_ALLOW_EXECUTION;
?>

</table>
</form>

<br>

<table cellspacing="0" cellpadding="0" style="width: 600px;" class="cm_table_rows_text">
	<tr>
		<td style="width: 7px"></td>
		<td style="width: 10px;"><img src="./gfx/bkg/arw1.gif"></td>
		<td style="width: 7px"></td>
		<td>������ <a href="#" onclick="checkAll();" class="cm_table_link">������</a> / <a href="#" onclick="checkNone();" class="cm_table_link">�����</a> / <a href="#" onclick="checkInverse();" class="cm_table_link">�������</a></td>
		<td align="right">
			&nbsp;&nbsp;<a href="#" onclick="deleteSelected()" class="cm_table_link">������ ���������</a>&nbsp;<img src="./gfx/icons/cm_del.gif">
			&nbsp;&nbsp;<a href="#" onclick="restoreSelected()" class="cm_table_link">���������� ���������</a>&nbsp;<img src="./gfx/icons/um_act.gif">
		</td>
	</tr>
</table>

<br>

<script type="text/javascript" src="cm_trash.js"></script>