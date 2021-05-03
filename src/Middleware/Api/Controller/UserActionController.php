<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Middleware\Api\Controller;

use FrontController\PresentationFrontControllerAbstract;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Http\Response;
use Pes\Http\Response\RedirectResponse;
use Pes\Application\AppFactory;


/**
 * Description of PostController
 *
 * @author pes2704
 */
class UserActionController extends PresentationFrontControllerAbstract {

    public function app(ServerRequestInterface $request, $app) {
        return RedirectResponse::withRedirect(
            new Response(),
            $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().$app.''); // 302
    }


}
