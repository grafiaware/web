<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored\Paper;

use Component\ViewModel\Authored\AuthoredViewModelInterface;
use Red\Model\Entity\PaperAggregatePaperContentInterface;
use Red\Model\Entity\PaperInterface;
use Red\Model\Entity\MenuItemInterface;

/**
 *
 * @author pes2704
 */
interface PaperViewModelInterface extends AuthoredViewModelInterface {

    public function setItemId($menuItemId);

    /**
     * Pro existující paper vrací PaperAggregate, pokud paper neexistuje vrací vově vytvořený Paper.
     * <ul><li>
     * Pokud byla zadána hodnota setMenuItemId() a zadaný menu item obsahuje Paper vrací PaperAggregate (potomek Paper) příslušný k menuItem.</li><li>
     * Pokud byla zadána hodnota setMenuItemId() a zadaný menu item neobsahuje Paper vrací nově vytvořený Paper k menuItem.</li><li>
     *
     * @return PaperInterface|null
     */
    public function getPaper(): ?PaperInterface;

    public function isEditableArticle(): bool;
}
