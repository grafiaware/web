<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Middleware\Events\Controler;

use FrontControler\PresentationFrontControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Application\AppFactory;
use Pes\Http\Request\RequestParams;
use Pes\Http\Response;
use Pes\Http\Response\RedirectResponse;

use Red\Model\Repository\{
    StatusSecurityRepo, StatusFlashRepo, StatusPresentationRepo, EnrollRepo
};

use Middleware\Api\Controler\Exception\UnexpectedLanguageException;

use \Events\Model\Arraymodel\EventViewModel;
use \Model\Entity\Enroll;

/**
 * Description of PostControler
 *
 * @author pes2704
 */
class EventControler extends PresentationFrontControlerAbstract {

    private $enrollRepo;

    private $eventListModel;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            EnrollRepo $enrollRepo,
            EventViewModel $eventListModel
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->enrollRepo = $enrollRepo;
        $this->eventListModel = $eventListModel;
    }

    public function enroll(ServerRequestInterface $request) {
        $requestedEventId = (new RequestParams())->getParsedBodyParam($request, 'event_enroll');
        if (isset($requestedEventId)) {
            $boxItem = $this->eventListModel->getEventBoxItem($requestedEventId);
            if (isset($boxItem)) {
                $loginAggregateCredentials = $this->statusSecurityRepo->get()->getLoginAggregate();
                if (isset($loginAggregateCredentials)) {
                    $loginName = $loginAggregateCredentials->getLoginName();
                    $enroll = new Enroll();
                    $enroll->setLoginName($loginName)->setEventid($requestedEventId);
                    $this->enrollRepo->add($enroll);
                    $title = $boxItem['title'];
                    $this->addFlashMessage("Přihlášeno! ".PHP_EOL.$title.PHP_EOL."Ve svém návštěvnickém profilu v menu najdete odkaz.");
                }
            }
        }

        return $this->redirectSeeLastGet($request); // 303 See Other
    }

}
