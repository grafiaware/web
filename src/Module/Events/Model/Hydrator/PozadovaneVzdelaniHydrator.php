<?php
namespace Events\Model\Hydrator;

use Model\Entity\EntityInterface;

use Events\Model\Entity\PozadovaneVzdelani;
use Model\RowData\RowDataInterface;

/**
 * Description of PozadovaneVzdelaniHydrator
 *
 * @author vlse2610
 */
class PozadovaneVzdelaniHydrator implements HydratorInterface {
    
    
   /**
    * 
    * @param PozadovaneVzdelani $pozadovaneVzdelani
    * @param RowDataInterface $rowData
    */
    public function hydrate(EntityInterface $pozadovaneVzdelani, RowDataInterface $rowData) {
        /** @var  PozadovaneVzdelani $pozadovaneVzdelani */
        $pozadovaneVzdelani
                
            ->sesId($rowData->offsetGet('stupen'))   
            ->setName($rowData->offsetGet('vzdelani'));
    } 

    /**
     * 
     * @param EntityInterface $pozadovaneVzdelani
     * @param RowDataInterface $rowData
     */
    public function extract(EntityInterface $pozadovaneVzdelani, RowDataInterface $rowData) {
        /** @var  PozadovaneVzdelani $pozadovaneVzdelani */
        // neni  autoincrement       
        $rowData->offsetSet('stupen', $pozadovaneVzdelani->getStupen() );
        $rowData->offsetSet('vzdelani', $pozadovaneVzdelani->getVzdelani() ); 
    }
    
}
