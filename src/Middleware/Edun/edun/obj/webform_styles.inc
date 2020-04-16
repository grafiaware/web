<?php
/**
 * @name webform_styles
 * @author 
 * @version 1.0
 * @package webform_obj
 */
/**
 *Třída webform_styles. Shrnuje stylovani zobrazovanych prvku formulare.
 */
class webform_styles {
    
    public static $form=              ' style="border: black 0px solid" ';
    public static $skripta_background=' style="padding:  8px; background-color:gray; border:1px solid gray;" ';
    public static $error=             ' class=chyba ';
    
    public static $input_text=      ' ';
    public static $cselect=         ' style =  "font-size: 100%;" ';
    
    public static $input_checkbox=  ' border="0" cellpadding="1" cellspacing="1" style="font-size: 90%;" ';
    public static $input_submit=    'value="Uložit" ';
    public static $input_reset=     'value="Původní obsah" '; 
    public static $textarea=        ' style = "width:100%;" ';
    
    public static $multi =           ' style= "border: #C0C0C0 1px solid; "   ';   
    public static $drobna_poznamka = ' style= "font-size: 70%; font-style:italic;"';
    public static $pokyn =               ' style= "font-size: 85%; font-style:italic;"';
    
    public static $odkaz_butt =          ' style= "border: red 2px solid; font-size: 120%; padding: 5px; " ';
    public static $drobny_butt =         ' style= "font-size: 80%;"';
    public static $formular_text =       ' style= " display:block; width: 99%; font-size: 85%; background-color: #FFFFEE; border: #C0C0C0 1px solid;"'; 
      //zobrazeni obsahu modulu v kk v editaci kurzu, v editaci casti, v zobrazeni pouziti modulu, casti (u modulu, casti) 
    
     
    public static $formular_cast_nazev = ' style= " display:block;  background-color: white; border: #C0C0C0 0px solid;"';
    public static $formular_cast =       ' style= " display:block;   border: white 4px solid;"';
    
    public static $formular_kurz_modul_nazev = ' style= " display:block;  background-color: #E0E0DD; border: #C0C0C0 1px solid;" ';  // #E0E0DD seda
    public static $formular_kurz_modul_obsah = ' style= " display:none;" ';  // #E0E0DD seda
    
    public static $formular_cast_modul_nazev = ' style= " display:block;  background-color: #E0E0DD; border: #C0C0C0 1px solid;" ';  // #E0E0DD seda
    public static $formular_cast_modul_obsah = ' style= " display:none;" ';  // #E0E0DD seda
       
    
    
    //------------------------  
    public static $kurzy_table='style="border:red 0px solid; font-size:80%";';
    public static $kurzy_table_spacing = array(
                                               'before' =>          ' style= "width:3%; border: black 0px solid;"',
                                               'kod_kurzu' =>       ' style= "width:5%; border: black 0px solid; text-align: right; "',
                                               'kurz_poradove_cislo' => ' style= " width:7%;   border: black 0px solid; text-align: right; "',
                                               'nazev_kurzu' =>     ' style= "          border: black 0px solid; text-align: center; "',
                                               'druh_kurzu' =>      ' style= "width:20%; border: black 0px solid; text-align: left; "',
                                               'kategorie_kurzu' => ' style= "width:20%; border: black 0px solid; text-align: left; "',
                                               'delka_obsahu_skript_kurzu' => ' style= "width:1%; border: black 0px solid; text-align: right; font-size:80% "',
                                               'buttons' =>         ' style= "width:17%; border: black 0px solid;"',
                                               'over' =>            ' style= "width:1%; border: black 0px solid;"')  ;
    public static $kurzy_table_td_doprava = ' style="text-align:right;" ';
    public static $kurzy_table_tlacitka = array(
                             'pridat' =>      ' style= "width: 40%;"',
                             'publikovat' =>  ' style= "width: 40%;"' ); 
    
    //------------------------  
    public static $casti_table=' style="border:red 0px solid; font-size:80%";';
    public static $casti_table_spacing = array(
                            'before' =>                ' style= "           border: black 0px solid;"',
                            'v_kurzu' =>     ' colspan=4 style= "           border: black 0px solid; text-align: left;  "',
                            'v_kurzu_poradove_cislo' =>' style= "width:4%;   border: black 0px solid; text-align: right;  "',
                            'v_kurzu_kod' =>        ' style= "width:3%;            border: black 0px solid; text-align: right; "',
                            'v_kurzu_nazev' =>      ' style= "width:27%;  border: black 0px solid; text-align: left; "',
                            'cast_kod' =>           ' style= "width:5%;   border: black 0px solid; text-align:right; "',
                            'cast_poradove_cislo' =>' style= "width:5%;   border: black 0px solid; text-align:right; "',
                            'cast_nazev' =>         ' style= "width:27%;            border: black 0px solid; text-align:center; "',   
                            'cast_kategorie' =>     ' style= "width:12%;   border: black 0px solid; text-align: left; "', 
                            'pocet_modulu'   =>     ' style= "width:1%;    border: black 0px solid; text-align: right; font-size:80%  "', 
                            'buttons' =>            ' style= "width:10%;   border: black 0px solid;"',
                            'over' =>               ' style= "width:1%;    border: black 0px solid;"');
    public static $casti_table_td_doprava = ' style="text-align:right;"';    
    //------------------------  
    public static $moduly_table=' style="border:red 0px solid; font-size:80%";';
    public static $moduly_table_spacing = array(
                            'before' =>         ' style= "width:1%;   border: black 0px solid;"',
                            'modul_kod' =>      ' style= "width:7%;   border: black 0px solid; text-align: right; "',
                            'modul_poradove_cislo' =>' style= "width:7%;   border: black 0px solid; text-align:right; "',
                            'modul_nazev' =>    ' style= "width:45%;   border: black 0px solid; text-align: center; "',   
                            'modul_kategorie' =>' style= "width:20%;   border: black 0px solid; text-align: left; "',  
        'delka_obsahu_skript_modulu' =>   ' style= "width:1%;   border: black 0px solid; text-align: right; font-size:80% "',
        'delka_obsahu_modulu' =>   ' style= "width:1%;   border: black 0px solid; text-align: right; font-size:80% "',
                            'buttons' =>        ' style= "width:15%;  border: black 0px solid;"',
                            'over' =>           ' style= "width:1%;   border: black 0px solid;"');
    public static $moduly_table_td_doprava = ' style="text-align:right;" ';
    //-----------------------
   
    public static $plan_kurzy_table='style="border:red 0px solid; font-size:80%";';
    public static $plan_kurzy_table_spacing = array(
                                    'before' =>      ' style= "            border: black 0px solid;"',
                                    'kurz_poradove_cislo' => ' style= "width: 7%; border: black 0px solid; text-align: right; "',
                                    'kod_kurzu' =>   ' style= "width: 7%; border: black 0px solid; text-align: right; "',
                                    'nazev_kurzu' => ' style= "           border: black 0px solid; text-align: center; "',                                 
                                    'misto_obec' =>  ' style= "width: 20%; border: black 0px solid;text-align: left; "',
                                    'cena_plan' =>   ' style= "width: 10%; border: black 0px solid; text-align: right; "',
                                    'datum_kurzu' => ' style= "width: 20%; border: black 0px solid; text-align: left; "',
                                    'buttons' =>     ' style= "width: 15%; border: black 0px solid;"',
                                    'over' =>        ' style= "width: 1%;  border: black 0px solid;"');     
    public static $plan_kurzy_table_td_doprava = ' style="text-align:right;" ';
    
    
    //---------------------  tyto fragmenty textu se ulozi do vygenerovaneho kodu, kterym se pak zobrazuje (harmonogram)
    public static $kurzy_table_web    = ' border="0" style="border-collapse: collapse;" ' ;
    public static $kurzy_table_web_spacing = array(
                  'before' =>     ' style=" width=3"',
                  'kod_kurzu' =>  ' style="border: black px solid;" ',   
                  'nazev_kurzu' =>' style="border: black 1px solid;  " ',
                  'misto_obec' => ' style="border: black 1px solid; width=20%; " ',
                  'datum_kurzu' =>' style="border: black 1px solid; width=25%; " ',
                  'cena_plan' =>  ' style="border: black 1px solid;  " ',
                  'podrobnosti' =>' style="border: black 1px solid; width=15%; " ',
                  'prihlasit' =>  ' style="border: black 1px solid; width=15%; " ' );
    public static $kurzy_table_td_web =         ' style="border: black 1px solid; padding-left: 3px; padding-right: 3px; " ';
    public static $kurzy_table_td_web_doprava = ' style="border: black 1px solid; padding-left: 3px; padding-right: 3px; text-align:right;" ';
    //----------------------
    
    public static $path_to_htmlarea = "./";
    public static $name_of_htmlarea_config_js = "app/edun/HTMLarea_default.js";    // tinyMCE.init
    public static $name_of_htmlarea_config_skripta_js = "app/edun/HTMLarea_skripta.js";  // tinyMCE.init
    
    public static $podtrzeni  =      ' style= "text-decoration: underline;"' ;
    public static $podtrzenion  =    ' style= "text-decoration: underline; background:silver"' ;

    public static $display_block  =  ' style= "display:block; " ' ;
    public static $display_none  =   ' style= "display:none; " ' ;
    
     function webform_styl_zahlavi($por,$order){                  
     }
}    