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
 * Description
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
     * @param type $jobId
     * @return VisitorJobRequestInterface|null
     */
    public function get($loginName, $jobId): ?VisitorJobRequestInterface {
        return $this->getEntity($loginName, $jobId);
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
          return $this->findEntities( "login_login_name = :login_login_name AND position_name = :position_name",
                                      [':login_login_name' => $shortName, ':position_name' => $positionName]);
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

    protected function indexFromEntity( VisitorJobRequestInterface $visitorJobRequest) {
        return  $visitorJobRequest->getLoginLoginName() . $visitorJobRequest->getJobId() ;
    }


    protected function indexFromRow($row) {
        return  $row['login_login_name']. $row['job_id'] ;
    }


}
