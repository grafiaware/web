<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;
use ArrayAccess;
use Model\Hydrator\TypeHydratorAbstract;

use Events\Model\Entity\LoginInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class LoginHydrator  extends TypeHydratorAbstract implements HydratorInterface {

    /**
     *
     * @param LoginInterface $login
     * @param type $rowData
     */
    public function hydrate(EntityInterface $login, ArrayAccess $rowData) {
        /** @var LoginInterface $login */
        $login->setLoginName( $this->getPhpValue( $rowData, 'login_name'))     
       
            ->setCreated($this->getPhpValue( $rowData, 'created') ? \DateTime::createFromFormat('Y-m-d H:i:s', $this->getPhpValue( $rowData, 'created') ) : NULL)
            ->setUpdated($this->getPhpValue( $rowData, 'updated') ? \DateTime::createFromFormat('Y-m-d H:i:s', $this->getPhpValue( $rowData, 'updated') ) : NULL)
            ->setDeletedDueToAuth( $this->getPhpValue( $rowData, 'deleted_due_to_auth')) ;
//            ->setCreated($rowData->offsetGet('created') ? \DateTime::createFromFormat('Y-m-d H:i:s', $rowData->offsetGet('created')) : NULL)
//            ->setUpdated($rowData->offsetGet('updated') ? \DateTime::createFromFormat('Y-m-d H:i:s', $rowData->offsetGet('updated')) : NULL);           
    }

    /**
     *
     * @param LoginInterface $login
     * @param array $rowData
     */
    public function extract(EntityInterface $login, ArrayAccess $rowData) {
        /** @var LoginInterface $login */
         $this->setSqlValue( $rowData, 'login_name', $login->getLoginName());
         $this->setSqlValue( $rowData, 'deleted_due_to_auth', $login->getDeletedDueToAuth());
         // created a updated jsou timestamp - readonly
                
    }

}
