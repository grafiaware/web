<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Model\Repository;

use Auth\Model\Entity\LoginAggregateFullInterface;

/**
 *
 * @author pes2704
 */
interface LoginAggregateFullRepoInterface  extends LoginRepoInterface {
    public function get($loginName): ?LoginInterface;
    public function findByRole($role);
}