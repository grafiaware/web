<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Middleware\Sendmail\Controler;

use Site\Configuration;

use Controller\PresentationFrontControllerAbstract;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Application\AppFactory;
use Pes\Http\Request\RequestParams;
use Pes\Http\Response;
use Pes\Http\Response\RedirectResponse;

use Mail\Mail;
use Mail\MessageFactory\HtmlMessage;

use Mail\Params;
use Mail\Params\{Content, Attachment, Party};

use Model\Repository\{
    StatusSecurityRepo, StatusFlashRepo, StatusPresentationRepo, EnrollRepo, LoginAggregateCredentialsRepo
};


/**
 * Description of PostController
 *
 * @author pes2704
 */
class MailController extends PresentationFrontControllerAbstract {

    private $loginAggregateCredentialsRepo;


    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            LoginAggregateCredentialsRepo $loginAggregateCredentialsRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->loginAggregateCredentialsRepo = $loginAggregateCredentialsRepo;
    }


    private function getLogins() {
        $visitorsLoginAgg = $this->loginAggregateCredentialsRepo->findAll();
        return $visitorsLoginAgg;
    }

    public function send(ServerRequestInterface $request, $campaign) {
        $visitorsLoginAgg = $this->getLogins();
        foreach ($visitorsLoginAgg as $visitorLoginAgg) {
            $credentials = $visitorLoginAgg->getCredentials();
            if (isset($credentials) AND ($credentials->getRole() === "visitor")) {
                /** @var Mail $mail */
                $mail = $this->container->get(Mail::class);
                /** @var HtmlMessage $mailMessageFactory */
                $mailMessageFactory = $this->container->get(HtmlMessage::class);
                $subject =  'Veletrh práce - Poděkování, odkazy a "virtuální igelitka"';
                $body = $mailMessageFactory->create(__DIR__."/Messages/podekovani-odkazy-igelitka2.php",
                                                    ['registerJmeno' => $credentials->getLoginNameFk(),

                                                    ]);
                $attachments = [
//                                (new Attachment())
//                                ->setFileName(Configuration::mail()['mail.attachments'].'logo_grafia.png')  // /_www_vp_files/attachments/
//                                ->setAltText('Logo Grafia'),
                                (new Attachment())
                                ->setFileName(Configuration::mail()['mail.attachments'].'Katalog veletrhPRACE.online 2021.pdf')  // /_www_vp_files/attachments/
                                ->setAltText('Katalog veletrhPRACE.online 2021'),
                                (new Attachment())
                                ->setFileName(Configuration::mail()['mail.attachments'].'Letak nabor studenti POSSEHL.pdf')  // /_www_vp_files/attachments/
                                ->setAltText('Leták nábor studenti_POSSEHL'),
                                (new Attachment())
                                ->setFileName(Configuration::mail()['mail.attachments'].'MD ELEKTRONIK Serizovac.pdf')  // /_www_vp_files/attachments/
                                ->setAltText('Leták MD ELEKTRONIK Seřizovač'),

                               ];
                $params = (new Params())
                            ->setContent(  (new Content())
                                             ->setSubject($subject)
                                             ->setHtml($body)
                                             ->setAttachments($attachments)
                                        )
                            ->setParty  (  (new Party())
                                             ->setFrom('it.grafia@gmail.com', 'veletrhprace.online')
                                             ->addTo('svoboda@grafia.cz', 'Registace vystavovatele veletrhprace.online')
                                             ->addTo('ingpetrsvoboda@seznam.cz', 'Registace vystavovatele veletrhprace.online')
                                        );
                $mail->mail($params); // posle mail
            }
        }

        return $this->createResponseFromString($request, "Mail");
    }

}
