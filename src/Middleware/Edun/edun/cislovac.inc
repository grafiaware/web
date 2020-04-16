<?php

class Cislovac {


public static function ocisluj_kurzy() {
    $db = Middleware\Edun\AppContext::getDb();
    
    $sql_query = 'SELECT * from c_kategorie ' ; 
     $res_kat= $db->query($sql_query); 
    
     while($kateg = $res_kat->fetch(\PDO::FETCH_ASSOC)) {   

                   $sql_query = 'SELECT * from kurz where id_c_kategorie_FK =' . $kateg['id_c_kategorie']  .
                         ' order by inserted' ;      
                            //echo "<br>*quéry* " . $sql_query;            
                    $res= $db->query($sql_query); 
                    $i=0;            
                    while($radka_kurz = $res->fetch(\PDO::FETCH_ASSOC)) {      
                                    $i= $i+1; 
                            echo '<br>id_kurz * ' . $radka_kurz['id_kurz'] .' *'.  ' * ' .
                                    '  ---poradcislominule '. $radka_kurz['kurz_poradove_cislo']  . '*' .
                                    '-----i cyklu ' .   "* " . $i . ' *';     

                            $sql_query1 = 'UPDATE kurz set kurz_poradove_cislo= ' . $i .
                                            ' where id_kurz = ' .$radka_kurz['id_kurz'] ; 
                                            //. ' and (' .  $radka_kurz['id_kurz'] . '< 5) ';     //' . $radka_kurz['id_kurz'] ;  
                            $res1=& $db->exec($sql_query1);                               
                    }  //while    
     } //while
}   //function ocisluj_kurzy()


public static function ocisluj_casti() {
    $db = self::get_dbh();
    
     $sql_query = 'SELECT * from c_kategorie ' ; 
     $res_kat= $db->query($sql_query); 
    
     while($kateg = $res_kat->fetch(\PDO::FETCH_ASSOC)) {   
  
            $sql_query = 'SELECT * from cast_kurzu where id_c_kategorie_FK =' . $kateg['id_c_kategorie']  .
                         ' order by inserted' ;     
                    //echo "<br>*quéry* " . $sql_query;            
            $res= $db->query($sql_query); 
            $i=0;            
            while($radka_cast = $res->fetch(\PDO::FETCH_ASSOC)) {      
                            $i= $i+1; 
                    echo '<br>id_cast_kurzu * ' . $radka_cast['id_cast_kurzu'] .' *'.  mb_substr($radka_cast['cast_nazev'],0,20,'utf-8') . ' * ' .
                            '  ---poradcislominule '. $radka_cast['cast_poradove_cislo']  . '*' .
                            '-----i cyklu ' .   "* " . $i . ' *';     

                    $sql_query1 = 'UPDATE cast_kurzu set cast_poradove_cislo= ' . $i .
                                    ' where id_cast_kurzu = ' .$radka_cast['id_cast_kurzu'] ;                             
                    $res1=& $db->exec($sql_query1);                                
            }  //while    
            
     }  //while
}   //function ocisluj_casti()


public static function ocisluj_moduly() {
    $db = self::get_dbh();
    
    $sql_query = 'SELECT * from c_kategorie ' ; 
     $res_kat= $db->query($sql_query); 
    
     while($kateg = $res_kat->fetch(\PDO::FETCH_ASSOC)) {   
    
                    $sql_query = 'SELECT * from modul_kurzu where id_c_kategorie_FK =' . $kateg['id_c_kategorie']  .
                         ' order by inserted' ;   
                            //echo "<br>*quéry* " . $sql_query;            
                    $res= $db->query($sql_query); 
                    $i=0;            
                    while($radka_modul = $res->fetch(\PDO::FETCH_ASSOC)) {      
                                    $i= $i+1; 
                            echo '<br>id_modul_kurzu * ' . $radka_modul['id_modul_kurzu'] .' *'.  mb_substr($radka_modul['modul_nazev'],0,20,'utf-8') . ' * ' .
                                    '  ---poradcislominule '. $radka_modul['modul_poradove_cislo']  . '*' .
                                    '-----i cyklu ' .   "* " . $i . ' *';     

                            $sql_query1 = 'UPDATE modul_kurzu set modul_poradove_cislo= ' . $i .
                                            ' where id_modul_kurzu = ' .$radka_modul['id_modul_kurzu'] ;                             
                            $res1=& $db->exec($sql_query1);                                
                    }  //while
                    
     } // while
    
}   //function ocisluj_moduly()


}
//
//$dbh = Middleware\Edun\AppContext::getDb();
//print_r($dbh);

?>
