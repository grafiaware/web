<?php
declare(strict_types=1);
namespace Test\Integration\Dao;

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


use Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;
use Model\Repository\MenuItemAggregateRepo;
use Model\Repository\PaperAggregateRepo;

use Model\Entity\MenuItemAggregatePaperInterface;

// pro contents repo
use Pes\Database\Handler\HandlerInterface;
use Model\Dao\PaperContentDao;
use Model\Hydrator\PaperContentHydrator;
use Model\Repository\PaperContentRepo;

use Model\Entity\PaperContentInterface;
use Model\Entity\PaperContent;
use Model\Entity\PaperAggregatePaperContentInterface;


/**
 * Description of PaperContentDaoTest
 *
 * @author pes2704
 */
class PaperContentManipulationTest extends TestCase {

    private static $inputStream;

    private $app;
    private $container;

    private $langCode;
    private $uid;

    /**
     *  @var MenuItemAggregateRepo
     */
    private $menuItemAggRepo;

    /**
     * @var MenuItemAggregatePaperInterface
     */
    private $menuItemAgg;
    private $paper;
    private $contents = [];

    private $contentRepoIsolated;

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


        $this->menuItemAggRepo = $this->container->get(MenuItemAggregateRepo::class);

        /** @var HierarchyAggregateReadonlyDao $hierarchy */
        $hierarchy = $this->container->get(HierarchyAggregateReadonlyDao::class);
        $this->langCode = 'cs';
        $this->title = 'Tests Integration';
        $node = $hierarchy->getByTitleHelper($this->langCode, $this->title);
        if (!isset($node)) {
            throw new \LogicException("Error in setUp: Nelze spouštět integrační testy - v databázi projektu není položka menu v jazyce '$this->langCode' s názvem '$this->title'");
        }

        //  node.uid, (COUNT(parent.uid) - 1) AS depth, node.left_node, node.right_node, node.parent_uid
        $this->uid = $node['uid'];

        $this->menuItemAgg = $this->menuItemAggRepo->get($this->langCode, $this->uid);
        $this->paper = $this->menuItemAgg->getPaper();
        if (!$this->paper instanceof PaperAggregateInterface) {
            throw new \LogicException("Error in setUp: Nelze spustit integrační test - v set\up() metodě nevznikl paper.");
        }
        ######

//            $paperContentDao = new PaperContentDao($this->container->get(HandlerInterface::class));
//
//            $paperContentHydrator = new PaperContentHydrator();
//            $this->contentRepoIsolated = new PaperContentRepo($paperContentDao, $paperContentHydrator);

    }

    public function testPaperHasContents() {
        $this->assertIsArray($this->paper->getPaperContentsArray());
    }

    public function test11Columns() {
        $this->assertInstanceOf(PaperContentInterface::class, $this->paper->getPaperContentsArray()[0]);
    }

    public function testInsert() {
        $oldContentsArray = $this->paper->getPaperContentsArray();
        $oldContent = $oldContentsArray[0];
        $oldContentCount = count($oldContentsArray);

        $paperIdFk = $this->paper->getId();
        $newContent = new PaperContent();
        $newContent->setContent("bflmpsvz ".($oldContentCount+1));
        $newContent->setPaperIdFk($paperIdFk);

        /** @var PaperContentRepo $paperContentRepo */
        $paperContentRepo = $this->container->get(PaperContentRepo::class);
        $paperContentRepo->add($newContent);

        $paperContentRepo->flush();  // unset nevyvolá zavolání destruktoru

        // reset odstraní repo - voláním container->get() pak vznikne nové repo
        // nestačí resetovat MenuItemAggregateRepo - to se sice vygeneruje znovu, ale v něm obsažené PaperAggregateRepo se zachová a použije
        // a obdobně PaperContentRepo obsažené v PaperAggregateRepo
        $this->container->reset(MenuItemAggregateRepo::class);
        $this->container->reset(PaperAggregateRepo::class);
        $this->container->reset(PaperContentRepo::class);
        /** @var MenuItemAggregateRepo $menuItemAggRepo */
        $this->menuItemAggRepo = $this->container->get(MenuItemAggregateRepo::class);
        $this->menuItemAgg = $this->menuItemAggRepo->get($this->langCode, $this->uid);
        $this->paper = $this->menuItemAgg->getPaper();
        $newContentsArray = $this->paper->getPaperContentsArray();

        $this->assertTrue(count($newContentsArray) == $oldContentCount+1, "Není o jeden obsah více po paper->exchangePaperContentsArray ");

        // tohle nefunguje!
//        $this->assertTrue(count($newContentsArray) == count($oldContentsArray)+1, "Není o jeden obsah více po paper->exchangePaperContentsArray ");

    }

//    public function testUpdate() {
//        $menuItemRow = $this->dao->get($this->langCode, $this->uid);
//        $oldActive = $menuItemRow['active'];
//        $this->assertIsInt($oldActive);
//        //
//        $this->setUp();
//        $menuItemRow['active'] = 1;
//        $this->dao->update($menuItemRow);
//        $this->setUp();
//        $menuItemRowRereaded = $this->dao->get($this->langCode, $this->uid);
//        $this->assertEquals($menuItemRow, $menuItemRowRereaded);
//        $this->assertEquals(1, $menuItemRowRereaded['active']);
//
//        $this->setUp();
//        $menuItemRow['active'] = 0;
//        $this->dao->update($menuItemRow);
//        $this->setUp();
//        $menuItemRowRereaded = $this->dao->get($this->langCode, $this->uid);
//        $this->assertEquals($menuItemRow, $menuItemRowRereaded);
//        $this->assertEquals(0, $menuItemRowRereaded['active']);
//
//        // vrácení původní hodnoty
//        $this->setUp();
//        $menuItemRow['active'] = $oldActive;
//        $this->dao->update($menuItemRow);
//        $this->setUp();
//        $this->dao = $this->container->get(MenuItemDao::class);
//        $menuItemRowRereaded = $this->dao->get($this->langCode, $this->uid);
//        $this->assertEquals($menuItemRow, $menuItemRowRereaded);
//        $this->assertEquals($oldActive, $menuItemRowRereaded['active']);
//
//    }
//
//    public function testDelete() {
//        $menuItemRow = $this->dao->get($this->langCode, $this->uid);
//        $this->expectException(\LogicException::class);
//        $this->dao->delete($menuItemRow);
//    }
}
