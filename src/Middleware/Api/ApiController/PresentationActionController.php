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
    StatusSecurityRepo, StatusPresentationRepo, LanguageRepo, MenuItemRepo
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

    public function __construct(StatusSecurityRepo $statusSecurityRepo, StatusPresentationRepo $statusPresentationRepo, LanguageRepo $languageRepo, MenuItemRepo $menuItemRepo) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo);
        $this->languageRepo = $languageRepo;
        $this->menuItemRepo = $menuItemRepo;
    }

    public function setLangCode(ServerRequestInterface $request) {
        $requestedLangCode = (new RequestParams())->getParsedBodyParam($request, 'langcode');
        $language = $this->languageRepo->get($requestedLangCode);
        if (isset($language)) {
            $this->statusPresentation->setLanguage($language);
        } else{
            throw new UnexpectedLanguageException("Požadavek a nastavení neznámého jazyka aplikace s kódem $requestedLangCode.");
        }
        return $this->response($request);
    }

    public function setPresentedItem(ServerRequestInterface $request) {
        $requestedUid = (new RequestParams())->getParsedBodyParam($request, 'uid');
        $langCodeFk = $this->statusPresentation->getLanguage()->getLangCode();
        $menuItem = $this->menuItemRepo->get($langCodeFk, $requestedUid);
        $this->statusPresentation->setMenuItem($menuItem);  // bez kontroly
        return $this->response($request);
    }

    public function setEditArticle(ServerRequestInterface $request) {
        $edit = (new RequestParams())->getParsedBodyParam($request, 'edit_article');
        $this->statusPresentation->getUserActions()->setEditableArticle($edit);
        return $this->response($request);
    }

    public function setEditLayout(ServerRequestInterface $request) {
        $edit = (new RequestParams())->getParsedBodyParam($request, 'edit_layout');
        $this->statusPresentation->getUserActions()->setEditableLayout($edit);
        return $this->response($request);
    }

    private function response($request) {
        $uidFk = $this->statusPresentation->getMenuItem()->getUidFk();
        $langCodeFk = $this->statusPresentation->getLanguage()->getLangCode();
        return RedirectResponse::withPostRedirectGet(new Response(), $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath()."www/item/$langCodeFk/$uidFk/"); // 303 See Other

    }
}
