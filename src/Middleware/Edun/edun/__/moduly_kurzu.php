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
   
        //echo '<br>***(v moduly_kurzu.php)****';           //*SEL
        //print_r($_SESSION);
        // echo ('*** $_POST ***'); print_r( $_POST);   //*SEL*
        //echo ('<br>***$_SERVER***');
        //echo ('<br>***$_SERVER[REQUEST_URI]***');print_r( $_SERVER['REQUEST_URI']);
        //  echo  "<br>dekodovano " . id_code::decode($_POST['item_id']);
       
                                                                         
        if (isset($_POST['action'])) {       
            $dbh=web_db::get_dbh($_SESSION['login']['user']);               /* dej spojeni na db*/
                    
                    
                                                                          
            switch ($_POST['action']) {
                case "edit":
                    $modul = new f_moduly_kurzu_modul('modul_formular',$_SERVER['REQUEST_URI'],id_code::decode($_POST['item_id']));
                    $modul->Display();
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
                    
               /*        
               case "poradove_cislo_modulu":
                      web_db::ocisluj_moduly();             
                   break;        
               */ 
                
            }
            
        }
        else {
            /* vytvori objekt seznamu modulu a zobrazi  */
            /* vcetne tlacitek Upravit, Smazat  - u modulu*/
            /* tl. Pridat novy modul - pod seznamem */
            
            $moduly_kurzu = new f_moduly_kurzu_seznam('seznam_modulu_formular',$_SERVER['REQUEST_URI']);
            $moduly_kurzu->Display($order);
        }
    ?>