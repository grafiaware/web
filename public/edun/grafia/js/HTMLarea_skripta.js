//        // Creates a new editor instance
//var ed = new tinymce.Editor('textareaid', {
//    some_setting : 1
//});

        tinyMCE.init({		                              
                selector: "textarea.clas_edit_skripta",      //nevybira skin!!!
                //selector: "textarea#modul_obsah_skripta",  //nevybira skin!!!
               
                body_class: 'clas_edit_skripta',
        	language : "cs",                                
		
              //skin : "cvicnyskintmavy",
               skin : "lightgray",
//              skin_url : "jscripts/tinymce/skins/cvicnyskintmavy ",
                
                //body_id: "skripta_id",
                //body_class: "skripta_class",

                plugins: [
                        "advlist autolink lists link image  charmap print preview hr anchor pagebreak",
                        "searchreplace wordcount visualblocks visualchars code fullscreen",
                        "insertdatetime nonbreaking  table contextmenu directionality",
                        "paste textcolor colorpicker textpattern searchreplace",
                        "save autosave "  
                ],           //autoresize     
                //removed_menuitems: 'undo, redo',
                height : 600,     
     
// pro  ukladani url obrazku s absolutni cestou       
//              convert_urls: true, tj.default
                relative_urls: false,
                remove_script_host: false,
                document_base_url: 'http://www.grafia.cz/rs/',
                
                toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link   image",
                toolbar2: " preview  | forecolor backcolor | fontselect  fontsizeselect nonbreaking | searchreplace",  // | hr charmap",   
                
                image_advtab: true,                   
                //imagetools_toolbar: "rotateleft rotateright | flipv fliph | editimage imageoptions"
//                image_class_list: [  //class se 'nescitaji' , vzdy jen jedna 
//                        {title: 'None', value: ''},  {title: 'Dog', value: 'dog'},  {title: 'Cat', value: 'cat'}
//                    ],
              
                font_formats: 
                    "Arial=arial,helvetica,sans-serif;"+
                    "Arial Black=arial black,avant garde;"+      
                    "Verdana=verdana,geneva;",
                fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",

                insertdatetime_formats: [ "%d.%m.%Y", "%H:%M:%S" ],               
                content_css : "http://www.grafia.cz/rs/styles_skripta.css", //vyzadovana abs.url, protoze vyse nastaveno ukladani url (napr pro obrazky) s absolutni cestou 
                nonbreaking_force_tab : true,  //povoluje vlozit 3x&nbsp; tabulatorovym tlacitkem
                
            style_formats_merge: true, 
            style_formats : [     
                    {title: "Skripta-stylování", 
                        items: [
                    {title : 'Nadpis části', block : 'p', classes : 'nadpis-casti'},
                    {title : 'Nadpis modulu', block : 'p', classes : 'nadpis-modulu'},
                    {title : 'Nadpis části modulu', block : 'p', classes : 'nadpis-casti-modulu'},
                    {title : 'Nadpis bodu', block : 'p', classes : 'nadpis-bodu'},
                    {title : 'Odstavec', block : 'p', classes : 'odstavec'},
                    {title : 'Heslo (v rámečku)', block : 'p', classes : 'heslo'},
                    {title : 'Cvičení nápis', block : 'p', classes : 'cviceni-napis'},
                    {title : 'Cvičení zadani', block : 'p', classes : 'cviceni-zadani'},
                    {title : 'Odrážky', block : 'p', classes : 'seznam-odrazky'},
                    {title : 'Číslování', block : 'p', classes : 'seznam-cislovani'},
                    {title : 'Seznam - abc', inline : 'ol', classes : 'seznam-abc'},
                   /* {title : 'Seznamp - abc', block : 'ol', classes : 'seznam-abc'},
                     {title : 'Seznamli - abc', selector : 'li', classes : 'seznam-abc'},
                     {title : 'Seznamol - abc', inline : 'ol', classes : 'seznam-abc'},*/
                    {title : 'Tabulka - skripta', block : 'table', classes : 'tabulka-skripta'}
                    ]}
            ],
              
            image_list: [ 
                    {title:"21",  value: "skripta_obrazky/21.jpg"},     
                    {title:"balance",  value: "skripta_obrazky/balance.jpg"},
                    {title:"bludne_kruhy", value: "skripta_obrazky/bludne_kruhy.png"},
                    {title:"byt_jiny", value: "skripta_obrazky/byt_jiny.jpg"},
                    {title:"cas", value: "skripta_obrazky/cas.jpg"},
                    {title:"cb_Eysenckova_typologie_osobnosti", value: "skripta_obrazky/cb_Eysenckova_typologie_osobnosti.jpg"},
                    {title:"cb_nocni_mura_s_telefonem", value: "skripta_obrazky/cb_nocni_mura_s_telefonem.jpg"},
                    {title:"cb_panacek_nachytava_kolemjdouci_na_ozubene_kolo", value: "skripta_obrazky/cb_panacek_nachytava_kolemjdouci_na_ozubene_kolo.jpg"},
                    {title:"cb_panacek_s_rybickou", value: "skripta_obrazky/cb_panacek_s_rybickou.jpg"},
                    {title:"cb_panacek_telefonuje_u_pocitace", value: "skripta_obrazky/cb_panacek_telefonuje_u_pocitace.jpg"},
                    {title:"cb_pani_s_telefonem", value: "skripta_obrazky/cb_pani_s_telefonem.jpg"},
                    {title:"cb_pani_telefonuje_u_pocitace", value: "skripta_obrazky/cb_pani_telefonuje_u_pocitace.jpg"},
                    {title:"cb_pocitac_se_vysmiva_uzivateli", value: "skripta_obrazky/cb_pocitac_se_vysmiva_uzivateli.jpg"},
                    {title:"cb_popelnice_utika_s_novinama", value: "skripta_obrazky/cb_popelnice_utika_s_novinama.jpg"},
                    {title:"cb_potapec_vcela_noviny", value: "skripta_obrazky/cb_potapec_vcela_noviny.jpg"},
                    {title:"cb_rodinka_kouka_z_postele_na_sroubky", value: "skripta_obrazky/cb_rodinka_kouka_z_postele_na_sroubky.jpg"},
                    {title:"cb_rozcestnik", value: "skripta_obrazky/cb_rozcestnik.jpg"},
                    {title:"cb_skoleni", value: "skripta_obrazky/cb_skoleni.jpg"},
                    {title:"cb_telefon_knihy", value: "skripta_obrazky/cb_telefon_knihy.jpg"},
                    {title:"ctyri_opory", value: "skripta_obrazky/ctyri_opory.jpg"},
                    {title:"dojizdet_curve", value: "skripta_obrazky/dojizdet_curve.jpg"},
                    {title:"duvera-spoluprace", value: "skripta_obrazky/duvera-spoluprace.jpg"},
                    {title:"Eysenckova_typologie_osobnosti", value: "skripta_obrazky/Eysenckova_typologie_osobnosti.jpg"},
                    {title:"holka_leze_z_krabice", value: "skripta_obrazky/holka_leze_z_krabice.jpg"},
                    {title:"jehla", value: "skripta_obrazky/jehla.jpg"},
                    {title:"kdo_jsem2", value: "skripta_obrazky/kdo_jsem2.jpg"},
                    {title:"kdo_jsem", value: "skripta_obrazky/kdo_jsem.jpg"},
                    {title:"kdyz_me_nevezmou", value: "skripta_obrazky/kdyz_me_nevezmou.jpg"},
                    {title:"kolektiv_02", value: "skripta_obrazky/kolektiv_02.jpg"},
                    {title:"kolektiv", value: "skripta_obrazky/kolektiv.jpg"},
                    {title:"kompetitivni_vyjednavani_mira_naplneni_zajmu_ucastniku", value: "skripta_obrazky/kompetitivni_vyjednavani_mira_naplneni_zajmu_ucastniku.jpg"},
                    {title:"kooperativni_vyjednavani_mira_naplneni_zajmu_ucastniku", value: "skripta_obrazky/kooperativni_vyjednavani_mira_naplneni_zajmu_ucastniku.jpg"},
                    {title:"konkurz", value: "skripta_obrazky/konkurz.jpg"},
                    {title:"kridla", value: "skripta_obrazky/kridla.jpg"},
                    {title:"kridla_reklama", value: "skripta_obrazky/kridla_reklama.jpg"},
                    {title:"kridla_edit", value: "skripta_obrazky/kridla_edit.jpg"},
                    {title:"motivacni_pyramida", value: "skripta_obrazky/motivacni_pyramida.jpg"},
                    {title:"nezdary", value: "skripta_obrazky/nezdary.jpg"},
                    {title:"nocni_mura_s_telefonem", value: "skripta_obrazky/nocni_mura_s_telefonem.jpg"},
                    {title:"nuda_chytam_lelky.png",  value:"skripta_obrazky/nuda_chytam_lelky.png"},
                    {title:"odolnost_stres_zatez", value: "skripta_obrazky/odolnost_stres_zatez.png"},
                    {title:"panacek_leze_z_krabice_rozhlizi_se", value: "skripta_obrazky/panacek_leze_z_krabice_rozhlizi_se.jpg"},
                    {title:"panacek_nachytava_kolemjdouci_na_ozubene_kolo", value: "skripta_obrazky/panacek_nachytava_kolemjdouci_na_ozubene_kolo.jpg"},
                    {title:"panacek_s_holkou_lezou_z_krbice", value: "skripta_obrazky/panacek_s_holkou_lezou_z_krbice.jpg"},
                    {title:"panacek_s_rybickou", value: "skripta_obrazky/panacek_s_rybickou.jpg"},
                    {title:"panacek_telefonuje_u_pocitace", value: "skripta_obrazky/panacek_telefonuje_u_pocitace.jpg"},
                    {title:"panacek_vcelka_noviny", value: "skripta_obrazky/panacek_vcelka_noviny.jpg"},
                    {title:"pani_telefonuje_u_pocitace", value: "skripta_obrazky/pani_telefonuje_u_pocitace.jpg"},
                    {title:"pocitac_se_vysmiva_uzivateli", value: "skripta_obrazky/pocitac_se_vysmiva_uzivateli.jpg"},
                    {title:"popelnice_utika_s_novinama", value: "skripta_obrazky/popelnice_utika_s_novinama.jpg"},
                    {title:"posta_scan_1_2", value: "skripta_obrazky/posta_scan_1_2.jpg"},
                    {title:"potapec", value: "skripta_obrazky/potapec.jpg"},
                    {title:"potrebne_dokumenty", value: "skripta_obrazky/potrebne_dokumenty.jpg"},
                    {title:"praclovek_vytesal_do_kamene_slovo_plat", value: "skripta_obrazky/praclovek_vytesal_do_kamene_slovo_plat.jpg"},
                    {title:"pravni_minimum", value: "skripta_obrazky/pravni_minimum.jpg"},
                    {title:"presypaci_hodiny", value: "skripta_obrazky/presypaci_hodiny.jpg"},
                    {title:"prevlek", value: "skripta_obrazky/prevlek.jpg"},
                    {title:"prevlek_edit.png",  value: "skripta_obrazky/prevlek_edit.png"},
                    {title:"proti-zakladne_komuniste_blogeri", value: "skripta_obrazky/proti-zakladne_komuniste_blogeri.jpg"},
                    {title:"pruvodni_dopis_curve", value: "skripta_obrazky/pruvodni_dopis_curve.jpg"},
                    {title:"prvni_krucky", value: "skripta_obrazky/prvni_krucky.jpg"},
                    {title:"pyramida_vzteku", value: "skripta_obrazky/pyramida_vzteku.jpg"},
                    {title:"recnik_kocka_a_pes.png", value: "skripta_obrazky/recnik_kocka_a_pes.png"},
                    {title:"reznik.png",  value: "skripta_obrazky/reznik.png"},
                    {title:"scream", value: "skripta_obrazky/scream.png"},
                    {title:"skoleni", value: "skripta_obrazky/skoleni.jpg"},
                    {title:"smile", value: "skripta_obrazky/smile.png"},  
                    {title:"telefon_knihy", value: "skripta_obrazky/telefon_knihy.jpg"},
                    {title:"teleworking", value: "skripta_obrazky/teleworking.jpg"}, 
                    {title:"terc_se_zasahy", value: "skripta_obrazky/terc_se_zasahy.png"},
                    {title:"transakce_dite-dospely", value: "skripta_obrazky/transakce_dite-dospely.jpg"},
                    {title:"transakce_kontaminace", value: "skripta_obrazky/transakce_kontaminace.jpg"},
                    {title:"ucitel_u_tabule_a_zak.png", value: "skripta_obrazky/ucitel_u_tabule_a_zak.png"},
                    {title:"urednik_s_razitky.png",  value: "skripta_obrazky/urednik_s_razitky.png"},
                    {title:"u_doktora.png",   value: "skripta_obrazky/u_doktora.png"},
                    {title:"u_zamestnavatele", value: "skripta_obrazky/u_zamestnavatele.jpg"},
                    {title:"uzitek_vyhody-produktu_potreby-zakaznika", value: "skripta_obrazky/uzitek_vyhody-produktu_potreby-zakaznika.png"},
                    {title:"volba_zamestnavatele", value: "skripta_obrazky/volba_zamestnavatele.jpg"},
                    {title:"vrba", value: "skripta_obrazky/vrba.jpg"},
                    {title:"vvztahy_mezi_jednotlivymi_typy_osobnosti", value: "skripta_obrazky/vztahy_mezi_jednotlivymi_typy_osobnosti.jpg"},                   
                    {title:"zahranici", value: "skripta_obrazky/zahranici.jpg"},
                    {title:"zivotopis_zaujme", value: "skripta_obrazky/zivotopis_zaujme.jpg"}
                                               
            ]

	});


	function fileBrowserCallBack(field_name, url, type, win) {
		// This is where you insert your custom filebrowser logic
		alert("Example of filebrowser callback: field_name: " + field_name + ", url: " + url + ", type: " + type);

		// Insert new URL, this would normaly be done in a popup
		win.document.forms[0].elements[field_name].value = "contents/help.html";
	}
	