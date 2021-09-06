<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Model\Entity\EntityAbstract;
use Red\Model\Entity\ItemActionInterface;

/**
 * Description of StatusPresentation
 *
 * @author pes2704
 */
class UserActions extends EntityAbstract implements UserActionsInterface {

    private $editLayout = false;
    private $editPaper = false;
    private $editMenu = false;
    private $userItemAction = [];


    /**
     * Informuje, zda prezentace je přepnuta do modu editace layoutu.
     *
     * @return bool
     */
    public function presentEditableLayout(): bool {
        return $this->editLayout;
    }

    /**
     * Informuje, zda prezentace je přepnuta do modu editace článku.
     *
     * @return bool
     */
    public function presentEditableArticle(): bool {
        return $this->editPaper;
    }

    /**
     * Informuje, zda prezentace je přepnuta do modu editace menu.
     *
     * @return bool
     */
    public function presentEditableMenu(): bool {
        return $this->editMenu;
    }

    /**
     * Nastaví informaci, že prezentace je přepnuta do modu editace layoutu.
     *
     * @param mixed $editLayout Metoda převede zadanou hodnotu na boolen hodnotu.
     * @return UserActionsInterface
     */
    public function setEditableLayout($editLayout): UserActionsInterface {
        $this->editLayout = boolval($editLayout);
        return $this;
    }

    /**
     * Nastaví informaci, že prezentace je přepnuta do modu editace článku
     *
     * @param mixed $editPaper Metoda převede zadanou hodnotu na boolen hodnotu.
     * @return UserActionsInterface
     */
    public function setEditableArticle($editPaper): UserActionsInterface {
        $this->editPaper = boolval($editPaper);
        return $this;
    }

    /**
     * Nastaví informaci, že pretentace je přepnuta do modu editace menu
     *
     * @param mixed $editableMenu Metoda převede zadanou hodnotu na boolen hodnotu.
     * @return UserActionsInterface
     */
    public function setEditableMenu($editableMenu): UserActionsInterface {
        $this->editMenu = boolval($editableMenu);
        return $this;
    }

    ### user actions ###

    public function addUserItemAction(ItemActionInterface $itemAction): void {
        $this->userItemAction[$itemAction->getTypeFk()][$itemAction->getItemId()] = $itemAction;
    }

    public function removeUserItemAction(ItemActionInterface $itemAction): void {
        unset($this->userItemAction[$itemAction->getTypeFk()][$itemAction->getItemId()]);
    }

    public function hasUserAction($typeFk, $itemId): bool {
        return (array_key_exists($typeFk, $this->userItemAction) AND array_key_exists($itemId, $this->userItemAction[$typeFk]));
    }

    public function getUserAction($typeFk, $itemId): ?ItemActionInterface {
        return $this->hasUserAction($typeFk, $itemId) ? $this->userItemAction[$typeFk][$itemId] : null;
    }
}
