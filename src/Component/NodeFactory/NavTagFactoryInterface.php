<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\NodeFactory;

/**
 *
 * @author pes2704
 */
interface NavTagFactoryInterface extends NodeFactoryInterface {
    public function setRendererContainer(ContainerInterface $rendererContainer): NavTagFactoryInterface;
    public function setItemRendererName($itemRendererName): NavTagFactoryInterface;

    public function setClassmap(ClassMapInterface $classMap): NavTagFactoryInterface;

}
