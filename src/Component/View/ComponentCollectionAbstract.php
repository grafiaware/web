<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View;

use Pes\View\CollectionView;

use Component\View\ComponentCollectionInterface;

use Configuration\ComponentConfigurationInterface;

/**
 * Description of CompositeComponentAbstract
 *
 * @author pes2704
 */
abstract class ComponentCollectionAbstract extends CollectionView implements ComponentCollectionInterface {

    /**
     *
     * @var ComponentConfigurationInterface
     */
    protected $configuration;
    
    public function __construct(ComponentConfigurationInterface $configuration) {
        $this->configuration = $configuration;
    }

}
