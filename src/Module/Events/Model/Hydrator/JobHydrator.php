<?php

namespace Events\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;
use Model\RowData\RowDataInterface;

use Events\Model\Entity\JobInterface;
/**
 * Description of JobHydrator
 *
 * @author vlse2610
 */
class JobHydrator implements HydratorInterface {
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
            ->setId($rowData->offsetGet('id'))
            ->setCompanyId($rowData->offsetGet('company_id') )
            ->setPozadovaneVzdelaniStupen($rowData->offsetGet('pozadovane_vzdelani_stupen') )
            ->setNazev($rowData->offsetGet('nazev') )
            ->setMistoVykonu($rowData->offsetGet('misto_vykonu')  )
            ->setPopisPozice($rowData->offsetGet('popis_pozice'))
            ->setPozadujeme($rowData->offsetGet('pozadujeme')  )
            ->setNabizime($rowData->offsetGet('nabizime')  );
    }

    /**
     * 
     * @param JobInterface $job
     * @param RowDataInterface $rowData
     */
    public function extract(EntityInterface $job, RowDataInterface $rowData) {
        /** @var JobInterface $job */
        // id je autoincrement
        $rowData->offsetSet('company_id', $job->getCompanyId());
        $rowData->offsetSet('pozadovane_vzdelani_stupen', $job->getPozadovaneVzdelaniStupen() ) ;
        $rowData->offsetSet('nazev', $job->getNazev() ) ;
        $rowData->offsetSet('misto_vykonu', $job->getMistoVykonu());
        $rowData->offsetSet('popis_pozice', $job->getPopisPozice());
        $rowData->offsetSet('pozadujeme', $job->getPozadujeme());
        $rowData->offsetSet('nabizime', $job->getNabizime());
    }
        
}
