<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FrontControler;

use Psr\Http\Message\ServerRequestInterface;

use FrontControler\PresentationFrontControlerAbstract;
use Pes\View\View;

/**
 * Description of RestrictedContentController
 *
 * @author pes2704
 */
class RestrictedContentController extends PresentationFrontControlerAbstract {

    ### action metody ###############

    public function none(ServerRequestInterface $request) {
        $view = $this->container->get(View::class);
        /* @var $view View */
        $view->setData("<div style='display:none'>not permitted</div>");
        return $this->createResponseFromView($request, $view);
    }

    public function notPermitted(ServerRequestInterface $request) {
        $view = $this->container->get(View::class);
        /* @var $view View */
        $view->setData("<div>not permitted</div>");
    }

}
