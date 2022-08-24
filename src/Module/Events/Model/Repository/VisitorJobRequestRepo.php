<?php

namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;

use Events\Model\Entity\VisitorJobRequestInterface;
use Events\Model\Entity\VisitorJobRequest;
use Events\Model\Dao\VisitorJobRequestDao;
use Events\Model\Hydrator\VisitorJobRequestHydrator;
use Events\Model\Repository\VisitorJobRequestRepoInterface;

//use Model\Repository\Exception\UnableRecreateEntityException;


/**
 * Description of Menu
 *
 * @author
 */
class VisitorJobRequestRepo extends RepoAbstract implements VisitorJobRequestRepoInterface {

    public function __construct(VisitorJobRequestDao $visitorJobRequestDao, VisitorJobRequestHydrator $visitorDataPostHydrator) {
        $this->dataManager = $visitorJobRequestDao;
        $this->registerHydrator($visitorDataPostHydrator);
    }

    
    /**
     * 
     * @param type $loginName
     * @return VisitorJobRequestInterface|null
     */
    public function get($loginName): ?VisitorJobRequestInterface {
        $key = $this->dataManager->getPrimaryKeyTouples(['login_login_name'=>$loginName]);
        return $this->getEntity($key);
    }

    
    /**
     * 
     * @return VisitorJobRequestInterface[]
     */
    public function find($whereClause=null, $touplesToBind=[]) : array {
        return $this->findEntities($whereClause, $touplesToBind);
    }

   
    /**
     * 
     * @return VisitorJobRequestInterface[]
     */
    public function findAll() : array{
        return $this->findEntities();
    }
    
    
    /**
     * 
     * @return VisitorJobRequestInterface[]
     */
    public function findByLoginNameAndPosition($loginName, $positionName): array {
        $whereClause = "login_name = :login_name AND `login_name` = :login_name";
        $touplesToBind = [':short_name' => $shortName, ':position_name' => $positionName];
        return $this->findEntities($whereClause, $touplesToBind);
        
                return $this->findEntities("login_login_name_fk = :login_login_name_fk", [":login_login_name_fk"=>$loginName]);

    }

    
    /**
     * 
     * @param VisitorJobRequestInterface $visitorJobRequest
     * @return void
     */
    public function add(VisitorJobRequestInterface $visitorJobRequest) :void {
        $this->addEntity($visitorJobRequest);
    }
    
    
    /**
     * 
     * @param VisitorJobRequestInterface $visitorJobRequest
     * @return void
     */
    public function remove(VisitorJobRequestInterface $visitorJobRequest) :void {
        $this->removeEntity($visitorJobRequest);
    }

    
    
    
    protected function createEntity() {
        return new VisitorJobRequest();
    }

    protected function indexFromEntity(VisitorJobRequestInterface $visitorJobRequest) {
        return $visitorJobRequest->getLoginLoginName();
    }
    

    protected function indexFromRow($row) {
        return $row['login_name'];
    }


}
