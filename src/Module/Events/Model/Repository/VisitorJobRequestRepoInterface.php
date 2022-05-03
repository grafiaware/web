<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Repository;

use Model\Repository\RepoInterface;

use Events\Model\Entity\VisitorJobRequestInterface;

/**
 *
 * @author pes2704
 */
interface VisitorJobRequestRepoInterface extends RepoInterface {
    /**
     *
     * @param string $loginName
     * @param string $shortName
     * @param string $positionName
     * @return VisitorJobRequestInterface|null
     */
    public function get($loginName, $shortName, $positionName): ?VisitorJobRequestInterface;

    public function add(VisitorJobRequestInterface $visitorDataPost);

    public function remove(VisitorJobRequestInterface $visitorDataPost);
}
