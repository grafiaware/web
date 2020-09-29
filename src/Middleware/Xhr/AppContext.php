<?php
/**
 * Kontejner na globalni proměnné aplikace
 * @author Petr Svoboda
 */
namespace Middleware\Xhr;

use PDO;

class AppContext {
############ CESTY KE SLOŽKÁM #############################
    public static function getPublicDirectory() {
        return 'public/common/';  // relativní cesta
    }

    public static function getAppPublicDirectory() {
        return 'public/web/';  // relativní cesta
    }

    public static function getTinyPublicDirectory() {
        return 'public/tiny_templates/';  // relativní cesta
    }

    public static function getFilesDirectory() {
        return '/_www_gr_files/';  // relativní cesta vzhledem k DOCUMENT_ROOT (htdocs)
    }

    ##### TITLE ############
    public static function getWebTitle() {
        return "Grafia, s.r.o.";
    }

}