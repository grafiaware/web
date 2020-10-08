<?php
use Pes\Session\SessionStatusHandlerInterface;

use Pes\Utils\Directory;

/**
 * Kontejner na globalni proměnné aplikace
 * @author Petr Svoboda
 */

class AppContext {

    private static $sessionHandler;

########### PATH A DIRECTORY ##############################################
    public static function workingPath() {
        return Directory::workingPath();
    }

    public static function rootRelativePath() {
        return Directory::rootRelativePath();
    }

}