	tinyMCE.init({		 
                selector: "textarea.clas_edit_bezne",  
                // selector: "textarea#modul_obsah",    //nevybira skin!!!! 

                language : "cs",
                skin : "lightgray",
                //skin : "cvicnyskintmavy",
		
                plugins: [
                        "advlist lists  charmap code insertdatetime",
                        "searchreplace wordcount visualblocks visualchars nonbreaking",
                        "paste  save  autosave"                      
                        //autoresize  " autolink  link  print preview hr anchor pagebreak",  " directionality", " textcolor colorpicker textpattern"
                ],
//                autoresize_on_init: false,
//                autoresize_bottom_margin: 10,
                //height : 150,
              
                menubar: false,               
                toolbar1: "undo redo | bold italic | bullist numlist | searchreplace code visualchars nonbreaking charmap insertdatetime",                
                font_formats: 
                    "Arial=arial,helvetica,sans-serif;"+
                    "Arial Black=arial black,avant garde;"+      
                    "Verdana=verdana,geneva;",
                fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",                         
                insertdatetime_formats: [ "%d.%m.%Y", "%H:%M:%S"  ],               	
                nonbreaking_force_tab : true,  //povoluje vlozit 3x&nbsp; tabulatorovym tlacitkem           
               
                style_formats_merge: true, 
                style_formats : [
                     {title : ' *Žádné* '}  ],                                 
	});

	function fileBrowserCallBack(field_name, url, type, win) {
		// This is where you insert your custom filebrowser logic
		alert("Example of filebrowser callback: field_name: " + field_name + ", url: " + url + ", type: " + type);

		// Insert new URL, this would normaly be done in a popup
		win.document.forms[0].elements[field_name].value = "contents/help.html";
	}
	

