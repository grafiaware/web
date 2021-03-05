<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

use Model\Entity\MenuItemAggregatePaperInterface;
use Model\Entity\MenuItem;
use Model\Entity\Paper;
use Model\Entity\PaperContent;

/**
 * Description of Article
 *
 * @author pes2704
 */
class MenuItemAggregatePaper extends MenuItem implements MenuItemAggregatePaperInterface {

    /**
     * @var PaperAggregatePaperContentInterface
     */
    private $paper;

    /**
     *
     * @return PaperAggregatePaperContentInterface
     */
    public function getPaper(): PaperAggregatePaperContentInterface {
        return $this->paper;
    }

    /**
     *
     * @param Paper $paperAggregate
     * @return \Model\Entity\MenuItemAggregatePaperInterface
     */
    public function setPaper(PaperAggregatePaperContentInterface $paperAggregate): MenuItemAggregatePaperInterface {
        $this->paper = $paperAggregate;
        return $this;
    }

}
