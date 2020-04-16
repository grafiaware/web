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
    StatusSecurityRepo, StatusPresentationRepo, LanguageRepo
};

use Middleware\Api\ApiController\Exception\UnexpectedLanguageException;

/**
 * Description of PostController
 *
 * @author pes2704
 */
class PresentationController extends PresentationFrontControllerAbstract {

    private $languageRepo;

    public function __construct(StatusSecurityRepo $statusSecurityRepo, StatusPresentationRepo $statusPresentationRepo, LanguageRepo $languageRepo) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo);
        $this->languageRepo = $languageRepo;
    }

    public function setLangCode(ServerRequestInterface $request) {
            $langCode = (new RequestParams())->getParsedBodyParam($request, 'langcode');
            $language = $this->languageRepo->get($langCode);
            if (isset($language)) {
                $this->statusPresentation->get()->setLanguage($language);
            } else{
                throw new UnexpectedLanguageException("Požadavek a nastevení neznámého jazyka aplikace s k=dem $langCode.");
            }
        return RedirectResponse::withPostRedirectGet(new Response(), $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().'www/last/'); // 303 See Other
    }

    public function setUid(ServerRequestInterface $request) {
            $uid = (new RequestParams())->getParsedBodyParam($request, 'uid');
            $this->statusPresentation->get()->setItemUid($itemUid);  // bez kontroly
        return RedirectResponse::withPostRedirectGet(new Response(), $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().'www/last/'); // 303 See Other
    }
}
