<?php

namespace Events\Model\Hydrator;

use Model\Entity\EntityInterface;

use Events\Model\Entity\JobTag;
use ArrayAccess;
use Model\Hydrator\TypeHydratorAbstract;
use Model\Hydrator\HydratorInterface;


/**
 * Description of JobTagHydrator
 *
 * @author vlse2610
 */
class JobTagHydrator extends TypeHydratorAbstract implements HydratorInterface {
    
   /**
    * 
    * @param JobTag $jobTag
    * @param ArrayAccess $rowData
    */
    public function hydrate(EntityInterface $jobTag, ArrayAccess $rowData) {
        /** @var  JobTag $jobTag */
        $jobTag                
             
            ->setTag($this->getPhpValue($rowData, 'tag' ));
    } 

    /**
     * 
     * @param JobTag $jobTag
     * @param ArrayAccess $rowData
     */
    public function extract(EntityInterface $jobTag, ArrayAccess $rowData) {
        /** @var  JobTag $jobTag */
        $this->setSqlValue( $rowData, 'tag', $jobTag->getTag() ); 
    }
}
