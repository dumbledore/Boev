<?php
	/* 
	
		----------------------------------------------
		1. MySQL Variables
		1.1		character_set_server - if not CP1251, change charset of UUIDS
		1.2		max_allowed_packet //32MB on host.bg
		2.		for the record - magic_quotes_gpc is a bad thing but it's handled by the engine
		3.		check inclusion paths
		3.1		check paths/urls in *ENGINE*->_settings.php; also check ROOT and DOMAIN constants
		3.2		check engine inclusion path in *SITE*->_connect.php
		3.3		check engine inclusion path in *ADMIN*->_connect.php
		3.4		check engine inclusion path in *FCK_EDITOR*->editor->filemenager->connectors->php->config.php
		3.5		check admin inclusion path in *FCK_EDITOR*->fckconfig.js
		4.		check page width, background, fontstyle and size in fck_editorarea.css
		5.		check rendering settings in *SITE*->layout->layout_render.php
		
		----------------------------------------------
		Folder Constants
		PATH_SITE		URL_SITE
		PATH_ADMIN		URL_ADMIN
		PATH_ENGINE		*n/a*
		PATH_PAGES		URL_PAGES
		PATH_USERS		URL_USERS
		PATH_SESSIONS	*n/a*
		
		Main site structure. *SITE* and *ADMIN* are the (only) starting points
		
		*SITE* #Must be visible!
			|
			.htaccess			# Config Apachi
			__readme.php		#this file
			_connect.php		# Connects the site to the engine
			activate.php		# Activates user accounts. Put here for security considerations
			exit.php			# Logs the user out
			jump.php			# Used for controlled header jumping if DEBUG is on
			index.php			# Main page opened by the browser
			inform.php			# Information messages
			sys_allowexec.php	# Simple control on inclusion files
			sys_login.php		# login support connector
			...
			layout #
				|
				images			# used gfx
				_settings.php	# layout settings
				...
		
		*ADMIN* #Must be visible
			|
			_connect.php		# connects the CPL to the engine
			_cpl_active.php		# enables/disables the CPL. Used to close the CPL on system error then to reopen it by the admin
			_standard.php	# Standard functionality (e.g. start/stop CPL);
			*.js				# Control the form input/output
			func_layout.php		# Layouting functionality (used for drawing)
			func_post.php		# POST data control
			func_space.php		# used for spacebar drawing
			gates.php			# CGI file
			gt_*				# CGI / no authentication
			gtA_*				# CGI / AUTHENTICATED, read through gates.php
			incl_*				# normal pages, read through index.php
			inclA_*				# normal AUTHENTICATED pages, read through main.php
			index.php			# reads incl_*
			layout_*			# layouting
			main.php			# AUTHENTICATED, reads inclA_*
			revalidate.php		# revalidation tool
			sys_cm.php			# Content menagement internal functionality
			unlock.php			# Unlock the CPL when down. Admin only, of course.
		
		*ENGINE* #Better be somewhere invisible
			|
			_settings.php	# Main site config
			_standard.php	# Standard functionality
			_types.php		# List of supported page types and their options
			..
			func_*
			..
			func_showmsg.php	# used for informing through sys_inform.php
			layout_debug.php	# Appears under the page when debug is on
			sys_allowexec.php	# Inclusion execution control with regard to user credentials
			sys_auth.php		# Authentication system
			sys_dbc.php			# DB Support
			sys_debug.php		# Debugging Support
			sys_users.php		# Users menagement internal functionality
				|
				user_verify
				user_get
				user_get_by_mail
				user_add
				user_edit
				user_make_pass
				user_prepare_user_activation
				user_activate_by_request
		
		*PAGES* #Must be visible
			|
			AAAAAAAA-BBBB-CCCC-DDDD-EEEEEEEEEEEE #page folder
				|
				... #page dirs and files
				
		*USERS* #Must be visible
			|
			username #user folder
				|
				...
				
		*SESSIONS* # VERY NECESSARY somewhere invisible
			|
			... # session datafiles here. only temp folder of course, but under OUR control.
		
		----------------------------------------------
		Used prefixes:
		
		_	declarations for inclusion
		func_	external functionality
		incl_	PAGE files, NOT requiring authentication	(responsible: index.php)
		gt_	GATES files, NOT requiring authentication	(own responsibility)
		inclA_	PAGE files, REQUIRING authentication		(responsible: main.php)
		gtA_	GATES files, REQUIRING authentication		(responsible: gates.php);
		layout_	used for common layouting
		sys_	system functionality
		
	*/
?>