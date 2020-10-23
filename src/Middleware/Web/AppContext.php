<?php
/**
 * Kontejner na globalni proměnné aplikace
 * @author Petr Svoboda
 */
namespace Middleware\Web;

use PDO;

class AppContext {
############ CESTY KE SLOŽKÁM #############################
    public static function getPublicDirectory() {
        return 'public/common/';  // relativní cesta
    }

    public static function getAppPublicDirectory() {
        return 'public/site/';  // relativní cesta
    }

    public static function getAppSitePublicDirectory() {
        return 'public/web/site/';  // relativní cesta
    }

    public static function getTinyPublicDirectory() {
        return 'public/templates/author/';  // relativní cesta
    }

    public static function getScriptsDirectory() {
        return \AppContext::workingPath()."Middleware/Web/";  // absolutní cesta vzhledem ke kořenovému adresáři skriptu (index.php)
    }

    public static function getFilesDirectory() {
        return '/_www_gr_files/';  // relativní cesta vzhledem k DOCUMENT_ROOT (htdocs)
    }

    ##### TITLE ############
    public static function getWebTitle() {
        return "Grafia, s.r.o.";
    }

}