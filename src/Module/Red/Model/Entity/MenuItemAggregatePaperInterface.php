<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Red\Model\Entity\MenuItemAggregatePaperInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;

/**
 * Description of PaperInterface
 *
 * @author pes2704
 */
interface MenuItemAggregatePaperInterface extends MenuItemInterface {

    /**
     *
     * @return PaperAggregatePaperSectionInterface
     */
    public function getPaper(): PaperAggregatePaperSectionInterface;

    public function setPaper(PaperAggregatePaperSectionInterface $headline): MenuItemAggregatePaperInterface;

}
