<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Middleware\Api\ApiController;

use Controller\PresentationFrontControllerAbstract;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Http\Request\RequestParams;
use Pes\Http\Response;
use Pes\Http\Response\RedirectResponse;

use Pes\Router\Router;
use Pes\Router\RouteInterface;

/**
 * Description of PostController
 *
 * @author pes2704
 */
class UserActionController extends PresentationFrontControllerAbstract {

    public function app(ServerRequestInterface $request, $app) {
        return RedirectResponse::withRedirect(
            new Response(),
            $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().$app.'/'); // 302
    }

    public function setEditArticle(ServerRequestInterface $request) {
            $edit = (new RequestParams())->getParsedBodyParam($request, 'edit_article');
            $this->statusPresentation->get()->getUserActions()->setEditableArticle($edit);
        return RedirectResponse::withPostRedirectGet(new Response(), $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().'www/last/'); // 303 See Other
    }

    public function setEditLayout(ServerRequestInterface $request) {
            $edit = (new RequestParams())->getParsedBodyParam($request, 'edit_layout');
            $this->statusPresentation->get()->getUserActions()->setEditableLayout($edit);
        return RedirectResponse::withPostRedirectGet(new Response(), $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().'www/last/'); // 303 See Other
    }
}
