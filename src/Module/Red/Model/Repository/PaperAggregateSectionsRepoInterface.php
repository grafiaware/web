<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Model\Repository\RepoInterface;

use Red\Model\Entity\PaperAggregatePaperSectionInterface;

/**
 *
 * @author pes2704
 */
interface PaperAggregateSectionsRepoInterface extends RepoInterface {
    public function get($id): ?PaperAggregatePaperSectionInterface;
    public function getByMenuItemId($menuItemId): ?PaperAggregatePaperSectionInterface;
    public function add(PaperAggregatePaperSectionInterface $paper);
    public function remove(PaperAggregatePaperSectionInterface $paper);
}
