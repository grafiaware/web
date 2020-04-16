<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Middleware\Web\Controller;

use Controller\FrontControllerInterface;
use Psr\Container\ContainerInterface;

/**
 *
 * @author pes2704
 */
interface LayoutControllerInterface extends FrontControllerInterface {

    public function injectContainer(ContainerInterface $componentContainer): LayoutControllerInterface;

}
