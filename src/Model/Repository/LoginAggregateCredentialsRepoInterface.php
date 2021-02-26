<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Entity\LoginAggregateCredentialsInterface;

/**
 *
 * @author pes2704
 */
interface LoginAggregateCredentialsRepoInterface  extends LoginRepoInterface {
    public function get($loginName): ?LoginAggregateCredentialsInterface;

}
