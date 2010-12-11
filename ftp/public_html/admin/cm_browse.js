//Helper Functions
function implode( glue, pieces ) {
    // http://kevin.vanzonneveld.net
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Waldo Malqui Silva
    // *     example 1: implode(' ', ['Kevin', 'van', 'Zonneveld']);
    // *     returns 1: 'Kevin van Zonneveld'
 
    return ( ( pieces instanceof Array ) ? pieces.join( glue ) : pieces );
}

//Some more helper ones
function checkAll() {
	for (var i=0; i < cm_selected.elements.length; i++)
	{
		if (cm_selected.elements[i].disabled == '')
			cm_selected.elements[i].checked = true;
	}
}

function checkNone() {
	for (var i=0; i < cm_selected.elements.length; i++)
	{
		if (cm_selected.elements[i].disabled == '')
			cm_selected.elements[i].checked = false;
	}
}

function checkInverse() {
	for (var i=0; i < cm_selected.elements.length; i++)
	{
		if (cm_selected.elements[i].disabled == '')
			cm_selected.elements[i].checked = !cm_selected.elements[i].checked;
	}
}

function getSelected() {
	var selected = Array();
	for (var i=0; i < cm_selected.elements.length; i++)
	{
		var element = cm_selected.elements[i];
		if (element.checked && element.disabled == '')
			selected[selected.length++] = element.name;
	}
	return implode(";", selected);
}

//Work on several selected elements
function deleteSelected() {
	var selected = getSelected();
	if (selected != '')
	{	
		if(confirm("Сигурни ли сте, че желаете да изтриете избраните страници?"))
		{
			cm_remove.cm_selected.value = selected;
			cm_remove.submit();
		}
	}
}

function moveSelected() {
	var selected = getSelected();
	if (selected != '')
	{	
		cm_move.cm_selected.value = selected;
		cm_move.submit();
	}
}

//Work on individual elements
function deleteOne(id, caption) {
	if(confirm('Сигурни ли сте, че желаете да изтриете "' + caption + '"'))
	{
		cm_remove.cm_selected.value = id;
		cm_remove.submit();
	}
}

function moveUpDnOne(id, direction) { //0 up, otherwise down
	cm_move_updn.cm_selected.value = id;
	cm_move_updn.cm_direction.value = direction;
	cm_move_updn.submit();
}

function moveOne(id) {
	cm_move.cm_selected.value = id;
	cm_move.submit();
}

function homePage(id) {
	if(confirm('Сигурни ли сте, че искате да зададете страницата за начална?'))
	{
		cm_homepage.cm_selected.value = id;
		cm_homepage.submit();
	}
}

function notifyUsers(id, caption) {
	msg_notify.setHtmlContent(notify_users_1 + id + notify_users_2 + caption + notify_users_3);
	msg_notify.display();
}

function newPage() {
	msg_add_page.display();
}

function checkPage() {
	if (cm_add.cm_caption_bg.value == '' || cm_add.cm_caption_en.value == '')
		alert('Моля, попълнете заглавията');
	else
		cm_add.submit();
}