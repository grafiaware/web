<?php
namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;

use Events\Model\Hydrator\JobTagHydrator;
use Events\Model\Entity\JobTag;
use Events\Model\Entity\JobTagInterface;
use Events\Model\Dao\JobTagDao;
use Events\Model\Repository\JobTagRepoInterface;



/**
 * Description of JobTagRepo
 *
 * @author vlse2610
 */
class JobTagRepo extends RepoAbstract implements JobTagRepoInterface {

   
    public function __construct( JobTagDao $jobTagDao, JobTagHydrator $jobTagHydrator) {
        $this->dataManager = $jobTagDao;
        $this->registerHydrator($jobTagHydrator);
    }

//    /**
//     *
//     * @param type $tag
//     * @return JobTagInterface|null
//     */
//    public function get($tag): ?JobTagInterface {
//        return $this->getEntity($tag);
//    }

   
    /**
     * 
     * @param type $id
     * @return JobTagInterface|null
     */
    public function get($id): ?JobTagInterface {
        return $this->getEntity($id);
    }

    
    
    /**
     *
     * @param type $whereClause
     * @param type $touplesToBind
     * @return JobTagInterface[]
     */
    public function find($whereClause=null, $touplesToBind=[]) : array {
        return $this->findEntities($whereClause, $touplesToBind);
    }

    
    
    /**
     *
     * @return JobTagInterface[]
     */
    public function findAll() :array  {
        return $this->findEntities();
    }


    /**
     *
     * @param JobTagInterface $jobtag
     * @return void
     */
    public function add( JobTagInterface $jobtag ) :void {
        $this->addEntity($jobtag);
    }


     /**
     *
     * @param JobTagInterface $jobtag
     * @return void
     */
    public function remove( JobTagInterface $jobtag ) :void {
        $this->removeEntity($jobtag);
    }



    protected function createEntity() {
        return new JobTag();
    }

    protected function indexFromEntity(JobTagInterface $jobtag) {
        return $jobtag->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }
}
