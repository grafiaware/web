<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Repository;

use Model\Repository\RepoInterface;

use Events\Model\Entity\VisitorDataPostInterface;

/**
 *
 * @author pes2704
 */
interface VisitorDataPostRepoInterface extends RepoInterface {
    /**
     *
     * @param string $loginName
     * @param string $shortName
     * @param string $positionName
     * @return VisitorDataPostInterface|null
     */
    public function get($loginName, $shortName, $positionName): ?VisitorDataPostInterface;

    public function add(VisitorDataPostInterface $visitorDataPost);

    public function remove(VisitorDataPostInterface $visitorDataPost);
}
