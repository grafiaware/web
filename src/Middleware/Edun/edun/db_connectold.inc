<?php
require_once '../../../pear/MDB2.php'; //knihovna pro obj. přístup k databázi

class web_db {
    private static $dbh = false;
    private static $dsn;
    private static $dsn1 = array(
             'phptype'  => 'mysql',
             'dbsyntax' => false,
             'username' => 'grafiacz001',    //'root',     //'grafiacz001',
             'password' => 'RACrzrja',       //'spravce',        //'RACrzrja',
             'protocol' => false,
             'hostspec' => '127.0.0.1',
             'port'     => false,
             'socket'   => false,
             'database' => 'grafiacz',       //'grafiauni',   //'grafiacz'
             'new_link' => false,
             'service'  => false,
            );
    private static $dsn2 = array(
             'phptype'  => 'mysql',
             'dbsyntax' => false,
             'username' => 'grafiadb',
             'password' => 'Hj89jKnK0',
             'protocol' => false,
             'hostspec' => 'posta.grafia.cz',
             'port'     => false,
             'socket'   => false,
             'database' => 'web_db',
             'new_link' => false,
             'service'  => false,
            );
    private static $dsn3 = array(
             'phptype'  => 'mysql',
             'dbsyntax' => false,
             'username' => 'grafiadb',
             'password' => 'Hj89jKnK0',
             'protocol' => false,
             'hostspec' => 'localhost',
             'port'     => false,
             'socket'   => false,
             'database' => 'web_db',
             'new_link' => false,
             'service'  => false,
            );
    private static $dsn4 = array(
             'phptype'  => 'mysql',
             'dbsyntax' => false,
             'username' => 'grafiacz001',     //'root',   //'grafiacz001',
             'password' => 'RACrzrja',        //  'spravce', //'RACrzrja',
             'protocol' => false,
             'hostspec' => false,
             'port'     => false,
             'socket'   => false,
             'database' => 'grafiacz',       //'grafiauni',      //'grafiacz',
             'new_link' => false,
             'service'  => false,
            );
    
    private static $dsn_list = array('dsn1');
    private static $options = array(
             'debug'         => 2, //Úroveň vypisování informací
             'result_buffering'   => true,
            );
    

public static function get_dbh($user=false) {
        if(self::$dbh) {
            return self::$dbh;
        }
        else { //Vytvoření pripojení k databázi
            foreach(self::$dsn_list as $dsn) {
                if(self::$options['debug']>2){
                    $current_dsn = self::$$dsn;
                    echo " Try to connect to:".$current_dsn['hostspec']."\n";
                }
                self::$dbh =& MDB2::connect(self::$$dsn,self::$options);
                if(!PEAR::isError(self::$dbh)) {
                    //Nastavení kodové stránky klienta
                    if(self::$options['debug']>3){
                        echo " Setting client code page"."\n";
                    }
                    self::$dbh->exec("set names utf8");
                    if(self::$options['debug']>3){
                        echo " Setting current user variable"."\n";
                    }
                    if($user) {
                        self::$dbh->exec('SET @user="'.$user.'"');
                        
                    }
                    return self::$dbh;
                }
                elseif(self::$options['debug']>2) {
                    print_r(self::$dbh);
                    echo "Chyba:".self::$dbh->getMessage()."\n";
                }
                       
            }
            die (self::$dbh->getMessage());
        }
            

    }
}
//
//$dbh = Middleware\Edun\AppContext::getDb();
//print_r($dbh);
?>
