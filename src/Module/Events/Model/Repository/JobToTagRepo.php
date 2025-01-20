<?php
namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;

use Events\Model\Entity\JobToTagInterface;
use Events\Model\Entity\JobToTag;
use Events\Model\Dao\JobToTagDao;
use Events\Model\Hydrator\JobToTagHydrator;

//use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of JobToTagRepo
 *
 * @author vlse2610
 */
class JobToTagRepo extends RepoAbstract implements JobToTagRepoInterface {

    public function __construct(JobToTagDao $jobToTagDao, JobToTagHydrator $jobToTagHydrator) {
        $this->dataManager = $jobToTagDao;
        $this->registerHydrator($jobToTagHydrator);
    }

 
    /**
     * 
     * @param type $jobId
     * @param type $jobTagId
     * @return JobToTagInterface|null
     */
    public function get($jobId, $jobTagId): ?JobToTagInterface {
        return $this->getEntity($jobId, $jobTagId);
    }




    /**
     *
     * @param type $jobId
     * @return JobToTagInterface[]
     */
    public function findByJobId($jobId) : array {
        return $this->findEntities("job_id = :job_id", [":job_id"=>$jobId] );
    }
    
    
    /**
     * 
     * @param type $jobTagId
     * @return array
     */
    public function findByJobTagId($jobTagId) : array {
        return $this->findEntities("job_tag_id = :job_tag_id", [":job_tag_id"=>$jobTagId] );
    }
    
    
     /**
     * 
     * @param string $whereClause Příkaz where v SQL syntaxi vhodné pro PDO, s placeholdery
     * @param array $touplesToBind Pole dvojic jméno-hodnota, ze kterého budou budou nahrazeny placeholdery v příkazu where
     * @return JobToTagInterface[]
     */
    public function find($whereClause="", $touplesToBind=[]) : array {
        return $this->findEntities( $whereClause, $touplesToBind );
    }


    /**
     *
     * @return JobToTagInterface[]
     */
    public function findAll(): array  {
        return $this->findEntities();
    }



    /**
     *
     * @param JobToTagInterface $jobToTag
     * @return void
     */
    public function add(JobToTagInterface $jobToTag) :void {
        $this->addEntity($jobToTag);
    }



    /**
     *
     * @param JobToTagInterface $jobToTag
     * @return void
     */
    public function remove(JobToTagInterface $jobToTag)  :void {
        $this->removeEntity($jobToTag);
    }



    protected function createEntity() {
        return new JobToTag();
    }

    protected function indexFromEntity(JobToTagInterface $jobToTag) {
       return $jobToTag->getJobId() . $jobToTag->getJobTagId() ;
    }

    protected function indexFromRow($row) {
        return $row['job_id']. $row['job_tag_id'] ;
    }

}
