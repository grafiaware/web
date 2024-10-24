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
        $login->setLoginName( $this->getPhpValue( $rowData, 'login_name'));
        
    }

    /**
     *
     * @param LoginInterface $login
     * @param array $rowData
     */
    public function extract(EntityInterface $login, ArrayAccess $rowData) {
        /** @var LoginInterface $login */
         $this->setSqlValue( $rowData, 'login_name', $login->getLoginName());
                
    }

}
