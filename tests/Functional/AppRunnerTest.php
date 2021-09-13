<?php
declare(strict_types=1);
namespace Test\Functional;

use Test\AppRunner\AppRunner;

use Pes\Http\Factory\EnvironmentFactory;

use Application\WebAppFactory;
use Application\SelectorItems;
use Pes\Middleware\Selector;
use Pes\Container\Container;

use Pes\Middleware\NoMatchSelectorItemRequestHandler;
use Pes\Http\ResponseSender;

use Container\AppContainerConfigurator;

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
class AppRunnerTest extends AppRunner {

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
    }
    protected function setUp(): void {
        $this->container =
                       (new AppContainerConfigurator())->configure(new Container());
    }
    /**
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
     */
    public function testRun() {
        $app = (new WebAppFactory())->createFromEnvironment(self::createEnvironment());
        $selector = new Selector();
        (new SelectorItems($app))->addItems($selector);

        $response = $app->run($selector, new NoMatchSelectorItemRequestHandler());
        $this->assertInstanceOf(\Pes\Http\Response::class, $response);
        $size = $response->getBody()->getSize();
        $this->assertGreaterThan(1000000, 0, "málo");
    }
}
