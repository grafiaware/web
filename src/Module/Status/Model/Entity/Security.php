<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Status\Model\Entity;

use Model\Entity\PersistableEntityAbstract;

use Model\Entity\SecurityPersistableEntityInterface;

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
     * @return SecurityInterface
     */
    #[\Override]
    public function removeContext(): SecurityInterface {
        if (isset($this->loginAggregate)) {
            if (isset($this->editorActions)) {
               $this->editorActions->processActionsForLossOfSecurityContext($this->loginAggregate->getLoginName());
            }            
            if (isset($this->represantativeActions)) {
               $this->represantativeActions->processActionsForLossOfSecurityContext($this->loginAggregate->getLoginName());
            }
            $this->loginAggregate = null;
        } else {
            if (isset($this->editorActions)) {
                $this->editorActions->processActionsForLossOfSecurityContext("unknown");
            }            
            if (isset($this->represantativeActions)) {
                $this->represantativeActions->processActionsForLossOfSecurityContext("unknown");
            }
        }
        $this->info = [];
        return $this;
    }

    /**
     * {@inheritdoc}
     * 
     * @param LoginAggregateFullInterface $loginAggregate
     * @return void
     */
    #[\Override]
    public function new(LoginAggregateFullInterface $loginAggregate): SecurityInterface {
        $this->loginAggregate = $loginAggregate;
        $this->editorActions = new EditorActions();
        $this->represantativeActions = new RepresentationActions();
        return $this;
    }
    
    

    /**
     * {@inheritdoc}
     * 
     * @return bool
     */
    #[\Override]
    public function hasValidSecurityContext(): bool {
        return isset($this->loginAggregate) AND $this->loginAggregate->isPersisted();
    }
    
    ### GETTERY
    
    /**
     * Vrací LoginAggregateFull - login s credentials a registration
     *
     * @return LoginAggregateFullInterface|null
     */
    #[\Override]
    public function getLoginAggregate(): ?LoginAggregateFullInterface {
        return $this->loginAggregate;
    }

    /**
     *
     * @return EditorActionsInterface|null
     */
    #[\Override]
    public function getEditorActions(): ?EditorActionsInterface {
        return $this->editorActions;
    }
    #[\Override]
    public function getRepresentativeActions(): ?RepresentationActionsInterface {
        return $this->represantativeActions;
    }
    #[\Override]
    public function setInfo($name, $value) {
        $this->info[$name] = $value;
    }
    #[\Override]
    public function getInfo($name) {
        return $this->info[$name] ?? null;
    }
    #[\Override]
    public function getInfos(): array {
        return $this->info;
    }    
}
