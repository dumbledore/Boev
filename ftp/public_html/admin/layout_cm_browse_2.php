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
		<td style="width: 10px;"><img src="./gfx/bkg/arw1.gif" alt=""></td>
		<td style="width: 7px"></td>
		<td>������ <a href="#" onclick="checkAll();" class="cm_table_link">������</a> / <a href="#" onclick="checkNone();" class="cm_table_link">�����</a> / <a href="#" onclick="checkInverse();" class="cm_table_link">�������</a></td>
		<td align="right">
			&nbsp;&nbsp;<a href="#" onclick="deleteSelected()" class="cm_table_link">������ ���������</a>&nbsp;<img src="./gfx/icons/cm_del.gif" alt="������ ���������">
			&nbsp;&nbsp;<a href="#" onclick="moveSelected()" class="cm_table_link">�������� ���������</a>&nbsp;<img src="./gfx/icons/cm_move.gif" alt="�������� ���������">
		</td>
	</tr>
</table>

<br>

<table cellspacing="0" cellpadding="0" style="width: 600px; height: 32px;" class="cm_table_rows_text">
	<tr>
		<td align="center">
			<input type="image" alt="���� ��������" src="./gfx/icons/btn_new_page.png" onclick="newPage()">
		</td>
	</tr>
</table>

<script type="text/javascript" src="cm_browse.js"></script>