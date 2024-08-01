<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Test\Integration\Red\Model\Context;

use Model\Context\ContextProviderInterface;

/**
 * Description of ContextFactory
 *
 * @author pes2704
 */
class ContextProviderMock implements ContextProviderInterface {

    private $forceShowOnlyPublished;

    public function __construct($editableMode=false) {
        $this->editableMode = (bool) $editableMode;
    }

    /**
     * Metoda slouží pro změnu nastavení, které se v produkčních objektech contect provider provádí výhradne pomocí parametrů konstruktoru.
     *
     * Tento mock lze nastavovat, je jen třeba dbát na to, aby nastavení proběhlo před prvním čtením.
     * @param type $forceShowOnlyPublished
     * @return void
     */
    public function forceShowOnlyPublished($forceShowOnlyPublished=null): void {
        $this->forceShowOnlyPublished = $forceShowOnlyPublished;
    }
    
    public function showOnlyPublished(): bool {
        if (isset($this->forceShowOnlyPublished)) {
            $show = (bool) $this->forceShowOnlyPublished;
        } else {
            $show = true;
        }
        return $show;
    }
}
