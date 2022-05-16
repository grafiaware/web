<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Test\Integration\Red\Model\Context;

use Model\Context\ContextFactoryInterface;
use Model\Context\PublishedContextInterface;
use Model\Context\PublishedContext;

/**
 * Description of ContextFactory
 *
 * @author pes2704
 */
class ContextFactoryMock implements ContextFactoryInterface {

    private $active;
    private $actual;

    public function __construct($active=false, $actual=false) {
        $this->active = (bool) $active;
        $this->actual = (bool) $actual;
    }

    /**
     * Produkční PublishedContext odvozuje kontext z presentation statusu
     * @return PublishedContextInterface
     */
    public function createPublishedContext(): PublishedContextInterface {
        return new PublishedContext($this->active, $this->actual);
    }
}
