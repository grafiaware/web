<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Status\Model\Entity;

use Model\Entity\PersistableEntityInterface;

use Auth\Model\Entity\LoginAggregateFullInterface;
use Red\Model\Entity\EditorActionsInterface;

/**
 * Třída nemá metodu getUser(), nikdy nevrací celý objekt User. Tak nelze měnit vlastnosti objektu User získaného z StatusSecurity.
 * Nelze použít:
 * <code>
 * $statusSecurity->getUser()->setUserName('Adam');
 * </code>
 * Lze nastavit jen nový objekt User metodou setUser(). Jde o malou zábranu změnám vlastností objektu User bez změny ostatních objektů
 * v bezpečnostním kontextu.
 *
 * @author pes2704
 */
interface StatusSecurityInterface extends PersistableEntityInterface {

    /**
     * Odstraní parametry. Určeno pro volání při záníku securiry kontextu nebo pokud se zjistí, že security kontext neexistuje.
     * Zypicky po odhlášení uživatele nebo při zpracování požadavku a zjištění, že uživatžel není přihlášen.
     * 
     * @param LoginAggregateFullInterface $loginAggregate
     * @return void
     */
    public function removeContext(): StatusSecurityInterface;

    /**
     * Nastaví výchozí parametry po vzniku security kontextu. Určeno pro volání po přihlášení uživatele.
     * 
     * @param LoginAggregateFullInterface $loginAggregate
     * @return StatusSecurityInterface
     */
    public function new(LoginAggregateFullInterface $loginAggregate): StatusSecurityInterface;

    /**
     * Informuje, zda security kontext existuje a zda je platný.
     * 
     * @return bool
     */
    public function hasValidSecurityContext(): bool;

    /**
     *
     * @return LoginAggregateFullInterface|null
     */
    public function getLoginAggregate(): ?LoginAggregateFullInterface;

    /**
     *
     * @return EditorActionsInterface|null
     */
    public function getEditorActions(): ?EditorActionsInterface;
    
    public function setInfo($name, $value);
    
    public function getInfo($name);

    public function getInfos(): array;        
}
