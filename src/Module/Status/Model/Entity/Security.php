<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Status\Model\Entity;

use Model\Entity\PersistableEntityAbstract;
use Status\Model\Entity\SecurityInterface;

use Auth\Model\Entity\LoginAggregateFullInterface;
use Red\Model\Entity\EditorActions;
use Red\Model\Entity\EditorActionsInterface;
use Events\Model\Entity\RepresentationActions;
use Events\Model\Entity\RepresentationActionsInterface;

/**
 * Description of Security
 *
 * @author pes2704
 */
class Security extends PersistableEntityAbstract implements SecurityInterface {

    /**
     * @var LoginAggregateFullInterface
     */
    private $loginAggregate;
    
    private $userNameVerifyedWithinSession = [];
    
    private $loggedOffUserName;
    
    /**
     * @var EditorActionsInterface
     */
    private $editorActions;
    
    /**
     * 
     * @var RepresentationActionsInterface
     */
    private $represantativeActions;
    
    private $info = [];

    /**
     * {@inheritdoc}
     * 
     * @param LoginAggregateFullInterface $loginAggregate
     * @return void
     */
    #[\Override]
    public function newContext(LoginAggregateFullInterface $loginAggregate): SecurityInterface {
        $this->loggedOffUserName = null;        
        $this->loginAggregate = $loginAggregate;
        $this->editorActions = new EditorActions();
        $this->represantativeActions = new RepresentationActions();
        return $this;
    }
    
    /**
     * {@inheritdoc}
     * 
     * @return SecurityInterface
     */
    #[\Override]
    public function removeContext(): SecurityInterface {
        $this->processActionsForLossOfSecurityContext($this->loginAggregate?->getLoginName());
        return $this;
    }
    
    /**
     * {@inheritdoc}
     * 
     * @param string|null $loggedOffUserName
     */
    #[\Override]
    public function processActionsForLossOfSecurityContext(?string $loggedOffUserName=null) {
        if (isset($this->editorActions)) {
           $this->editorActions->processActionsForLossOfSecurityContext($loggedOffUserName);
        }            
        if (isset($this->represantativeActions)) {
           $this->represantativeActions->processActionsForLossOfSecurityContext($loggedOffUserName);
        }
        $this->loggedOffUserName = $loggedOffUserName;      // uložení login name pro další request
        $this->loginAggregate = null;
        $this->info = [];        
    }    

    #[\Override]
    public function hasValidSecurityContext(): bool {
        return isset($this->loginAggregate) AND $this->loginAggregate->isPersisted();
    }

    ### UserNameVerifyedWithinSession
    
    #[\Override]
    public function addUserNameVerifyedWithinSession(string $loginName): void {
        $this->userNameVerifyedWithinSession[$loginName] = true;  // jméno jako klíč - nevzniknou duplicity
    }
    
    #[\Override]
    public function removeUserNameVerifyedWithinSession(string $loginName): void {
        unset($this->userNameVerifyedWithinSession[$loginName]);
    }
    
    #[\Override]
    public function isUserNameVerifyedWithinSession(string $loginName): bool {
        return $this->userNameVerifyedWithinSession[$loginName] ?? false;
    }
    
    ### GETTERY

    #[\Override]
    public function lastLoggedOffUsername(): ?string {
        return $this->loggedOffUserName;
    }
    
    #[\Override]
    public function getLoginAggregate(): ?LoginAggregateFullInterface {
        return $this->loginAggregate;
    }

    #[\Override]
    public function getEditorActions(): ?EditorActionsInterface {
        return $this->editorActions;
    }
    
    #[\Override]
    public function getRepresentativeActions(): ?RepresentationActionsInterface {
        return $this->represantativeActions;
    }
    
    #[\Override]
    public function setInfo(string|int $name, mixed $value) {
        $this->info[$name] = $value;
    }
    
    #[\Override]
    public function getInfo(string|int $name): mixed {
        return $this->info[$name] ?? null;
    }
    
    #[\Override]
    public function getInfos(): array {
        return $this->info;
    }    
}
