<?php
declare(strict_types=1);

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use PHPUnit\Framework\TestCase;

use Pes\Http\Factory\EnvironmentFactory;

use Application\WebAppFactory;

use Pes\Container\Container;
use Container\WebContainerConfigurator;
use Container\HierarchyContainerConfigurator;
use Model\Dao\Hierarchy\NodeAggregateReadonlyDao;
use Model\Repository\MenuItemAggregateRepo;

use Model\Entity\MenuItemPaperAggregate;
use Model\Entity\PaperPaperContentsAggregate;
/**
 * Description of MenuItemPaperRepositoryTest
 *
 * @author pes2704
 */
class MenuItemAggregateRepositoryTest extends TestCase {

    private static $inputStream;

    private $app;
    private $container;

    /**
     *
     * @var MenuItemAggregateRepo
     */
    private $repo;

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
            'HTTP_USER_AGENT'      => 'Slim Framework',
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

            define('PROJECT_DIR', 'c:/ApacheRoot/www_grafia_development_v0_6/');

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

        $this->container = (new HierarchyContainerConfigurator())->configure(
                    new Container(
                        (new WebContainerConfigurator())->configure(
                                new Container($this->app->getAppContainer())
                        )
                    )
                );

        $this->repo = $this->container->get(MenuItemAggregateRepo::class);

        /** @var ReadHierarchy $hierarchy */
        $hierarchy = $this->container->get(NodeAggregateReadonlyDao::class);
        $this->langCode = 'cs';
        $this->title = 'VZDĚLÁVÁNÍ';
        $node = $hierarchy->getByTitleHelper($this->langCode, $this->title, false, false);
        //  node.uid, (COUNT(parent.uid) - 1) AS depth, node.left_node, node.right_node, node.parent_uid
        $this->uid = $node['uid'];
    }

    public function testSetUp() {
        $this->assertIsString($this->langCode);
        $this->assertIsString($this->uid);
        $this->assertInstanceOf(MenuItemAggregateRepo::class, $this->repo);
    }

    public function testRepoGet() {
        $entity = $this->repo->get($this->langCode, $this->uid);
        $this->assertInstanceOf(MenuItemPaperAggregate::class, $entity);
        $this->assertEquals($this->title, $entity->getTitle());
        /** @var PaperPaperContentsAggregate $paper */      // není interface
        $paper = $entity->getPaper();
        $this->assertInstanceOf(PaperPaperContentsAggregate::class, $paper);
        $contents = $paper->getPaperContentsArray();
        $this->assertIsArray($contents);
        $this->assertTrue(count($contents)>0, "Nenalezen žádný obsah");

    }

    public function testRepoFindByPaperFulltextSearch() {
        $items = $this->repo->findByPaperFulltextSearch($this->langCode, "Grafia", false, false);
        $this->assertIsArray($items);
        $this->assertTrue(count($items) > 0, 'Nebyl nalezen alespoň jeden výskyt řetězce.');
    }

}
