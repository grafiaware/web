<?php
namespace Events\Model\Hydrator;

use Model\Entity\EntityInterface;

use Events\Model\Entity\PozadovaneVzdelani;
use Model\RowData\RowDataInterface;
use Model\Hydrator\RowHydratorInterface;
use Model\Hydrator\TypeHydratorAbstract;



/**
 * Description of PozadovaneVzdelaniHydrator
 *
 * @author vlse2610
 */
class PozadovaneVzdelaniHydrator  extends TypeHydratorAbstract implements RowHydratorInterface {
    
    
   /**
    * 
    * @param PozadovaneVzdelani $pozadovaneVzdelani
    * @param RowDataInterface $rowData
    */
    public function hydrate( EntityInterface $pozadovaneVzdelani, RowDataInterface $rowData) {
        /** @var  PozadovaneVzdelani $pozadovaneVzdelani */
        $pozadovaneVzdelani
                
            ->setStupen($this->getPhpValue( $rowData, 'stupen' ))   
            ->setVzdelani($this->getPhpValue( $rowData, 'vzdelani' )); 
    } 
    
    
    

    /**
     * 
     * @param EntityInterface $pozadovaneVzdelani
     * @param RowDataInterface $rowData
     */
    public function extract( EntityInterface $pozadovaneVzdelani, RowDataInterface $rowData) {
        /** @var  PozadovaneVzdelani $pozadovaneVzdelani */
        // neni  autoincrement   
        
        $this->setSqlValue( $rowData, 'stupen', $pozadovaneVzdelani->getStupen() );
        $this->setSqlValue( $rowData, 'vzdelani', $pozadovaneVzdelani->getVzdelani() ); 
         
    }
    
}
