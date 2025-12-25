<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel;

use Component\ViewModel\ViewModelInterface;

use Psr\Container\ContainerInterface;

use Red\Model\Entity\StaticItemInterface;

/**
 *
 * @author pes2704
 */
interface StaticItemViewModelInterface extends ViewModelInterface {

    public function injectContainer(ContainerInterface $container): StaticItemViewModelInterface;
    
    /**
     * 
     * @return StaticItemInterface
     */
    public function getStaticItem(): StaticItemInterface;
    
    /**
     * 
     * @return string
     */
    public function getStaticTemplatePath(): string;    
}
