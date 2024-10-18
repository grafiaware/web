<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Context;

use Model\Context\ContextProviderInterface;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;

/**
 * Description of ContextFactory
 *
 * @author pes2704
 */
class ContextProvider implements ContextProviderInterface {

    /**
     * @var StatusSecurityRepo
     */
    protected $statusSecurityRepo;

    /**
     * @var StatusPresentationRepo
     */
    protected $statusPresentationRepo;
    
    private $forceShowOnlyPublished;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo
            ) {
        $this->statusSecurityRepo = $statusSecurityRepo;
        $this->statusPresentationRepo = $statusPresentationRepo;
    }
    
    /**
     * Jen pokud víte co děláte!
     * Metoda slouží pro změnu nastavení, které contect provider provádí pomocí parametrů konstruktoru.
     *
     * Touto metodou lze vynutit pevné nastavení, je jen třeba dbát na to, aby nastavení proběhlo před prvním čtením.
     * 
     * POZOR! Použití této metody může vést ke komplikovaným důsledkům. Například pokud se ContextProvider používá v kontejneru, pak je s největší pravděpodobností použit 
     * v různých Dao a Repository a změna jeho nastavé ovlivní všechny. Navíc až od okamžiku nastavení. 
     * Lze to snad eliminovat voláním metody s bool hodnotou, provést operaci s jedním Dao (Repo - pozor na agregátní repo) a ihned zavolat metodu s hodnotou parametru null.
     * Zavolánim metody s hodnotou null se vrátí chování objektu k defaultnímu.
     * 
     * @param bool $forceShowOnlyPublished
     */
    public function forceShowOnlyPublished($forceShowOnlyPublished=null): void {
        $this->forceShowOnlyPublished = $forceShowOnlyPublished;
    }
    
    public function showOnlyPublished(): bool {
        if (isset($this->forceShowOnlyPublished)) {
            $show = (bool) $this->forceShowOnlyPublished;
        } else {
            $userActions = $this->statusSecurityRepo->get()->getEditorActions();
            $show = isset($userActions) ? !$userActions->presentEditableContent() : true;
        }
        return $show;
    }
}
