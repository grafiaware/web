<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Nav;

use Component\View\ComponentAbstract;
use Component\NodeFactory\NavTagFactoryInterface;

/**
 * Description of NavCompositeComponentAbstract
 *
 * @author pes2704
 */
abstract class NavCompositeComponentAbstract extends ComponentAbstract {

    /**
     * @var NavTagFactoryInterface
     */
    protected $nodeFactory;

    // opravit - konstruktor neobsahuje konfiguraci
    public function __construct(NavTagFactoryInterface $nodeFactory) {
        $this->nodeFactory = $nodeFactory;
    }

}
