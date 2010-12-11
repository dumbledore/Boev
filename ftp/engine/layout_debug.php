<?php
	if (ALLOW_EXECUTION === TRUE)
	{

		echo '<br><hr><br>DEBUGGING IS ON<br><br>';

		$dbg_table_open = FALSE; //Tells if an HTML table is not closed

		# Drawing Functions
		function dwin_table($title) {
			dwin_table_close();
			echo '
				<table cellspacing="1" cellpadding="2" align="center" width="800">
				<tr><th colspan="3" class="dbg_title">'.$title.'</th></tr>';
			
			global $dbg_table_open;
			$dbg_table_open = TRUE; //We have just opened a table, eh?
		}
		
		function dwin_table_close() {
			global $dbg_table_open;
			if ($dbg_table_open === TRUE)
				echo '<tr style="height: 15px;"><th colspan="3"></th></tr></table>';
			$dbgtable = FALSE;
		}
		
		function dwin_msg($title, $msg) { //write message to debug window
			global $dbg_line;
			if (!isset($dbg_line))
				$dbg_line = 0;

			echo '<tr style="background-color: #'.(($dbg_line % 2 == 0) ? '333344' : '444433').';">
					<td class="dbg_header" style="width: 140px;">'.$title.'</td>
					<td style="width: 5px;"></td>
					<td class="common" style="width: 650px;">
						<pre>';
			
			if	(is_array($msg))
				print_r($msg);
			else
				echo $msg;

			echo '		</pre>
					</td>
					</tr>';
			$dbg_line++;
		}
		
		if (isset($_COOKIE[SESS_NAME])) //SSID options, etc.
		{
			include_once SYS_AUTHENTICATION;
			if (session_id() == '')
				initiate_session();
			
			dwin_table('DEBUG DATA');
			dwin_msg('Execution time', (time()-EXEC_START_TIME).' seconds');
			dwin_msg('SSID', session_id());
			dwin_msg('credentials', $_SESSION['credentials']);
			dwin_msg('Seconds from last time revalidation', time() - $_SESSION['accessed']);
			dwin_msg('Timestamp', date('l jS \of F Y h:i:s A', $_SESSION['accessed']));
			dwin_msg('Session expires after', (SESS_EXP_TIME / 60).' min');
			dwin_msg('Session dies after', (SESS_LIFE_TIME / 60).' min');
			dwin_msg('browser', wordwrap($_SESSION['browser']));
			
			debug_variable('sess_state');
			
			if (
				isset($_SESSION['rev_active']) || isset($_SESSION['rev_page']) || isset($_SESSION['rev_isgate']) || isset($_SESSION['rev_data'])
				)
			{
				dwin_table('REVALIDATION');
				dwin_msg('Revalidation', isset($_SESSION['rev_active']) && ($_SESSION['rev_active'] === TRUE) ? 'active' : '---');
				dwin_msg('GoTo Page',(isset($_SESSION['rev_page'])) ? $_SESSION['rev_page'] : '---');
				dwin_msg('Is it a Gate?', (isset($_SESSION['rev_isgate'])) ? (($_SESSION['rev_isgate']) ? 'yes' : 'no') : '---');
				dwin_msg('Saved Post Data', (isset($_SESSION['rev_data'])) ? ((empty($_SESSION['rev_data'])) ? 'none' : 'yes') : '---');
			}
		}
		
			if (count($error_buffer) > 0)
			{
				dwin_table('PHP ERROR MESSAGES');
				for ($i=0; $i < count($error_buffer) && $i < 10; $i++)
				{
					dwin_msg($error_buffer[$i][0], $error_buffer[$i][1]);
				}
			}
			
			if (mysql_errno() != 0)
			{
				dwin_table('MYSQL ERROR MESSAGES');
				dwin_msg(mysql_errno(), mysql_error());
			}
			
			if (count($dbgmsg_buffer) > 0)
			{
				dwin_table('CUSTOM DEBUGGING MESSAGES');
				for ($i=0; $i < count($dbgmsg_buffer); $i++)
				{
					dwin_msg($dbgmsg_buffer[$i][0], $dbgmsg_buffer[$i][1]);
				}
			}
		
		dwin_table_close();
	}
?>