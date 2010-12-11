function showInfo() {
	var myopts = document.getElementById("cm_mode").options;
	var mysel = "unset";
	
	for (i = 0; i < myopts.length; i++)
	{
		if (myopts[i].selected === true)
		{
			mysel = myopts[i].value;
		}
		document.getElementById("cm_mode_" + myopts[i].value).style.display = "none";
	}
	
	document.getElementById("cm_mode_unset").style.display = "none";
	document.getElementById("cm_mode_" + mysel).style.display = "block";
}

function ismaxlength(obj, mlength){
	if (obj.value.length > mlength)
		obj.value=obj.value.substring(0,mlength)
}

function disableFile(isimage) {
	page.cm_image.disabled = page.cm_del_image.checked;
	if (isimage)
	{
		page.cm_image_small.disabled = page.cm_del_image.checked;
		page.cm_image_slide.disabled = page.cm_del_image.checked;
		page.cm_image_thumb.disabled = page.cm_del_image.checked;
	}
}

function hideFile() {
	if (page.cm_auto_image.checked)
	{
		document.getElementById("cm_image_caption").style.display = "none";
		document.getElementById("cm_image_manual").style.display = "none";
	}
	 else
	{
		document.getElementById("cm_image_caption").style.display = "block";
		document.getElementById("cm_image_manual").style.display = "block";
	}
}

function filterFile(obj, extensions, type) {
	var regex = /[^.]+$/;
	var ext = ((regex.exec(obj.value)).toString()).toLowerCase();
	var correct = false;
	for (var i = 0; i < extensions.length; i++)
	{
		if (extensions[i] == ext)
		{
			correct = true;
			break;
		}
	}
	if (!correct)
	{
		obj.value = '';
		alert('Типът на избрания файл е некоректен или не се поддържа!\nМоля, изберете файл от тип `'+type+'`.')
	}
}