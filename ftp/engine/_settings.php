<?php
	//Debugging
	define('DEBUG', false);
	define('EXEC_START_TIME', time());
	
	//The next one freezes the control panel (e.g. when need to leave in the middle of one's work
	define('SITE_CPL_FREEZE', FALSE);

	define('SITE_SHOW_SUBSCRIBE_BUTTON', TRUE); # Wheather to show the subsrcibe button
	
	define('SITE_ACC_OFF', 0); //CPL completely hidden from site
	define('SITE_ACC_ON', 1); //If logged, one would see the the login bar on the site, otherwise - not
	define('SITE_ACC_ALWAYS', 2); //One would always see the login bar on the site
	define('SITE_ACC_LOGGED_BEFORE', 3); //After one's first logon, they would always see the bar
	
	define('SITE_ACCOUNTS', SITE_ACC_ALWAYS);
	
	#define('SITE_KEY', 'AF03-0019-136B-E072-AC21'); # Currently not used.
	define('SITE_SPACE', 256); //in MB
	
	define('SITE_HIGH_SECURITY_MODE', FALSE); # In High-Sec mode the CPL always wants revalidation before entering
	
	#DB settings
	
	#If the charset of the server is different from CP1251,
	#set it to true. If false, but server uses UTF-8 for instance,
	#resulst will not be get/set properly.
	define('DB_FIX_CHARSET', true);
	
	#Root and domain
	define('ROOT', '/home/boev/');
	define('DOMAIN', 'debian.fmi.uni-sofia.bg');
	define('PATH_MAIN', '/~boev/');
	define('COOKIE_DOMAIN', false); # Weather the cookie shall be set for the whole domain
	
	#System Paths
	define('PATH_SITE', ROOT.'public_html/');
	define('URL_SITE', 'http://'.DOMAIN.PATH_MAIN);
	
	define('PATH_ENGINE', ROOT.'engine/');
	
	define('PATH_ADMIN', ROOT.'public_html/admin/');
	define('URL_ADMIN', 'http://'.DOMAIN.PATH_MAIN.'admin/');
	
	define('PATH_USERS', ROOT.'public_html/users/');
	define('URL_USERS', 'http://'.DOMAIN.PATH_MAIN.'users/');
	
	define('PATH_PAGES', ROOT.'public_html/pages/');
	define('URL_PAGES', 'http://'.DOMAIN.PATH_MAIN.'pages/');
	
	define('PATH_SESSIONS', ROOT.'sessions/');
	
	define('URL_EDITOR', './scripts/fckeditor/');
	
	# Inclusion paths for SYSTEM functionality
	define('SYS_ALLOW_EXECUTION', PATH_ENGINE.'sys_allowexec.php');
	define('SYS_AUTHENTICATION', PATH_ENGINE.'sys_auth.php');
	define('SYS_DB_CONNECT', PATH_ENGINE.'sys_dbc.php');
	define('SYS_MAIL', PATH_ENGINE.'sys_mail.php');
	define('SYS_RENDER', PATH_ENGINE.'sys_render.php');
	define('SYS_USERS', PATH_ENGINE.'sys_users.php');
	
	define('USER_NAME_MIN_LENGTH', 4);
	define('USER_NAME_MAX_LENGTH', 32);
	define('USER_PASS_MIN_LENGTH', 6);
	define('USER_PASS_MAX_LENGTH', 32);
	
	define('SESS_NAME', 'BOEVSESSID');

	define('SESS_OK', 0);
	define('SESS_NOT_IN_CPL', 1);
	define('SESS_EXPIRED', 2);

	//Everything up to this point (excluding) may be considered
	//a valid session state, depending on the circumstances.
	define('SESS_INVALID_STATE', 8);
	define('SESS_UNAVAILABLE', 9);
	define('SESS_KILLED', 10);
	define('SESS_KILLED_BY_GC', 11); //The session file is not available as GC has deleted it.

	//authencity
	define('SESS_FALSE_AUTHENCITY', 16);
	define('SESS_INVALID_PASSWORD', 17);
	
	//From here on all errors are considered
	//hijacking attempts. many functions
	//use $sess_state >= SESS_FALSE_AUTHENCITY
	//to exclude any attempts from this category
	define('SESS_HIJACK_ATTEMPT', 32);
	define('SESS_INVALID_IP', 33);
	define('SESS_INVALID_BROWSER', 34);
	
	
	//defines in sec when to prompt for pass
	define('SESS_EXP_TIME', 30*60); //30 minutes of inactivity
	
	//defines in sec when to kill session (usualy happens after SESS_EXP_TIME)
	define('SESS_LIFE_TIME', 3*60*60); //3 hours of inactivity

	define('SESS_COOKIE_EXP_TIME', 31 * 24 * 60 * 60); //31 days

	//WARNING!
	//If SESS_EXP_TIME is set too low (e.g. 0 or 1),
	//and the time for response from the server is not fast enough
	//the user may be forced to reenter one's password several times
	//even though they have entered it OK.
	//
	//Such values must be used for expiremental purpose only.

	//MAIL
	define('MAIL_SEND_OK', 0);
	define('MAIL_SEND_FAILED', 1);
	
	define('MAIL_FROM_ADDRESS', 'noreply@'.DOMAIN);
	define('MAIL_FROM_NAME', 'Biomathematics at FMI');
	
	define('MAIL_WEBMASTER_ADDRESS', 'galileostudios@gmail.com');

	//SITE CONTENT MENAGEMENT
	define('CM_ID_LENGTH', 36); //Exact ID length. When using MYSQL's UUID, it must be 36
	define('CM_ID_VALIDCHARS', '/[^A-Fa-f0-9-]/'); //that is hex code, delimitered with dashes
	
	//IMAGE SETTINGS
	define('IMG_MAX_SIZE', 1 * 1024 * 1024); # 1MB size
	define('IMG_THUMB_RES', 128);
	define('IMG_SMALL_RES', 400);
	define('IMG_SLIDE_RES', 615);
	define('IMG_RES', 4000);
	define('IMG_QUALITY', 80);
	define('IMG_SUPPORTED_TYPES', '\'jpeg\', \'jpg\', \'png\', \'gif\'');
	
	include_once PATH_ENGINE.'_types.php';
	include_once PATH_ENGINE.'_std.php';
	include_once PATH_ENGINE.'sys_debug.php';
?>