<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Model\Repository;

use Model\Repository\RepoInterface;

use Auth\Model\Entity\LoginInterface;

/**
 *
 * @author pes2704
 */
interface LoginRepoInterface extends RepoInterface {

    /**
     *
     * @param type $loginName
     * @return LoginInterface|null
     */
    public function get($loginName): ?LoginInterface;

    /**
     *
     * @param type $whereClause
     * @param type $touplesToBind
     * @return LoginInterface[]
     */
    public function find($whereClause="", $touplesToBind=[]): array  ;
       
    
    /**
     *
     * @param LoginInterface $login
     */
    public function add(LoginInterface $login);

    /**
     *
     * @param LoginInterface $login
     */
    public function remove(LoginInterface $login);
}
