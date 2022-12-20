<?php

namespace Events\Model\Hydrator;

use Model\Entity\EntityInterface;

use Events\Model\Entity\JobTag;
use Model\RowData\RowDataInterface;
use Model\Hydrator\TypeHydratorAbstract;
use Model\Hydrator\RowHydratorInterface;


/**
 * Description of JobTagHydrator
 *
 * @author vlse2610
 */
class JobTagHydrator extends TypeHydratorAbstract implements RowHydratorInterface {
    
   /**
    * 
    * @param JobTag $jobTag
    * @param RowDataInterface $rowData
    */
    public function hydrate(EntityInterface $jobTag, RowDataInterface $rowData) {
        /** @var  JobTag $jobTag */
        $jobTag                
             
            ->setTag($this->getPhpValue($rowData, 'tag' ));
    } 

    /**
     * 
     * @param JobTag $jobTag
     * @param RowDataInterface $rowData
     */
    public function extract(EntityInterface $jobTag, RowDataInterface $rowData) {
        /** @var  JobTag $jobTag */
        $this->setSqlValue( $rowData, 'tag', $jobTag->getTag() ); 
    }
}
