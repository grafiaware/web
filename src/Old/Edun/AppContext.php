<?php
/**
 * Kontejner na globalni proměnné aplikace Rs
 * @author Petr Svoboda
 */
namespace Middleware\Edun;

use PDO;

 class AppContext {

############ CESTY KE SLOŽKÁM #############################

    public static function getPublicDirectory() {
        return \AppContext::rootRelativePath().'public/common/';  // relativní cesta vzhledem ke kořenovému adresáři skriptu
    }

    public static function getAppPublicDirectory() {
        return \AppContext::rootRelativePath().'public/edun/';  // relativní cesta vzhledem ke kořenovému adresáři skriptu
    }

    public static function getScriptsDirectory() {
        return \AppContext::workingPath()."Middleware/Edun/";  // absolutní cesta vzhledem ke kořenovému adresáři skriptu (index.php)
    }

############# DATABÁZE #############
    const DEFAULT_DB_NICK = 'grafia';
    const PRODUCTION_MACHINE_HOST_NAME = 'xxxxx';

    /**
     * Informuje, zda skript běží na produkčním stroji.
     * @return boolean
     */
    private static function isRunningOnProductionMachine() {
        if (strpos(strtolower(gethostname()), self::PRODUCTION_MACHINE_HOST_NAME)===0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * @var Framework_Database_HandlerSqlInterface
     */
    private static $db = array();

    /**
     * Metoda vrací objekt pro přístup k databázi. Metoda se podle označení databáze (nick) zadaném jako prametr rozhoduje,
     * který objekt pro přístup k databázi vytvoří. Ke každé databázi vytváří jednu instanci objektu.
     * @param string $nick Označení databáze používané v tomto projektu. Jedná se o označení používané v rámci aplikace, nikoli o skutečný název
     * databáze v databázovém stroji.
     * @return \PDO
     * @throws UnexpectedValueException
     */
    public static function getDb($nick = self::DEFAULT_DB_NICK) {
        switch ($nick) {
            case self::DEFAULT_DB_NICK:
                if(!isset(self::$db[self::DEFAULT_DB_NICK])) {
                    if (PES_PRODUCTION) {
                        assert(TRUE, 'Není implementováno vytvoření dbh na produkčním stroji.');
                    } else {
                        $dsn = 'mysql:host=' . "localhost" . ';dbname=' . "grafiacz" ;
                        $dbh = new PDO($dsn, "root", "spravce", array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
                    }
                    self::$db['grafia'] = $dbh;
                } else {
                    $dbh = self::$db[self::DEFAULT_DB_NICK];
                }

                break;

            default:
                throw new UnexpectedValueException('Neznámy název databáze '.$nick.'.');
        }
        return $dbh;
    }

    /**
     * Vrací defaulní označení (nick) databáze. Jedná se o označení používané v rámci aplikace, nikoli o skutečný název
     * databáze v databázovém stroji.
     * @return string
     */
    public static function getDefaultDatabaseNick() {
        return self::DEFAULT_DB_NICK;
    }
}