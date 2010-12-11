// ALBUS SPECIALIZED CONFIG


// If one prefers good HTML, the next should be TRUE
// In the other case, <p> is converted to <div> in order
// to maintain the visual design from Word

//Doctype. Please, do not omit.
FCKConfig.DocType = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';

//Next one forces the editor to submit '' instead of <p></p> if there is no text added
FCKConfig.IgnoreEmptyParagraphValue = true ;

//Next one fixes using <div> instead of <p> when pasting from MS Word
FCKConfig.CleanWordKeepsStructure = true;

//FCKConfig.EnterMode = 'br' ; 
FCKConfig.ShiftEnterMode = 'br' ; 

//Lang settings
FCKConfig.AutoDetectLanguage = false ;
FCKConfig.DefaultLanguage = "bg" ;

//Tabs
FCKConfig.TabSpaces = 10;

//Fonts
FCKConfig.FontNames= 'Arial;Courier New;Georgia;Tahoma;Times New Roman;Verdana';
FCKConfig.DefaultFontLabel = 'Times New Roman';
FCKConfig.FontSizes = '0.9em/10 pt;1.0em/12 pt;1.2em/14 pt;1.3em/16 pt;1.4em/18 pt;1.6em/22 pt;2.0em/30 pt';
//FCKConfig.FontSizes = '14px/Fourteen Pixels;1.5em/A bit biggger;80%/A bit smaller';
FCKConfig.DefaultFontSizeLabel = '12 pt';
//Styles
FCKConfig.CustomStyles = 
						{
							'заглавни' : {Element :'span', Styles : {'letter-spacing' : '2px'} }
						};

//Available toolbars
FCKConfig.ToolbarSets["AdminVersion"] =  [
	['NewPage','Source','-','Preview','-'],
	['Cut','Copy','Paste','PasteText','PasteWord','-','Print'],
	['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
	['Form','Checkbox','Radio','TextField','Textarea','Select','Button','ImageButton','HiddenField'],
	'/',
	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'],
	['OrderedList','UnorderedList','-','Outdent','Indent'],
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
	['Link','Unlink','Anchor'],
	['Image','Flash','Table','Rule','SpecialChar','PageBreak'],
	'/',
	['Style', 'FontFormat','FontName','FontSize'],
	['TextColor','BGColor'],
	['FitWindow','ShowBlocks','-','About']		// No comma for the last row.
] ;
										
FCKConfig.ToolbarSets["EditorVersion"] =  [
	['NewPage','Preview'],
	['Cut','Copy','Paste', 'PasteText', 'PasteWord', '-','Print'],
	['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
	['FitWindow','ShowBlocks'],
	'/',
	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'],
	['OrderedList','UnorderedList','-','Outdent','Indent'],
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
	['Link','Unlink','Anchor'],
	['Image','Flash','Table','Rule','SpecialChar','PageBreak'],
	'/',
	['Style', 'FontName','FontSize'],
	['TextColor','BGColor']		// No comma for the last row.
] ;