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

use Sendmail\Middleware\Sendmail\Controler\Contents\MailContent;
use Sendmail\Middleware\Sendmail\Controler\Contents\RecipientsValidator;
use Sendmail\Middleware\Sendmail\Controler\Contents\MailRecipients;


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
                    
            MailContent::class => function(ContainerInterface $c) {   
                return new MailContent(
                        $c->get(HtmlMessage::class),      
                );
            },             
                    
            RecipientsValidator::class => function(ContainerInterface $c) {   
                return new RecipientsValidator();                    
            },                    
            MailRecipients::class => function(ContainerInterface $c) {   
                return new MailRecipients(
                         $c->get(RecipientsValidator::class), 
                );                    
            },                                         
                                        
            MailControler::class => function(ContainerInterface $c) {
                return (new MailControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(AccessPresentation::class),
                        $c->get(LoginAggregateCredentialsRepo::class),
                        $c->get(RegistrationRepo::class),
                        $c->get(Mail::class), 
                        $c->get(MailContent::class),
                        $c->get(MailRecipients::class)
                    ));
                        //->injectContainer($c);  // inject component kontejner
            },

        ];
    }

}
