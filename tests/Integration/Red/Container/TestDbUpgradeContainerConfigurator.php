<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Test\Integration\Red\Container;

use Container\DbUpgradeContainerConfigurator;

use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

// context
use Model\Context\ContextFactoryInterface;
use Test\Integration\Red\Model\Context\ContextFactoryMock;

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
//                'dbUpgrade.db.connection.name' => 'web_red_test',
                #
                ###################################
                # Konfigurace logu databáze pro test
                #
                'dbUpgrade.logs.db.directory' => 'TestLogs/Red',
                'dbUpgrade.logs.db.file' => 'Database.log',
                #
                #################################

            ]+
            parent::getParams();
    }
}
