<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Test\Integration\Event\Model\Context;

use Pes\Model\Context\ContextProviderInterface;
use Pes\Model\Context\PublishedContextInterface;
use Pes\Model\Context\PublishedContext;

/**
 * Description of ContextFactory
 *
 * @author pes2704
 */
class ContextProviderMock implements ContextProviderInterface {

    private $published;

    public function __construct($published=false) {
        $this->published = (bool) $published;
    }

    public function showOnlyPublished(): bool {
        return new PublishedContext($this->published);
    }
}
