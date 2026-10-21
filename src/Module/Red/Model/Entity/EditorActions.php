<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Pes\Model\Entity\PersistableEntityAbstract;
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
class EditorActions extends PersistableEntityAbstract implements EditorActionsInterface {

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
     * Nastaví informaci, že prezentace je přepnuta do modu editace
     *
     * @param mixed $editContent Metoda převede zadanou hodnotu na boolean hodnotu.
     * @return EditorActionsInterface
     */
    public function setEditableContent($editContent): EditorActionsInterface {
        $this->editableContent = boolval($editContent);
        return $this;
    }
    
    /**
     * {@inheritdoc}
     * 
     * @param string|null $loggedOffUserName
     */
    #[\Override]
    public function processActionsForLossOfSecurityContext(?string $loggedOffUserName=null) {
        $this->setEditableContent(false);
        unset($this->userItemActions);
    }
    
    /**
     * Vrací POUZE JEDNOU login name uložené metodou processActionsForLossOfSecurityContext. Uložený login name smaže.
     * Metoda použita pro smazání ItemAction v RED databázi - to proběhne až při příštím GET requestu.
     * 
     * @return LoginInterface
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
