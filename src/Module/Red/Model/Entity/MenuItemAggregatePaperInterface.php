<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Red\Model\Entity\MenuItemAggregatePaperInterface;
use Red\Model\Entity\PaperAggregatePaperContentInterface;

/**
 * Description of PaperInterface
 *
 * @author pes2704
 */
interface MenuItemAggregatePaperInterface extends MenuItemInterface {

    /**
     *
     * @return PaperAggregatePaperContentInterface
     */
    public function getPaper(): PaperAggregatePaperContentInterface;

    public function setPaper(PaperAggregatePaperContentInterface $headline): MenuItemAggregatePaperInterface;

}
