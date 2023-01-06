<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Entity\PersistableEntityInterface;

/**
 * Interface pro POTOMKOVSKÉ repository s asociací 1:1
 *
 * @author pes2704
 */
interface RepoAssotiatedOneInterface extends RepoInterface {
    public function getByReference(string $referenceName, ...$referenceParams): ?PersistableEntityInterface;
}
