<?php
declare(strict_types=1);
namespace Test\Integration\Repository;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use PHPUnit\Framework\TestCase;

use Pes\Http\Factory\EnvironmentFactory;

use Application\WebAppFactory;

use Pes\Container\Container;

use Container\DbUpgradeContainerConfigurator;
use Container\HierarchyContainerConfigurator;
use Test\Integration\Model\Container\TestModelContainerConfigurator;

use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;
use Red\Model\Repository\HierarchyJoinMenuItemRepo;

use Red\Model\Entity\HierarchyAggregateInterface;

/**
 * Description of MenuItemPaperRepositoryTest
 *
 * @author pes2704
 */
class HierarchyAggregateRepositoryTest extends TestCase {

    private static $inputStream;

    private $app;
    private $container;

    /**
     *
     * @var HierarchyJoinMenuItemRepo
     */
    private $hirerchyAggRepo;

    private $title;
    private $langCode;
    private $uid;
    private $id;
    private $prettyUri;


    public static function mock(array $userData = []) {
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

    public static function setUpBeforeClass(): void {
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

    protected function setUp(): void {

        $environment = $this->mock(
                ['HTTP_USER_AGENT'=>'AppRunner']

                );
        $this->app = (new WebAppFactory())->createFromEnvironment($environment);

        $this->container =
                (new TestModelContainerConfigurator())->configure(  // přepisuje ContextFactory
                    (new HierarchyContainerConfigurator())->configure(
                       (new DbUpgradeContainerConfigurator())->configure(
                            (new Container(
//                                        new Container($this->app->getAppContainer())
                                )
                            )
                        )
                    )
                );


        $this->hirerchyAggRepo = $this->container->get(HierarchyJoinMenuItemRepo::class);

        /** @var HierarchyAggregateReadonlyDao $hierarchyDao */
        $hierarchyDao = $this->container->get(HierarchyAggregateReadonlyDao::class);
        $this->langCode = 'cs';
        $this->title = 'Tests Integration';
        $node = $hierarchyDao->getByTitleHelper($this->langCode, $this->title);
        if (!isset($node)) {
            throw new \LogicException("Error in setUp: Nelze spouštět integrační testy - v databázi projektu není položka menu v jazyce '$this->langCode' s názvem '$this->title'");
        }
        //  node.uid, (COUNT(parent.uid) - 1) AS depth, node.left_node, node.right_node, node.parent_uid
        $this->uid = $node['uid'];
        $this->id = $node['id'];
    }

    public function testSetUp() {
        $this->assertIsString($this->langCode);
        $this->assertIsString($this->uid);
        $this->assertInstanceOf(HierarchyJoinMenuItemRepo::class, $this->hirerchyAggRepo);
    }

    public function testGet() {
        $entity = $this->hirerchyAggRepo->get($this->langCode, $this->uid);
        $this->assertInstanceOf(HierarchyAggregateInterface::class, $entity);
        $this->assertEquals($this->title, $entity->getMenuItem()->getTitle());
    }

    public function testUpdateChildMenuItem() {
        $entity = $this->hirerchyAggRepo->get($this->langCode, $this->uid);
        $this->assertInstanceOf(HierarchyAggregateInterface::class, $entity);
        $entity->getMenuItem()->setTitle('Tests Integration HierarchyAggregateRepositoryTest');
    }

    public function testAfterUpdateHierarchy() {
        $entity = $this->hirerchyAggRepo->get($this->langCode, $this->uid);
        $this->assertInstanceOf(HierarchyAggregateInterface::class, $entity);
        $this->assertStringStartsWith($this->title, $entity->getMenuItem()->getTitle());
    }
}
