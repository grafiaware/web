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
use Events\Model\Entity\RepresentativeActionsInterface;

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

    //TODO: getEditorActions() a getRepresentativeActions() pryč -> nový StatusSecurityEditor (+repo, dao) do modulu RED a nový StatusSecurityRepresentative do modulu EVENTS, obě repo 
    // budou repliky StatusSecurityRepo s const FRAGMENT_NAME = 'security.editor'; a const FRAGMENT_NAME = 'security.representative'; => entity se budou ukládat do $_SESSION
    // jako fragmenty pod fragment security - viz Pes\Session\SessionStatusHandler
    // a) smazáním StatusSecurity se (snad!!) smaže celý secirity fragment ze session a tím automaticky i security.editor a security.representative fragmenty
    // b) StatusSecurityEditorRepo->get() vrací typově správně StatusSecurityEditor a StatusSecurityRepresentativeRepo->get() vrací typově správně StatusSecurityRepresentative atd.
    // podmínka: nový StatusSecurityEditor (+repo, dao) do modulu RED a nový StatusSecurityRepresentative do modulu EVENTS => nedá se používat v jiném modulu -> statusSecurity musíš vymýtit
    // z modulu WEB /např. PresentationFrontControlerAbstract, Prepare (????!!), LayoutControllerAbstract
    
    
    /**
     *
     * @return EditorActionsInterface|null
     */
    public function getEditorActions(): ?EditorActionsInterface;
    
    /**
     * 
     * @return RepresentativeActionsInterface|null
     */
    public function getRepresentativeActions(): ?RepresentativeActionsInterface; 
    
    public function setInfo($name, $value);
    
    public function getInfo($name);

    public function getInfos(): array;        
}
