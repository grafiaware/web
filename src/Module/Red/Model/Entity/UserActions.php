<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Model\Entity\PersistableEntityAbstract;
use Red\Model\Entity\ItemActionInterface;

/**
 * Description of UserActions
 *
 * UserActions je součástí StatusPresentation, ukládána do session prostřednictvím StatusPresentationRepo.
 * Obsahuje stav prezentace a také akce uživatele - ItemActionInterface entity
 *
 * @author pes2704
 */
class UserActions extends PersistableEntityAbstract implements UserActionsInterface {

    private $editArticle = false;
    private $editMenu = false;
    private $userItemAction = [];

    /**
     * Informuje, zda je některá část prezentace přepnuta do editačního módu.
     *
     * @return bool
     */
    public function presentAnyInEditableMode(): bool {
        return  $this->presentEditableContent() OR $this->presentEditableMenu();
    }

    /**
     * Informuje, zda prezentace je přepnuta do modu editace článku.
     *
     * @return bool
     */
    public function presentEditableContent(): bool {
        return $this->editArticle;
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
//    public function setEditableLayout($editLayout): UserActionsInterface {
//        $this->editLayout = boolval($editLayout);
//        return $this;
//    }

    /**
     * Nastaví informaci, že prezentace je přepnuta do modu editace článku
     *
     * @param mixed $editPaper Metoda převede zadanou hodnotu na boolen hodnotu.
     * @return UserActionsInterface
     */
    public function setEditableContent($editPaper): UserActionsInterface {
        $this->editArticle = boolval($editPaper);
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

    public function hasUserItemAction($typeFk, $itemId): bool {
        return (array_key_exists($typeFk, $this->userItemAction) AND array_key_exists($itemId, $this->userItemAction[$typeFk]));
    }

    public function getUserItemAction($typeFk, $itemId): ?ItemActionInterface {
        return $this->hasUserItemAction($typeFk, $itemId) ? $this->userItemAction[$typeFk][$itemId] : null;
    }
}
