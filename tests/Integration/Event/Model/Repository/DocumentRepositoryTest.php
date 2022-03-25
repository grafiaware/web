<?php
declare(strict_types=1);

namespace Test\Integration\Repository;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Test\AppRunner\AppRunner;
use Pes\Container\Container;

//use Container\DbUpgradeContainerConfigurator;
use Container\HierarchyContainerConfigurator;
//use Test\Integration\Model\Container\TestModelContainerConfigurator;

use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;


use Events\Model\Entity\Document;
use Events\Model\Dao\DocumentDao;
use Events\Model\Repository\DocumentRepo;

use Model\RowData\RowData;


/**
 *
 * @author pes2704
 */
class DocumentRepositoryTest extends AppRunner {


    private $container;

    /**
     *
     * @var VisitorProfileRepo
     */
    private $DocumentRepo;

    private static $idCv;
    private static $idLetter;

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
    }

    private static function insertRecord(Container $container) {
        /** @var DocumentDao $documentDao */
        $documentDao = $container->get(DocumentDao::class);
        $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension

        $cvFilename = "CV.doc";
        $letterFilename = "DOPIS.doc";

        $cvFilepathName = __DIR__."/".$cvFilename;
        $cvMime = finfo_file($finfo, $cvFilepathName);
        $cvContent = file_get_contents($cvFilepathName);

        $letterFilepathName = __DIR__."/".$letterFilename;
        $letterMime = finfo_file($finfo, $letterFilepathName);
        $letterContent = file_get_contents($letterFilepathName);

        $rowData = new RowData([
            'document' => $cvContent,
            'document_filename' => $cvFilepathName,
            'document_mimetype' => $cvMime,
        ]);
        $documentDao->insert($rowData);
        self::$idCv = (int) $documentDao->getLastInsertedId();
        $rowData = new RowData([
            'document' => $letterContent,
            'document_filename' => $letterFilepathName,
            'document_mimetype' => $letterMime,
        ]);
        $documentDao->insert($rowData);
        self::$idLetter = (int) $documentDao->getLastInsertedId();
    }

    private static function deleteRecords(Container $container) {
        /** @var VisitorProfileDao $visitorDataDao */
        $visitorDataDao = $container->get(VisitorProfileDao::class);

        $cvFilename = "CV.doc";
        $letterFilename = "DOPIS.doc";

        $rows = $visitorDataDao->find("document_filename LIKE '%$cvFilename'", []);
        foreach($rows as $row) {
            $visitorDataDao->delete($row);
        }
        $rows = $visitorDataDao->find("document_filename LIKE '%$letterFilename'", []);
        foreach($rows as $row) {
            $visitorDataDao->delete($row);
        }
    }

    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->DocumentRepo = $this->container->get(VisitorProfileRepo::class);
    }

    protected function tearDown(): void {
        $this->DocumentRepo->flush();
    }

    public static function tearDownAfterClass(): void {
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );

        self::deleteRecords($container);
    }

    public function testSetUp() {
        $this->assertInstanceOf(DocumentRepo::class, $this->DocumentRepo);
    }

    public function testGetNonExisted() {
        $document = $this->DocumentRepo->get(-1);
        $this->assertNull($document);
    }

    public function testGetAfterSetup() {
        $visitorProfile = $this->DocumentRepo->get(self::$idCv);    // !!!! jenom po insertu v setUp - hodnotu vrací dao
        $this->assertInstanceOf(VisitorProfile::class, $visitorProfile);
    }

    public function testAdd() {
        $document = new Document();
        $cvFilename = "CV.doc";
        $letterFilename = "DOPIS.doc";

        $filepathName = __DIR__."/".$cvFilename;
        $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
        $mime = finfo_file($finfo, $filepathName);
        $content = file_get_contents($filepathName);
        $document->setDocument($content);
        $document->setDocumentMimetype($mime);
        $document->setDocumentFilename($filepathName);

        $this->DocumentRepo->add($document);
        $this->assertTrue($document->isLocked());
        self::$visitorProfileAdded = $document;

//        $cvFinfo = new \SplFileInfo($cvFilepathName);
//        $file = $cvFinfo->openFile();
    }

    public function testGetAfterAdd() {
        $visitorProfile = $this->DocumentRepo->get(self::$loginNameAdded);
        $this->assertInstanceOf(VisitorProfile::class, $visitorProfile);
    }

    public function testAddAndReread() {
        $loginName = self::insertLoginRecord($this->container);

        $visitorProfile = new VisitorProfile();
        $visitorProfile->setLoginLoginName($loginName);
        $visitorProfile->setPrefix("Trdlo.");
        $visitorProfile->setName("Julián");
        $visitorProfile->setSurname("Bublifuk");
        $this->DocumentRepo->add($visitorProfile);
        $this->DocumentRepo->flush();
        $visitorProfileRereaded = $this->DocumentRepo->get($loginName);
        $this->assertInstanceOf(VisitorProfile::class, $visitorProfileRereaded);
        $this->assertTrue($visitorProfileRereaded->isPersisted());
    }

    public function testFindAll() {
        $visitorProfile = $this->DocumentRepo->findAll();
        $this->assertTrue(is_array($visitorProfile));
    }

    public function testFind() {
        $prefix = self::$loginNamePrefix;
        $visitorsProfile = $this->DocumentRepo->find("login_login_name LIKE '$prefix%'", []);
        $this->assertTrue(is_array($visitorsProfile));
    }

    public function testRemove() {
        $visitorProfile = $this->DocumentRepo->get(self::$idCv);    // !!!! jenom po insertu v setUp - hodnotu vrací dao
        $this->assertInstanceOf(VisitorProfile::class, $visitorProfile);
        $this->DocumentRepo->remove($visitorProfile);
        $this->assertFalse($visitorProfile->isPersisted());
        $this->assertTrue($visitorProfile->isLocked());   // maže až při flush
        $this->DocumentRepo->flush();
        $this->assertFalse($visitorProfile->isLocked());
        // pokus o čtení
        $visitorProfile = $this->DocumentRepo->get(self::$idCv);
        $this->assertNull($visitorProfile);
    }

}
