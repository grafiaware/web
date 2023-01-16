<?php
namespace Events\Model\Hydrator;

use Model\Entity\EntityInterface;

use Events\Model\Entity\PozadovaneVzdelani;
use ArrayAccess;
use Model\Hydrator\HydratorInterface;
use Model\Hydrator\TypeHydratorAbstract;



/**
 * Description of PozadovaneVzdelaniHydrator
 *
 * @author vlse2610
 */
class PozadovaneVzdelaniHydrator  extends TypeHydratorAbstract implements HydratorInterface {
    
    
   /**
    * 
    * @param PozadovaneVzdelani $pozadovaneVzdelani
    * @param ArrayAccess $rowData
    */
    public function hydrate( EntityInterface $pozadovaneVzdelani, ArrayAccess $rowData) {
        /** @var  PozadovaneVzdelani $pozadovaneVzdelani */
        $pozadovaneVzdelani
                
            ->setStupen($this->getPhpValue( $rowData, 'stupen' ))   
            ->setVzdelani($this->getPhpValue( $rowData, 'vzdelani' )); 
    } 
    
    
    

    /**
     * 
     * @param EntityInterface $pozadovaneVzdelani
     * @param ArrayAccess $rowData
     */
    public function extract( EntityInterface $pozadovaneVzdelani, ArrayAccess $rowData) {
        /** @var  PozadovaneVzdelani $pozadovaneVzdelani */
        // neni  autoincrement   
        
        $this->setSqlValue( $rowData, 'stupen', $pozadovaneVzdelani->getStupen() );
        $this->setSqlValue( $rowData, 'vzdelani', $pozadovaneVzdelani->getVzdelani() ); 
         
    }
    
}
