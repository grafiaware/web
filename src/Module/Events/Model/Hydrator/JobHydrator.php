<?php
namespace Events\Model\Hydrator;

use Model\Entity\EntityInterface;
use Events\Model\Entity\JobInterface;

use ArrayAccess;
use Model\Hydrator\TypeHydratorAbstract;
use Model\Hydrator\HydratorInterface;


/**
 * Description of JobHydrator
 *
 * @author vlse2610
 */
class JobHydrator extends TypeHydratorAbstract implements HydratorInterface {

    
     /**
      * 
      * @param JobInterface $job
      * @param ArrayAccess $rowData
      */    
     public function hydrate(EntityInterface $job, ArrayAccess $rowData) {
        /** @var JobInterface $job */
        $job
            ->setId( $this->getPhpValue( $rowData, 'id'))
            ->setCompanyId( $this->getPhpValue   ( $rowData, 'company_id') )
            ->setPublished( $this->getPhpValue   ( $rowData, 'published') )
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
     * @param ArrayAccess $rowData
     */
    public function extract(EntityInterface $job, ArrayAccess $rowData) {
        /** @var JobInterface $job */
        // id je autoincrement
        $this->setSqlValue( $rowData, 'company_id', $job->getCompanyId() );
        $this->setSqlValue( $rowData, 'published', $job->getPublished() );
        $this->setSqlValue( $rowData, 'pozadovane_vzdelani_stupen', $job->getPozadovaneVzdelaniStupen() ) ;
        $this->setSqlValue( $rowData, 'nazev', $job->getNazev() ) ;
        $this->setSqlValue( $rowData, 'misto_vykonu', $job->getMistoVykonu() );
        $this->setSqlValue( $rowData, 'popis_pozice', $job->getPopisPozice() );
        $this->setSqlValue( $rowData, 'pozadujeme', $job->getPozadujeme() );
        $this->setSqlValue( $rowData, 'nabizime', $job->getNabizime() );
            
    }
        
}
