var config = new HTMLArea.Config(); // create a new configuration object
                                    // having all the default values
config.width = '485px';
//config.height = '600px';
//----------------------------------------------------------------------------

// the following sets a style for the page body (black text on yellow page)
// and makes all paragraphs be bold by default
config.pageStyle =
  'body { color: black; FONT-FAMILY: "arial ce", "helvetica ce", arial, helvetica, sans-serif; font-size: 12px; text-align: justify; font-weight:normal; width:470px; } ' +
  'a { color: #FD0000; font-family: Arial, Helvetica, sans-serif; font-weight: bold; text-decoration: none } ' +
  'h1 { font-size: 18px; color: #2D5F94; } ' +
  'h2 { font-size: 18px; color: #FD0000; } ' +
  'h3 { font-size: 15px; color: #2D5F94; } ' +
  'h4 { font-size: 15px; color: #FD0000; } ' +
  'h5 { font-size: 12px; color: #2D5F94; } ' +
  'h6 { font-size: 10px; color: #2D5F94; } ' +
  'li { color:green; } ' ;
  
// the following replaces the textarea with the given id with a new
// HTMLArea object having the specified configuration

//-----------------------------------------------------------------------------

config.toolbar = [
[ "fontname", "space",
  "fontsize", "space",
  "formatblock", "space",
  "bold", "italic", "underline","strikethrough","separator",
  "subscript", "superscript", "separator"],
		
[ "justifyleft", "justifycenter", "justifyright", "justifyfull", "separator",
  "insertorderedlist", "insertunorderedlist", "outdent", "indent", "separator",
  "forecolor", "hilitecolor", "separator",
  "inserthorizontalrule", "createlink", "inserttable", "htmlmode", "separator",
  "showhelp" ],
  
[ "copy", "cut", "paste", "space"],  
];


HTMLArea.replace('ed_pole1', config);



