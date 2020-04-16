<?php
/**
 * Casti kurzu
 * 
 * @name casti_kurzu.php
 * @author 
 * @version 1.0
 * @package webform_obj
 */

require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/casti_kurzu_seznam.inc";
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/casti_kurzu_cast.inc";

require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/moduly_kurzu_modul.inc";

   
        //echo '<br>***(v casti_kurzu.php)****';           //*SEL
        //print_r($_SESSION);
        // echo ('*** $_POST ***'); print_r( $_POST);   //*SEL*
        //echo ('<br>***$_SERVER***');
        //echo ('<br>***$_SERVER[REQUEST_URI]***');print_r( $_SERVER['REQUEST_URI']);
        // echo  "<br>dekodovano " . id_code::decode($_POST['item_id']);
       
                                                                         
if (isset($_POST['action']) ) {       
            $dbh=Middleware\Edun\AppContext::getDb();
                    
       
 if  (  (isset($_POST['edun_prechod'] )) and
       // (isset($_POST['item_id_promodul'] )) and 
        ($_POST['edun_prechod'] == "prechod_tam")   //1
     )
 {                             
               $pp=$_SERVER['REQUEST_URI'];
               $pp= str_replace( "casti_kurzu", "moduly_kurzu", $pp);
               
               //$modul = new f_moduly_kurzu_modul('modul_formular', $pp, id_code::decode($_POST['item_id_promodul']) );
               $modul = new f_moduly_kurzu_modul('modul_formular', $pp, id_code::decode($_POST['edun_prechod_item_id_tam']) );
               $modul->Display();
               
 }
 else {
                                
                                                                          
            switch ($_POST['action']) {
                case "edit":
                    $cast = new f_casti_kurzu_cast('cast_formular',$_SERVER['REQUEST_URI'],id_code::decode($_POST['item_id']));
                    $cast->Display();
                    break;
                
                case "drop":
                    //echo "<br>drop"; //*SEL*
                    $cast = new f_casti_kurzu_cast('cast_formular',$_SERVER['REQUEST_URI'],id_code::decode($_POST['item_id']));
                    $cast->Drop();
                    break;

                case "new":
                    $cast = new f_casti_kurzu_cast('cast_formular',$_SERVER['REQUEST_URI']);
                    $cast->Display();
                    break;
                
                case "dupl":     //prisel ze seznamu  duplikacnim tlacitkem
                     //echo '<br> casti  - duplikace casti';     //*SEL*
                     $cast = new f_casti_kurzu_cast('cast_formular',$_SERVER['REQUEST_URI'], id_code::decode($_POST['item_id']),true ); //true=duplikace
                     $cast->Display();
                    break;

                
                 
                case "form":
                    $cast = new f_casti_kurzu_cast('cast_formular',$_SERVER['REQUEST_URI'],@id_code::decode($_POST['cast_id']));
                    if($cast->Get_errors()){
                        $cast->Display();
                    }
                    else {
                        $cast->Save();
                        /*...$katalog_kurzu = new f_katalog_kurz('katalog_kurzu',$_SERVER['REQUEST_URI']);
                        ...$katalog_kurzu->Display();*/
                    }
                    break;
                
                
                case "form_dupl":  
                    //prislo se z formulare jednoho modulu    //*SEL*
                    $cast = new f_casti_kurzu_cast('cast_formular',$_SERVER['REQUEST_URI'],@id_code::decode($_POST['cast_id']), true); 
                    if($cast->Get_errors()){
                        $cast->Display();
                    }
                    else {
                        $cast->Save(true);
                    }
                    break;
                
            } //switch
 }            
 
 }  //action
        
        else {
            /* vytvori objekt seznamu casti a zobrazi  */
            /* vcetne tlacitek Upravit, Smazat....  - u casti*/
            /* tl. Pridat novou cast - pod seznamem */
            
            $casti_kurzu = new f_casti_kurzu_seznam('seznam_casti_formular',$_SERVER['REQUEST_URI']);
            $casti_kurzu->Display($order); //$seznam_poradi je GET promenna
        }
    ?>