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

use Mail\Params;
use Mail\Params\Content;
use Mail\Params\Attachment;
use Mail\Params\Party;

use Sendmail\Middleware\Sendmail\Controler\Contents\MailContent;




use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Auth\Model\Repository\LoginAggregateCredentialsRepo;
use Auth\Model\Repository\RegistrationRepo;

use Access\AccessPresentationInterface;
use Access\Enum\AccessPresentationEnum;
/**
 * Description of PostControler
 *
 * @author pes2704
 */
class MailControler extends PresentationFrontControlerAbstract {

    private $loginAggregateCredentialsRepo;
    private $registrationRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            AccessPresentationInterface $accessPresentation,
            LoginAggregateCredentialsRepo $loginAggregateCredentialsRepo,
            RegistrationRepo $registrationRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo, $accessPresentation);
        $this->loginAggregateCredentialsRepo = $loginAggregateCredentialsRepo;
        $this->registrationRepo = $registrationRepo;
    }


    private function getLogins() {
        $visitorsLoginAgg = $this->loginAggregateCredentialsRepo->find();
        return $visitorsLoginAgg;
    }

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
                        $params = (new Params())
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
    
    
    
   
    public function  sendCampaign( ServerRequestInterface $request, string $assembly, int $campaign=-1000) {      
        
        $count = 10;
        $min = ($campaign-1)*$count+1;
        $max = $min + $count-1;

        $counter = 0;
        $sended = 0;
        
        $adresati = [ "selnerova@grafia.cz", "webmaster@grafia.cz" ];
        foreach ($adresati as $adresat) {
                        
            $counter++;
//            if ($counter>=$min AND $counter<=$max )  {
                                                        
                        /** @var Mail $mail */
                $mail = $this->container->get(Mail::class);
                        
                //$params = (new Params())
                $obsahMailu =  (new MailContent() )  ;
                $para = $obsahMailu->getParams($assembly, $adresat);
                        
                        
//                        /** @var HtmlMessage $mailMessageFactory */
//                        $mailMessageFactory = $this->container->get(HtmlMessage::class);
//                        $subject =  'Veletrh práce - Poděkování, odkazy a "virtuální igelitka"';
//                        $body = $mailMessageFactory->create(__DIR__."/Messages/podekovani-odkazy-igelitka2.php",
//                                                            ['registerJmeno' => $credentials->getLoginNameFk(),
//
//                                                            ]);
//                        $attachments = [//
//                                        (new Attachment())
//                                        ->setFileName(ConfigurationCache::mail()['mail.attachments'].'Katalog veletrhPRACE.online 2021.pdf')
//                                        ->setAltText('Katalog veletrhPRACE.online 2021'),                                        
//                                       ];
//                        $params = (new Params())
//                                    ->setContent(  (new Content())
//                                                     ->setSubject($subject)
//                                                     ->setHtml($body)
//                                                     ->setAttachments($attachments)
//                                                )
//                                    ->setParty  (  (new Party())
//                                                     ->setFrom('it.grafia@gmail.com', 'veletrhprace.online')
//                                                     ->addTo('svoboda@grafia.cz', $credentials->getLoginNameFk().' veletrhprace.online')
//                                                     ->addTo($registration->getEmail(), $credentials->getLoginNameFk().' veletrhprace.online')
//                                                );
                                                
                                                
                                                
                       $mail->mail($para); // posle mail
                       
                                                                                            
                     $sended++;                     
//            }                                  
               
        }
              
        $this->addFlashMessage("Prošlo sendmailem()SSSSS. ");                             
        
        return $this->createStringOKResponse("Mail: campaign: $campaign, min= $min, max=$max, odesláno $sended.");        
        
    }
    
    
    
    
    
    
    
    
    
    
    
    
    public function sendVS(ServerRequestInterface $request) {        
        
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
            $params = (new Params())
                        ->setContent(  (new Content())
                                         ->setSubject($subject)
                                         ->setHtml($body)
                                         ->setAttachments($attachments)
                                    )
                        ->setParty  (  (new Party())
                                                 ->setFrom('info@najdisi.cz', 'web praci.najdisi.cz')
                                         ->addTo('svoboda@grafia.cz', $credentials->getLoginNameFk().' veletrhprace.online')
                                         ->addTo($registration->getEmail(), $credentials->getLoginNameFk().' veletrhprace.online')
                                    );
          //  $mail->mail($params); // posle mail

       
        return $this->createStringOKResponse("Mail: campaign: $campaign, min= $min, max=$max, odesláno $sended.");
    }

}
