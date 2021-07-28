<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Middleware\Redactor\Controler;

use FrontControler\PresentationFrontControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Http\Response;
use Pes\Http\Response\RedirectResponse;
use Pes\Application\AppFactory;


/**
 * Description of PostControler
 *
 * @author pes2704
 */
class UserActionControler extends PresentationFrontControlerAbstract {

    public function app(ServerRequestInterface $request, $app) {
        return RedirectResponse::withRedirect(
            new Response(),
            $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().$app.''); // 302
    }


}
