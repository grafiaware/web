<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Web\Component\NodeFactory;

use Pes\Dom\Node\NodeInterface;

/**
 *
 * @author pes2704
 */
interface NodeFactoryInterface {
    public function createTag(): NodeInterface;

}
