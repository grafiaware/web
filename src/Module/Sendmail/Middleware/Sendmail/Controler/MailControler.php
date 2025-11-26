<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sendmail\Middleware\Sendmail\Controler;

use Site\ConfigurationCache;

use FrontControler\PresentationFrontControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;

use Mail\Mail;
use Mail\MessageFactory\HtmlMessage;

use Mail\Assembly;
use Mail\Assembly\Content;
use Mail\Assembly\Attachment;
use Mail\Assembly\Party;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Auth\Model\Repository\LoginAggregateCredentialsRepo;
use Auth\Model\Repository\RegistrationRepo;

use Access\AccessPresentationInterface;

use Sendmail\Middleware\Sendmail\Recipients\MailSenderInterface;
use Sendmail\Middleware\Sendmail\Recipients\MailRecipientsInterface;
use Sendmail\Middleware\Sendmail\Campaign\CampaignProviderInterface;

use Auth\Model\Entity\LoginAggregateCredentialsInterface;
/**
 * Description of PostControler
 *
 * @author pes2704
 */
class MailControler extends PresentationFrontControlerAbstract {

    private $loginAggregateCredentialsRepo;
    private $registrationRepo;
    
    private $mailSender;
    private $mailRecipients;
    private $campaignProvider;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            AccessPresentationInterface $accessPresentation,
            LoginAggregateCredentialsRepo $loginAggregateCredentialsRepo,
            RegistrationRepo $registrationRepo,
            
            MailSenderInterface $mailSender,
            MailRecipientsInterface $mailRecipients,
            CampaignProviderInterface $campaignProvider
            
        ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo, $accessPresentation);
        $this->loginAggregateCredentialsRepo = $loginAggregateCredentialsRepo;
        $this->registrationRepo = $registrationRepo;

        $this->mailSender = $mailSender;
        $this->mailRecipients = $mailRecipients;
        $this->campaignProvider = $campaignProvider;
    }

    /**
     * Načte z databáze a vrací všechny entity LoginAggregateCredentials.
     * 
     * @return LoginAggregateCredentialsInterface[]
     */
    private function getLogins() {
        $visitorsLoginAgg = $this->loginAggregateCredentialsRepo->find();
        return $visitorsLoginAgg;
    }

    public function  validate( ServerRequestInterface $request, $campaignName) {
        // config
        $campaignConfig = $this->campaignProvider->getCampaignConfig($campaignName);

        // připojení nových dat ze zdrojového souboru
        $numberOfAppended = $this->mailRecipients->appendSourceCsv($campaignConfig);
        $html = "
            <h4>Append:</h4>
            <pre>$numberOfAppended rows</pre>
        ";        

        $newValidatedData = $this->mailRecipients->validateEmailsInCsvFile($campaignConfig);  // default runtime 10 sekund -> poslední test musí začít nejpozději do 10. sekund
        $dataPrint = print_r($newValidatedData, true);
        $html .= "
            <h4>Validation:</h4>
            <pre>$dataPrint</pre>
        ";
        return $this->createStringOKResponse($html);
    }
    
    public function  sendCampaign( ServerRequestInterface $request, string $campaignName) {
        // config
        $campaignConfig = $this->campaignProvider->getCampaignConfig($campaignName);
        $maxRuntime=20;
        $maxQuantity=50;
        $report = $this->mailSender->sendEmails($campaignConfig, $maxRuntime, $maxQuantity);

        $sended = count($report);
        $html = "<h4>Mail: campaign: '$campaignName'</h4>";
        $html .= "<p>maxRuntime: $maxRuntime s, maxQuantity $maxQuantity</p>";
        $html .= "<p>Proběhl pokus o odeslánÍ $sended mailů.</p><hr/>";
        $html .= "<pre>".print_r($report,true)."</pre>";
        
        return $this->createStringOKResponse($html);       
    }
    
    /**
     * Stará metoda pro Veletrh online pro odesílání mail kampaní návštěvníkům. Rozesílá po dávkách uživatelům s rolí 'visitor', 
     * kteří mají zaregistrovaný email. Obsah mailu je natvrdo v metodě.
     * 
     * @param ServerRequestInterface $request
     * @param int $campaign Pořadové číslo dávky v kampani počínaje jedničkou.
     * @return type
     */
    public function send(ServerRequestInterface $request, int $campaign=-1000) {
        $count = 10;
        $min = ($campaign-1)*$count+1;
        $max = $min + $count-1;

        $counter = 0;
        $sended = 0;
        $visitorsLoginAgg = $this->getLogins();
        foreach ($visitorsLoginAgg as $visitorLoginAgg) {
            $credentials = $visitorLoginAgg->getCredentials();
            if (isset($credentials) AND ($credentials->getRoleFk() === "visitor")) {
                $counter++;
                if ($counter>=$min AND $counter<=$max ) {
                    $registration = $this->registrationRepo->get($visitorLoginAgg->getLoginName());
                    if (isset($registration) AND $registration->getEmail()) {
                        /** @var Mail $mail */
                        $mail = $this->container->get(Mail::class);
                        /** @var HtmlMessage $mailMessageFactory */
                        $mailMessageFactory = $this->container->get(HtmlMessage::class);
                        $subject =  'Veletrh práce - Poděkování, odkazy a "virtuální igelitka"';
                        $body = $mailMessageFactory->create(__DIR__."/Messages/podekovani-odkazy-igelitka2.php",
                                                            ['registerJmeno' => $credentials->getLoginNameFk(),

                                                            ]);
                        $attachments = [

                                        (new Attachment())
                                        ->setFileName(ConfigurationCache::mail()['mail.attachments'].'Katalog veletrhPRACE.online 2021.pdf')
                                        ->setAltText('Katalog veletrhPRACE.online 2021'),
                                        (new Attachment())
                                        ->setFileName(ConfigurationCache::mail()['mail.attachments'].'Letak nabor studenti POSSEHL.pdf')
                                        ->setAltText('Leták nábor studenti_POSSEHL'),
                                        (new Attachment())
                                        ->setFileName(ConfigurationCache::mail()['mail.attachments'].'MD ELEKTRONIK Serizovac min.pdf')
                                        ->setAltText('Leták MD ELEKTRONIK Seřizovač'),
                                        (new Attachment())
                                        ->setFileName(ConfigurationCache::mail()['mail.attachments'].'GRAFIA letaky.pdf')
                                        ->setAltText('Letáky Grafia'),
                                        (new Attachment())
                                        ->setFileName(ConfigurationCache::mail()['mail.attachments'].'logo_grafia.png')
                                        ->setAltText('Logo Grafia'),


                                       ];
                        $params = (new Assembly())
                                    ->setContent(  (new Content())
                                                     ->setSubject($subject)
                                                     ->setHtml($body)
                                                     ->setAttachments($attachments)
                                                )
                                    ->setParty  (  (new Party())
                                                     ->setFrom('it.grafia@gmail.com', 'veletrhprace.online')
                                                     ->addTo('svoboda@grafia.cz', $credentials->getLoginNameFk().' veletrhprace.online')
                                                     ->addTo($registration->getEmail(), $credentials->getLoginNameFk().' veletrhprace.online')
                                                );
                        $mail->mail($params); // posle mail
                        $sended++;
                    }
                }
            }
        }        
        return $this->createStringOKResponse("Mail: campaign: $campaign, min= $min, max=$max, odesláno $sended.");
    }
}
