<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Middleware\Redactor\Controller;

use FrontController\PresentationFrontControllerAbstract;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Application\AppFactory;
use Pes\Http\Request\RequestParams;
use Pes\Http\Response;
use Pes\Http\Response\RedirectResponse;

use Status\Model\Repository\{
    StatusSecurityRepo, StatusFlashRepo, StatusPresentationRepo
};
use Red\Model\Repository\{
    LanguageRepo, MenuItemRepo
};

use Red\Middleware\Redactor\Controller\Exception\UnexpectedLanguageException;

/**
 * Description of PostController
 *
 * @author pes2704
 */
class PresentationActionController extends PresentationFrontControllerAbstract {

    private $languageRepo;

    private $menuItemRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            LanguageRepo $languageRepo,
            MenuItemRepo $menuItemRepo) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->languageRepo = $languageRepo;
        $this->menuItemRepo = $menuItemRepo;
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
        $this->statusSecurityRepo->get()->getUserActions()->setEditableArticle($edit);
        $this->addFlashMessage("set editable article $edit");
        if ($edit OR $this->isPresentedItemActive($request)) {
            return $this->redirectSeeLastGet($request); // 303 See Other
        } else {
            return $this->redirectSeeOther($request, ''); // 303 See Other -> home - jinak zůstane prezentovaný poslední segment layoutu, který nyl editován v režimu edit layout
        }
    }

    public function setEditLayout(ServerRequestInterface $request) {
        $edit = (new RequestParams())->getParsedBodyParam($request, 'edit_layout');
//        $this->switchEditable('layout', $edit);
        $this->statusSecurityRepo->get()->getUserActions()->setEditableLayout($edit);
        $this->addFlashMessage("set editable layout $edit");
        if ($edit OR $this->isPresentedItemActive($request)) {
            return $this->redirectSeeLastGet($request); // 303 See Other
        } else {
            return $this->redirectSeeOther($request, ''); // 303 See Other -> home - jinak zůstane prezentovaný poslední articele, který nyl editován v režimu edit article
        }
    }

    public function setEditMenu(ServerRequestInterface $request) {
        $edit = (new RequestParams())->getParsedBodyParam($request, 'edit_menu');
//        $this->switchEditable('menu', $edit);
        $this->addFlashMessage("set editable menu $edit");
        $this->statusSecurityRepo->get()->getUserActions()->setEditableMenu($edit);
        if ($edit OR $this->isPresentedItemActive($request)) {
            return $this->redirectSeeLastGet($request); // 303 See Other
        } else {
            return $this->redirectSeeOther($request, ''); // 303 See Other -> home - jinak zůstane prezentovaný poslední articele, který nyl editován v režimu edit article
        }
    }

    private function isPresentedItemActive($request) {
        $requestedUid = (new RequestParams())->getParsedBodyParam($request, 'uid');
        $statusPresentation = $this->statusPresentationRepo->get();
        $langCodeFk = $statusPresentation->getLanguage()->getLangCode();
        $menuItem = $this->menuItemRepo->get($langCodeFk, $requestedUid);
        return $menuItem ? $menuItem->getActive() : false;
    }

    protected function switchEditable($name, $value) {
        $userAction = $this->statusSecurityRepo->get()->getUserActions();
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
