<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

use Model\Entity\MenuItemPaperAggregateInterface;
use Model\Entity\PaperHeadline;
use Model\Entity\PaperContent;

/**
 * Description of PaperInterface
 *
 * @author pes2704
 */
interface MenuItemPaperAggregateInterface extends MenuItemInterface {

    /**
     *
     * @return PaperHeadline
     */
    public function getPaperHeadline(): PaperHeadline;

    /**
     *
     * @param type $id
     * @return PaperContent|null
     */
    public function getPaperContent($id): ?PaperContent;

    /**
     *
     * @return PaperContent array of
     */
    public function getPaperContentsArray(): array;

    public function setPaperHeadline(PaperHeadline $headline): MenuItemPaperAggregateInterface;

    public function exchangePaperContentsArray(array $contents=[]): MenuItemPaperAggregateInterface;
}
