<?php
					# get slide data and effectively check if value is OK
					if (!($res = query('SELECT `id` FROM `cm_stuct_main` WHERE `parent` = \''.$id.'\' LIMIT '.($slide_num -1).', 1;')))
					{
						echo 'Има проблем с базата данни. Работи се по отстраняването му!';
						render_notify('page: slideshow select', $id);
						return;
					}
					
										$slideshow_found = false;
					
					if (mysql_num_rows($res) == 0)
					{
						if ($slide_num > 1)
						{
							#start a new one
							$slide_num = 1;
							if (!($res = query('SELECT `id` FROM `cm_stuct_main` WHERE `parent` = \''.$id.'\' LIMIT '.($slide_num -1).', 1;')))
							{
								echo 'Има проблем с базата данни. Работи се по отстраняването му!';
								render_notify('page: slideshow select', $id);
								return;
							}
							
							if (mysql_num_rows($res) > 0)
								$slideshow_found = true;
						}
					}
					 else
						$slideshow_found = true;
					
					if ($slideshow_found)
					{
						#$slideshow = lrender_slideshow($slide_data['id'], $slide_data['type'] == 'image', $slide_data['caption_'.$lang], $slide_data['descr_'.$lang], $lang);
						$slide_id = mysql_fetch_assoc($res);
						$slideshow = render_page($slide_id['id'], true); #slid-showed ;-)
						# !!! show next/prev slide bar
					}
					 else
						$slideshow = lrender_slideshow_na();
				}

?>