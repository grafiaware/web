<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Model\Entity\PersistableEntityAbstract;
use Red\Model\Entity\ItemActionInterface;
use Auth\Model\Entity\LoginInterface;
/**
 * Description of UserActions
 *
 * UserActions je součástí StatusPresentation, ukládána do session prostřednictvím StatusPresentationRepo.
 * Obsahuje stav prezentace a také akce uživatele - ItemActionInterface entity
 *
 * @author pes2704
 */
class UserActions extends PersistableEntityAbstract implements UserActionsInterface {

    private $editableContent = false;
    private $editableMenu = false;
    private $userItemActions = [];
    
    private $loggedOffUserName;


    /**
     * Informuje, zda prezentace je přepnuta do modu editace článku.
     *
     * @return bool
     */
    public function presentEditableContent(): bool {
        return $this->editableContent;
    }

    /**
     * Informuje, zda prezentace je přepnuta do modu editace menu.
     *
     * @return bool
     */
    public function presentEditableMenu(): bool {
        return $this->editableMenu;
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
        $this->editableContent = boolval($editPaper);
        return $this;
    }

    /**
     * Nastaví informaci, že pretentace je přepnuta do modu editace menu
     *
     * @param mixed $editableMenu Metoda převede zadanou hodnotu na boolen hodnotu.
     * @return UserActionsInterface
     */
    public function setEditableMenu($editableMenu): UserActionsInterface {
        $this->editableMenu = boolval($editableMenu);
        return $this;
    }
    
    /**
     * Uloží Login entitu pro použití v jiném middleware a příštím requestu, odstraní stav editable content a editable menu.
     * 
     * Akce spojené s ItemActions je pak třeba provést v budoucnu, v middleware s přístupem k databázi s uloženými informacemi zavíslými na ItemActions.
     * 
     * @param string $loggedOffUserName
     */
    public function processActionsForLossOfSecurityContext($loggedOffUserName) {
        $this->loggedOffUserName = $loggedOffUserName;
        $this->setEditableContent(false);
        $this->setEditableMenu(false);
        unset($this->userItemActions);
    }
    
    /**
     * {@inheritDoc}
     * 
     * @return string|null
     */
    public function lastLoggedOffUsername(): ?string {
        $lo = $this->loggedOffUserName;
        unset($this->loggedOffUserName);
        return $lo;
    }

    ### item actions ###

    public function addItemAction(ItemActionInterface $itemAction): void {
        $this->userItemActions[$itemAction->getItemId()] = $itemAction;
    }

    public function removeItemAction($itemId): void {
        unset($this->userItemActions[$itemId]);
    }

    public function hasItemAction($itemId): bool {
        return (array_key_exists($itemId, $this->userItemActions));
    }

    public function getItemAction($itemId): ?ItemActionInterface {
        return $this->hasItemAction($itemId) ? $this->userItemActions[$itemId] : null;
    }
}
