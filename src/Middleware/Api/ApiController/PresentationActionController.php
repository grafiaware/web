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
use Model\Entity\StatusFlash;

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
        $this->addFlashMessage("setLanguage({$language->getLangCode()})");
        return $this->response($request);
    }

    public function setPresentedItem(ServerRequestInterface $request) {
        $requestedUid = (new RequestParams())->getParsedBodyParam($request, 'uid');
        $statusPresentation = $this->statusPresentationRepo->get();
        $langCodeFk = $statusPresentation->getLanguage()->getLangCode();
        $menuItem = $this->menuItemRepo->get($langCodeFk, $requestedUid);
        $statusPresentation->setMenuItem($menuItem);  // bez kontroly
        $this->addFlashMessage("setMenuItem({$menuItem->getTitle()})");
        return $this->response($request);
    }

    public function setEditArticle(ServerRequestInterface $request) {
        $edit = (new RequestParams())->getParsedBodyParam($request, 'edit_article');
        $this->statusSecurityRepo->get()->getUserActions()->setEditableArticle($edit);
        $this->addFlashMessage("setEditableArticle($edit)");
        return $this->response($request);
    }

    public function setEditLayout(ServerRequestInterface $request) {
        $edit = (new RequestParams())->getParsedBodyParam($request, 'edit_layout');
        $this->statusSecurityRepo->get()->getUserActions()->setEditableLayout($edit);
        $this->addFlashMessage("setEditableLayout($edit)");
        return $this->response($request);
    }

    private function response($request) {
        $uidFk = $this->statusPresentationRepo->get()->getMenuItem()->getUidFk();
        $langCodeFk = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        return RedirectResponse::withPostRedirectGet(new Response(), $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath()."www/item/$langCodeFk/$uidFk/"); // 303 See Other
    }

    private function addFlashMessage($message) {
        $statusFlash = $this->statusFlashRepo->get();
        if ($statusFlash) {
            $message = $statusFlash->getFlash().PHP_EOL.$message;
        } else {
            $statusFlash = new StatusFlash();
        }
        $statusFlash->setFlash($message);
    }
}
