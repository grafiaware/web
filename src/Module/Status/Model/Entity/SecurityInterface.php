<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Status\Model\Entity;

use Model\Entity\SecurityPersistableEntityInterface;

use Auth\Model\Entity\LoginAggregateFullInterface;
use Red\Model\Entity\EditorActionsInterface;
use Events\Model\Entity\RepresentationActionsInterface;

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
interface SecurityInterface extends SecurityPersistableEntityInterface {

    /**
     * Nastaví výchozí parametry po vzniku security kontextu. Určeno pro volání po přihlášení uživatele.
     * 
     * @param LoginAggregateFullInterface $loginAggregate
     * @return SecurityInterface
     */
    public function newContext(LoginAggregateFullInterface $loginAggregate): SecurityInterface;
    
    /**
     * Odstraní parametry. Určeno pro volání při záníku securiry kontextu nebo pokud se zjistí, že security kontext neexistuje.
     * Zypicky po odhlášení uživatele nebo při zpracování požadavku a zjištění, že uživatžel není přihlášen.
     * 
     * @param LoginAggregateFullInterface $loginAggregate
     * @return void
     */
    public function removeContext(): SecurityInterface;
        
    /**
     * Informuje, zda security kontext existuje a zda je platný.
     * 
     * @return bool
     */
    public function hasValidSecurityContext(): bool; 
    
    /**
     * Vrací login name uložené metodou processActionsForLossOfSecurityContext.
     * Metoda použita pro smazání údajů vázaných na login na v databázi - to proběhne až při příštím GET requestu.
     * 
     * @return string|null
     */
    public function lastLoggedOffUsername(): ?string;
    
    /**
     * Přidá informaci, že uživatel s login name byl již v průběhu session ověřen.
     * @param string $loginName
     * @return void
     */
    public function addUserNameVerifiedWithinSession(string $loginName): void;
    
    /**
     * Smaže informaci, že uživatel s login name byl již v průběhu session ověřen.
     * @param string $loginName
     * @return void
     */
    public function removeUserNameVerifiedWithinSession(string $loginName): void;
    
    /**
     * Poskytne informaci, jestli uživatel s login name byl již v průběhu session ověřen.
     * @param string $loginName
     * @return bool
     */
    public function isUserNameVerifiedWithinSession(string $loginName): bool;
    
    /**
     * Vrací LoginAggregateFull - login s credentials a registration
     *
     * @return LoginAggregateFullInterface|null
     */
    public function getLoginAggregate(): ?LoginAggregateFullInterface;
    
    /**
     * 
     * @return EditorActionsInterface|null
     */
    public function getEditorActions(): ?EditorActionsInterface;
    
    /**
     * 
     * @return RepresentationActionsInterface|null
     */
    public function getRepresentativeActions(): ?RepresentationActionsInterface; 
    
    /**
     * Nastaví (přidá) informaci jako dvojici jméno->hodnota.
     * 
     * @param string|int $name
     * @param mixed $value
     */
    public function setInfo(string|int $name, mixed $value);
    
    /**
     * Vrací informaci nastavenou (přidanou) s daným jménem.
     * 
     * @param string|int $name
     * @return mixed
     */
    public function getInfo(string|int $name): mixed;

    /**
     * Vrací všechny nastavené (přidané) informace jako asociativní pole.
     * 
     * @return array
     */
    public function getInfos(): array;        
}
