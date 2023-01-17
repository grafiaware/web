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
use Model\Context\ContextProviderInterface;
use Test\Integration\Red\Model\Context\ContextProviderMock;

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
            ContextProviderInterface::class => ContextProviderMock::class,
        ]
        +parent::getAliases();
    }

    public function getServicesDefinitions(): iterable {
        return [
                ContextProviderMock::class => function(ContainerInterface $c) {
                    return new ContextProviderMock(false, false);
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
