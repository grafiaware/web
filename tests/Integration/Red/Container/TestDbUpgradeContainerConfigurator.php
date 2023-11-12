<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Test\Integration\Red\Container;

use Container\DbUpgradeContainerConfigurator;

use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

use Pes\Database\Manipulator\Manipulator;

// context
use Model\Context\ContextProviderInterface;
use Test\Integration\Red\Model\Context\ContextProviderMock;

use Pes\Database\Handler\Account;
use Pes\Database\Handler\Handler;
use Pes\Database\Handler\HandlerInterface;
use Pes\Database\Handler\DbTypeEnum;

/**
 * Description of MenuContainerFactory
 *
 * @author pes2704
 */
class TestDbUpgradeContainerConfigurator extends DbUpgradeContainerConfigurator {

    public function getParams(): iterable {
        return
            [
                #####################################
                # Konfigurace připojení k databázi pro test
                #
                #
//                'red.db.connection.name' => 'web_red_test',
                #
                ###################################
                # Konfigurace logu databáze pro test
                #
                'red.logs.db.directory' => 'TestLogs/Red',
                'red.logs.db.file' => 'Database.log',
                #
                #################################

            ]+
            parent::getParams();
    }

    public function getServicesDefinitions(): iterable {
        return
        [
            Manipulator::class => function(ContainerInterface $c) : Manipulator {
                return new Manipulator($c->get(Handler::class), $c->get('dbUpgradeLogger'));
            },
        ]
        +
        parent::getServicesDefinitions();
    }
}
