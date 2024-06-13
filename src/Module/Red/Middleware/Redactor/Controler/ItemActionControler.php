<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Middleware\Redactor\Controler;

use FrontControler\FrontControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

use Site\ConfigurationCache;
use Status\Model\Enum\FlashSeverityEnum;

use Red\Service\ItemAction\ItemActionServiceInterface;
use Red\Service\ItemAction\Exception\UnableToAddItemActionForItemException;

use DateInterval;
/**
 * Description of PostControler
 *
 * @author pes2704
 */
class ItemActionControler extends FrontControlerAbstract {

    /**
     *
     * @var ItemActionServiceInterface
     */
    private $itemActionService;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            ItemActionServiceInterface $itemActionService) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->itemActionService = $itemActionService;
    }

    public function addUserItemAction(ServerRequestInterface $request, $itemId) {
        $statusSecurity = $this->statusSecurityRepo->get();
        $loginName = $statusSecurity->getLoginAggregate()->getLoginName();
        // vyčištění starých item action
        $interval = new DateInterval(ConfigurationCache::itemActionControler()['timeout']);
        try {
            $newItemAction = $this->itemActionService->refreshItemActionsAndCreateNew($interval, $itemId, $loginName);  // uložení do repo, vyhodí výjimku, pokud jiný editor upravuje item
            $statusSecurity->getUserActions()->addItemAction($newItemAction);
            $this->addFlashMessage("Zahájena úprava položky (item) $itemId.", FlashSeverityEnum::INFO);
        } catch (UnableToAddItemActionForItemException $e) {
            $activeEditor = $this->itemActionService->getActiveEditor($itemId);
            $this->addFlashMessage("Položku (item) $itemId upravuje $activeEditor.", FlashSeverityEnum::WARNING);
        }
//        return $this->createJsonPutNoContentResponse(["refresh"=>"closest"], 200);
        //TODO: POST version        
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    public function removeUserItemAction(ServerRequestInterface $request, $itemId) {
        $statusSecurity = $this->statusSecurityRepo->get();
        $loginName = $statusSecurity->getLoginAggregate()->getLoginName();
        $this->itemActionService->remove($itemId, $loginName);  // odstranění z repo
        $statusSecurity->getUserActions()->removeItemAction($itemId);
        $this->addFlashMessage("Ukončena úprava položky (item $itemId)", FlashSeverityEnum::INFO);
//        return $this->createJsonPutNoContentResponse(["refresh"=>"closest"], 200);
        //TODO: POST version        
        return $this->redirectSeeLastGet($request); // 303 See Other
    }
}
