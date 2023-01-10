<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository\Association;

use Model\Hydrator\HydratorInterface;
use Model\Repository\RepoInterface;

/**
 * Description of AssotiationAbstract
 *
 * @author pes2704
 */
abstract class ManyAbstract extends AssociationAbstract implements AssociationInterface {

    /**
     * @var RepoInterface
     */
    protected $childRepo;  // zde je jen pro případ, že by nebyla definována v konkrétní asociaci

    /**
     *
     * @param RepoAssotiatedOneInterface $childRepo
     */
    public function __construct(RepoAssotiatedOneInterface $childRepo) {
        $this->childRepo = $childRepo;
    }


}
