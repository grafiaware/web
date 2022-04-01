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
            ->setId($rowData->offsetGet('id'))   
            ->setTag($rowData->offsetGet('tag'));
    } 

    /**
     * 
     * @param JobTag $jobTag
     * @param RowDataInterface $rowData
     */
    public function extract(EntityInterface $jobTag, RowDataInterface $rowData) {
        /** @var  JobTag $jobTag */
        // id je autoincrement       
        $rowData->offsetSet('tag', $jobTag->getTag() ); 
    }
}
