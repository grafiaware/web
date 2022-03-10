<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Middleware\Redactor\Controler;

use FrontControler\FrontControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Application\AppFactory;
use Pes\Http\Request\RequestParams;
use Pes\Http\Response;
use Pes\Http\Response\RedirectResponse;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

use Status\Model\Enum\FlashSeverityEnum;

use Red\Model\Repository\LanguageRepo;
use Red\Model\Repository\MenuItemRepo;
use Red\Model\Repository\ItemActionRepo;

use Red\Model\Entity\ItemAction;
use Red\Model\Enum\AuthoredTypeEnum;

use Red\Middleware\Redactor\Controler\Exception\UnexpectedLanguageException;

/**
 * Description of PostControler
 *
 * @author pes2704
 */
class ItemActionControler extends FrontControlerAbstract {

    private $itemActionRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            ItemActionRepo $itemActionRepo) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->itemActionRepo = $itemActionRepo;
    }


    public function addUserItemAction(ServerRequestInterface $request, $typeFk, $itemId) {
        $userActions = $this->statusPresentationRepo->get()->getUserActions();
        $typeFk = (new AuthoredTypeEnum())($typeFk);
        if (! $userActions->hasUserItemAction($typeFk, $itemId)) {
            $itemAction = new ItemAction();
            $itemAction->setTypeFk($typeFk);
            $itemAction->setItemId($itemId);
            $itemAction->setEditorLoginName($this->statusSecurityRepo->get()->getLoginAggregate()->getLoginName());
            $this->itemActionRepo->add($itemAction);  // do repo
            $userActions->addUserItemAction($itemAction);  // do statusu
            $this->addFlashMessage("add user action for $typeFk (item $itemId)", FlashSeverityEnum::INFO);
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    public function removeUserItemAction(ServerRequestInterface $request, $typeFk, $itemId) {
        // mažu nezávisle itemAction z statusPresentation (session) i z itemActionRepo (db) - hrozí chyby při opakované modeslání požadavku POST nebo naopak při ztrátě session
        $userActions = $this->statusPresentationRepo->get()->getUserActions();
        if ($userActions->hasUserItemAction($typeFk, $itemId)) {
            $userActions->removeUserItemAction($userActions->getUserItemAction($typeFk, $itemId));
        }
        $itemAction = $this->itemActionRepo->get($typeFk, $itemId);  // nestačí načíst itemAction z UserAction - v itemActionRepo pak není entity v kolekci a nelze volat remove
        if (isset($itemAction)) {
            $this->itemActionRepo->remove($itemAction);
        }
        $this->addFlashMessage("remove user action for $typeFk (item $itemId)", FlashSeverityEnum::INFO);
        return $this->redirectSeeLastGet($request); // 303 See Other
    }
}
