<?php

namespace Events\Model\Hydrator;

use Model\Entity\EntityInterface;

use Events\Model\Entity\JobToTag;
use Model\RowData\RowDataInterface;
/**
 * Description of JobToTagHydrator
 *
 * @author vlse2610
 */
class JobToTagHydrator implements HydratorInterface {
    
    /**
     * 
     * @param EntityInterface $jobToTag
     * @param RowDataInterface $rowData
     */
    public function hydrate(EntityInterface $jobToTag, RowDataInterface $rowData) {
        /** @var JobToTag $jobToTag */
        $jobToTag                
            ->setJobId($rowData->offsetGet('job_id'))
            ->setJobTagId($rowData->offsetGet('job_tag_id'));
    } 

    /**
     * 
     * @param EntityInterface $jobToTag
     * @param RowDataInterface $rowData
     */
    public function extract(EntityInterface $jobToTag, RowDataInterface $rowData) {
        /** @var JobToTag $jobToTag */
        
        // neni autoincrement       
        $rowData->offsetSet('jobId', $jobToTag->getJobId() );
        $rowData->offsetSet('jobTagId', $jobToTag->getJobTagId() ); 
    }
    
    
}
