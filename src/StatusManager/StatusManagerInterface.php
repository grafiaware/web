<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace StatusManager;

use Model\Entity\EntitySingletonInterface;
use Model\Entity\LoginAggregateCredentialsInterface;

/**
 *
 * @author pes2704
 */
interface StatusManagerInterface {

    public function createStatus(): EntitySingletonInterface;


    public function renewStatus(EntitySingletonInterface $loginAggregate=null): void;
}
