<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Model\Entity\PersistableEntityInterface;
use Red\Model\Entity\ItemActionInterface;
use Auth\Model\Entity\LoginInterface;

/**
 *
 * @author pes2704
 */
interface UserActionsInterface extends PersistableEntityInterface {

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
    
    /**
     * Metoda provede ptřebná nastavení UserAction v situaci ztráty security kontextu (typicky při odhlášení uživatele). 
     * Metoda přijímá login jméno uživatele, který se právě odhlásil. Nastavení této informace lze pak použít v následném requestu, 
     * který bude přistupovat k databázi se zápisem informací závislých na přihlášeném uživateli.
     * 
     * Entita UserAction je součástí statusu ukládaného do session dat. Tuto metodu tak lze volat z libivolného middleware (například Auth) 
     * a uloženou informaci pak použít v jiném middleware - např. v middleware přípravujícím prezentaci.
     * 
     * @param string $loggedOffUserName
     */
    public function processActionsForLossOfSecurityContext($loggedOffUserName);
    
    /**
     * Vrací POUZE JEDNOU login name uložené metodou processActionsForLossOfSecurityContext. Uložený login name smaže.
     * 
     * @return LoginInterface
     */
    public function provideLoggedOffUsername(): ?string;
    public function addItemAction(ItemActionInterface $itemAction): void;
    public function removeItemAction($itemId): void ;
    public function getItemAction($itemId): ?ItemActionInterface;
    public function hasItemAction($itemId): bool;
}
