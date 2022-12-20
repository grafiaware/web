<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\RowHydratorInterface;
use Model\RowData\RowDataInterface;

use Auth\Model\Entity\LoginAggregateCredentialsInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class LoginAggregateHydrator implements RowHydratorInterface {

    /**
     *
     * @param EntityInterface $login
     * @param type $rowData
     */
    public function hydrate(EntityInterface $loginAggregate, RowDataInterface $rowData) {
        /** @var LoginAggregateCredentialsInterface $loginAggregate */
        $loginAggregate->setLoginName($rowData->offsetGet('login_name'));
        $loginAggregate->setCredentials($rowData->offsetGet('credentials'));
    }

    public function extract(EntityInterface $entity, RowDataInterface $rowData) {
        ;
    }
}
