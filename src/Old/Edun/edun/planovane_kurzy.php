<?php
/**
 * Planovane kurzy
 * 
 * @name planovane_kurzy.php
 * @version 1.0
 * @package webform_obj
 */
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/planovane_kurzy_kurz.inc";
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/planovane_kurzy_seznam.inc";

// potrebuje get promennou $seznam_poradi - viz index.php

        //echo ('<br>*** $_SESSION ***'); print_r($_SESSION);
        //echo "<br>sess prava: "  ; print_r($sess_prava);
        //echo ('<br>*** $_POST ***');  echo'<br>'; var_dump($_POST);  echo'<br>';/*SEL*/
        //print_r("sess user: " . $_SESSION['login']['user']);

        //echo ('<br>***$_SERVER[REQUEST_URI]***');print_r( $_SERVER['REQUEST_URI']);
                                                                         
        if (isset($_POST['action'])) {       /*  */
            $dbh=Middleware\Edun\AppContext::getDb();
                                                                          
            switch ($_POST['action']) {
                case "edit":
                     //echo '<br> planovane kurzy - editace kurzu';    // *SEL* 
                     $planovany_kurz = new f_planovane_kurzy_kurz('plan_kurz_formular',$_SERVER['REQUEST_URI'], id_code::decode($_POST['item_id']));
                    
                     $planovany_kurz->Display();
                    break;
                
                case "drop":
                    //echo '<br> planovane kurzy - vymazat kurz';    // *SEL* 
                    $planovany_kurz = new f_planovane_kurzy_kurz('plan_kurz_formular',$_SERVER['REQUEST_URI'],id_code::decode($_POST['item_id']) );
                    
                    $planovany_kurz->Drop();
                    break;

                case "new":
                     //echo '<br> planovane kurzy - novy';    // *SEL* 
                     $planovany_kurz = new f_planovane_kurzy_kurz('plan_kurz_formular',$_SERVER['REQUEST_URI']);
                    
                     $planovany_kurz->Display();
                    break;
                
                case "dupl":     //prisel ze seznamu planovanych duplikacnim tlacitkem
                     //echo '<br> planovane kurzy - duplikace kurzu';    // *SEL* 
                     $planovany_kurz = new f_planovane_kurzy_kurz('plan_kurz_formular',$_SERVER['REQUEST_URI'], id_code::decode($_POST['item_id']),true ); //true=duplikace
                    
                     $planovany_kurz->Display();
                    break;
                
                
                case "publ_planovane":
                     //echo '<br> planovane kurzy - publikace tabulky';     /* SEL*/
                     $planovane_kurzy = new f_planovane_kurzy_seznam('seznam_plan_kurzu_formular',$_SERVER['REQUEST_URI']);
                     $planovane_kurzy->Publikuj($order);  
                    break;
                
                
                
                case "form":
                    //echo '<br> planovane kurzy - ulozit'; //prislo se z formulare jednoho kurzu (ukladacim tlacitkem)   // *SEL* 
                    $planovany_kurz = new f_planovane_kurzy_kurz('plan_kurz_formular',$_SERVER['REQUEST_URI'],@id_code::decode($_POST['planovany_kurz_id']));
                    if($planovany_kurz->Get_errors()){
                        $planovany_kurz->Display();
                    }
                    else {
                        $planovany_kurz->Save();
                        
                           /*$katalog_kurzu = new f_katalog_kurz('katalog_kurzu',$_SERVER['REQUEST_URI']);
                           $katalog_kurzu->Display();*/
                    }
                    break;
                
                 case "form_dupl":  
                     //prislo se z formulare jednoho kurzu   // *SEL* 
                    $planovany_kurz = new f_planovane_kurzy_kurz('plan_kurz_formular',$_SERVER['REQUEST_URI'],@id_code::decode($_POST['planovany_kurz_id']), true);
                    if($planovany_kurz->Get_errors()){
                        $planovany_kurz->Display();
                    }
                    else {
                        $planovany_kurz->Save(true);
                    }
                    break;
            }
            
        }
        else {
            /* vytvori objekt formulare seznamu naplanovanych kurzu a zobrazi  */
            /* vcetne tlacitek Upravit, Smazat ,duplikace - u kurzu*/
            /* tl. Pridat novy naplanovany kurz - pod seznamem */
            
           // $katalog_kurzu = new f_katalog_kurz('katalog_kurzu',$_SERVER['REQUEST_URI']);
            $planovane_kurzy = new f_planovane_kurzy_seznam('seznam_plan_kurzu_formular',$_SERVER['REQUEST_URI']);
            $planovane_kurzy->Display($order);   //$seznam_poradi je GET promenna
        }
    ?>