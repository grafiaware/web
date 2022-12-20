<?php
namespace Events\Model\Hydrator;

use Model\Entity\EntityInterface;
use Events\Model\Entity\JobInterface;

use Model\RowData\RowDataInterface;
use Model\Hydrator\TypeHydratorAbstract;
use Model\Hydrator\RowHydratorInterface;


/**
 * Description of JobHydrator
 *
 * @author vlse2610
 */
class JobHydrator extends TypeHydratorAbstract implements RowHydratorInterface {
//    `job`.`id`,//NOT NULL
//    `job`.`company_id`,  //NOT NULL
//    `job`.`pozadovane_vzdelani_stupen`,  
//    `job`.`nazev`,
//    `job`.`misto_vykonu`,
//    `job`.`popis_pozice`,
//    `job`.`pozadujeme`,
//    `job`.`nabizime`
    
     /**
      * 
      * @param JobInterface $job
      * @param RowDataInterface $rowData
      */    
     public function hydrate(EntityInterface $job, RowDataInterface $rowData) {
        /** @var JobInterface $job */
        $job
            ->setId( $this->getPhpValue( $rowData, 'id'))
            ->setCompanyId( $this->getPhpValue   ( $rowData, 'company_id') )
            ->setPozadovaneVzdelaniStupen( $this->getPhpValue( $rowData, 'pozadovane_vzdelani_stupen') )
            ->setNazev( $this->getPhpValue       ( $rowData, 'nazev') )
            ->setMistoVykonu( $this->getPhpValue ( $rowData, 'misto_vykonu') )
            ->setPopisPozice ($this->getPhpValue ( $rowData, 'popis_pozice') )
            ->setPozadujeme( $this->getPhpValue  ( $rowData, 'pozadujeme')  )
            ->setNabizime( $this->getPhpValue    ( $rowData, 'nabizime')  );
        
    }

    /**
     * 
     * @param JobInterface $job
     * @param RowDataInterface $rowData
     */
    public function extract(EntityInterface $job, RowDataInterface $rowData) {
        /** @var JobInterface $job */
        // id je autoincrement
        $this->setSqlValue( $rowData, 'company_id', $job->getCompanyId() );
        $this->setSqlValue( $rowData, 'pozadovane_vzdelani_stupen', $job->getPozadovaneVzdelaniStupen() ) ;
        $this->setSqlValue( $rowData, 'nazev', $job->getNazev() ) ;
        $this->setSqlValue( $rowData, 'misto_vykonu', $job->getMistoVykonu() );
        $this->setSqlValue( $rowData, 'popis_pozice', $job->getPopisPozice() );
        $this->setSqlValue( $rowData, 'pozadujeme', $job->getPozadujeme() );
        $this->setSqlValue( $rowData, 'nabizime', $job->getNabizime() );
            
    }
        
}
