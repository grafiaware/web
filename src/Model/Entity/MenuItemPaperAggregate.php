<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

use Model\Entity\MenuItemPaperAggregateInterface;
use Model\Entity\MenuItem;
use Model\Entity\Paper;
use Model\Entity\PaperContent;

/**
 * Description of Article
 *
 * @author pes2704
 */
class MenuItemPaperAggregate extends MenuItem implements MenuItemPaperAggregateInterface {

    /**
     * @var PaperAggregateInterface
     */
    private $paper;

    /**
     *
     * @return PaperAggregateInterface
     */
    public function getPaper(): PaperAggregateInterface {
        return $this->paper;
    }

    /**
     *
     * @param Paper $paperAggregate
     * @return \Model\Entity\MenuItemPaperAggregateInterface
     */
    public function setPaper(PaperAggregateInterface $paperAggregate): MenuItemPaperAggregateInterface {
        $this->paper = $paperAggregate;
        return $this;
    }

}
