<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Test\Integration\Model\Container;

use Site\Configuration;

use Pes\Container\ContainerConfiguratorAbstract;

use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

// context
use Model\Context\ContextFactoryInterface;
use Test\Integration\Model\Context\ContextFactoryMock;


/**
 * Description of MenuContainerFactory
 *
 * @author pes2704
 */
class TestModelContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams() {
        return [];
    }

    public function getFactoriesDefinitions() {
        return [
        ];
    }

    public function getAliases() {
        return [
            ContextFactoryInterface::class => ContextFactoryMock::class
        ];
    }

    public function getServicesDefinitions() {
        return [
            ContextFactoryMock::class => function(ContainerInterface $c) {
                return new ContextFactoryMock(false, false);
            },
        ];
    }

    public function getServicesOverrideDefinitions() {
        return [

        ];
    }
}
