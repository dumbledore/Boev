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
	for (var i=0; i < um_selected.elements.length; i++)
	{
		if (um_selected.elements[i].disabled == '')
			um_selected.elements[i].checked = true;
	}
}

function checkNone() {
	for (var i=0; i < um_selected.elements.length; i++)
	{
		if (um_selected.elements[i].disabled == '')
			um_selected.elements[i].checked = false;
	}
}

function checkInverse() {
	for (var i=0; i < um_selected.elements.length; i++)
	{
		if (um_selected.elements[i].disabled == '')
			um_selected.elements[i].checked = !um_selected.elements[i].checked;
	}
}

function getSelected() {
	var selected = Array();
	for (var i=0; i < um_selected.elements.length; i++)
	{
		var element = um_selected.elements[i];
		if (element.checked && element.disabled == '')
			selected[selected.length++] = element.name;
	}
	return implode(";", selected);
}

function verifyEmail(email){
	var emailRegEx = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;

	if (email.search(emailRegEx) == -1)
	{
		return false;
	}
	return true;
}

//Work on several selected elements
function deleteSelected() {
	var selected = getSelected();
	if (selected != '')
	{	
		if(confirm("Сигурни ли сте, че желаете да изтриете избраните потребители?"))
		{
			um_remove.um_selected.value = selected;
			um_remove.submit();
		}
	}
}

//Work on individual elements
function deleteOne(username) {
	if(confirm('Сигурни ли сте, че желаете да изтриете "' + username + '"'))
	{
		um_remove.um_selected.value = username;
		um_remove.submit();
	}
}

function activateOne(id, mode) {
	um_activate.um_selected.value = id;
	um_activate.um_mode.value = mode;
	um_activate.submit();
}

//
function newUser() {
	myMSG.display();
}

function checkUser() {
	if (um_add.um_username.value == '' || !verifyEmail(um_add.um_email.value))
		alert('Моля, попълнете името и въведете валиден е-мейл адрес!');
	else
		um_add.submit();
}