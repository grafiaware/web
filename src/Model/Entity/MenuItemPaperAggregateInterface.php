<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

use Model\Entity\MenuItemPaperAggregateInterface;
use Model\Entity\PaperAggregateInterface;

/**
 * Description of PaperInterface
 *
 * @author pes2704
 */
interface MenuItemPaperAggregateInterface extends MenuItemInterface {

    /**
     *
     * @return PaperAggregateInterface
     */
    public function getPaper(): PaperAggregateInterface;

    public function setPaper(PaperAggregateInterface $headline): MenuItemPaperAggregateInterface;

}
