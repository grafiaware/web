<?php
declare(strict_types=1);

namespace Test\Integration\Event\Model\Repository;

use Test\AppRunner\AppRunner;
use Pes\Container\Container;
use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Model\Repository\Exception\OperationWithLockedEntityException;
use Model\RowData\RowData;

use Events\Model\Dao\LoginDao;
use Events\Model\Dao\DocumentDao;

use Events\Model\Entity\VisitorProfile;
use Events\Model\Entity\VisitorProfileInterface;
use Events\Model\Dao\VisitorProfileDao;
use Events\Model\Repository\VisitorProfileRepo;



 /**
 *
 * @author vlse2610
 */
class VisitorProfileRepositoryTest extends AppRunner {
    private $container;

    /**
     *
     * @var VisitorProfileRepo
     */
    private $visitorProfileRepo;    
    
    private static $loginNameTest = "testVisitorProfileRepo";
    private static $loginNameAdded;
    private static $idCv;       
    

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        // mazání - zde jen pro případ, že minulý test nebyl dokončen
        self::deleteRecords( $container );
       
        self::insertRecord( $container );
    }
    
    private static function insertRecord(Container $container ) {        
        // toto je příprava testu
            // login
        self::$loginNameTest = self::insertLoginRecord($container);           
        
         /** @var DocumentDao $documentDao */
        $documentDao = $container->get(DocumentDao::class);
        $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension

        $cvFilename = "CV.doc";        
        $cvFilepathName = __DIR__."/".$cvFilename;
        $cvMime = finfo_file($finfo, $cvFilepathName);
        $cvContent = file_get_contents($cvFilepathName);       
        $rowData = new RowData();
        $rowData->import([
            'document' => $cvContent,
            'document_filename' => $cvFilepathName,
            'document_mimetype' => $cvMime,
        ]);
        $documentDao->insert($rowData);
        self::$idCv = $documentDao->lastInsertIdValue();                
        
    }

    
    private static function insertLoginRecord(Container $container) {
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        // prefix + uniquid - bez zamykání db
        do {
            $loginName = self::$loginNameTest . "_"  .uniqid();
            $login = $loginDao->get(['login_name' => $loginName]);
        } while ($login);
        $rowData = new RowData();
        $rowData->import([
            'login_name' => $loginName,
        ]);
        $loginDao->insert($rowData);
        return $loginName;
    }
    

    private static function deleteRecords(Container $container) {
        /** @var VisitorProfileDao $visitorProfileDao */
        $visitorProfileDao = $container->get(VisitorProfileDao::class);        
        $rows = $visitorProfileDao->find( "login_login_name LIKE '". self::$loginNameTest  . "%'", []);      
        foreach($rows as $row) {
            $visitorProfileDao->delete($row);
        }        
        
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
        
        //login
          /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);        
        $rows = $loginDao->find( " login_name LIKE '". self::$loginNameTest  . "%'", []);      
        foreach($rows as $row) {
            $loginDao->delete($row);
        }  
        
        
        
    }

    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->visitorProfileRepo = $this->container->get(VisitorProfileRepo::class);
    }

    protected function tearDown(): void {
        $this->visitorProfileRepo->flush();
    }
    
    public static function tearDownAfterClass(): void {
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        self::deleteRecords($container);
    }

    
    
    public function testSetUp() {
        $this->assertInstanceOf(VisitorProfileRepo::class, $this->visitorProfileRepo);
    }

    
    public function testGetNonExisted() {
        $visitorProfile = $this->visitorProfileRepo->get('dlksdhfweuih');
        $this->assertNull($visitorProfile);
    }


    
    public function testAdd() {
        self::$loginNameAdded = self::insertLoginRecord($this->container);
        $visitorProfile = new VisitorProfile();
        $visitorProfile->setLoginLoginName( self::$loginNameAdded );
                        
        $visitorProfile->setPrefix("Bleble.");
        $visitorProfile->setName("Jméno");
        $visitorProfile->setSurname("Příjmení");
        $visitorProfile->setPostfix("Blabla.");
        $visitorProfile->setEmail("qwertzuio@twrqew.qt");
        $visitorProfile->setPhone('+999 888 777 666');
        $visitorProfile->setCvEducationText("Školy mám.");
        $visitorProfile->setCvSkillsText("Umím fčecko nejlýp.");        
        //dokument
        $visitorProfile->setCvDocument(self::$idCv);
        $visitorProfile->setLetterDocument(self::$idCv);

        $this->visitorProfileRepo->add($visitorProfile);
        $this->assertTrue($visitorProfile->isLocked());
    }
    

    public function testGetAfterAdd() {
        $visitorProfile = $this->visitorProfileRepo->get(self::$loginNameAdded);
        $this->assertInstanceOf(VisitorProfileInterface::class, $visitorProfile);
    }

         
    
    
    public function testAddAndReread() {
        $loginName = self::insertLoginRecord($this->container);
        
        /** @var VisitorProfile $visitorProfile */        
        $visitorProfile = new VisitorProfile();
        $visitorProfile->setLoginLoginName($loginName);
                
        $visitorProfile->setPrefix("Bleble.");
        $visitorProfile->setName("Jméno");
        $visitorProfile->setSurname("Příjmení");
        $visitorProfile->setPostfix("Blabla.");
        $visitorProfile->setEmail("qwertzuio@twrqew.qt");
        $visitorProfile->setPhone('+999 888 777 666');
        $visitorProfile->setCvEducationText("Školy mám.");
        $visitorProfile->setCvSkillsText("Umím fčecko nejlýp.");
                        
        $this->visitorProfileRepo->add($visitorProfile);
        $this->visitorProfileRepo->flush();
        $visitorProfileRereaded = $this->visitorProfileRepo->get($loginName);
        $this->assertInstanceOf(VisitorProfileInterface::class, $visitorProfileRereaded);
        $this->assertTrue($visitorProfileRereaded->isPersisted());
    }

    public function testFindAll() {
        $visitorProfiles = $this->visitorProfileRepo->findAll();
        $this->assertTrue(is_array($visitorProfiles));
    }

    
    public function testFind() {
        $name = self::$loginNameTest;
        $visitorProfiles = $this->visitorProfileRepo->find("login_login_name LIKE '$name%'", []);
        $this->assertTrue(is_array($visitorProfiles));
    }
    
 

    public function testRemove_OperationWithLockedEntity() {
        $loginName = self::insertLoginRecord($this->container);
        
        /** @var VisitorProfile  $visitorProfile */        
        $visitorProfile = new VisitorProfile();
        $visitorProfile->setLoginLoginName( $loginName );        
        
        $this->visitorProfileRepo->add($visitorProfile);
       
        $this->assertFalse($visitorProfile->isPersisted());
        $this->assertTrue($visitorProfile->isLocked());

        $this->expectException( OperationWithLockedEntityException::class);
        $this->visitorProfileRepo->remove($visitorProfile);
   }


   
    public function testRemove() {
        /** @var VisitorProfile  $visitorProfile */        
        $visitorProfile = $this->visitorProfileRepo->get( self::$loginNameAdded );
               
        $this->assertInstanceOf(VisitorProfileInterface::class, $visitorProfile);
        $this->assertTrue($visitorProfile->isPersisted());
        $this->assertFalse($visitorProfile->isLocked());
        
        $this-$this->visitorProfileRepo->remove($visitorProfile);
        
        $this->assertTrue($visitorProfile->isPersisted());
        $this->assertTrue($visitorProfile->isLocked());   // zatim zamcena entita, maže až při flush
        $this->visitorProfileRepo->flush();
        //  uz neni locked
        $this->assertFalse($visitorProfile->isLocked());
        
        // pokus o čtení, entita VisitorProfile.$loginNameAdded uz  neni
        $visitorProfile = $this->visitorProfileRepo->get(  self::$loginNameAdded );
        $this->assertNull($visitorProfile);
        
    }
    
       
    
    
    
    
    
    
    
    //----------------------------------------
//
//    private static $loginNamePrefix;
//    private static $loginName;
//    private static $loginNameAdded;
//    private static $visitorProfileAdded;
//
//    public static function setUpBeforeClass(): void {
//        self::bootstrapBeforeClass();
//        $container =
//            (new EventsContainerConfigurator())->configure(
//                (new DbEventsContainerConfigurator())->configure(new Container())
//            );
//        // mazání - zde jen pro případ, že minulý test nebyl dokončen
//        self::deleteRecords($container);
//
//        // toto je příprava testu
//        self::$loginNamePrefix = "testVisitorProfile";
//
//        // visitor profile record pro testy get
//        self::$loginName = self::insertLoginRecord($container);
//        self::insertRecord($container, self::$loginName);
//    }
//
//    private static function insertRecord(Container $container, $loginName) {
//        /** @var VisitorProfileDao $visitorProfileDao */
//        $visitorProfileDao = $container->get(VisitorProfileDao::class);
//
//        $rowData = new RowData();
//        $rowData->import([
//            'login_login_name' => $loginName,
//            'name' => "Name" . (string) (1000+random_int(0, 999)),
//            'surname' => "Name" . (string) (1000+random_int(0, 999)),
//            'email' => "mail" . (string) (1000+random_int(0, 999)).'@ztrewzqtrwzeq.cc',
//            'phone' => (string) (1000+random_int(0, 999)).' '.(string) (1000+random_int(0, 999)).' '.(string) (1000+random_int(0, 999))
//        ]);
//        $visitorProfileDao->insert($rowData);
//    }
//
//    private static function insertLoginRecord(Container $container) {
//        /** @var LoginDao $loginDao */
//        $loginDao = $container->get(LoginDao::class);
//        // prefix + uniquid - bez zamykání db
//        do {
//            $loginName = self::$loginNamePrefix."_".uniqid();
//            $login = $loginDao->get(['login_name' => $loginName]);
//        } while ($login);
//
//        $rowData = new RowData();
//        $rowData->import([
//            'login_name' => $loginName,
//        ]);
//        $loginDao->insert($rowData);
//
//        return $loginName;
//    }
//
//    private static function deleteRecords(Container $container) {
//        /** @var VisitorProfileDao $visitorDataDao */
//        $visitorDataDao = $container->get(VisitorProfileDao::class);
//        $prefix = self::$loginNamePrefix;
//        $rows = $visitorDataDao->find("login_login_name LIKE '$prefix%'", []);
//        foreach($rows as $row) {
//            $visitorDataDao->delete($row);
//        }
//    }
//
//    protected function setUp(): void {
//        $this->container =
//            (new EventsContainerConfigurator())->configure(
//                (new DbEventsContainerConfigurator())->configure(new Container())
//            );
//        $this->visitorProfileRepo = $this->container->get(VisitorProfileRepo::class);
//    }
//
//    protected function tearDown(): void {
//        $this->visitorProfileRepo->flush();
//    }
//
//    public static function tearDownAfterClass(): void {
//        $container =
//            (new EventsContainerConfigurator())->configure(
//                (new DbEventsContainerConfigurator())->configure(new Container())
//            );
//
//        self::deleteRecords($container);
//    }
//
//    public function testSetUp() {
//        $this->assertInstanceOf(VisitorProfileRepo::class, $this->visitorProfileRepo);
//    }
//
//    public function testGetNonExisted() {
//        $visitorProfile = $this->visitorProfileRepo->get('dlksdhfweuih');
//        $this->assertNull($visitorProfile);
//    }
//
//    public function testGetAfterSetup() {
//        $visitorProfile = $this->visitorProfileRepo->get(self::$loginName);    // !!!! jenom po insertu v setUp - hodnotu vrací dao
//        $this->assertInstanceOf(VisitorProfile::class, $visitorProfile);
//    }
//
//    public function testAdd() {
//        self::$loginNameAdded = self::insertLoginRecord($this->container);
//
//        $visitorProfile = new VisitorProfile();
//        $visitorProfile->setLoginLoginName(self::$loginNameAdded);
//        $visitorProfile->setPrefix("Bleble.");
//        $visitorProfile->setName("Jméno");
//        $visitorProfile->setSurname("Příjmení");
//        $visitorProfile->setPostfix("Blabla.");
//        $visitorProfile->setEmail("qwertzuio@twrqew.qt");
//        $visitorProfile->setPhone('+999 888 777 666');
//        $visitorProfile->setCvEducationText("Školy mám.");
//        $visitorProfile->setCvSkillsText("Umím fčecko nejlýp.");
//
//        $this->visitorProfileRepo->add($visitorProfile);
//        $this->assertTrue($visitorProfile->isLocked());
//        self::$visitorProfileAdded = $visitorProfile;
//
////        $cvFinfo = new \SplFileInfo($cvFilepathName);
////        $file = $cvFinfo->openFile();
//    }
//
//    public function testGetAfterAdd() {
//        $visitorProfile = $this->visitorProfileRepo->get(self::$loginNameAdded);
//        $this->assertInstanceOf(VisitorProfile::class, $visitorProfile);
//    }
//
//    public function testAddAndReread() {
//        $loginName = self::insertLoginRecord($this->container);
//
//        $visitorProfile = new VisitorProfile();
//        $visitorProfile->setLoginLoginName($loginName);
//        $visitorProfile->setPrefix("Trdlo.");
//        $visitorProfile->setName("Julián");
//        $visitorProfile->setSurname("Bublifuk");
//        $this->visitorProfileRepo->add($visitorProfile);
//        $this->visitorProfileRepo->flush();
//        $visitorProfileRereaded = $this->visitorProfileRepo->get($loginName);
//        $this->assertInstanceOf(VisitorProfile::class, $visitorProfileRereaded);
//        $this->assertTrue($visitorProfileRereaded->isPersisted());
//    }
//
//    public function testFindAll() {
//        $visitorProfile = $this->visitorProfileRepo->findAll();
//        $this->assertTrue(is_array($visitorProfile));
//    }
//
//    public function testFind() {
//        $prefix = self::$loginNamePrefix;
//        $visitorsProfile = $this->visitorProfileRepo->find("login_login_name LIKE '$prefix%'", []);
//        $this->assertTrue(is_array($visitorsProfile));
//    }
//
//    public function testRemove() {
//        $visitorProfile = $this->visitorProfileRepo->get(self::$loginName);    // !!!! jenom po insertu v setUp - hodnotu vrací dao
//        $this->assertInstanceOf(VisitorProfile::class, $visitorProfile);
//        $this->visitorProfileRepo->remove($visitorProfile);
//        $this->assertFalse($visitorProfile->isPersisted());
//        $this->assertTrue($visitorProfile->isLocked());   // maže až při flush
//        $this->visitorProfileRepo->flush();
//        $this->assertFalse($visitorProfile->isLocked());
//        // pokus o čtení
//        $visitorProfile = $this->visitorProfileRepo->get(self::$loginName);
//        $this->assertNull($visitorProfile);
//    }

}
