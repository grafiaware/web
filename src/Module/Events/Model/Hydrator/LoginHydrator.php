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
        $login->setLoginName  ( $this->getPhpValue ( $rowData, 'login_name') )
                    ->setRole ( $this->getPhpValue ( $rowData, 'role') )
                    ->setEmail( $this->getPhpValue ( $rowData, 'email') )
                    ->setInfo ( $this->getPhpValue ( $rowData, 'info') )
                    ->setModul( $this->getPhpValue ( $rowData, 'modul') )    ;
        
    }

    /**
     *
     * @param LoginInterface $login
     * @param array $rowData
     */
    public function extract(EntityInterface $login, ArrayAccess $rowData) {
        /** @var LoginInterface $login */
        $this->setSqlValue( $rowData, 'login_name', $login->getLoginName());         
        $this->setSqlValue( $rowData, 'role', $login->getRole() );
        $this->setSqlValue( $rowData, 'email', $login->getEmail() );
        $this->setSqlValue( $rowData, 'info', $login->getInfo() );
        $this->setSqlValue( $rowData, 'modul', $login->getModul() );                
    }

}
