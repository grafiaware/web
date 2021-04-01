<?php
declare(strict_types=1);
namespace Test\Integration\Repository;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use PHPUnit\Framework\TestCase;

use Pes\Container\Container;

use Container\DbUpgradeContainerConfigurator;
use Container\HierarchyContainerConfigurator;
use Test\Integration\Model\Container\TestModelContainerConfigurator;

use Model\Dao\VisitorDataDao;
use Model\Dao\Exception\DaoKeyVerificationFailedException;
use Model\Repository\VisitorDataRepo;

use Model\Entity\VisitorData;


/**
 *
 * @author pes2704
 */
class VisitorDataRepositoryTest extends TestCase {


    private $container;

    /**
     *
     * @var VisitorDataRepo
     */
    private $visitorDataRepo;

    private static $loginName;

    public static function setUpBeforeClass(): void {
        if ( !defined('PES_DEVELOPMENT') AND !defined('PES_PRODUCTION') ) {
            define('PES_FORCE_DEVELOPMENT', 'force_development');
            //// nebo
            //define('PES_FORCE_PRODUCTION', 'force_production');

            define('PROJECT_PATH', 'c:/ApacheRoot/web/');

            include '../vendor/pes/pes/src/Bootstrap/Bootstrap.php';
        }

        $container =
                (new TestModelContainerConfigurator())->configure(  // přepisuje ContextFactory
                    (new HierarchyContainerConfigurator())->configure(
                       (new DbUpgradeContainerConfigurator())->configure(new Container())
                    )
                );

        // mazání - zde jen pro případ, že minulý test nebyl dokončen
        self::deleteRecords($container);

        // toto je příprava testu
        /** @var VisitorDataDao $visitirDataDao */
        $visitirDataDao = $container->get(VisitorDataDao::class);
        $loginName = "testVisitorData login name" . (string) (1000+random_int(0, 999));
        $visitirDataDao->insertWithKeyVerification([
            'login_name' => $loginName,
            'name' => "Name" . (string) (1000+random_int(0, 999)),
            'surname' => "Name" . (string) (1000+random_int(0, 999)),
            'mail' => "mail" . (string) (1000+random_int(0, 999)).'@ztrewzqtrwzeq.cc',
            'phone' => (string) (1000+random_int(0, 999)).' '.(string) (1000+random_int(0, 999)).' '.(string) (1000+random_int(0, 999))
        ]);
        self::$loginName = $loginName;
        for($i=1; $i<=4; $i++) {
            try {
            $loginName = "testVisitorData login name" . (string) (1000+random_int(0, 999));

            $visitirDataDao->insertWithKeyVerification([
                'login_name' => $loginName,
                'name' => "Name" . (string) (1000+random_int(0, 999)),
                'surname' => "Name" . (string) (1000+random_int(0, 999)),
                'mail' => "mail" . (string) (1000+random_int(0, 999)).'@ztrewzqtrwzeq.cc',
                'phone' => (string) (1000+random_int(0, 999)).' '.(string) (1000+random_int(0, 999)).' '.(string) (1000+random_int(0, 999))
            ]);
            } catch (DaoKeyVerificationFailedException $e) {

            }
        }

    }

    private static function deleteRecords(Container $container) {
        /** @var VisitorDataDao $visitorDataDao */
        $visitorDataDao = $container->get(VisitorDataDao::class);
        $rows = $visitorDataDao->find("login_name LIKE 'testVisitorData%'", []);
        foreach($rows as $row) {
            $visitorDataDao->delete($row);
        }
    }

    protected function setUp(): void {
        $this->container =
                (new TestModelContainerConfigurator())->configure(  // přepisuje ContextFactory
                    (new HierarchyContainerConfigurator())->configure(
                       (new DbUpgradeContainerConfigurator())->configure(new Container())
                    )
                );
        $this->visitorDataRepo = $this->container->get(VisitorDataRepo::class);
    }

    protected function tearDown(): void {
        $this->visitorDataRepo->flush();
    }

    public static function tearDownAfterClass(): void {
        $container =
                (new TestModelContainerConfigurator())->configure(  // přepisuje ContextFactory
                    (new HierarchyContainerConfigurator())->configure(
                       (new DbUpgradeContainerConfigurator())->configure(new Container())
                    )
                );

        self::deleteRecords($container);
    }

    public function testSetUp() {
        $this->assertInstanceOf(VisitorDataRepo::class, $this->visitorDataRepo);
    }

    public function testGetNonExisted() {
        $visitorData = $this->visitorDataRepo->get('dlksdhfweuih');
        $this->assertNull($visitorData);
    }

    public function testGetAfterSetup() {
        $visitorData = $this->visitorDataRepo->get(self::$loginName);    // !!!! jenom po insertu v setUp - hodnotu vrací dao
        $this->assertInstanceOf(VisitorData::class, $visitorData);
    }

    public function testAdd() {
        $visitorData = new VisitorData();
        $visitorData->setLoginName("testVisitorData Add");
        $visitorData->setName("testVisitorData Add - name");
        $cvFilename = "CV.doc";
        $letterFilename = "DOPIS.doc";

        $cvFilepathName = __DIR__."/".$cvFilename;
        $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
        $mime = finfo_file($finfo, $cvFilepathName);
        $content = file_get_contents($cvFilepathName);
        $visitorData->setCvDocument($content);
        $visitorData->setCvDocumentMimetype($mime);
        $visitorData->setCvDocumentFilename($cvFilepathName);

        $letterFilepathName = __DIR__."/".$letterFilename;
        $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
        $mime = finfo_file($finfo, $letterFilepathName);
        $content = file_get_contents($letterFilepathName);
        $visitorData->setLetterDocument($content);
        $visitorData->setLetterDocumentMimetype($mime);
        $visitorData->setLetterDocumentFilename($letterFilepathName);

        $this->visitorDataRepo->add($visitorData);
        $this->assertTrue($visitorData->isPersisted());  // DaoKeyDbVerifiedInterface Dao - ukládá hned

//        $cvFinfo = new \SplFileInfo($cvFilepathName);
//        $file = $cvFinfo->openFile();
    }

    public function testFindAll() {
        $visitorData = $this->visitorDataRepo->findAll();
        $this->assertTrue(is_array($visitorData));
    }

    public function testFind() {
        $visitorsData = $this->visitorDataRepo->find("login_name LIKE 'testVisitorData%'", []);
        $this->assertTrue(is_array($visitorsData));
        $visitorsData = $this->visitorDataRepo->find("login_name LIKE 'testVisitorData Add%'", []);
        $this->assertTrue(is_array($visitorsData));
        $this->assertCount(1, $visitorsData);
    }

    public function testRemove() {
        $visitorData = $this->visitorDataRepo->get(self::$loginName);    // !!!! jenom po insertu v setUp - hodnotu vrací dao
        $this->assertInstanceOf(VisitorData::class, $visitorData);
        $this->visitorDataRepo->remove($visitorData);
        $this->assertFalse($visitorData->isPersisted());
        $this->assertTrue($visitorData->isLocked());   // maže až při flush
    }

    public function testGetAfterRemove() {
        $visitorData = $this->visitorDataRepo->get(self::$loginName);
        $this->assertNull($visitorData);
    }
}
