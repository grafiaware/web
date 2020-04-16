<?php
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/webform_obj.inc";
        
class f_katalog_kurzu_kurz extends webform{
    private $error_bool=false;
    private $mdb2;
    private $kurz_id;
    private $kurz_kod;       // $kod_kurzu; 
    private $druh_kurzu_kod;
    private $modul_kurzu_kod;
    private $pocet_ucastniku_min;
    private $pocet_ucastniku_max;
    private $delka_hodin;
    private $delka_detail;
    private $delka_prubeh;
    private $cena;
    private $cena_detail;
    private $kurz_Nazev;
    private $kurz_Anotace;
    private $kurz_Cile;
    private $kurz_Obsah;
    private $kurz_Vstupni_znalosti;
    private $kurz_Doporuceni;
    private $cilova_skupina;
    private $anotace;
    private $dalsi_kurzy;
    
    
    
    public function __construct($name,$action_script,$kurz_id=false) {
        $this->mdb2 = Middleware\Edun\AppContext::getDb();
        parent::__construct($name,$action_script);
        $this->kurz_id= $kurz_id;
        if($this->kurz_id) {
            $sql_query = 'SELECT
                    (SELECT kod FROM c_druh_kurzu WHERE c_druh_kurzu.id_c_druh_kurzu = kurz.id_c_druh_kurzu) AS druh_kurzu,
                    (SELECT kod FROM c_modul_kurzu WHERE c_modul_kurzu.id_c_modul_kurzu = kurz.id_c_modul_kurzu) AS modul_kurzu,
                    id_popis_kurzu,
                    kod_kurzu,
                    pocet_ucastniku_min,
                    pocet_ucastniku_max,
                    delka_hodin,
                    delka_detail,
                    (SELECT kod FROM c_delka_prubeh WHERE c_delka_prubeh.id_c_delka_prubeh = kurz.id_c_delka_prubeh) AS delka_prubeh,
                    cena,
                    cena_detail
                FROM kurz
                WHERE id_kurz = '.$this->kurz_id.';';
            $res=$this->mdb2->query($sql_query);
            $kurz = $res->fetch(\PDO::FETCH_ASSOC);
            //print_r($kurz);
            $sql_query = 'SELECT
                    kurz_Nazev,
                    kurz_Anotace,
                    kurz_Cile,
                    kurz_Obsah,
                    kurz_Vstupni_znalosti,
                    kurz_Doporuceni
                FROM popis_kurzu
                WHERE id_popis_kurzu = '.$kurz['id_popis_kurzu'].';';
                
            $res=$this->mdb2->query($sql_query);
            $popis_kurzu = $res->fetch(\PDO::FETCH_ASSOC);
            //print_r($popis_kurzu);
            
            $sql_query = 'SELECT
                            kod
                            FROM sp_kurz_cilova_skupina Inner Join c_cilova_skupina
                            ON sp_kurz_cilova_skupina.id_c_cilova_skupina = c_cilova_skupina.id_c_cilova_skupina
                            WHERE id_kurz = '.$this->kurz_id.';';
            $res=$this->mdb2->query($sql_query);
            $cilova_skupina = array();
            while($cilova_skupina_polozka = $res->fetch(\PDO::FETCH_ASSOC)) {
                array_push($cilova_skupina,$cilova_skupina_polozka['kod']);
            }
            //print_r($cilova_skupina);
            
            $sql_query = 'SELECT
                            id_kurz_navazujici            
                            FROM navazujici_kurzy
                            WHERE id_kurz = '.$this->kurz_id.';';
            $res=$this->mdb2->query($sql_query);
            $navazujici_kurzy = array();
            while($navazujici_kurzy_polozka = $res->fetch(\PDO::FETCH_ASSOC)) {
                array_push($navazujici_kurzy,$navazujici_kurzy_polozka['id_kurz_navazujici']);
            }
            //print_r($navazujici_kurzy);
        }
        $this->kurz_kod = new c_webform_input_text("kod","Kód kurzu",10,2,10,@$kurz['kod_kurzu']);
        $this->kurz_Nazev = new c_webform_input_text("kurz_Nazev","Název kurzu",50,5,200,@$popis_kurzu['kurz_nazev']);
        $this->druh_kurzu_kod = new c_webform_cselect("druh_kurzu_kod","Druh kurzu:",1,"c_druh_kurzu",@$kurz['druh_kurzu']);
        $this->modul_kurzu_kod = new c_webform_cselect("modul_kurzu_kod","Modul kurzu:",1,"c_modul_kurzu",@$kurz['modul_kurzu']);
        $this->pocet_ucastniku_min = new c_webform_input_text("pocet_ucastniku_min","Počet účastníků od:",5,0,5,@$kurz['pocet_ucastniku_min']);
        $this->pocet_ucastniku_max = new c_webform_input_text("pocet_ucastniku_max"," do:",5,1,5,@$kurz['pocet_ucastniku_max']);
        $this->delka_hodin = new c_webform_input_text("delka_hodin","Délka kurzu v hodinách:",5,1,5,@$kurz['delka_hodin']);
        $this->delka_detail = new c_webform_input_text("delka_detail","Upřesnění průběhu kurzu:",30,0,100,@$kurz['delka_detail']);
        $this->delka_prubeh = new c_webform_cselect("delka_prubeh","Průběh kurzu:",1,"c_delka_prubeh",@$kurz['delka_prubeh']);
        $this->cena = new c_webform_input_text("cena","Cena kurzu pro účastníka:",5,0,10,@$kurz['cena']);
        $this->cena_detail = new c_webform_input_text("cena_detail","Bližší informace k ceně:",30,0,100,@$kurz['cena_detail']);
        $this->cilova_skupina = new c_webform_input_checkbox("cilova_skupina","Cílová skupina:",4,"c_cilova_skupina",@$cilova_skupina);
        $this->kurz_Anotace = new c_webform_textarea("anotace","Anotace kurzu:",80,10,10,300,"toje zkušební text",@$popis_kurzu['kurz_anotace']);
        $this->kurz_Cile = new c_webform_textarea("cile","Cíle kurzu:",80,10,10,300,@$popis_kurzu['kurz_cile']);
        $this->kurz_Obsah = new c_webform_textarea("obsah","Obsah kurzu:",80,20,10,800,@$popis_kurzu['kurz_obsah']);
        $this->kurz_Vstupni_znalosti = new c_webform_textarea("znalosti","Doporučené vstupní znalosti účastníka:",80,10,0,200,@$popis_kurzu['kurz_vstupni_znalosti']);
        $this->kurz_Doporuceni = new c_webform_textarea("doporuceni","Další doporučení ke kurzu:",80,10,0,200,@$popis_kurzu['kurz_doporuceni']);
        $this->dalsi_kurzy = new c_webform_cselect_multi("dalsi_kurzy","Další doporučené kurzy",1,"view_c_kurzy",@$navazujici_kurzy);
    }
    
    public function Display() {
        parent::Display('top');
        $this->kurz_kod->Display();
        echo "<BR><BR>\n";
        $this->kurz_Nazev->Display();
        echo "<BR><BR>\n";
        $this->druh_kurzu_kod->Display();
        echo "<BR><BR>\n";
        $this->modul_kurzu_kod->Display();
        echo "<BR><BR>\n";
        $this->kurz_Anotace->Display();
        echo "<BR><BR>\n";
        $this->kurz_Cile->Display();
        echo "<BR><BR>\n";
        $this->kurz_Obsah->Display();
        echo "<BR><BR>\n";
        $this->pocet_ucastniku_min->Display();
        //echo "<BR><BR>\n";
        $this->pocet_ucastniku_max->Display();
        echo "<BR><BR>\n";
        $this->delka_hodin->Display();
        echo "<BR><BR>\n";
        $this->delka_detail->Display();
        echo "<BR><BR>\n";
        $this->delka_prubeh->Display();
        echo "<BR><BR>\n";
        $this->cena->Display();
        echo "<BR><BR>\n";
        $this->cena_detail->Display();
        echo "<BR><BR>\n";
        $this->cilova_skupina->Display();
        echo "<BR><BR>\n";
        $this->kurz_Vstupni_znalosti->Display();
        echo "<BR><BR>\n";
        $this->kurz_Doporuceni->Display();
        echo "<BR><BR>\n";
        $this->dalsi_kurzy->Display();
        echo "<BR><BR><BR><BR>\n";
        parent::Display('bottom');
    }
    public function Get_errors() {
        $this->error_bool=false;
        //test vnitřních hodnot
        if( $this->kurz_kod->Get_errors() OR
            $this->kurz_Nazev->Get_errors() OR
            $this->druh_kurzu_kod->Get_errors() OR
            $this->modul_kurzu_kod->Get_errors() OR
            $this->kurz_Anotace->Get_errors() OR
            $this->kurz_Cile->Get_errors() OR
            $this->kurz_Obsah->Get_errors() OR
            $this->pocet_ucastniku_min->Get_errors() OR
            $this->pocet_ucastniku_max->Get_errors() OR
            $this->delka_hodin->Get_errors() OR
            $this->delka_detail->Get_errors() OR
            $this->delka_prubeh->Get_errors() OR
            $this->cena->Get_errors() OR
            $this->cena_detail->Get_errors() OR
            $this->cilova_skupina->Get_errors() OR
            $this->kurz_Vstupni_znalosti->Get_errors() OR
            $this->dalsi_kurzy->Get_errors() OR
            $this->kurz_Doporuceni->Get_errors())
        {
            $this->error_bool=true;
        }
        // kontrola počtu osob
        if($this->pocet_ucastniku_max->get_value() < $this->pocet_ucastniku_min->get_value() ) {
            $this->error_bool=true;
            $this->pocet_ucastniku_max->set_error("Minimální počet účastníků musí být menší než maximální počet účastníků");
        }
        
        if($this->error_bool) {
            return true;
        }
        else {
            return false;
        }
    }
    
    public function Save() {
        //print_r($_POST);
        if(!$this->error_bool) {
            if(!$this->kurz_id) {
                $sql_query = 'INSERT INTO popis_kurzu (
                                                kurz_Nazev,
                                                kurz_Anotace,
                                                kurz_Cile,
                                                kurz_Obsah,
                                                kurz_Vstupni_znalosti,
                                                kurz_Doporuceni)
                                    VALUES(
                                            '.$this->mdb2->quote($this->kurz_Nazev->Get_value(),"text").',
                                            '.$this->mdb2->quote($this->kurz_Anotace->Get_value(),"text").',
                                            '.$this->mdb2->quote($this->kurz_Cile->Get_value(),"text").',
                                            '.$this->mdb2->quote($this->kurz_Obsah->Get_value(),"text").',
                                            '.$this->mdb2->quote($this->kurz_Vstupni_znalosti->Get_value(),"text").',
                                            '.$this->mdb2->quote($this->kurz_Doporuceni->Get_value(),"text").'
                                            )';
                //echo $sql_query;                            
                $res=$this->mdb2->exec($sql_query);
                $id_popis_kurzu = $this->mdb2->lastInsertID();
                $sql_query = 'INSERT INTO kurz (
                                            id_c_druh_kurzu,
                                            id_c_modul_kurzu,
                                            id_popis_kurzu,
                                            kod_kurzu,
                                            pocet_ucastniku_min,
                                            pocet_ucastniku_max,
                                            id_c_delka_prubeh,
                                            delka_hodin,
                                            delka_detail,
                                            cena,
                                            cena_detail)
                                    VALUES (
                                        (SELECT id_c_druh_kurzu FROM c_druh_kurzu WHERE kod = '.$this->mdb2->quote($this->druh_kurzu_kod->Get_value(),"text").'),
                                        (SELECT id_c_modul_kurzu FROM c_modul_kurzu WHERE kod = '.$this->mdb2->quote($this->modul_kurzu_kod->Get_value(),"text").'),
                                        '.$this->mdb2->quote($id_popis_kurzu,"integer").',
                                        '.$this->mdb2->quote($this->kurz_kod->Get_value()).',
                                        '.$this->mdb2->quote($this->pocet_ucastniku_min->Get_value(),"integer").',
                                        '.$this->mdb2->quote($this->pocet_ucastniku_max->Get_value(),"integer").',
                                        (SELECT id_c_delka_prubeh FROM c_delka_prubeh WHERE kod = '.$this->mdb2->quote($this->delka_prubeh->Get_value(),"text").'),
                                        '.$this->mdb2->quote($this->delka_hodin->Get_value(),"integer").',
                                        '.$this->mdb2->quote($this->delka_detail->Get_value(),"text").',
                                        '.$this->mdb2->quote($this->cena->Get_value(),"text").',
                                        '.$this->mdb2->quote($this->cena_detail->Get_value(),"text").');';
            
                //print_r( $sql_query);
                $res=$this->mdb2->exec($sql_query);
                $this->kurz_id = $this->mdb2->lastInsertID();
            }
            else {
                $sql_query = 'UPDATE popis_kurzu
                                SET
                                    kurz_Nazev = '.$this->mdb2->quote($this->kurz_Nazev->Get_value(),"text").',
                                    kurz_Anotace = '.$this->mdb2->quote($this->kurz_Anotace->Get_value(),"text").',
                                    kurz_Cile = '.$this->mdb2->quote($this->kurz_Cile->Get_value(),"text").',
                                    kurz_Obsah = '.$this->mdb2->quote($this->kurz_Obsah->Get_value(),"text").',
                                    kurz_Vstupni_znalosti = '.$this->mdb2->quote($this->kurz_Vstupni_znalosti->Get_value(),"text").',
                                    kurz_Doporuceni = '.$this->mdb2->quote($this->kurz_Doporuceni->Get_value(),"text").'
                                WHERE id_popis_kurzu = (SELECT id_popis_kurzu FROM kurz WHERE id_kurz ="'.$this->kurz_id.'");';
                $res=$this->mdb2->exec($sql_query);

                $sql_query = 'UPDATE kurz
                                SET
                                    id_c_druh_kurzu = (SELECT id_c_druh_kurzu FROM c_druh_kurzu WHERE kod = '.$this->mdb2->quote($this->druh_kurzu_kod->Get_value(),"text").'),
                                    id_c_modul_kurzu = (SELECT id_c_modul_kurzu FROM c_modul_kurzu WHERE kod = '.$this->mdb2->quote($this->modul_kurzu_kod->Get_value(),"text").'),
                                    kod_kurzu = '.$this->mdb2->quote($this->kurz_kod->Get_value()).',
                                    pocet_ucastniku_min = '.$this->mdb2->quote($this->pocet_ucastniku_min->Get_value(),"integer").',
                                    pocet_ucastniku_max = '.$this->mdb2->quote($this->pocet_ucastniku_max->Get_value(),"integer").',
                                    id_c_delka_prubeh = (SELECT id_c_delka_prubeh FROM c_delka_prubeh WHERE kod = '.$this->mdb2->quote($this->delka_prubeh->Get_value(),"text").'),
                                    delka_hodin = '.$this->mdb2->quote($this->delka_hodin->Get_value(),"integer").',
                                    delka_detail = '.$this->mdb2->quote($this->delka_detail->Get_value(),"text").',
                                    cena = '.$this->mdb2->quote($this->cena->Get_value(),"text").',
                                    cena_detail = '.$this->mdb2->quote($this->cena_detail->Get_value(),"text").';';
                //print_r($sql_query);
                $res=$this->mdb2->exec($sql_query);
                echo "Updatuji";
            }
                
            //
            
            
            $sql_query ='DELETE FROM sp_kurz_cilova_skupina 
                                WHERE id_kurz ='.$this->kurz_id.' 
                                  AND id_c_cilova_skupina NOT IN (SELECT id_c_cilova_skupina
                                                                FROM c_cilova_skupina
                                                                WHERE kod IN ("'.implode('","',$this->cilova_skupina->Get_value()).'"))';
                                
            $res=$this->mdb2->exec($sql_query);
            //print_r($sql_query);
            if( count($this->cilova_skupina->Get_value())) {
                $sql_query = 'INSERT INTO sp_kurz_cilova_skupina (
                                                                    id_kurz,
                                                                    id_c_cilova_skupina)
                                                          SELECT
                                                                '.$this->kurz_id.' as id_kurz,
                                                                id_c_cilova_skupina
                                                            FROM c_cilova_skupina
                                                            WHERE id_c_cilova_skupina IN (SELECT id_c_cilova_skupina FROM c_cilova_skupina WHERE kod IN ("'.implode('","',$this->cilova_skupina->Get_value()).'")) 
                                                            AND id_c_cilova_skupina NOT IN (SELECT id_c_cilova_skupina FROM sp_kurz_cilova_skupina WHERE id_kurz='.$this->kurz_id.');';
                //print_r($sql_query);
                $res=$this->mdb2->exec($sql_query);
            }
            
            $sql_query ='DELETE FROM navazujici_kurzy 
                                WHERE id_kurz ='.$this->kurz_id.' 
                                  AND id_kurz_navazujici NOT IN ("'.implode('","',$this->dalsi_kurzy->Get_value()).'");';
                                
            $res=$this->mdb2->exec($sql_query);
            if ( count($this->dalsi_kurzy->Get_value())) {
                $sql_query = 'INSERT INTO navazujici_kurzy (
                                                                    id_kurz,
                                                                    id_kurz_navazujici)
                                                          SELECT
                                                                '.$this->kurz_id.' as id_kurz,
                                                                id_kurz as id_kurz_navazujici
                                                            FROM kurz
                                                            WHERE id_kurz IN ('.implode(',',$this->dalsi_kurzy->Get_value()).')
                                                            AND id_kurz NOT IN (SELECT id_kurz_navazujici FROM navazujici_kurzy WHERE id_kurz='.$this->kurz_id.');';
                                                          
                
                $res=$this->mdb2->exec($sql_query);
            }
            
        }
    }
    public function Load($kurz_id) {
        $this->kurz_id = $kurz_id;
        $sql_query = 'SELECT
                            (SELECT kod FROM c_druh_kurzu WHERE c_druh_kurzu.id_c_druh_kurzu = kurz.id_c_druh_kurzu) AS druh_kurzu,
                            (SELECT kod FROM c_modul_kurzu WHERE c_modul_kurzu.id_c_modul_kurzu = kurz.id_c_modul_kurzu) AS modul_kurzu,
                            id_popis_kurzu,
                            kod_kurzu,
                            pocet_ucastniku_min,
                            pocet_ucastniku_max,
                            delka_hodin,
                            delka_detail,
                            cena,
                            cena_detail
                        FROM kurz
                        WHERE id_kurz = '.$this->kurz_id.';';
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">

<HTML>
<HEAD>
	<TITLE>Testovací rozhraní formuláře</TITLE>
	<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#0000FF" VLINK="#800080" ALINK="#FF0000">
    <?php
        $kurz = new f_katalog_kurzu_kurz('katalog_kurzu','test.php',13);
        if(isset ($_POST) AND count($_POST)>1){
            if($kurz->Get_errors()){
                $kurz->Display();
            }
            else {
                $kurz->Save();
                echo "Je to OK";
            }
        }
        else {
            $kurz->Display();
        }
    ?>
</BODY>