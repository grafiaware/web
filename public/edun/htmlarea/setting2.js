var config = new HTMLArea.Config(); // create a new configuration object
                                    // having all the default values
//----------------------------------------------------------------------------

// the following sets a style for the page body (black text on yellow page)
// and makes all paragraphs be bold by default
config.pageStyle =
  'body {color: black; FONT-FAMILY: "arial ce", "helvetica ce", arial, helvetica, sans-serif; font-size: 13px; } ' +
  'a { color: rgb(197,33,49); font-family: Arial, Helvetica, sans-serif; font-weight: bold; text-decoration: none } ' +
  'a:hover { color:black; } ' +
  'h5 { color:green; } ' ;

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


HTMLArea.replace('ed_pole2', config);
