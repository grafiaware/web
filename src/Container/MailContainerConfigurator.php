<?php
namespace Container;

use Site\Configuration;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

// logger
use Pes\Logger\FileLogger;

// mail
use Mail\Mail;
use Mail\ParamsContainer;

/**
 *
 *
 * @author pes2704
 */
class MailContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams() {
        return Configuration::mail();
    }

    public function getFactoriesDefinitions() {
        return [];
    }

    public function getAliases() {
        return [

        ];
    }

    public function getServicesDefinitions() {
        return [
            'mailLogger' => function(ContainerInterface $c) {
                return FileLogger::getInstance($c->get('mail.logs.directory'), $c->get('mail.logs.file'), FileLogger::APPEND_TO_LOG); //new NullLogger();
            },
            Mail::class => function(ContainerInterface $c) {
                return new Mail(ParamsContainer::params($c->get('mail.paramsname')), $c->get('mailLogger'));
            }

        ];
    }

    public function getServicesOverrideDefinitions() {
        return [];
    }
}
