<?php
	include_once '_connect.php';
	//aim_ stands for admin panel info message
	//refer text, type and redirection time to message id

	//setting defaults
	$aim_redirect = 7;
	$aim_msgtype = 'error';
	$aim_msginfo = (isset($_COOKIE['aim_msginfo']) ? $_COOKIE['aim_msginfo'] : '');

	switch ($_COOKIE['aim_msgid']) {
		//WARNING!
		//If $aim_msgtext is omitted (or the name is mistaken),
		//then the users is AUTOMATICALLY redirected to index.php
		
		case 'generic_error':
			$aim_msgtext = '�������� ������������ ������. ��������������� ���� ��������.<br>����������� ����� �� ���� �������� �� ������������ �� ��������.';
			$aim_redirect = 0;
			break;
		
		case 'invalid_user':
			$aim_msgtext = '������ ������!';
			break;
		
		case 'invalid_recheck':
			$aim_msgtext = '������ ������.<br>����, ��������� ������!';
			$aim_redirect = 4;
			break;

		case 'missing_mail':
			$aim_msgtext = '�� ���� ������� ������, ������������ �� ���� �����.';
			break;
		
		case 'password_sent':
			$aim_msgtext = '������ ����� ���� ��������� �������.';
			$aim_msgtype = 'ok';
			$aim_redirect = 5;
			break;
			
		case 'user_activated':
			$aim_msgtext = '����� ������ ���� ��������� �������.';
			$aim_msgtype = 'ok';
			$aim_redirect = 5;
			break;
			
		case 'user_activate_failed':
			$aim_msgtext = '�������� ������ ��� ����������� �� �������.<br>����, �������� �� ��� ��������������.';
			break;

		# UPDATED CODE FOLLOWS			
		case 'user_exists':
			$aim_msgtext = '���� ���������� ���������� � ������ ���!';
			$aim_msgtype = 'warn';
			break;
			
		case 'user_invalid_name':
			$aim_msgtext = '�������� � ��������� ������������� ���!';
			$aim_msgtype = 'warn';
			break;
			
		case 'user_invalid_mail':
			$aim_msgtext = '������� � ��������� �����!';
			$aim_msgtype = 'warn';
			break;
			
		case 'user_cannot_send_activation':
			$aim_msgtext = '����������� �� ���� �� �� �������!';
			$aim_msgtype = 'warn';
			break;
		
		case 'on_enter':
			$aim_msgtext = '��������� �� ������...';
			$aim_msgtype = 'refresh';
			$aim_redirect = 1;
			break;

		case 'on_exit':
			$aim_msgtext = '������� ��������� �� ���������������� �����.';
			$aim_msgtype = 'ok';
			$aim_redirect = 3;
			break;
			
		case 'panel_unlocked':
			$aim_msgtext = '������� ���� ��������.';
			$aim_msgtype = 'ok';
			break;
			
		case 'cannot_move':
			$aim_msgtext = '��������� �������� �� ����� �� ����� ����������, ��� ����<br>������������� ���� ������� / ���������� �� ���� ����������.';
			$aim_msgtype = 'warn';
			break;
			
		case 'cannot_restore':
			$aim_msgtext = '��������� �������� �� ����� �� ����� ������������, ��� ����<br>������������ ���� ������������ �� ���� ����������.';
			$aim_msgtype = 'warn';
			break;
			
		case 'page_deleted':
			$aim_msgtext = '��������� �������� ������������� ���� ������� �� ���� ����������.';
			$aim_msgtype = 'warn';
			break;
		
		case 'tables_optimized':
			$aim_msgtext = '��������� ���� ������������.';
			$aim_msgtype = 'ok';
			break;
		
		case 'requests_flushed':
			$aim_msgtext = '�������� ���� ���������. �������������� ��� ������ ����������� ���� �������.';
			$aim_msgtype = 'ok';
			break;
		
		default:
			//if ID notfound one would be directly transferred to index.php
			$aim_msgtext = '';
	}
	
	//Link of return page
	if ($_COOKIE['aim_returnpage'] == '')
		$aim_returnpage = 'index.php';
	else
		$aim_returnpage = $_COOKIE['aim_returnpage'];

	//Set defaults
	$aim_icon['info'] = 'info';
	$aim_icon['error'] = 'error';
	$aim_icon['warn'] = 'warn';
	$aim_icon['ok'] = 'ok';
	$aim_icon['refresh'] = 'refresh';

	$aim_title['info'] = '���������';
	$aim_title['error'] = '������';
	$aim_title['warn'] = '��������';
	$aim_title['ok'] = '������� ����������';
	$aim_title['refresh'] = '������...';

	$aim_style['info'] = 'info';
	$aim_style['error'] = 'error';
	$aim_style['warn'] = 'warn';
	$aim_style['ok'] = 'ok';
	$aim_style['refresh'] = 'info';

	$aim_link['info'] = '�������� ������';
	$aim_link['error'] = '�������� ������';
	$aim_link['warn'] = '�������� ������';
	$aim_link['ok'] = '�������� ������';
	$aim_link['refresh'] = '������ �������';

	//destroy cookies;
	setcookie('aim_msgid', FALSE, time() - 3600, PATH_MAIN, (COOKIE_DOMAIN ? '.' : '').DOMAIN);
	setcookie('aim_returnpage', FALSE, time() - 3600, PATH_MAIN, (COOKIE_DOMAIN ? '.' : '').DOMAIN);
	setcookie('aim_msginfo', FALSE, time() - 3600, PATH_MAIN, (COOKIE_DOMAIN ? '.' : '').DOMAIN);
	
	if ($aim_msgtext == '') {
		jump_to('index.php', 'sys_inform: COOKIE aim_msgtext not set');
	} else {
		$layout_title = $aim_title[$aim_msgtype];
		if ($aim_redirect > 0)
			$layout_metainfo = '<meta http-equiv="refresh" content="'.$aim_redirect.';url='.$aim_returnpage.'">';
		define('ALLOW_EXECUTION', true);
		include PATH_ADMIN.'layout_header.php';
		echo '
			<table align="center" cellspacing="0" cellpadding="0"><tr valign="middle">
			<td><img src="'.URL_ADMIN.'gfx/icons/msg_'.$aim_icon[$aim_msgtype].'.png" alt="'.$aim_title[$aim_msgtype].'"></td>
			<td style="width: 15px;"></td>
			<td class="'.$aim_style[$aim_msgtype].'">'.
			$aim_msgtext
			.(($aim_msginfo != '') ? '<br /><br />'.wordwrap($aim_msginfo, 75, '<br />') : '').
			'</td>
			</tr>
			</table>
			<br><br><a href="'.$aim_returnpage.'" class="link">'.$aim_link[$aim_msgtype].'</a><br>
		';
		include PATH_ADMIN.'layout_footer.php';
	}

?>