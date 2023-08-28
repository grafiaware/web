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

use Status\Model\Enum\FlashSeverityEnum;

use Red\Model\Repository\ItemActionRepo;
use Red\Model\Entity\ItemAction;
use Red\Model\Enum\AuthoredTypeEnum;

/**
 * Description of PostControler
 *
 * @author pes2704
 */
class ItemActionControler extends FrontControlerAbstract {

    /**
     *
     * @var ItemActionRepo
     */
    private $itemActionRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            ItemActionRepo $itemActionRepo) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->itemActionRepo = $itemActionRepo;
    }

    public function addUserItemAction(ServerRequestInterface $request, $itemId) {
        $statusSecurity = $this->statusSecurityRepo->get();
        $userActions = $statusSecurity->getUserActions();
        if (! $userActions->hasItemAction($itemId)) {
            $itemAction = new ItemAction();
            $itemAction->setItemId($itemId);
            $itemAction->setEditorLoginName($this->statusSecurityRepo->get()->getLoginAggregate()->getLoginName());
            $this->itemActionRepo->add($itemAction);  // do repo
            $userActions->addItemAction($itemAction);  // do statusu
            $this->addFlashMessage("Zahájena úprava položky (item) $itemId.", FlashSeverityEnum::INFO);
        } else {
            $activeEditor = $userActions->getItemAction($itemId)->getEditorLoginName();
            $this->addFlashMessage("Položku (item) $itemId upravuje $activeEditor.", FlashSeverityEnum::WARNING);            
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    public function removeUserItemAction(ServerRequestInterface $request, $itemId) {
        // mažu nezávisle itemAction z statusPresentation (session) i z itemActionRepo (db) - hrozí chyby při opakované modeslání požadavku POST nebo naopak při ztrátě session
        $userActions = $this->statusSecurityRepo->get()->getUserActions();  // načtení ze sesiion
        if ($userActions->hasItemAction($itemId)) {
            $userActions->removeItemAction($userActions->getItemAction($itemId));
        }
        $itemAction = $this->itemActionRepo->get($itemId);  // načtení z db
        if (isset($itemAction)) {
            $this->itemActionRepo->remove($itemAction);
        }
        $this->addFlashMessage("Ukončena úprava položky (item $itemId)", FlashSeverityEnum::INFO);
        return $this->redirectSeeLastGet($request); // 303 See Other
    }
}
