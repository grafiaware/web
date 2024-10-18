<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Status\Model\Entity;

use Model\Entity\PersistableEntityAbstract;

use Auth\Model\Entity\LoginAggregateFullInterface;
use Red\Model\Entity\EditorActions;

use Red\Model\Entity\EditorActionsInterface;

/**
 * Description of Login
 *
 * @author pes2704
 */
class StatusSecurity extends PersistableEntityAbstract implements StatusSecurityInterface {

    /**
     * @var LoginAggregateFullInterface
     */
    private $loginAggregate;

    /**
     * @var EditorActionsInterface
     */
    private $editorActions;
    
    private $info = [];

    /**
     * {@inheritdoc}
     * 
     * @return StatusSecurityInterface
     */
    public function removeContext(): StatusSecurityInterface {
        if (isset($this->loginAggregate)) {
            if (isset($this->editorActions)) {
               $this->editorActions->processActionsForLossOfSecurityContext($this->loginAggregate->getLoginName());
            }            
            $this->loginAggregate = null;
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     * 
     * @param LoginAggregateFullInterface $loginAggregate
     * @return void
     */
    public function new(LoginAggregateFullInterface $loginAggregate): StatusSecurityInterface {
        $this->loginAggregate = $loginAggregate;
        $this->editorActions = new EditorActions();
        return $this;
    }

    /**
     * {@inheritdoc}
     * 
     * @return bool
     */
    public function hasValidSecurityContext(): bool {
        return isset($this->loginAggregate) AND $this->loginAggregate->isPersisted();
    }
    
    ### GETTERY
    
    /**
     * Vrací LoginAggregateFull - login s credentials a registration
     *
     * @return LoginAggregateFullInterface|null
     */
    public function getLoginAggregate(): ?LoginAggregateFullInterface {
        return $this->loginAggregate;
    }

    /**
     *
     * @return EditorActionsInterface|null
     */
    public function getEditorActions(): ?EditorActionsInterface {
        return $this->editorActions;
    }
    
    public function setInfo($name, $value) {
        $this->info[$name] = $value;
    }
    public function getInfo($name) {
        return $this->info[$name] ?? null;
    }
    public function getInfos(): array {
        return $this->info;
    }    
}
