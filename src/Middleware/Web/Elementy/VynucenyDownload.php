<?php

/**
 * Třída má jednu static metodu download. Ta slouží k vynucenému stahování souborů prostřednictvím
 * dialogu klienta (prohlížeče). 
 *
 * @author vlse2610
 */
class Elementy_VynucenyDownload {
    /**
     * Metoda slouží k vynucenému stahování souborů prostřednictvím dialogu klienta (prohlížeče). 
     * Metoda odešle http response a skončí běh php skriptu. Response obsahuje hlavičky pro vynucení downloadu a v těle obsah stahovaného souboru.
     * Použití: odkaz vedoucí na zavolání této metody umístěte do HTML stránky. Po kliknutí na takový odkaz
     * se musí zavolat metoda download, ta odešle http response pro vynucení downloadu a v těle response obsah souboru.
     * Uživatelův prohlížeč nabídne uživateli dialog pro stažení nebo otevření souboru a obsah stránky předtím zobrazené v prohlížeči
     * se nijak nezmění. 
     * @param string $souborProDownload Relativní adresa souboru
     */    
    public static function download($souborProDownload)
                
        { 
            if (file_exists($souborProDownload)) {
                header('Content-Description: File Transfer');
                //header("Content-type: application/force-download"); 
                //header('Content-Type','text/html; charset=windows-1250');
                //header('Content-type: application/pdf');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($souborProDownload).'"');
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($souborProDownload));
                ob_clean();
                flush();
                readfile($souborProDownload);
                exit;
            }
        }
}
