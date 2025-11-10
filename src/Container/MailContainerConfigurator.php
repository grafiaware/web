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

use Sendmail\Middleware\Sendmail\Campaign\AssemblyProvider\AssemblyProvider;
use Sendmail\Middleware\Sendmail\Recipients\RecipientsValidator;
use Sendmail\Middleware\Sendmail\Recipients\MailRecipients;
use Sendmail\Middleware\Sendmail\Recipients\MailSender;
use Sendmail\Middleware\Sendmail\Campaign\CampaignProvider;
use Sendmail\Middleware\Sendmail\Csv\CampaignData;
use Sendmail\Middleware\Sendmail\Csv\CsvData;

use PHPMailer\PHPMailer\PHPMailer;

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
            PHPMailer::class => function(ContainerInterface $c) {
                return new PHPMailer(true);   // true enables exceptions
            },
            Mail::class => function(ContainerInterface $c) {
                return new Mail(
                        $c->get(PHPMailer::class),
                        ParamsTemplates::params($c->get('mail.paramsname')), 
                        $c->get('mailLogger')
                    );
            },
            HtmlMessage::class => function(ContainerInterface $c) {
                return new HtmlMessage();
            },
                    
            AssemblyProvider::class => function(ContainerInterface $c) {   
                return new AssemblyProvider(
                    $c->get(HtmlMessage::class),      
                );
            },             
                    
            RecipientsValidator::class => function(ContainerInterface $c) {   
                return new RecipientsValidator();                    
            },
            CsvData::class => function(ContainerInterface $c) {
                return new CsvData();
            },
            CampaignData::class => function(ContainerInterface $c) {
                return new CampaignData($c->get(CsvData::class));
            },
            CampaignProvider::class => function(ContainerInterface $c) {
                return new CampaignProvider();
            },
            MailRecipients::class => function(ContainerInterface $c) {   
                return new MailRecipients(
                    $c->get(RecipientsValidator::class),
                    $c->get(CampaignData::class)
                );                    
            },                                         
            MailSender::class => function(ContainerInterface $c) {
                return new MailSender(
                    $c->get(Mail::class), 
                    $c->get(AssemblyProvider::class),
                    $c->get(CampaignData::class)
                );
            },
            MailControler::class => function(ContainerInterface $c) {
                return new MailControler(
                    $c->get(StatusSecurityRepo::class),
                    $c->get(StatusFlashRepo::class),
                    $c->get(StatusPresentationRepo::class),
                    $c->get(AccessPresentation::class),
                    $c->get(LoginAggregateCredentialsRepo::class),
                    $c->get(RegistrationRepo::class),
                    $c->get(MailSender::class),
                    $c->get(MailRecipients::class),
                    $c->get(CampaignProvider::class)
                );
            },

        ];
    }

}
