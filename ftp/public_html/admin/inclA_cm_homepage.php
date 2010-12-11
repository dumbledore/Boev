<?php
	include_once '_connect.php';
	define('ACCESS_LEVEL', 'editor');
	include_once SYS_ALLOW_EXECUTION;
	$layout_title = '������� ��������';
	//$layout_onload = 'document.userdata.um_email.focus();';
	include 'layout_header.php';
	include_once 'func_layout.php';
	include_once 'sys_cm.php';
?>
<script type="text/javascript">
	function checkSelection() {
		if (cm_homepage.cm_selected.value == 'menu')
			alert('�������� ���� �� � ��������!');
		else
			cm_homepage.submit();
	}
</script>
<form action="gates.php?page=cm_homepage" name="cm_homepage" method="post">
<div align="center" style="width: 600px;" class="cm_table_rows_text">
	<div style="padding: 10px;">
	<center>
	<?php
		if (table_empty('cm_struct_main'))
		{ #no pages!
			
			echo section_add('�������� �� ������� ��������', '
				���� �������� ��������!<br>
				����, �������� �������� �� ���� <a href="main.php?page=cm_browse" class="cm_table_link">����������</a>
			');
			
			echo '
				<br>
				<center>
					<a href="main.php?page"><img src="./gfx/icons/btn_cancel.png" alt="������"></a>
				</center>
			';
		}
		 else
		{
			if ($homepage === NULL)
				$homepage_caption = '<span style="color: #FF0000;">�� � �������� ������� ��������</span>';
			 else
			{
				$homepage_caption = element_get($homepage, 'caption_bg');
				if (is_numeric($homepage_caption))
					$homepage_caption = '<span style="color: #FF0000;">�� � �������� ������� ��������</span>';
				else
					$homepage_caption = '�������� ������� �������� �: <span style="color: #FF3300;">'.$homepage_caption.'</span>';
			}
			echo section_add('�������� �� ������� ��������', '
				<br>
				<center>
				'.$homepage_caption.'
				<br><br>
				����, �������� ������� ��������.<br>
				<br>
				<select name="cm_selected" size="16" style="width: 350px">'.
				tree_get_for_moving()
				.'</select>
				</center>
				<br>
			');
			
			echo '
				<br>
				<center>
					<a href="#" onclick="checkSelection();"><img alt="�����" src="./gfx/icons/btn_set.png"></a>
					&nbsp;&nbsp;&nbsp;
					<a href="main.php?page"><img src="./gfx/icons/btn_cancel.png" alt="������"></a>
				</center>
			';
		}
	?>
	</center>
	</div>
</div>

</form>