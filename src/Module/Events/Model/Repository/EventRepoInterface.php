<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Repository;

use Model\Repository\RepoInterface;
use Events\Model\Entity\EventInterface;


/**
 *
 * @author pes2704
 */
interface EventRepoInterface extends RepoInterface  {

  /**
     *
     * @param type $id
     * @return EventInterface|null
     */
    public function get($id): ?EventInterface;
    

    public function findAll(): array;
    

    public function add(EventInterface $event);
    

    public function remove(EventInterface $event);
    
    
    
}
