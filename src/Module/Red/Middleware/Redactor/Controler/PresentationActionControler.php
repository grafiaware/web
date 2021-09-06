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

use Red\Model\Repository\LanguageRepo;
use Red\Model\Repository\MenuItemRepo;
use Red\Model\Repository\ItemActionRepo;

use Red\Model\Entity\ItemAction;
use Component\View\Authored\AuthoredEnum;

use Red\Middleware\Redactor\Controler\Exception\UnexpectedLanguageException;

/**
 * Description of PostControler
 *
 * @author pes2704
 */
class PresentationActionControler extends FrontControlerAbstract {

    private $languageRepo;

    private $menuItemRepo;

    private $itemActionRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            LanguageRepo $languageRepo,
            MenuItemRepo $menuItemRepo,
            ItemActionRepo $itemActionRepo) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->languageRepo = $languageRepo;
        $this->menuItemRepo = $menuItemRepo;
        $this->itemActionRepo = $itemActionRepo;
    }

    public function setLangCode(ServerRequestInterface $request) {
        $requestedLangCode = (new RequestParams())->getParsedBodyParam($request, 'langcode');
        $language = $this->languageRepo->get($requestedLangCode);
        if (isset($language)) {
            $this->statusPresentationRepo->get()->setLanguage($language);
        } else{
            throw new UnexpectedLanguageException("Požadavek a nastavení neznámého jazyka aplikace s kódem $requestedLangCode.");
        }
        $this->addFlashMessage("setLangCode({$language->getLangCode()})");
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

//    public function setPresentedItem(ServerRequestInterface $request) {
//        $requestedUid = (new RequestParams())->getParsedBodyParam($request, 'uid');
//        $statusPresentation = $this->statusPresentationRepo->get();
//        $langCodeFk = $statusPresentation->getLanguage()->getLangCode();
//        $menuItem = $this->menuItemRepo->get($langCodeFk, $requestedUid);
//        $statusPresentation->setHierarchyAggregate($menuItem);  // bez kontroly
//        $this->addFlashMessage("setPresentedItem({$menuItem->getTitle()})");
//        return $this->redirectSeeLastGet($request); // 303 See Other
//    }

    public function setEditArticle(ServerRequestInterface $request) {
        $edit = (new RequestParams())->getParsedBodyParam($request, 'edit_article');
//        $this->switchEditable('article', $edit);

        //TODO: nejdřív vypnu editable a pak teprve volám isPresentedItemActive() - pokud menuItem není active, tak se s vypnutým editable už v metodě isPresentedItemActive() nenačte - ?? obráceně?
        $this->statusPresentationRepo->get()->getUserActions()->setEditableArticle($edit);
        $this->addFlashMessage("set editable article $edit");
        if ($edit OR $this->isPresentedItemActive()) {
            return $this->redirectSeeLastGet($request); // 303 See Other
        } else {
            return $this->createResponseRedirectSeeOther($request, ''); // 303 See Other -> home - jinak zůstane prezentovaný poslední segment layoutu, který nyl editován v režimu edit layout
        }
    }

    public function addUserItemAction(ServerRequestInterface $request, $typeFk, $itemId) {
        $userActions = $this->statusPresentationRepo->get()->getUserActions();
        $typeFk = (new AuthoredEnum())($typeFk);
        if (! $userActions->hasUserAction($typeFk, $itemId)) {
            $itemAction = new ItemAction();
            $itemAction->setTypeFk($typeFk);
            $itemAction->setItemId($itemId);
            $itemAction->setEditorLoginName($this->statusSecurityRepo->get()->getLoginAggregate()->getLoginName());
            $this->itemActionRepo->add($itemAction);
            $userActions->addUserItemAction($itemAction);
            $this->addFlashMessage("add user action for $typeFk($itemId)");
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    public function removeUserItemAction(ServerRequestInterface $request, $typeFk, $itemId) {
        // mažu nezávisle itemAction z statusPresentation (session) i z itemActionRepo (db) - hrozí chyby při opakované modeslání požadavku POST nebo naopak při ztrátě session
        $userActions = $this->statusPresentationRepo->get()->getUserActions();
        if ($userActions->hasUserAction($typeFk, $itemId)) {
            $userActions->removeUserItemAction($userActions->getUserAction($typeFk, $itemId));
        }
        $itemAction = $this->itemActionRepo->get($typeFk, $itemId);  // nestačí načíst itemAction z UserAction - v itemActionRepo pak není entity v kolekci a nelze volat remove
        if (isset($itemAction)) {
            $this->itemActionRepo->remove($itemAction);
        }
        $this->addFlashMessage("remove user action for $typeFk($itemId)");
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    public function setEditLayout(ServerRequestInterface $request) {
        $edit = (new RequestParams())->getParsedBodyParam($request, 'edit_layout');
//        $this->switchEditable('layout', $edit);
        $this->statusPresentationRepo->get()->getUserActions()->setEditableLayout($edit);
        $this->addFlashMessage("set editable layout $edit");
        if ($edit OR $this->isPresentedItemActive()) {
            return $this->redirectSeeLastGet($request); // 303 See Other
        } else {
            return $this->createResponseRedirectSeeOther($request, ''); // 303 See Other -> home - jinak zůstane prezentovaný poslední articele, který nyl editován v režimu edit article
        }
    }

    public function setEditMenu(ServerRequestInterface $request) {
        $edit = (new RequestParams())->getParsedBodyParam($request, 'edit_menu');
//        $this->switchEditable('menu', $edit);
        $this->addFlashMessage("set editable menu $edit");
        $this->statusPresentationRepo->get()->getUserActions()->setEditableMenu($edit);
        if ($edit OR $this->isPresentedItemActive()) {
            return $this->redirectSeeLastGet($request); // 303 See Other
        } else {
            return $this->createResponseRedirectSeeOther($request, ''); // 303 See Other -> home - jinak zůstane prezentovaný poslední articele, který nyl editován v režimu edit article
        }
    }

    private function isPresentedItemActive() {
        $statusPresentation = $this->statusPresentationRepo->get();
        $menuItem = $statusPresentation->getMenuItem();
        return $menuItem ? $menuItem->getActive() : false;
    }

    protected function switchEditable($name, $value) {
        $userAction = $this->statusPresentationRepo->get()->getUserActions();
//        $isAnyOldAction = $userAction->isEditableArticle() OR $userAction->isEditableLayout() OR $userAction->isEditableMenu();
                $userAction->setEditableArticle(false);
                $userAction->setEditableLayout(false);
                $userAction->setEditableMenu(false);
        switch ($name) {
            case 'article':
                $userAction->setEditableArticle($value);
                break;
            case 'layout':
                $userAction->setEditableLayout($value);
                break;
            case 'menu':
                $userAction->setEditableMenu($value);
                break;
            default:
                break;
        }
    }

    private function getPublishedItem() {

    }
}
