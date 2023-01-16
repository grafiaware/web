<?php
namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;

use Events\Model\Entity\Job;
use Events\Model\Entity\JobInterface;
use Events\Model\Dao\JobDao;
use Events\Model\Hydrator\JobHydrator;
use Events\Model\Repository\JobRepoInterface;



/**
 * Description of JobRepo
 *
 * @author vlse2610
 */
class JobRepo extends RepoAbstract implements JobRepoInterface {

    public function __construct(JobDao $jobContactDao, JobHydrator $jobContactHydrator) {
        $this->dataManager = $jobContactDao;
        $this->registerHydrator($jobContactHydrator);
    }


    /**
     *
     * @param type $id
     * @return JobInterface|null
     */
    public function get($id): ?JobInterface {
        return $this->getEntity($id);
    }



    /**
     *
     * @param type $whereClause
     * @param type $touplesToBind
     * @return JobInterface[]
     */
    public function find($whereClause="", $touplesToBind=[]): array {
        return $this->findEntities($whereClause, $touplesToBind);
    }

    /**
     *
     * @return JobInterface[]
     */
    public function findAll(): array {
        return $this->findEntities();
    }

   /**
     *
     * @param JobInterface $jobContact
     * @return void
     */
    public function add( JobInterface $jobContact ) : void {
        $this->addEntity($jobContact);
    }


    /**
     *
     * @param JobInterface $jobContact
     * @return void
     */
    public function remove(JobInterface $jobContact)  :void {
        $this->removeEntity($jobContact);
    }




    protected function createEntity() : JobInterface {
        return new Job();
    }

    protected function indexFromEntity(JobInterface $jobContact) {
        return $jobContact->getId();
    }

    protected function indexFromRow( $row ) {
        return $row['id'];
    }
}

