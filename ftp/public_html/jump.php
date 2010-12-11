<?php
	include_once '_connect.php';
	define('ALLOW_EXECUTION', true);
	
	if (
			(!isset($_COOKIE['jump_loc']) || $_COOKIE['jump_loc'] == '') ||
			(!isset($_COOKIE['jump_msg']) || $_COOKIE['jump_msg'] == '')
		) {
			setcookie('jump_loc', FALSE, time() - 3600, PATH_MAIN, (COOKIE_DOMAIN ? '.' : '').DOMAIN);
			setcookie('jump_msg', FALSE, time() - 3600, PATH_MAIN, (COOKIE_DOMAIN ? '.' : '').DOMAIN);
			header('Location: index.php');
			exit;
		}
		
		setcookie('jump_loc', '', time() - 3600, PATH_MAIN, (COOKIE_DOMAIN ? '.' : '').DOMAIN);
		setcookie('jump_msg', '', time() - 3600, PATH_MAIN, (COOKIE_DOMAIN ? '.' : '').DOMAIN);
		$layout_title = '������� �� ���������';
		include PATH_ADMIN.'layout_header.php';
		echo '���� ��������� ���� ��� �������� <a href="'.$_COOKIE['jump_loc'].'" class="link">'.$_COOKIE['jump_loc'].'</a> � ���������:<br><br>'.$_COOKIE['jump_msg'];
		include PATH_ADMIN.'layout_footer.php';
?>