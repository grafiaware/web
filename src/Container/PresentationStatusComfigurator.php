<?php

namespace Container;

use Site\ConfigurationCache;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Pes\Container\Container;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

use Pes\Database\Handler\Account;

/**
 * Description of PresentationStatusComfigurator
 *
 * @author pes2704
 */
class PresentationStatusComfigurator extends ContainerConfiguratorAbstract {

    public function getParams(): iterable {
        return ConfigurationCache::web();
    }

    public function getFactoriesDefinitions(): iterable {
        return [];
    }

    public function getAliases(): iterable {
        return [];
    }

    public function getServicesDefinitions(): iterable {
        return [
            // database account
            Account::class => function(ContainerInterface $c) {
                $account = new Account($c->get('web.db.account.everyone.name'), $c->get('web.db.account.everyone.password'));
                return $account;
            },
        ];
    }
}
