<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Repository;

use Model\Repository\RepoInterface;
use Events\Model\Entity\EventContentInterface;

/**
 *
 * @author pes2704
 */
interface EventLinkRepoInterface extends RepoInterface  {
  /**
     *
     * @param string $id
     * @return EventContentInterface|null
     */
    public function get($id): ?EventContentInterface ;

    public function find($whereClause="", $touplesToBind=[]): array;
    public function findAll() :array ;


    public function add(EventContentInterface $eventContentType);


    public function remove(EventContentInterface $eventContent) ;


}