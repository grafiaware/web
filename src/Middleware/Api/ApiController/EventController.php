<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Middleware\Api\ApiController;

use Controller\PresentationFrontControllerAbstract;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Application\AppFactory;
use Pes\Http\Request\RequestParams;
use Pes\Http\Response;
use Pes\Http\Response\RedirectResponse;

use Model\Repository\{
    StatusSecurityRepo, StatusFlashRepo, StatusPresentationRepo, EnrollRepo
};

use Middleware\Api\ApiController\Exception\UnexpectedLanguageException;

use \Model\Arraymodel\EventList;
use \Model\Entity\Enroll;

/**
 * Description of PostController
 *
 * @author pes2704
 */
class EventController extends PresentationFrontControllerAbstract {

    private $enrollRepo;

    private $eventListModel;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            EnrollRepo $enrollRepo,
            EventList $eventListModel
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->enrollRepo = $enrollRepo;
        $this->eventListModel = $eventListModel;
    }

    public function XX(){
        $loginAggregateCredentials = $this->statusSecurityRepo->get()->getLoginAggregate();
        if (isset($loginAggregateCredentials)) {
            $role = $loginAggregateCredentials->getCredentials()->getRole();
            $permission = [
                'sup' => true,
                'editor' => true
            ];
            if (array_key_exists($role, $permission) AND $permission[$role]) {

            }
        }
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
                    $this->addFlashMessage("Přihlášeno! ".PHP_EOL.$title);
                }
            }
        }

        return $this->redirectSeeLastGet($request); // 303 See Other
    }

}