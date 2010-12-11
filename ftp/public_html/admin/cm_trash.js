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
		if(confirm("Сигурни ли сте, че желаете безвъзвратно да изтриете избраните страници?"))
		{
			cm_remove.cm_selected.value = selected;
			cm_remove.submit();
		}
	}
}

function restoreSelected() {
	var selected = getSelected();
	if (selected != '')
	{	
		cm_restore.cm_selected.value = selected;
		cm_restore.submit();
	}
}

//Work on individual elements
function deleteOne(id, caption) {
	if(confirm('Сигурни ли сте, че желаете безвъзвратно да изтриете "' + caption + '"'))
	{
		cm_remove.cm_selected.value = id;
		cm_remove.submit();
	}
}

function restoreOne(id) {
	cm_restore.cm_selected.value = id;
	cm_restore.submit();
}