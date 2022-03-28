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
    private static $idCvAdded;

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        self::insertRecords($container);
    }

    private static function insertRecords(Container $container) {
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
        self::$idCv = (int) $documentDao->getLastInsertId();
        $rowData = new RowData([
            'document' => $letterContent,
            'document_filename' => $letterFilepathName,
            'document_mimetype' => $letterMime,
        ]);
        $documentDao->insert($rowData);
        self::$idLetter = (int) $documentDao->getLastInsertId();
    }

    private static function deleteRecords(Container $container) {
        /** @var DocumentDao $documentDao */
        $documentDao = $container->get(DocumentDao::class);

        $dir = __DIR__;
        $rows = $documentDao->find("document_filename LIKE '$dir%'", []);
        foreach($rows as $row) {
            $documentDao->delete($row);
        }
    }

    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->DocumentRepo = $this->container->get(DocumentRepo::class);
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
        $this->assertInstanceOf(Document::class, $visitorProfile);
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
        $this->assertTrue($document->isPersisted());  // DocumentDao je DaoAutoincrementKeyInterface, k zápisu dojde ihned

//        $cvFinfo = new \SplFileInfo($cvFilepathName);
//        $file = $cvFinfo->openFile();
    }

    public function testAddAndReread() {
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
        $this->DocumentRepo->flush();
        $documentRereaded = $this->DocumentRepo->get($document->getId());
        $this->assertInstanceOf(Document::class, $documentRereaded);
        $this->assertTrue($documentRereaded->isPersisted());
    }

    public function testFindAll() {
        $visitorProfile = $this->DocumentRepo->findAll();
        $this->assertTrue(is_array($visitorProfile));
    }

    public function testFind() {
        $dir = __DIR__;
        $documents = $this->DocumentRepo->find("document_filename LIKE '$dir%'", []);
        $this->assertTrue(is_array($documents));
    }

    public function testRemove() {
        $document = $this->DocumentRepo->get(self::$idCv);    // !!!! jenom po insertu v setUp - hodnotu vrací dao
        $this->assertInstanceOf(Document::class, $document);
        $this->DocumentRepo->remove($document);
        $this->assertFalse($document->isPersisted());
        $this->assertTrue($document->isLocked());   // maže až při flush
        $this->DocumentRepo->flush();
        $this->assertFalse($document->isLocked());
        // pokus o čtení
        $document = $this->DocumentRepo->get(self::$idCv);
        $this->assertNull($document);
    }

}
