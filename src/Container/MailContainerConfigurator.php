<?php
namespace Container;

use Site\ConfigurationCache;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

// logger
use Pes\Logger\FileLogger;

// mail
use Mail\Mail;
use Mail\ParamsTemplates;
use Mail\MessageFactory\HtmlMessage;

use Sendmail\Middleware\Sendmail\Controler\MailControler;

// Access
use Access\AccessPresentation;

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
        return ConfigurationCache::mail();
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
                return new Mail(ParamsTemplates::params($c->get('mail.paramsname')), $c->get('mailLogger'));
            },
            HtmlMessage::class => function(ContainerInterface $c) {
                return new HtmlMessage();
            },
            MailControler::class => function(ContainerInterface $c) {
                return (new MailControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(AccessPresentation::class),
                        $c->get(LoginAggregateCredentialsRepo::class),
                        $c->get(RegistrationRepo::class)

                    ))->injectContainer($c);  // inject component kontejner
            },

        ];
    }

}
