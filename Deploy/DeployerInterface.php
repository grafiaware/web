<?php
namespace Deployer;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author pes2704
 */
interface DeployerInterface {
    public function injectContainer(ContainerInterface $buildContainer): DeployerInterface;

}
