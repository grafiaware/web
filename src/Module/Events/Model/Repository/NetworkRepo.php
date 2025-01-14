<?php
namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;

use Events\Model\Hydrator\NetworkHydrator;
use Events\Model\Entity\NetworkInterface;
use Events\Model\Entity\Network;
use Events\Model\Dao\NetworkDao;
use Events\Model\Repository\NetworkRepoInterface;



/**
 * Description of JobTagRepo
 *
 * @author vlse2610
 */
class NetworkRepo extends RepoAbstract implements NetworkRepoInterface {

   
    public function __construct(NetworkDao $jobTagDao, NetworkHydrator $networkHydrator) {
        $this->dataManager = $jobTagDao;
        $this->registerHydrator($networkHydrator);
    }

    /**
     * 
     * @param type $id
     * @return NetworkInterface|null
     */
    public function get($id): ?NetworkInterface {
        return $this->getEntity($id);
    }

    /**
     *
     * @param type $whereClause
     * @param type $touplesToBind
     * @return NetworkInterface[]
     */
    public function find($whereClause=null, $touplesToBind=[]) : array {
        return $this->findEntities($whereClause, $touplesToBind);
    }

    /**
     *
     * @return NetworkInterface[]
     */
    public function findAll() :array  {
        return $this->findEntities();
    }

    /**
     *
     * @param NetworkInterface $jobtag
     * @return void
     */
    public function add( NetworkInterface $jobtag ) :void {
        $this->addEntity($jobtag);
    }

     /**
     *
     * @param NetworkInterface $network
     * @return void
     */
    public function remove(NetworkInterface $network ) :void {
        $this->removeEntity($network);
    }

    protected function createEntity() {
        return new Network();
    }

    protected function indexFromEntity(NetworkInterface $network) {
        return $network->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }
    
}
