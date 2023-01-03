<?php
namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;

use Events\Model\Entity\VisitorProfileInterface;
use Events\Model\Entity\VisitorProfile;
use Events\Model\Dao\VisitorProfileDao;
use Events\Model\Hydrator\VisitorProfileHydrator;

/**
 * Description of Menu
 *
 * @author
 */
class VisitorProfileRepo extends RepoAbstract implements VisitorProfileRepoInterface {

    public function __construct(VisitorProfileDao $visitorDataDao, VisitorProfileHydrator $visitorDataHydrator) {
        $this->dataManager = $visitorDataDao;
        $this->registerHydrator($visitorDataHydrator);
    }

    /**
     *
     * @param type $loginName
     * @return VisitorProfileInterface|null
     */
    public function get($loginName): ?VisitorProfileInterface {
        return $this->getEntity($loginName);
    }

    /**
     *
     * @return VisitorProfileInterface[]
     */
    public function find($whereClause=null, $touplesToBind=[]): array {
        return $this->findEntities($whereClause, $touplesToBind);
    }


    /**
     *
     * @return VisitorProfileInterface[]
     */
    public function findAll()  : array {
        return $this->findEntities();
    }


     /**
     *
     * @param VisitorProfileInterface $visitorJobRequest
     * @return void
     */
    public function add(VisitorProfileInterface $visitorProfile) : void {
        $this->addEntity($visitorProfile);
    }

    /**
     *
     * @param VisitorProfileInterface $visitorJobRequest
     * @return void
     */
    public function remove(VisitorProfileInterface $visitorProfile): void {
        $this->removeEntity($visitorProfile);
    }



    protected function createEntity() {
        return new VisitorProfile();
    }

    protected function indexFromEntity(VisitorProfileInterface $visitorProfile) {
        return $visitorProfile->getLoginLoginName();
    }

    protected function indexFromRow($row) {
        return $row['login_login_name'];
    }


}
