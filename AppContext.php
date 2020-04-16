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

############# TEXT FIRMA ##################################
    public static function getTextFirma() {
        return 'Grafia';
    }

    /**
     *
     * @param SessionStatusHandlerInterface $sessionHandler
     */
    public static function setSessionHandler(SessionStatusHandlerInterface $sessionHandler) {
        if (isset(static::$sessionHandler)) {
            throw new LogicException("Opakovaný pokus o nastavení session handleru aplikace.");
        }
        static::$sessionHandler = $sessionHandler;
    }

    /**
     * @return SessionStatusHandlerInterface
     */
    public static function getSessionHandler() : SessionStatusHandlerInterface {
        return static::$sessionHandler;
    }

}