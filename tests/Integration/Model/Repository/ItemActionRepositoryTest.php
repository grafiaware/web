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
use Red\Model\Repository\ItemActionRepoInterface;
use Red\Model\Repository\ItemActionRepo;

use Red\Model\Entity\ItemAction;

/**
 * Description of MenuItemPaperRepositoryTest
 *
 * @author pes2704
 */
class ItemActionRepositoryTest extends TestCase {

    private static $inputStream;

    private $app;
    private $container;

    /**
     *
     * @var ItemActionRepoInterface
     */
    private $itemActionRepo;

    private $title;
    private $langCode;
    private $uid;


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


        $this->itemActionRepo = $this->container->get(ItemActionRepo::class);
    }

    public function testSetUp() {
        $this->assertInstanceOf(ItemActionRepoInterface::class, $this->itemActionRepo);
    }

    public function testGetNonExisted() {
        $entity = $this->itemActionRepo->get('ghgug', 111);
        $this->assertNull($entity);

    }

    public function testFindAll() {
        $allEntities = $this->itemActionRepo->findAll();
        $this->assertIsArray($allEntities);
        $this->assertTrue(count($allEntities) > 0);
    }

    public function testDeleteAll() {
        $allEntities = $this->itemActionRepo->findAll();
        foreach ($allEntities as $entity) {
            $this->itemActionRepo->remove($entity);
        }
        $this->itemActionRepo->flush();
        $allEntities = $this->itemActionRepo->findAll();
        $this->assertIsArray($allEntities);
        $this->assertTrue(count($allEntities) == 0);
    }

    public function testSaveNew() {
        $itemAction = new ItemAction();
        $itemAction->setTypeFk('paper');
        $itemAction->setItemId('111');
        $itemAction->setEditorLoginName('editoří jméno');
        $this->itemActionRepo->add($itemAction);
        $itemAction = new ItemAction();
        $itemAction->setTypeFk('article');
        $itemAction->setItemId('222');
        $itemAction->setEditorLoginName('pan Chytrý');
        $this->itemActionRepo->add($itemAction);

        $this->itemActionRepo->flush();
        $allEntities = $this->itemActionRepo->findAll();
        $this->assertTrue(count($allEntities) == 2);

    }

    public function testGet() {
        $entity = $this->itemActionRepo->get('paper', 111);
        $this->assertInstanceOf(ItemAction::class, $entity);
    }

}