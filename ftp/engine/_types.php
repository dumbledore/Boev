<?php
	# All available page types are described here.
	#
	# DO NOT EDIT THIS FILE
	#
	# However, if one is creating a new type, they should
	# add its description here too as this is the file
	# from which all depending on types functions read info
	# and so it affects which types would be shown in the
	# panel and would be deleted ACCORDINGLY
	
	# the following variables MUST NOT BE ALTERED in runtime
	
	# NOTE: DO NOT FORGET to update field `type` table `cm_struct_main` (as it is an emum)!
	
	#All type info
	$CM_PAGE_TYPES = array(
		'page' => array (
					'table' => 'cm_sp_pages',
					'caption' => '��������',
					'description' => '���������� ������������� �������, ����� �������� ������, ����� � �������.',
					'files' => array('files', 'images') #no sub directories are allowed!
				),
		
		'image' => array (
					'table' => 'cm_sp_images',
					'caption' => '������',
					'description' => '��������� ������ � ������ �������� � �������� "����������� ���������� �� ������������", ��� ��������� ��������� ����-�����.',
					'files' => array()
				),
		
		'addon' => array(
					'table' => 'cm_sp_addons',
					'caption' => 'addon',
					'description' => '������� �� �������� �������� ����������� ���������� ���. ������ �������� �� �� ������������� � ���� `����������`, � � ���� ���� (��� ������ �� �������������)',
					'files' => array('addon')
				),
		
		'link' => array(
					'table' => 'cm_sp_links',
					'caption' => '������',
					'description' => '�������� ���������� ������ ���������� �� �� ����� ���� ���� �� ����� ��� �� �� �������� ��� ���',
					'files' => array() #No special dirs
				),
		
		'math' => array(
					'table' => 'cm_sp_math',
					'caption' => '����������',
					'description' => '������������� ������������� �����. ������� ��������� (���������� �������) ��������, ����������� � ���� ������ (GIF+CSS), HTML ����� � ����������� ����� (PDF, DOC, etc.)',
					'files' => array('attach_bg', 'attach_en', 'text_bg', 'text_en')
				)
	);
	
	# Names for iteration
	$CM_PAGE_TYPES_NAMES = array_keys($CM_PAGE_TYPES);
	
	# Tables for iteration
	$CM_PAGE_TYPES_TABLES = array();
	for ($i = 0; $i < count($CM_PAGE_TYPES_NAMES); $i++)
		$CM_PAGE_TYPES_TABLES[$i] = $CM_PAGE_TYPES[$CM_PAGE_TYPES_NAMES[$i]]['table'];
	# END
?>