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
use Mail\MessageFactory\HtmlMessage;

use Middleware\Sendmail\Controler\MailController;

// repo - z app kontejneru
use Model\Repository\StatusSecurityRepo;
use Model\Repository\StatusPresentationRepo;
use Model\Repository\StatusFlashRepo;

use Model\Repository\LoginAggregateCredentialsRepo;

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
            },
            HtmlMessage::class => function(ContainerInterface $c) {
                return new HtmlMessage();
            },
            MailController::class => function(ContainerInterface $c) {
                return (new MailController(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(LoginAggregateCredentialsRepo::class)

                    ))->injectContainer($c);  // inject component kontejner
            },

        ];
    }

    public function getServicesOverrideDefinitions() {
        return [];
    }
}
