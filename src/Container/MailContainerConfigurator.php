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

use Sendmail\Middleware\Sendmail\Controller\MailController;

// repo - z app kontejneru
use Status\Model\Repository\{StatusSecurityRepo, StatusPresentationRepo, StatusFlashRepo};

use Auth\Model\Repository\LoginAggregateCredentialsRepo;
use Auth\Model\Repository\RegistrationRepo;

/**
 *
 *
 * @author pes2704
 */
class MailContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams(): iterable {
        return Configuration::mail();
    }

    public function getFactoriesDefinitions(): iterable {
        return [];
    }

    public function getAliases(): iterable {
        return [

        ];
    }

    public function getServicesDefinitions(): iterable {
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
                        $c->get(LoginAggregateCredentialsRepo::class),
                        $c->get(RegistrationRepo::class)

                    ))->injectContainer($c);  // inject component kontejner
            },

        ];
    }

}
