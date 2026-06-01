<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Model\Entity\SecurityPersistableEntityInterface;
use Red\Model\Entity\ItemActionInterface;
use Auth\Model\Entity\LoginInterface;

/**
 *
 * @author pes2704
 */
interface EditorActionsInterface extends SecurityPersistableEntityInterface {

    const STATUS_INFO_KEY = "editor_actions";
    
    /**
     * Informuje, zda prezentace je přepnuta do modu editace článku.
     *
     * @return bool
     */
    public function presentEditableContent(): bool;

    /**
     * Nastaví informaci, že prezentace je přepnuta do modu editace článku
     *
     * @param type $editablePaper
     * @return EditorActionsInterface
     */
    public function setEditableContent($editablePaper): EditorActionsInterface;

    public function addItemAction(ItemActionInterface $itemAction): void;
    public function removeItemAction($itemId): void ;
    public function getItemAction($itemId): ?ItemActionInterface;
    public function hasItemAction($itemId): bool;
}
