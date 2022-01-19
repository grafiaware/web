<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored;

use Component\ViewModel\StatusViewModelInterface;
use Red\Model\Entity\ItemActionInterface;
use Red\Model\Entity\MenuItemInterface;

/**
 *
 * @author pes2704
 */
interface AuthoredViewModelInterface extends StatusViewModelInterface {

    public function getItemType();
    public function getItemId();
    public function setItemId($menuItemId);

    /**
     * Informuje, zda menu item je aktivní - prezentovaný.
     * @return bool
     */
    public function isMenuItemActive(): bool;

    public function seekTemplate($templatesType, $templateName);
    public function getItemAction(): ?ItemActionInterface;
    public function getMenuItem(): MenuItemInterface;
    public function userPerformActionWithItem(): bool;
}
