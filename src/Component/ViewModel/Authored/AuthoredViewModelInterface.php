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

    const DEFAULT_TEMPLATE_NAME = 'default';

    public function setItemId($menuItemId);

    public function getItemType();
    public function getItemId();
    public function getAuthoredTemplateName();

    /**
     * Vrací id entity, kretá tvoří zobrazovaný obsah - např article, paper, multipge.
     * Spolu s hodnotou ItemType slouží ke generování url pro renderované ovládací prvky
     */
    public function getAuthoredContentId();

    /**
     * Informuje, zda menu item je aktivní - prezentovaný.
     * @return bool
     */
    public function isMenuItemActive(): bool;

    /**
     * Vyhledá plné jméno souboru s template itemu.
     * @return string Cesta k souboru s template nebo prázdný řetězec
     */
    public function seekTemplate(): string;
    public function getItemAction(): ?ItemActionInterface;
    public function getMenuItem(): MenuItemInterface;
    public function userPerformActionWithItem(): bool;
}
