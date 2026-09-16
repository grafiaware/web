<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Context;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Pes\Model\Context\ContextProviderInterface;

/**
 * Description of ContextFactory
 *
 * @author pes2704
 */
class ContextProvider implements ContextProviderInterface {

    /**
     * @var StatusSecurityRepo
     */
//    protected $statusSecurityRepo;

    /**
     * @var StatusPresentationRepo
     */
//    protected $statusPresentationRepo;


    //TODO: statusy
    public function __construct(
            ) {
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
    
    //TODO: Context factory pro events vrací showOnlyPublished() vždy true - vývojová verze - dodělat
    public function showOnlyPublished(): bool {
        return true;
    }
}
