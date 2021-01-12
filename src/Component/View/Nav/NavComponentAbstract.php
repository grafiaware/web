<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Nav;

use Component\View\CompositeComponentAbstract;
use Component\NodeFactory\NavTagFactoryInterface;

/**
 * Description of NavComponentAbstract
 *
 * @author pes2704
 */
abstract class NavComponentAbstract extends CompositeComponentAbstract {

    /**
     * @var NavTagFactoryInterface
     */
    protected $nodeFactory;

    public function __construct(NavTagFactoryInterface $nodeFactory) {
        $this->nodeFactory = $nodeFactory;
    }

}
