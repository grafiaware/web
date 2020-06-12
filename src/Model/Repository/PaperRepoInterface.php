<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Entity\PaperInterface;

/**
 *
 * @author pes2704
 */
interface PaperRepoInterface {
    public function get($id): ?PaperInterface;
    public function getByFk($menuItemIdFk): ?PaperInterface;
    public function add(PaperInterface $paper);
    public function remove(PaperInterface $paper);
}
