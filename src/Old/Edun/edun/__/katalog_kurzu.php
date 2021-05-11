<?php
/**
 * Katalog kurzu management
 * 
 * @name katalog_kurzu.php
 * @author Tomas Cerny <it@grafia.cz
 * @version 1.0
 * @package webform_obj
 */
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/katalog_kurzu_seznam.inc";
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/katalog_kurzu_kurz.inc";
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/skripta_kurzu.inc";
   
        //echo '<br>***(v katalog_kurzu.php)****';           /*SEL*/
        //print_r($_SESSION);
        //echo ('*** $_POST ***'); print_r($_POST);  echo ('<br>*** konec $_POST ***<br>'); /*sel*/
        //echo ('<br>***$_SERVER***');
        //echo ('<br>***$_SERVER[REQUEST_URI]***');print_r( $_SERVER['REQUEST_URI']);
       
                                                                         
        if (isset($_POST['action'])) {       
            $dbh=web_db::get_dbh($_SESSION['login']['user']);               /* dej spojeni na db*/
                    
                    
                                                                          
            switch ($_POST['action']) {
                case "edit":
                    $kurz = new f_katalog_kurzu_kurz('kurz_formular',$_SERVER['REQUEST_URI'],id_code::decode($_POST['item_id']));
                    $kurz->Display();
                    break;
                
                case "drop":
                    $kurz = new f_katalog_kurzu_kurz('kurz_formular',$_SERVER['REQUEST_URI'],id_code::decode($_POST['item_id']));
                    $kurz->Drop();
                    break;

                case "new":
                    $kurz = new f_katalog_kurzu_kurz('kurz_formular',$_SERVER['REQUEST_URI']);
                    $kurz->Display();
                    break;
                
                
                case "dupl":     //prisel ze seznamu  duplikacnim tlacitkem
                     //echo '<br> kurzy  - duplikace kurzu';    // *SEL* 
                     $kurz = new f_katalog_kurzu_kurz('kurz_formular',$_SERVER['REQUEST_URI'], id_code::decode($_POST['item_id']),true ); //true=duplikace
                    
                     $kurz->Display();
                    break;
                
                
                
                
                
                case "skripta":     //prisel ze seznamu   tlacitkem
                     //echo '<br> kurzy  - skripta';    // *SEL* 
                     //echo ('<br>*** $_POST ***'); print_r($_POST);   /*sel*/
                    
                     $skripta= new o_skripta_kurzu(id_code::decode($_POST['item_id']));
                     $skripta->display();
                                          
                    break;
                
                case "skripta_download":     //prisel ze seznamu  tlacitkem
                     //echo '<br> kurzy  - skripta- download';    // *SEL* 
                     //echo ('<br>*** $_POST ***'); print_r($_POST);   /*sel*/
                    
                     $skripta= new o_skripta_kurzu(id_code::decode($_POST['item_id']));
                     $skripta->download();
                                          
                    break; 
                
                
                
                
                
                
                case "publ_katalog":
                     //echo '<br> katalog kurzu - publikace vybranych';     // *SEL* 
                     $katalog_kurzu = new f_katalog_kurzu_seznam('seznam_kurzu_formular',$_SERVER['REQUEST_URI']);
                     $katalog_kurzu->Publikuj($order);  
                    break;
                
                case "form":
                    //echo ('<br>*** $_POST ***'); print_r($_POST);   /*sel*/
                    $kurz = new f_katalog_kurzu_kurz('kurz_formular',$_SERVER['REQUEST_URI'],@id_code::decode($_POST['kurz_id']));
                  
                    if($kurz->Get_errors()){
                        $kurz->Display();
                    }
                    else {
                        //exit;
                        $kurz->Save();
                        /*$katalog_kurzu = new f_katalog_kurz('katalog_kurzu',$_SERVER['REQUEST_URI']);
                        $katalog_kurzu->Display();*/
                    }
                    break;
                
                case "form_dupl":  
                    //prislo se z formulare jednoho kurzu-vytvareni duplikatu    //*SEL*
                    $kurz = new f_katalog_kurzu_kurz('kurz_formular',$_SERVER['REQUEST_URI'],@id_code::decode($_POST['kurz_id']), true); 
                    if($kurz->Get_errors()){
                        $kurz->Display();
                    }
                    else {
                        $kurz->Save(true);
                    }
                    break;
                
                    
                    
                    
              /*      
               case "poradove_cislo_kurzu":
                   //echo ('<br>*** poradove_cislo_kurzu - tady se nastavi*'); 
                      web_db::ocisluj_kurzy();             
                   break; */
                
                
            }
            
        }
        else {
            /* vytvori objekt seznamu kurzu a zobrazi  */
            /* vcetne tlacitek Upravit, Smazat  - u kurzu*/
            /* tl. Pridat novy kurz - pod seznamem */
            
           // $katalog_kurzu = new f_katalog_kurz('katalog_kurzu',$_SERVER['REQUEST_URI']);
            $katalog_kurzu = new f_katalog_kurzu_seznam('seznam_kurzu_formular',$_SERVER['REQUEST_URI']);
            $katalog_kurzu->Display($order);   //$seznam_poradi je GET promenna
        }
    ?>