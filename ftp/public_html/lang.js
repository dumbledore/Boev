function setLang(lang) {
	if (lang != 0)
	{
		lang_text = 'bg';
		if (lang == 2)
			lang_text = 'en';
		myDate = new Date();
		myDate.setDate(myDate.getDate() + 3*365); //3 years
		document.cookie = 'lang = ' + lang_text + '; expires' + myDate + '; path = /';
		window.location.reload();
	}
}