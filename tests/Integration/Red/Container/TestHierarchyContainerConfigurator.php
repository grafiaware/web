<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Test\Integration\Red\Container;

use Container\RedModelContainerConfigurator;

use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

use Site\ConfigurationCache;

// context
use Model\Context\ContextFactoryInterface;
use Test\Integration\Red\Model\Context\ContextFactoryMock;

use Pes\Database\Handler\Account;

/**
 * Description of MenuContainerFactory
 *
 * @author pes2704
 */
class TestHierarchyContainerConfigurator extends RedModelContainerConfigurator {
    public function getParams(): iterable {
        return ConfigurationCache::web()
                +parent::getParams();
    }

    public function getAliases(): iterable {
        return [
            ContextFactoryInterface::class => ContextFactoryMock::class,
        ]
        +parent::getAliases();
    }

    public function getServicesDefinitions(): iterable {
        return [
                ContextFactoryMock::class => function(ContainerInterface $c) {
                    return new ContextFactoryMock(false, false);
                },
            ##
            // database account
            Account::class => function(ContainerInterface $c) {
                $account = new Account($c->get('web.db.account.administrator.name'), $c->get('web.db.account.administrator.password'));
                return $account;
            },
            ]
            +parent::getServicesDefinitions();
    }
}
