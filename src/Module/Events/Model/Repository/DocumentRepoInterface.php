<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Repository;

use Model\Repository\RepoInterface;

use Events\Model\Entity\DocumentInterface;

/**
 *
 * @author pes2704
 */
interface DocumentRepoInterface extends RepoInterface {
    public function get($id): ?DocumentInterface;
    public function add(DocumentInterface $document);
    public function remove(DocumentInterface $document);
}
