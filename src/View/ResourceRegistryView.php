<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace View;

use Pes\View\View;
use Pes\Router\Resource\ResourceRegistryInterface;

/**
 * Description of ResourceRegistryView
 *
 * @author pes2704
 */
class ResourceRegistryView extends View implements ResourceRegistryViewInterface {

    /**
     * @var ResourceRegistryInterface
     */
    private $resourceRegistry;

    public function __construct(ResourceRegistryInterface $resourceRegistry) {
        $this->resourceRegistry = $resourceRegistry;
    }
}
