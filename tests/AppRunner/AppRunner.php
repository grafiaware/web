<?php
declare(strict_types=1);
namespace Test\AppRunner;

use PHPUnit\Framework\TestCase;

use Pes\Http\Factory\EnvironmentFactory;
use Pes\Http\Environment;

use Application\WebAppFactory;
use Application\SelectorItems;

use Pes\Middleware\NoMatchSelectorItemRequestHandler;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AppRunner
 *
 * @author pes2704
 */
class AppRunner extends TestCase {

    protected static $inputStream;

    public static function bootstrapBeforeClass(): void {
        if ( !defined('PES_DEVELOPMENT') AND !defined('PES_PRODUCTION') ) {
            define('PES_FORCE_DEVELOPMENT', 'force_development');
            //// nebo
            //define('PES_FORCE_PRODUCTION', 'force_production');

            define('PROJECT_PATH', 'c:/ApacheRoot/web/');

            include '../vendor/pes/pes/src/Bootstrap/Bootstrap.php';
        }

        // input stream je možné otevřít jen jednou
        self::$inputStream = fopen('php://temp', 'w+');  // php://temp will store its data in memory but will use a temporary file once the amount of data stored hits a predefined limit (the default is 2 MB). The location of this temporary file is determined in the same way as the sys_get_temp_dir() function.
    }

    public static function mockEnvironment(array $userData = []): Environment {
        //Validates if default protocol is HTTPS to set default port 443
        if ((isset($userData['HTTPS']) && $userData['HTTPS'] !== 'off') ||
            ((isset($userData['REQUEST_SCHEME']) && $userData['REQUEST_SCHEME'] === 'https'))) {
            $defscheme = 'https';
            $defport = 443;
        } else {
            $defscheme = 'http';
            $defport = 80;
        }

        $data = array_merge([
            'SERVER_PROTOCOL'      => 'HTTP/1.1',
            'REQUEST_METHOD'       => 'GET',
            'REQUEST_SCHEME'       => $defscheme,
            'SCRIPT_NAME'          => '',
            'REQUEST_URI'          => '',
            'QUERY_STRING'         => '',
            'SERVER_NAME'          => 'localhost',
            'SERVER_PORT'          => $defport,
            'HTTP_HOST'            => 'localhost',
            'HTTP_ACCEPT'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.8',
            'HTTP_ACCEPT_CHARSET'  => 'ISO-8859-1,utf-8;q=0.7,*;q=0.3',
            'HTTP_USER_AGENT'      => 'PES',
            'REMOTE_ADDR'          => '127.0.0.1',
            'REQUEST_TIME'         => time(),
            'REQUEST_TIME_FLOAT'   => microtime(true),
        ], $userData);

         return (new EnvironmentFactory())->create($data, self::$inputStream);
    }


    public static function createEnvironment() {
        return self::mockEnvironment(
                ['HTTP_USER_AGENT'=>'AppRunner']

                );
    }

    /**
     *  Příklad testu
     * ################
     *
     * PHPUnit v průběhu testů posílá znaky na stdout. Při volání funkcí v testu, které posílají hlavičky (header(), session_start() atd.)
     * Pak nastane chyba "headers already sent...". Při použití Session z PES frameworku chyba "nelze nastartovat session...".
     *
     * Je nutné použít anotaci @runInSeparateProcess, ta zajistí spuštění testu v samostatném procesu, který ještě nic
     * neposlal. Alternativou je v phpunit.xml konfiguraci přida atribut to tagu <phpunit>:
     * <phpunit
     *  processIsolation="true"
     *  ...
     * >
     * Pak jsou všechny testy spouštěny izolovaně, ale běží pomaleji.
     *
     * @runInSeparateProcess
     */
    public function PrikladTestu() {

        $app = (new WebAppFactory())->createFromEnvironment($this->createEnvironment());
        $selector = (new SelectorItems($app))->create();
        $response = $app->run($selector, new NoMatchSelectorItemRequestHandler());
        $this->assertInstanceOf(\Pes\Http\Response::class, $response);
    }
}
