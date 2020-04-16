<?php
/**
 * Moduly kurzu
 * 
 * @name moduly_kurzu.php
 * @author 
 * @version 1.0
 * @package webform_obj
 */

require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/moduly_kurzu_seznam.inc";
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/moduly_kurzu_modul.inc";

require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/casti_kurzu_cast.inc";
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/katalog_kurzu_kurz.inc";

   
        //echo '<br>***(v moduly_kurzu.php)****';           //*SEL
        //print_r($_SESSION);
        // echo ('*** $_POST ***'); print_r( $_POST);   //*SEL*
        //echo ('<br>***$_SERVER[REQUEST_URI]***');
        //print_r( $_SERVER['REQUEST_URI']);
        //  echo  "<br>dekodovano " . id_code::decode($_POST['item_id']);  
 

if (isset($_POST['action'])) {       
            $dbh=Middleware\Edun\AppContext::getDb();
                    

if  (  (isset($_POST['edun_prechod'] )) and
      // (isset($_POST['item_id_procast'] )) and 
       ($_POST['edun_prechod'] == "prechod_zpet")    )  //2
{
    
    if  (!( strpos($_POST['navrat_script'], "casti_kurzu" ) === false) ) {  
        $cast = new f_casti_kurzu_cast('cast_formular', $_POST['navrat_script'],id_code::decode($_POST['edun_prechod_item_id_zpet'] ));
        $cast->Display();  
    }
    if  (!( strpos($_POST['navrat_script'], "katalog_kurzu" ) === false) ) { 
         $kurz = new f_katalog_kurzu_kurz('kurz_formular',$_POST['navrat_script'],id_code::decode($_POST['edun_prechod_item_id_zpet'] ));
         $kurz->Display();
    }  
    
                           
}
else {                  
            
                                                                          
            switch ($_POST['action']) {
                case "edit":      
                    $modul = new f_moduly_kurzu_modul('modul_formular',$_SERVER['REQUEST_URI'],id_code::decode($_POST['item_id']));
                    $modul->Display();
                   
//                    $vracenka = new f_moduly_kurzu_vraceci_formular('vraceci_formular',
//                                                       $_POST['navrat_script'],
//                                                        $_POST['item_id_procast_decod']
//                                                        );
//                    $vracenka->Display();
                    break;
                
                case "drop":
                    //echo "<br>drop"; //*SEL*
                    $modul = new f_moduly_kurzu_modul('modul_formular',$_SERVER['REQUEST_URI'],id_code::decode($_POST['item_id']));
                    $modul->Drop();
                    break;

                case "new":
                    $modul = new f_moduly_kurzu_modul('modul_formular',$_SERVER['REQUEST_URI']);
                    $modul->Display();
                    break;
                
                case "dupl":     //prisel ze seznamu  duplikacnim tlacitkem
                     //echo '<br> moduly  - duplikace modulu';     //*SEL*
                     $modul = new f_moduly_kurzu_modul('modul_formular',$_SERVER['REQUEST_URI'], id_code::decode($_POST['item_id']),true ); //true=duplikace
                     $modul->Display();
                    break;

                
                 
                case "form":
                    $modul = new f_moduly_kurzu_modul('modul_formular',$_SERVER['REQUEST_URI'],@id_code::decode($_POST['modul_id']));
                    if($modul->Get_errors()){
                        $modul->Display();    
                        
                    }
                    else {
                        $modul->Save();

                        if ( $_POST['edun_prechod'] == "prechod_tam" or ($_POST['vkladany_file']) )
                        {  //1 
                            $modul = new f_moduly_kurzu_modul('modul_formular',$_SERVER['REQUEST_URI'],@id_code::decode($_POST['modul_id']));
                            $modul->Display();
                                                                          
                        }
                        /*$katalog_kurzu = new f_katalog_kurz('katalog_kurzu',$_SERVER['REQUEST_URI']);
                        $katalog_kurzu->Display();*/
                    }
                    break;
                
                
                case "form_dupl":  
                    //prislo se z formulare jednoho modulu    //*SEL*
                    $modul = new f_moduly_kurzu_modul('modul_formular',$_SERVER['REQUEST_URI'],@id_code::decode($_POST['modul_id']), true); 
                    if($modul->Get_errors()){
                        $modul->Display();
                    }
                    else {
                        $modul->Save(true);
                    }
                    break;
                
                
            } //switch
}            
            
}  //action
        
        else {
            /* vytvori objekt seznamu modulu a zobrazi  */
            /* vcetne tlacitek Upravit, Smazat  - u modulu*/
            /* tl. Pridat novy modul - pod seznamem */
            
            $moduly_kurzu = new f_moduly_kurzu_seznam('seznam_modulu_formular',$_SERVER['REQUEST_URI']);
            $moduly_kurzu->Display($order);
        }
      
        
    ?>