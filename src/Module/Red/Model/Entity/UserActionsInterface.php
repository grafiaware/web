<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Model\Entity\PersistableEntityInterface;
use Red\Model\Entity\ItemActionInterface;

/**
 *
 * @author pes2704
 */
interface UserActionsInterface extends PersistableEntityInterface {

    /**
     * Informuje, zda je některá část prezentace přepnuta do editačního módu.
     *
     * @return bool
     */
    public function presentAnyInEditableMode(): bool;

    /**
     * Informuje, zda prezentace je přepnuta do modu editace článku.
     *
     * @return bool
     */
    public function presentEditableContent(): bool;

    /**
     * Informuje, zda prezentace je přepnuta do modu editace menu.
     *
     * @return bool
     */
    public function presentEditableMenu(): bool;

    ###

    /**
     * Nastaví informaci, že prezentace je přepnuta do modu editace layoutu.
     *
     * @param type $editableLayout
     * @return UserActionsInterface
     */
//    public function setEditableLayout($editableLayout): UserActionsInterface;

    /**
     * Nastaví informaci, že prezentace je přepnuta do modu editace článku
     *
     * @param type $editablePaper
     * @return UserActionsInterface
     */
    public function setEditableContent($editablePaper): UserActionsInterface;

    /**
     * Nastaví informaci, že pretentace je přepnuta do modu editace menu
     *
     * @param type $editableMenu
     * @return UserActionsInterface
     */
    public function setEditableMenu($editableMenu): UserActionsInterface;

    public function addItemAction(ItemActionInterface $itemAction): void;
    public function removeItemAction(ItemActionInterface $iemAction): void;
    public function getItemAction($itemId): ?ItemActionInterface;
    public function hasItemAction($itemId): bool;
}
