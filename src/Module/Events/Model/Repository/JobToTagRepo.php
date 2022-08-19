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
     * @param type $jobTagTag
     * @return JobToTagInterface|null
     */
    public function get($jobId, $jobTagTag): ?JobToTagInterface {
        $key = $this->dataManager->getPrimaryKeyTouples(['job_id' => $jobId, 'job_tag_tag' => $jobTagTag]);
        return $this->getEntity($key);
    }

     
           
     /**
     * 
     * @param type $jobTagTag     
     * @return JobToTagInterface[]
     */
    public function findByJobTagTag($jobTagTag) : array  {
        return $this->findEntities("job_tag_tag = :job_tag_tag", [":job_tag_tag"=>$jobTagTag]);
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
       return $jobToTag->getJobId() . $jobToTag->getJobTagTag() ;
    }

    protected function indexFromRow($row) {
        return $row['job_id']. $row['job_tag_tag'] ;
    }       
    
}
