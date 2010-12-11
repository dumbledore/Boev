<?php
	include_once '_connect.php';
	include_once SYS_ALLOW_EXECUTION;

	$layout_title = '����������� �� ������������� �����';
	$layout_onload = 'document.userdata.um_email.focus();';

	include 'layout_header.php';
	include_once 'func_layout.php';
	include_once SYS_USERS;

	if (!isset($_GET['username']))
		$username = $_SESSION['username'];
	 else
		$username = $_GET['username'];

	if (!($userdata = user_get($username))) //i.e. didn't work out
	{
		jump_to(URL_ADMIN.'main.php?page=um_browse', 'inclA_um_edit.php: invalid user');
		exit;
	}
	 else
	{
		if ($username != $_SESSION['username'] && $_SESSION['credentials'] != 'admin')
		{
			jump_to(URL_ADMIN.'main.php', 'inclA_um_edit.php: non-admin is trying to temper with admin things');
			exit;
		}
		$username = $_SESSION['username'];
	}
?>
<script type="text/javascript">
	function verifyEmail(email) {
		var emailRegEx = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;

		if (email.search(emailRegEx) == -1)
		{
			return false;
		}
		return true;
	}

	function verifyPassword(pass) {
		var passRegEx = /^[A-Za-z0-9_#]{6,32}$/;

		if (pass.search(passRegEx) == -1)
			return false;
		
		return true;
	}
	
	function checkUserData() {
		if (document.userdata.um_new_password.value.length > 0)
		{
			if (!verifyPassword(document.userdata.um_new_password.value))
			{
				alert('��������, ������ �� ���� ����� 6 � 32 ������� � �� ������� �����, �������� ����� ��� "_", "#".');
				return false;
			}
		}
			
		if ((document.userdata.um_new_password.value.length > 0 || document.userdata.um_new_password2.value.length > 0) && document.userdata.um_new_password.value != document.userdata.um_new_password2.value)
		{
			alert('������ ������ �� ������� � ���������� � ���������.');
			return false;
		}
			
		if (!verifyEmail(document.userdata.um_email.value))
		{
			alert('������ ��� ��������� ����.');
			return false;
		}
		
		if (!verifyPassword(document.userdata.um_password.value))
		{
			alert('�� ��� ������ �������� �� ������ ��� ��� � ��������.');
			return false;
		}
		
		return true;
	}
	
	function checkUser() {
		if (checkUserData())
			document.userdata.submit();
	}
	
</script>

<form action="gates.php?page=um_edit&$_GET['goto']" name="userdata" method="post">
<?php
	echo '<input type="hidden" name="goto" value="'.$_GET['goto'].'">';
?>
<div align="center" style="width: 600px;" class="cm_table_rows_text">
	<div style="padding: 10px;">
	
	<?php
		echo section_add('���� ���������', '
			<table cellspacing="0" cellpadding="0" class="formlabel">
				<tr>
					<td width="160">
						����������������� ���:
					</td>
					<td style="width: 10px;"></td>
					<td>
						<input type="hidden" name="um_username" value="'.$userdata['username'].'">
						<input class="formfield" size="42" disabled="disabled" value="'.$userdata['username'].'" style="background-color: #DDDDDD;">
						&nbsp;&nbsp;<input type="checkbox" name="um_active"'.($userdata['active'] ? ' checked="checked"' : '').($_SESSION['credentials'] != 'admin' ? ' disabled="disabled"' : '').'>&nbsp;&nbsp;�������
					</td>
				</tr>
				<tr style="height: 5px;">
					<td></td>
				</tr>
				<tr>
					<td>
						�-����:
					</td>
					<td></td>
					<td>
						<input name="um_email" class="formfield" size="57" value="'.$userdata['email'].'">
					</td>
				</tr>
				<tr style="height: 5px;">
					<td></td>
				</tr>
				<tr>
					<td>
						���� ������:
					</td>
					<td></td>
					<td>
						<input type="password" name="um_new_password" class="formfield" size="57">
					</td>
				</tr>
				<tr style="height: 5px;">
					<td></td>
				</tr>
				<tr>
					<td>
						���� ������ (��������):
					</td>
					<td></td>
					<td>
						<input type="password" name="um_new_password2" class="formfield" size="57">
					</td>
				</tr>
				<tr style="height: 5px;">
					<td></td>
				</tr>
				<tr>
					<td>
						������ �� �����������
					</td>
					<td></td>
					<td>
						<input type="radio" name="um_type" value="viewer"'.($userdata['credentials'] == 'viewer' ? ' checked="checked"' : '').($_SESSION['credentials'] != 'admin' ? ' disabled="disabled"' : '').'>&nbsp;&nbsp;������
						<input type="radio" name="um_type" value="editor"'.($userdata['credentials'] == 'editor' ? ' checked="checked"' : '').($_SESSION['credentials'] != 'admin' ? ' disabled="disabled"' : '').'>&nbsp;&nbsp;��������
						<input type="radio" name="um_type" value="admin"'.($userdata['credentials'] == 'admin' ? ' checked="checked"' : '').($_SESSION['credentials'] != 'admin' ? ' disabled="disabled"' : '').'>&nbsp;&nbsp;�������������
					</td>
				</tr>
			</table>
		');
		
		echo section_add('�������', '
			<table cellspacing="0" cellpadding="0" class="formlabel">
				<tr>
					<td width="160">
						�������� ���:
					</td>
					<td style="width: 10px;"></td>
					<td>
						<input class="formfield" size="57" name="um_name" value="'.$userdata['name'].'">
					</td>
				</tr>
				<tr style="height: 5px;">
					<td></td>
				</tr>
				<tr>
					<td width="150">
						��������:
					</td>
					<td style="width: 10px;"></td>
					<td>
						<input class="formfield" size="57" value="'.$userdata['title'].'" name="um_title"'.($_SESSION['credentials'] == 'admin' ? '' : ' disabled="disabled" style="background-color: #DDDDDD;"').'>
					</td>
				</tr>
				<tr style="height: 5px;">
					<td></td>
				</tr>
				<tr>
					<td width="150">
						����� ���������:
					</td>
					<td style="width: 10px;"></td>
					<td>
						<input class="formfield" size="57" name="um_message" value="'.$userdata['message'].'">
					</td>
				</tr>
				<tr style="height: 5px;">
					<td></td>
				</tr>
				<tr>
					<td width="150">
						��� ��������:
					</td>
					<td style="width: 10px;"></td>
					<td>
						<input  class="formfield" size="57" name="um_aim" value="'.$userdata['aim'].'">
					</td>
				</tr>
				<tr style="height: 5px;">
					<td></td>
				</tr>
			</table>
		');
		
		echo section_add('��������', '
			<table cellspacing="0" cellpadding="0" class="formlabel">
				<tr>
					<th colspan="3" align="center" class="common">
						�� ����������� �� ���������, ����, �������� ������ (�����) ������.
					</th>
				</tr>
				<tr style="height: 5px;">
					<td></td>
				</tr>
				<tr>
					<td width="160">
						����� ������<br>(�� ��������):
					</td>
					<td style="width: 10px;"></td>
					<td>
						<input class="formfield" size="57" name="um_password" type="password">
					</td>
				</tr>
			</table>
		');
		

		echo '
			<br>
			<center>
				<a href="#" onclick="checkUser()"><img alt="�������" src="./gfx/icons/btn_edit.png"></a>
				&nbsp;&nbsp;&nbsp;
				<a href="main.php'.($_SESSION['credentials'] == 'admin' ? ($_GET['goto'] == 'checkpoint' ? '' : '?page=um_browse') : '').'"><img src="./gfx/icons/btn_cancel.png" alt="������"></a>
			</center>
		';
	?>
	</div>
</div>

</form>