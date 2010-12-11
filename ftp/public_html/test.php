<?php
/*
	function img_filter_1($in)
	{
		# Filters images with ALT tag
		
		# comes in:
		# 0: <img src="cells.jpg" width="200" alt= "From left to right: human erythrocyte, thrombocyte, leukocyte." class="ASD">
		# 1: src="cells.jpg" width="200" 
		# 2: alt="From left to right: human erythrocyte, thrombocyte, leukocyte."
		# 3: From left to right: human erythrocyte, thrombocyte, leukocyte.
		# 4: class="ASD"
			
			return '
				<table class="imgbox" align="right" width="1"><tr><td class="imgbox"><div>
			'.
				'<@@@ '.$in[1].'alt="'.$in[3].'"'.$in[4].'>'.'
				</div><div class="imgbox">
			'.
				$in[3]
			.'
				</div></td></tr></table>
			';
		}

	function img_filter_2($in)
	{
		# Filters images WITHOUT ALT tag
		
		# comes in:
		# 0: <img src="asdasdasd">
			
			return '
				<table class="imgbox" align="right"><tr><td class="imgbox"><div>
			'.
				$in[0]
			.'
				</div></td></tr></table>
			';
	}
	
	function img_filter_3($in)
	{
		#Restores img w/ ALT inicial tag, i.e. @@@ -> img
		return '<img '.$in[1].'>';
	}
	
	$text = 
'  
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Biomathematics</title>
<link REL="stylesheet" HREF="http://debian.fmi.uni-sofia.bg/~boev/newdesign/main.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<link rel="shortcut icon" href="favicon.ico">
</head>
<body bgcolor="FFFFFF">
	<table style="width: 780px; height: 100%; background-color: #7e7e7e;" cellspacing="0" cellpadding="0" align="center">
		<tr valign="middle">
			<td width="17" background="http://debian.fmi.uni-sofia.bg/~boev/newdesign/left.gif"></td>
			<td width="746">

			</td>
			<td width="17" background="http://debian.fmi.uni-sofia.bg/~boev/newdesign/right.gif"></td>
		</tr>
		<tr valign="top" height="25">
			<td width="17" background="http://debian.fmi.uni-sofia.bg/~boev/newdesign/left.gif"></td>
			<td width="746" background="http://debian.fmi.uni-sofia.bg/~boev/newdesign/menu.gif">
				<div style="height: 1px;"></div>
				<table cellspacing="0" cellpadding="0" width="96%" align="center">
					<tr>

						<td align="left"><span class="onpage">Биоматематика</span>&nbsp;&middot;&nbsp;<a href="#z" class="a1">Материали по курса</a>&nbsp;&middot;&nbsp;<a href="#z" class="a1">Лекции за биолози</a>&nbsp;&middot;&nbsp;<a href="#z" class="a1">Контакти</a>&nbsp;&nbsp;&nbsp;</td>
						<td align="right">
							<a href="#" ><img src="http://debian.fmi.uni-sofia.bg/~boev/newdesign/login.gif" border="0" alt="log in"></a>
						</td>
					</tr>
				</table>
			</td>

			<td width="17" background="http://debian.fmi.uni-sofia.bg/~boev/newdesign/right.gif"></td>
		</tr>
		<tr valign="top">
			<td width="17" background="http://debian.fmi.uni-sofia.bg/~boev/newdesign/left.gif"></td>
			<td width="746" align="center">
				<br>
				<table width="96%" cellspacing="0" cellpadding="0">
					<tr valign="top">
						<td align="left">

<h3>Importance</h3>
              Applying mathematics to biology has a long history, but only recently has there been an explosion of interest in the field. Some reasons for this include:<br>

              <br>
                * the explosion of data-rich information sets, due to the genomics revolution, which are difficult to understand without the use of analytical tools,<br>
                * recent development of mathematical tools such as chaos theory to help understand complex, nonlinear mechanisms in biology,<br>
                * an increase in computing power which enables calculations and simulations to be performed that were not previously possible, and<br>
                * an increasing interest in in silico experimentation due to ethical considerations, risk, unreliability and other complications involved in human and animal research.<br>
              <br>
              For use of statistics in biology, see Biostatistics.<br>

              <br>
                For use of basic arithmetics in biology, see relevant topic, such as Serial dilution.<br>
              <br>
              <h3>Areas of research</h3>
              <img src="http://debian.fmi.uni-sofia.bg/~boev/newdesign/cells.jpg" width="200" alt= "From left to right: human erythrocyte, thrombocyte, leukocyte." class="ASD">
              Several areas of specialized research in mathematical and theoretical biology[4][5][6][7][8][9] as well as external links to related projects in various universities are concisely presented in the following subsections, including also a large number of appropriate validating references from a list of several thousands of published authors contributing to this field. Many of the included examples are characterised by highly complex, nonlinear, and supercomplex mechanisms, as it is being increasingly recognised that the result of such interactions may only be understood through a combination of mathematical, logical, physical/chemical, molecular and computational models. Due to the wide diversity of specific knowledge involved, biomathematical research is often done in collaboration between mathematicians, biomathematicians, theoretical biologists, physicists, biophysicists, biochemists, bioengineers, engineers, biologists, physiologists, research physicians, biomedical researchers, oncologists, molecular biologists, geneticists, embryologists, zoologists, chemists, etc.<br>

              [edit] Computer models and automata theory<br>
              Main article: Modelling biological systems<br>
              <br>
              A monograph on this topic summarizes an extensive amount of published research in this area up to 1987,[10] including subsections in the following areas: computer modeling in biology and medicine, arterial system models, neuron models, biochemical and oscillation networks, quantum automata, quantum computers in molecular biology and genetics, cancer modelling, neural nets, genetic networks, abstract relational biology, metabolic-replication systems, category theory[11] applications in biology and medicine,[12] automata theory,cellular automata, tessallation models[13][14] and complete self-reproduction, chaotic systems in organisms, relational biology and organismic theories.[15][16] This published report also includes 390 references to peer-reviewed articles by a large number of authors.[17][18][19]<br>
              <br>
              Modeling cell and molecular biology<br>
              <br>
              <img src="http://debian.fmi.uni-sofia.bg/~boev/newdesign/cells.jpg">
              This area has received a boost due to the growing importance of molecular biology.[20]<br>
			  
			  </td>
					</tr>
				</table>
				<br>

			</td>
			<td width="17" background="http://debian.fmi.uni-sofia.bg/~boev/newdesign/right.gif"></td>
		</tr>
	</table>
</body>
</html>
';
	
	$tag_img_alt = '<\<img ([^\>]*)(alt[\s]*=[\s]*["]([^\"]*)["]){1,1}([^\>]*)[/]?\>>is';
	$tag_img_wo_alt = '<\<img [^\>]*[/]?\>>is';
	$tag_img_restore = '<\<@@@ ([^\>]*)[/]?\>>is';

	#echo '<textarea rows="100" cols="200">';
	$text = preg_replace_callback($tag_img_alt, 'img_filter_1', $text);
	$text = preg_replace_callback($tag_img_wo_alt, 'img_filter_2', $text);
	$text = preg_replace_callback($tag_img_restore, 'img_filter_3', $text);
	echo $text;
	#echo '</textarea>';

print_r(mail('galileostudios@gmail.com', 'asdasd', 'test message body', 'From: bill@gates.com'));
$mail_headers = 'From: '.$from."\r\n";
		
		if (!mail($to, $subject, wordwrap($msg, 70), $mail_headers))
*/
?>