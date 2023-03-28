<?php

namespace Events\Model\Hydrator;

use Model\Entity\EntityInterface;
use Events\Model\Entity\JobToTag;

use ArrayAccess;
use Model\Hydrator\TypeHydratorAbstract;
use Model\Hydrator\HydratorInterface; 


/**
 * Description of JobToTagHydrator
 *
 * @author vlse2610
 */
class JobToTagHydrator extends TypeHydratorAbstract implements HydratorInterface {
    
    /**
     * 
     * @param JobToTag $jobToTag
     * @param ArrayAccess $rowData
     */
    public function hydrate(EntityInterface $jobToTag, ArrayAccess $rowData) {
        /** @var JobToTag $jobToTag */
        $jobToTag                
            ->setJobId($this->getPhpValue( $rowData, 'job_id' ) )
            ->setJobTagTag($this->getPhpValue( $rowData,'job_tag_tag') );
        
    } 

    /**
     * 
     * @param JobToTag $jobToTag
     * @param ArrayAccess $rowData
     */
    public function extract(EntityInterface $jobToTag, ArrayAccess $rowData) {
        /** @var JobToTag $jobToTag */
        
        // neni autoincrement       
        $this->setSqlValue( $rowData, 'job_id', $jobToTag->getJobId() );
        $this->setSqlValue( $rowData, 'job_tag_tag', $jobToTag->getJobTagTag() ); 
        
    }
    
    
}
