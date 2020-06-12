<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

use Model\Entity\MenuItemPaperAggregateInterface;
use Model\Entity\PaperPaperContentsAggregateInterface;

/**
 * Description of PaperInterface
 *
 * @author pes2704
 */
interface MenuItemPaperAggregateInterface extends MenuItemInterface {

    /**
     *
     * @return PaperPaperContentsAggregateInterface
     */
    public function getPaper(): PaperPaperContentsAggregateInterface;

    public function setPaper(PaperPaperContentsAggregateInterface $headline): MenuItemPaperAggregateInterface;

}
