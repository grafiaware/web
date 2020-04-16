/**
 * JavaScript ClientSide function for Form management
 * 
 * 
 * @name form.js
 *
 * @version 1.0
 * @package webform_obj
 */

// ---- Globals ---- //
var default_values = new Array(); //init values for form reset
var cselect_multi_instances = new Array(); // array of itemname of cselect_multi_in_form
var divCounter = new Array(); // per element div counter

// ---- /Globals planovany kurz---- //
var gl_plan_kurz ;
var gl_pocet_ucast_min_plan ;
var gl_pocet_ucast_max_plan ;
var gl_od_data_plan;
var gl_do_data_plan;
//var gl_od_data_skut;
//var gl_do_data_skut;
var gl_misto_kurzu;
var gl_cena;
var gl_odberatel;  

// ---- /Globals kurz---- //
//var gl_kurz_kod;
var gl_kurz_Nazev;
       
//var gl_druh_kurzu_kod;
//var gl_kategorie_kurzu_kod;
//var gl_anotace;
//var gl_cile;

// ---- /Globals modul---- //
//var gl_modul_kod;
var gl_modul_nazev;

// ---- /Globals cast---- //
//var gl_cast_kod;
var gl_cast_nazev;
       
//var gl_druh_kurzu_kod;
//var gl_kategorie_kurzu_kod;
//var gl_anotace;
//var gl_cile;


//----------------------------------------------------------------------
/**
 *init
 *function init - preset the elements registered to globals array to default state accords to passed codes
 */
function init() {
    for(ii=0;ii<cselect_multi_instances.length;ii++) {
        cselect_multi_init(cselect_multi_instances[ii],default_values[cselect_multi_instances[ii]]);
    }
       
}

//----------------------------------------------------------------------
function submit_drop(coded_id) {
   
    var action = document.getElementById('action');
    var item_id = document.getElementById('item_id');
    
    form = document.getElementsByTagName("form")[0];
    //alert ("AAA");
    if ((form.name=='seznam_kurzu_formular' ) || (form.name=='seznam_plan_kurzu_formular' ) ) {
        otazka = "Opravdu chcete vybraný kurz smazat ?";
    }
    if (form.name=='seznam_modulu_formular' ) {
        otazka = "Opravdu chcete vybraný modul smazat ?";
    }
    if (form.name=='seznam_casti_formular' ) {
        otazka = "Opravdu chcete vybranou cast smazat ?";
    }
    
    if(window.confirm(otazka)) {
        action.value = 'drop';
        item_id.value = coded_id;
        document.forms[0].submit();
    }
}

//----------------------------------------------------------------------
function submit_edit(coded_id) { 
    var action = document.getElementById('action');
    var item_id = document.getElementById('item_id');
    action.value = 'edit';
    item_id.value = coded_id;
    // alert ("AAA");
    document.forms[0].submit();
}

function submit_edit_prechod(coded_id, coded_id_odkud) { 
    // alert ("AA");
    var action = document.getElementById('action');
    var item_id_tam = document.getElementById('edun_prechod_item_id_tam');
    var item_id_zpet = document.getElementById('edun_prechod_item_id_zpet');
    var edun_prechod = document.getElementById('edun_prechod');
    
    var form = document.getElementById('cast_formular');
    //        alert ('form-baseURI*:' + form.baseURI );  //+ form.action 
    //form.baseURI = "http://localhost" + uri;    //nenastavi!
    //         alert ('form - po zmene*:' + form.baseURI );  
 
    edun_prechod.value =  "prechod_tam" ;//1;
    action.value = 'edit';
    
    item_id_tam.value = coded_id;
    item_id_zpet.value = coded_id_odkud;   
    // alert ("AAA");
    document.forms[0].submit();
}

function submit_edit_navrat() {
      var action = document.getElementById('action');
      var edun_prechod = document.getElementById('edun_prechod');
      action.value = 'edit';
      edun_prechod.value = "prechod_zpet"; //2;
     
      //alert ("BBB");
      document.forms[0].submit();   
}


//----------------------------------------------------------------------
function submit_dupl(coded_id) {
    var action = document.getElementById('action');
    var item_id = document.getElementById('item_id');
    action.value = 'dupl';
    item_id.value = coded_id;
    document.forms[0].submit();
}


//----------------------------------------------------------------------
function submit_skripta(coded_id) {
    var action = document.getElementById('action');
    var item_id = document.getElementById('item_id');
    action.value = 'skripta';
    item_id.value = coded_id;
    
    document.forms[0].submit();
}

//----------------------------------------------------------------------
function submit_skripta_download(coded_id) {
    var action = document.getElementById('action');
    var item_id = document.getElementById('item_id');
    action.value = 'skripta_download';
    item_id.value = coded_id;
    
    document.forms[0].submit();
}

//----------------------------------------------------------------------
function submit_new() {
    var action = document.getElementById('action');
       // alert ("AAA");
    
    action.value = 'new';
    document.forms[0].submit();
}


//----------------------------------------------------------------------
function submit_publ_planovane() {
    var action = document.getElementById('action');
    action.value = 'publ_planovane';
    /*cislo = document.forms[0].publ_stranka.value;*/
    
   /* if (cislo=="") {
      alert ('\nZadejte čislo stránky, na kterou chcete tabulku publikovat. \n\n' +
             'Upozornění:\nVeškerý dosavadní obsah cílové webové stránky bude přepsán Vaší tabulkou!!!');
    }
    else {*/
       if ( confirm ('Upozornění:\nVeškerý dosavadní obsah stránky HARMONOGRAM bude přepsán Vaší tabulkou!!!')) {
         document.forms[0].submit();
       }
       else {}
    /*}   */
}

//----------------------------------------------------------------------
function submit_publ_katalog() {
    var action = document.getElementById('action');
    action.value = 'publ_katalog';
    /*cislo = document.forms[0].publ_stranka.value;*/
    
    /* if (cislo=="") {
      alert ('\nZadejte čislo stránky, pod kterou chcete vybrané kurzy  publikovat. \n\n' +
             'Upozornění:\nVeškerý dosavadní obsah cílové webové stránky bude přepsán!!!');
    }
    else { */
       if ( confirm ('Upozornění:\nVeškerý dosavadní obsah cílové webové stránky bude přepsán!!!')) {
         document.forms[0].submit();
       }
       else {}
    /*}   */
}




//----------------------------------------------------------------------
function submit_poradove_cislo_kurzu() {
    var action = document.getElementById('action');
       otazka = "Opravdu chcete přečíslovat kurzy ?";
       
     if(window.confirm(otazka)) {
        action.value = 'poradove_cislo_kurzu';
        document.forms[0].submit();
    }
}

function submit_poradove_cislo_casti() {
    var action = document.getElementById('action');
       otazka = "Opravdu chcete přečíslovat části ?";
       
     if(window.confirm(otazka)) {
        action.value = 'poradove_cislo_casti';
        document.forms[0].submit();
    }
}

function submit_poradove_cislo_modulu() {
    var action = document.getElementById('action');
       otazka = "Opravdu chcete přečíslovat moduly ?";
       
     if(window.confirm(otazka)) {
        action.value = 'poradove_cislo_modulu';
        document.forms[0].submit();
    }
}




//----------------------------------------------------------------------
function pri_odeslani() {
    form = document.getElementsByTagName("form")[0];
    
    //+++++++++++++++++++
    if (form.name=='plan_kurz_formular' ) {
      
//      alert( '***v pri_odeslani****' + '*' +form.name + '*' +'\n\n' + 
//            "gl_od_data_plan pri odeslani *" + gl_od_data_plan + '\n' +
//            "gl_do_data_plan pri odeslani *" + gl_do_data_plan + '\n' +
//            "gl_od_data_skut pri odeslani *" + gl_od_data_skut + '\n' +
//            "gl_do_data_skut pri odeslani *" + gl_do_data_skut + '\n' +
//            "gl_plan_kurz pri odeslani *" + gl_plan_kurz 
//      );
      
     
      $ok=true;
      
      if ( (gl_plan_kurz !=    form.plan_kurz.value) ||
           (gl_pocet_ucast_min_plan != form.pocet_ucast_min_plan.value) ||
           (gl_pocet_ucast_max_plan != form.pocet_ucast_max_plan.value) ||
           (gl_od_data_plan != form.od_data_plan.value) ||
           (gl_do_data_plan != form.do_data_plan.value) ||
           //(gl_od_data_skut != form.od_data_skut.value) ||
           //(gl_do_data_skut != form.do_data_skut.value) ||
           (gl_misto_kurzu !=  form.misto_kurzu.value) ||
           (gl_cena !=         form.cena.value) ||
           (gl_odberatel !=    form.odberatel.value)  )
      { $ok=true;
        //return true;    // chci odeslat!! 
      }
      
      else {
        $ok=false;
        alert ("V duplikovaném plánovaném kurzu je třeba alespoň některou z požadovaných hodnot změnit!");
        //return false;   // nechci odeslat!! 
      }
      
          
    }
    
    
    //+++++++++++++++++++
    if (form.name=='kurz_formular' ) {
        
//       alert('***v pri_odeslani****' + '*' +form.name + '*' +'\n\n' + 
//             //"gl_kurz_kod pri odeslani*" + gl_kurz_kod + " f**"  + form.kod.value + '\n' +
//             "gl_kurz_Nazev pri odeslani *" +  gl_kurz_Nazev  + " f**" + form.kurz_Nazev.value   );
//       
       $ok=true;
       //if  (gl_kurz_kod ==    form.kod.value) {$ok=false;}
       if  (gl_kurz_Nazev == form.kurz_Nazev.value) {$ok=false;}
       
       if ($ok) {
        //return true; // chci odeslat!!   pozn. chci odeslat vzdy , jeste hlasim chyby v php (v Get_errors nastavuji)
        }
       else {
        //alert ("V duplikovaném kurzu je třeba změnit zároveň Kód a Název kurzu!");
        alert ("V duplikovaném kurzu je třeba změnit Název kurzu!");
        form.kurz_Nazev.value=gl_kurz_Nazev;
        //form.kod.value=gl_kurz_kod;
        //return false; // nechci odeslat!!   pozn. chci odeslat vzdy , jeste hlasim chyby v php (v Get_errors nastavuji)
       }
    }
    
    
    //+++++++++++++++++++
       
     if (form.name=='modul_formular' ) {
        
//      alert('***v pri_odeslani****' + '*' +form.name + '*' +'\n\n' + 
//             //"gl_modul_kod pri odeslani*" + gl_modul_kod + " f**"  + form.modul_kod.value + '\n' +
//             "gl_modul_nazev pri odeslani *" +  gl_modul_nazev  + " f**" + form.modul_nazev.value   );
//       
       $ok=true;
       //if  (gl_modul_kod ==    form.modul_kod.value) {$ok=false;}
       if  (gl_modul_nazev == form.modul_nazev.value) {$ok=false;}
       
       if ($ok) {
         //return true;
       }
       else {
        //alert ("V duplikovaném modulu je třeba změnit zároveň Kód a Název modulu!");
        alert ("V duplikovaném modulu je třeba změnit  Název modulu!");
        form.modul_nazev.value=gl_modul_nazev;
        //form.modul_kod.value=gl_modul_kod;
        //return false;
       }
     }
     
     
     //+++++++++++++++++++
       
     if (form.name=='cast_formular' ) {
//        
//      alert('***v pri_odeslani****' + '*' +form.name + '*' +'\n\n' + 
//             //"gl_cast_kod pri odeslani*" + gl_cast_kod + " f**"  + form.cast_kod.value + '\n' +
//             "gl_cast_nazev pri odeslani *" +  gl_cast_nazev  + " f**" + form.cast_nazev.value   );
       
       $ok=true;
       //if  (gl_cast_kod ==    form.cast_kod.value) {$ok=false;}
       if  (gl_cast_nazev == form.cast_nazev.value) {$ok=false;}
       
       if ($ok) {
         //return true;
       }
       else {
        //alert ("V duplikovaném části je třeba změnit zároveň Kód a Název části!");
        alert ("V duplikované části je třeba změnit Název části!");
        form.cast_nazev.value=gl_cast_nazev;
        //form.cast_kod.value=gl_cast_kod;
        //return false;
       }
     }
    
    
    //alert ("pri_odeslani");   
    return true;
}


//----------------------------------------------------------------------
function pri_natazeni() {
    form = document.getElementsByTagName("form")[0];
    elem=document.getElementById("action");
    
     //alert ('pri_natazeni' + '*' +form.name + '*' + elem.value);
    
    if ((form.name=='plan_kurz_formular') && (elem.value=="form_dupl")) {  //natahuji formular pro duplikaci plan.kurzu
       gl_plan_kurz =    form.plan_kurz.value;
       gl_pocet_ucast_min_plan = form.pocet_ucast_min_plan.value;
       gl_pocet_ucast_max_plan = form.pocet_ucast_max_plan.value;
       gl_od_data_plan = form.od_data_plan.value;
       gl_do_data_plan = form.do_data_plan.value;
       //gl_od_data_skut = form.od_data_skut.value;
       //gl_do_data_skut = form.do_data_skut.value;
       gl_misto_kurzu =  form.misto_kurzu.value;
       gl_cena =         form.cena.value;
       gl_odberatel =    form.odberatel.value;
       
       
       //alert('***v pri_natazeni****' + '*' +form.name + '*' +'\n\n' + 
       //      "gl_od_data_plan pri natazeni *" + gl_od_data_plan + '\n' +            // *SEL*
       //     "gl_do_data_plan pri natazeni *" + gl_do_data_plan + '\n' +
            //"gl_od_data_skut pri natazeni " + gl_od_data_skut + '\n' +
            //"gl_do_data_skut pri natazeni " + gl_do_data_skut + '\n' +
       //     "gl_plan_kurz pri natazeni *" + gl_plan_kurz 
       //);
       
    }
    //++++++++++++++
   
    if ((form.name=='kurz_formular') && (elem.value=="form_dupl")) {  /*natahuji formular pro duplikaci kurzu*/
       //gl_kurz_kod =    form.kod.value;
       gl_kurz_Nazev = form.kurz_Nazev.value;
       
       //gl_druh_kurzu_kod = form.druh_kurzu_kod.value;
       //gl_kategorie_kurzu_kod = form.kategorie_kurzu_kod.value;
       //gl_anotace = form.anotace.value;
       //gl_cile =  form.cile.value;
       
        
//       alert('***v pri_natazeni****' + '*' +form.name + '*' +'\n\n' + 
//            // "gl_kurz_kod pri natazeni *" + gl_kurz_kod + '\n' +                 // *SEL*
//            "gl_kurz_Nazev pri natazeni *" + gl_kurz_Nazev + '\n' 
//            //"gl_druh_kurzu_kod pri natazeni *" + gl_druh_kurzu_kod + '\n' +
//            //"gl_kategorie_kurzu_kod pri natazeni *" + gl_kategorie_kurzu_kod + '\n' +
//            //"gl_anotace pri natazeni *" + gl_anotace + '\n' +
//            //"gl_cile pri natazeni *" + gl_cile 
//        ); 
    }
    //++++++++++++++
    
     if ((form.name=='modul_formular') && (elem.value=="form_dupl")) {  /*natahuji formular pro modul*/
       //gl_modul_kod =    form.modul_kod.value;
       gl_modul_nazev = form.modul_nazev.value;
       
        
//       alert('***v pri_natazeni****' + '*' +form.name + '*' +'\n\n' + 
//             //"gl_modul_kod pri natazeni *" + gl_modul_kod + '\n' +               // *SEL*
//            "gl_modul_nazev pri natazeni *" + gl_modul_nazev + '\n' 
//       ); 
    }
    
    
    //++++++++++++++
    
     if ((form.name=='cast_formular') && (elem.value=="form_dupl")) {  /*natahuji formular pro cast*/
       //gl_cast_kod =    form.cast_kod.value;
       gl_cast_nazev = form.cast_nazev.value;
       
        
//       alert('***v pri_natazeni****' + '*' +form.name + '*' +'\n\n' + 
//             //"gl_modul_kod pri natazeni *" + gl_modul_kod + '\n' +               // *SEL
//            "gl_modul_nazev pri natazeni *" + gl_modul_nazev + '\n' 
//       ); 
    }
   
}



//-----------------------------------------------------

function zobraz_skryj_div_tlacitkem(id_divu,id_tlacitka,text) {
         elbutt = document.getElementById(id_tlacitka);
         //alert("elbuttname " + elbutt.name + "\n elbuttvalue " +  elbutt.value);
        
         eldiv = document.getElementById(id_divu);
         //alert ("eldiv style display " + eldiv.style.display );
         
         if (eldiv.style.display == 'block') {
            eldiv.style.display = 'none';
            elbutt.value= 'Zobraz' + ' ' + text;
         }
         else {
            eldiv.style.display = 'block';
            elbutt.value= 'Skryj' + ' ' + text;
         }
         
         //eldiv.style.display=(eldiv.style.display == 'block')?'none':'block';
        return;
}




//----------------------------------------------------------------------
function zobr_radio_click(id) {
    //alert("_radio_cklick **"  + id );
    
    if ((id=="zobrazeni_obsahu1") || (id=="zobrazeni_obsahu2")  || (id=="zobrazeni_obsahu3")) {
      eldivokurz = document.getElementById("div_obsah_kurz");
      //eldivomoduly = document.getElementById("div_obsah_moduly");
      eldivocasti = document.getElementById("div_obsah_casti");
       //alert ("eldivocasti style display1* " + eldivocasti.style.display );
      
      if (id=="zobrazeni_obsahu1") {
        //alert("zobrazeni_obsahu1**"  + id );
        eldivokurz.style.display = 'block';
        //eldivomoduly.style.display = 'none';
        eldivocasti.style.display = 'none';
      }
    
      if (id=="zobrazeni_obsahu2") {
        //alert("zobrazeni_obsahu2**"  + id );
        eldivokurz.style.display = 'none';
        //eldivomoduly.style.display = 'block';
        eldivocasti.style.display = 'block';
      }
      
      if (id=="zobrazeni_obsahu3") {
        //alert("zobrazeni_obsahu3**"  + id );
        eldivokurz.style.display = 'block';
        //eldivomoduly.style.display = 'block';
        eldivocasti.style.display = 'block';
      }
    }
    //alert ("eldivocasti style display2* " + eldivocasti.style.display );
return;    
}



