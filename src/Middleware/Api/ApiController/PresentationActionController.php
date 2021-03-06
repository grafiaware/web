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
    StatusSecurityRepo, StatusFlashRepo, StatusPresentationRepo, LanguageRepo, MenuItemRepo
};

use Middleware\Api\ApiController\Exception\UnexpectedLanguageException;

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

    public function setPresentedItem(ServerRequestInterface $request) {
        $requestedUid = (new RequestParams())->getParsedBodyParam($request, 'uid');
        $statusPresentation = $this->statusPresentationRepo->get();
        $langCodeFk = $statusPresentation->getLanguage()->getLangCode();
        $menuItem = $this->menuItemRepo->get($langCodeFk, $requestedUid);
        $statusPresentation->setHierarchyAggregate($menuItem);  // bez kontroly
        $this->addFlashMessage("setPresentedItem({$menuItem->getTitle()})");
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    public function setEditArticle(ServerRequestInterface $request) {
        $edit = (new RequestParams())->getParsedBodyParam($request, 'edit_article');
        $oldEditableLayoutStatus = $this->statusSecurityRepo->get()->getUserActions()->isEditableLayout();
        $this->statusSecurityRepo->get()->getUserActions()->setEditableArticle($edit);
        $this->statusSecurityRepo->get()->getUserActions()->setEditableLayout(false);
        $this->addFlashMessage("setEditArticle($edit)");
        if ($edit AND $oldEditableLayoutStatus) {
            return $this->redirectSeeOther($request, ''); // 303 See Other -> home - jinak zůstane prezentovaný poslední segment layoutu, který nyl editován v režimu edit layout
        } else {
            return $this->redirectSeeLastGet($request); // 303 See Other
        }
    }

    public function setEditLayout(ServerRequestInterface $request) {
        $edit = (new RequestParams())->getParsedBodyParam($request, 'edit_layout');
        $oldEditableArticleStatus = $this->statusSecurityRepo->get()->getUserActions()->isEditableArticle();
        $this->statusSecurityRepo->get()->getUserActions()->setEditableLayout($edit);
        $this->statusSecurityRepo->get()->getUserActions()->setEditableArticle(false);
        $this->addFlashMessage("setEditLayout($edit)");
        if ($edit AND $oldEditableArticleStatus) {
            return $this->redirectSeeOther($request, ''); // 303 See Other -> home - jinak zůstane prezentovaný poslední articele, který nyl editován v režimu edit article
        } else {
            return $this->redirectSeeLastGet($request); // 303 See Other
        }
    }
}
