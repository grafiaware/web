<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Repository;

use Model\Repository\RepoInterface;
use Events\Model\Entity\EventLinkInterface;

/**
 *
 * @author pes2704
 */
interface EventLinkRepoInterface extends RepoInterface  {
  /**
     *
     * @param string $id
     * @return EventLinkInterface|null
     */
    public function get($id): ?EventLinkInterface ;

    public function find($whereClause="", $touplesToBind=[]): array;
    public function findAll() :array ;


    public function add(EventLinkInterface $eventContentType);


    public function remove(EventLinkInterface $eventContent) ;


}
