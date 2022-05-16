<?php

namespace Events\Model\Hydrator;

use Model\Entity\EntityInterface;

use Events\Model\Entity\JobTag;
use Model\RowData\RowDataInterface;

/**
 * Description of JobTagHydrator
 *
 * @author vlse2610
 */
class JobTagHydrator implements HydratorInterface {
    
   /**
    * 
    * @param JobTag $jobTag
    * @param RowDataInterface $rowData
    */
    public function hydrate(EntityInterface $jobTag, RowDataInterface $rowData) {
        /** @var  JobTag $jobTag */
        $jobTag                
             
            ->setTag($rowData->offsetGet('tag'));
    } 

    /**
     * 
     * @param JobTag $jobTag
     * @param RowDataInterface $rowData
     */
    public function extract(EntityInterface $jobTag, RowDataInterface $rowData) {
        /** @var  JobTag $jobTag */
        $rowData->offsetSet('tag', $jobTag->getTag() ); 
    }
}
