<?php

namespace Events\Model\Hydrator;

use Model\Entity\EntityInterface;
use Events\Model\Entity\JobToTag;

use Model\RowData\RowDataInterface;
use Model\Hydrator\TypeHydratorAbstract;
use Model\Hydrator\RowHydratorInterface; 


/**
 * Description of JobToTagHydrator
 *
 * @author vlse2610
 */
class JobToTagHydrator extends TypeHydratorAbstract implements RowHydratorInterface {
    
    /**
     * 
     * @param JobToTag $jobToTag
     * @param RowDataInterface $rowData
     */
    public function hydrate(EntityInterface $jobToTag, RowDataInterface $rowData) {
        /** @var JobToTag $jobToTag */
        $jobToTag                
            ->setJobId($this->getPhpValue( $rowData, 'job_id' ) )
            ->setJobTagTag($this->getPhpValue( $rowData,'job_tag_tag') );
        
    } 

    /**
     * 
     * @param JobToTag $jobToTag
     * @param RowDataInterface $rowData
     */
    public function extract(EntityInterface $jobToTag, RowDataInterface $rowData) {
        /** @var JobToTag $jobToTag */
        
        // neni autoincrement       
        $this->setSqlValue( $rowData, 'job_id', $jobToTag->getJobId() );
        $this->setSqlValue( $rowData, 'job_tag_tag', $jobToTag->getJobTagTag() ); 
        
    }
    
    
}
