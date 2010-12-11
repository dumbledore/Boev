<?php
	include 'sys_allowexec.php';

	if (SHOW_BAR === TRUE)
	{
		echo '
				</td><td align="right">
			';
			
		if (LOGGED === TRUE)
		{ //user logged
			echo '
					<a href="'.URL_ADMIN.'index.php?goto=site"><img src="cache.php?id=layout/gfx/cpl.gif" border="0" alt="контролен панел"></a><img src="cache.php?id=layout/gfx/transparent.gif" width="8"><a href="'.URL_SITE.'exit.php?goto=site"><img src="cache.php?id=layout/gfx/exit.gif" border="0" alt="изход '.$_SESSION['username'].'"></a>
				';
		}
		 else
		{
			echo '<a href="'.URL_ADMIN.'index.php?goto=site"><img src="cache.php?id=layout/gfx/login.gif" border="0" alt="вход"></a>';
		}
	}
?>