<?php
declare(strict_types=1);
namespace Test\Integration\Event\Model\Repository;

use Test\AppRunner\AppRunner;
use Pes\Container\Container;

use Container\EventsModelContainerConfigurator;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

use Events\Model\Entity\Document;
use Events\Model\Entity\DocumentInterface;
use Events\Model\Dao\DocumentDao;
use Events\Model\Repository\DocumentRepo;

use Model\Repository\Exception\OperationWithLockedEntityException;
use Model\RowData\RowData;



/**
 *
 * @author pes2704
 */
class DocumentRepositoryTest extends AppRunner {
    private $container;

    /**
     *
     * @var DocumentRepo
     */
    private $documentRepo;

    private static $idCv;
    private static $idLetter;

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );

        // mazání - zde jen pro případ, že minulý test nebyl dokončen
        self::deleteRecords($container);

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
        finfo_close($finfo);

        $rowData = new RowData();
        $rowData->import([
            'content' => $cvContent,
            'document_filename' => $cvFilepathName,
            'document_mimetype' => $cvMime,
        ]);
        $documentDao->insert($rowData);
        self::$idCv = $documentDao->getLastInsertedPrimaryKey()[$documentDao->getAutoincrementFieldName()];

        $rowData = new RowData();
        $rowData->import([
            'content' => $letterContent,
            'document_filename' => $letterFilepathName,
            'document_mimetype' => $letterMime,
        ]);
        $documentDao->insert($rowData);
        self::$idLetter = $documentDao->getLastInsertedPrimaryKey()[$documentDao->getAutoincrementFieldName()];
    }

    private static function deleteRecords(Container $container) {
        /** @var DocumentDao $documentDao */
        $documentDao = $container->get(DocumentDao::class);
        $dir = __DIR__;
        //$rows = $documentDao->find( 'document_filename LIKE "' . $dir . '%"', []);
        //$rows = $documentDao->find( "document_filename LIKE 'C:%.doc'", []);
        // oescapovat
        $dir = str_replace('\\', '\\\\\\\\', $dir);  //OESCAPOVANO, hledam 1 zpet.lomitko a nahrazuji ho ctyrma
        $rows = $documentDao->find( "document_filename LIKE '$dir%'", []);
        foreach($rows as $row) {
            $ok = $documentDao->delete($row);
        }
    }

    protected function setUp(): void {
        $this->container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );
        $this->documentRepo = $this->container->get(DocumentRepo::class);
    }

    protected function tearDown(): void {
       // $this->documentRepo->flush();
        $this->documentRepo->__destruct();
    }

    public static function tearDownAfterClass(): void {
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );

        self::deleteRecords($container);
    }

    public function testSetUp() {
        $this->assertInstanceOf( DocumentRepo::class, $this->documentRepo);
    }

    public function testGetNonExisted() {
        $document = $this->documentRepo->get(-1);
        $this->assertNull($document);
    }

    public function testGetAfterSetup() {
        $document = $this->documentRepo->get(self::$idCv);
        $this->assertInstanceOf(DocumentInterface::class, $document);
    }

    public function testAdd() {
        $document = new Document();
        $cvFilename = "CV.doc";
        $letterFilename = "DOPIS.doc";

        $filepathName = __DIR__."/".$cvFilename;
        $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
        $mime = finfo_file($finfo, $filepathName);
        $content = file_get_contents($filepathName);
        finfo_close($finfo);

        $document->setContent($content);
        $document->setDocumentMimetype($mime);
        $document->setDocumentFilename($filepathName);

        $this->documentRepo->add($document);
        $this->assertTrue($document->isPersisted());  // !!!!!! DocumentDao ma DaoEditAutoincrementKeyInterface, k zápisu dojde ihned !!!!!!!
        // pro automaticky|generovany klic (tento pripad zde ) a  pro  overovany klic  - !!! zapise se hned !!!

    }

    public function testAddAndReread() {
        $document = new Document();
        $cvFilename = "CV.doc";
        $letterFilename = "DOPIS.doc";

        $filepathName = __DIR__."/".$cvFilename;
        $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
        $mime = finfo_file($finfo, $filepathName);
        $content = file_get_contents($filepathName);
        finfo_close($finfo);

        $document->setContent($content);
        $document->setDocumentMimetype($mime);
        $document->setDocumentFilename($filepathName);

        $this->documentRepo->add($document);
        $this->documentRepo->flush();
        $documentRereaded = $this->documentRepo->get($document->getId());
        $this->assertInstanceOf(DocumentInterface::class, $documentRereaded);
        $this->assertTrue($documentRereaded->isPersisted());
    }

    public function testFindAll() {
        $documentsArray = $this->documentRepo->findAll();
        $this->assertTrue(is_array($documentsArray));
        $this->assertGreaterThan(0,count($documentsArray)); //jsou tam minimalne 2
    }

    public function testFind() {
        $dir = __DIR__;
        //$documents = $this->documentRepo->find( 'document_filename LIKE "' . $dir . '%"', []);
        //$documents = $this->documentRepo->find( "document_filename LIKE 'C:%.doc'", []);
        //$documents = $this->documentRepo->find( "document_filename LIKE '" . $dir . "%'", []);

        $dir = str_replace('\\', '\\\\\\\\', $dir);     //OESCAPOVANO, hledam 1 zpet.lomitko a nahrazuji ho ctyrma
        $documents = $this->documentRepo->find( "document_filename LIKE '$dir%'", []);
        $this->assertTrue(is_array($documents));
        $this->assertGreaterThan(0,count($documents)); //jsou tam minimalne 2

    }

    public function testRemove_OperationWithLockedEntity() {
        $document = $this->documentRepo->get(self::$idCv);
        $this->assertInstanceOf(DocumentInterface::class, $document);
        $this->assertTrue($document->isPersisted());
        $this->assertFalse($document->isLocked());

        $document->lock();
        $this->expectException( OperationWithLockedEntityException::class);
        $this->documentRepo->remove($document);
    }

    public function testRemove() {
        $document = $this->documentRepo->get(self::$idCv);
        $this->assertInstanceOf(DocumentInterface::class, $document);
        $this->assertTrue($document->isPersisted());
        $this->assertFalse($document->isLocked());

        $this->documentRepo->remove($document);

        $this->assertTrue($document->isPersisted());
        $this->assertTrue($document->isLocked());   // maže až při flush
        $this->documentRepo->flush();
        // document uz neni locked
        $this->assertFalse($document->isLocked());

        // pokus o čtení, document uz  neni
        $document = $this->documentRepo->get(self::$idCv);
        $this->assertNull($document);
    }

}
