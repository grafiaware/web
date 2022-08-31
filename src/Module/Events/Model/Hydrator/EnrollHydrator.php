<?php

namespace Events\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;
use Model\RowData\RowDataInterface;
use Model\Hydrator\TypeHydratorAbstract;

use Events\Model\Entity\EnrollInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class EnrollHydrator extends TypeHydratorAbstract implements HydratorInterface {

    /**
     *
     * @param EnrollInterface $enroll
     * @param type $rowData
     */
    public function hydrate(EntityInterface $enroll, RowDataInterface $rowData) {
        /** @var EnrollInterface $enroll */
        $e = $this->getPhpValue( $rowData,'login_login_name_fk' );
        $e1 = $this->getPhpValue( $rowData,'event_id_fk') ;
        
        $enroll                     
            ->setLoginLoginNameFk( $this->getPhpValue( $rowData,'login_login_name_fk') )
            ->setEventIdFk( $this->getPhpValue( $rowData,'event_id_fk') )                   
            ;
    }

    /**
     *
     * @param EnrollInterface $enroll
     * @param type $rowData
     */
    public function extract(EntityInterface $enroll, RowDataInterface $rowData) {        
        $l =   $enroll->getLoginLoginNameFk() ;
        $this->setSqlValue( $rowData, 'login_login_name_fk',  $enroll->getLoginLoginNameFk() );
        
        $l1 =  $enroll->getEventIdFk() ;
        $this->setSqlValue( $rowData, 'event_id_fk', $enroll->getEventIdFk() );

        
    }

}
