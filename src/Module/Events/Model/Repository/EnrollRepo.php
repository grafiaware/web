<?php
namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;

use Events\Model\Entity\EnrollInterface;
use Events\Model\Entity\Enroll;
use Events\Model\Dao\EnrollDao;
use Events\Model\Hydrator\EnrollHydrator;

//use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class EnrollRepo extends RepoAbstract implements EnrollRepoInterface {

    public function __construct(EnrollDao $enrollDao, EnrollHydrator $enrollHydrator) {
        $this->dataManager = $enrollDao;
        $this->registerHydrator($enrollHydrator);
    }

    
    /**
     * 
     * @param type $loginName
     * @param type $eventId
     * @return EnrollInterface|null
     */
    public function get($loginName, $eventId): ?EnrollInterface {
        $key = $this->dataManager->getPrimaryKeyTouples(['login_login_name_fk' => $loginName, 'event_id_fk' => $eventId]);
        return $this->getEntity($key);
    }

     
           
    
    /**
     * 
     * @param type $loginName     
     * @return EnrollInterface[]
     */
    public function findByLoginName($loginName) : array{
        return $this->findEntities("login_login_name_fk = :login_login_name_fk", [":login_login_name_fk"=>$loginName]);
    }
    
    /**
     * 
     * @param type $eventId     
     * @return EnrollInterface[]
     */
    public function findByEvent($eventId) : array{
        return $this->findEntities("event_id_fk = :event_id_fk", [":event_id_fk"=>$eventId]);
    }
    
    
    /**
     * 
     * @return EnrollInterface[]
     */
    public function findAll() : array {
        return $this->findEntities();
    }

   
   
    /**
     * 
     * @param EnrollInterface $enroll
     * @return void
     */
    public function add(EnrollInterface $enroll) :void {
        $this->addEntity($enroll);
    }
    
    

    /**
     * 
     * @param EnrollInterface $enroll
     * @return void
     */
    public function remove(EnrollInterface $enroll) :void {
        $this->removeEntity($enroll);
    }
    
    

    protected function createEntity() {
        return new Enroll();
    }

    protected function indexFromEntity(EnrollInterface $enroll) {
       return $enroll->getLoginLoginNameFk() . $enroll->getEventIdFk() ;
    }

    protected function indexFromRow($row) {
        return $row['login_login_name_fk']. $row['event_id_fk'] ;
    }       
    
}

        //return $itemAction->getTypeFk().$itemAction->getItemId();
         //        return $row['type_fk'].$row['item_id'];