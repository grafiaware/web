<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Status\Middleware\Component\Controler;

use Site\ConfigurationCache;

use FrontControler\FrontControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;

// komponenty
use Web\Component\View\Flash\FlashComponent;

/**
 * Description of ComponentController
 *
 * @author pes2704
 */
class StatusComponentControler extends FrontControlerAbstract {

    ### action metody ###############

    public function flash(ServerRequestInterface $request) {
        $view = $this->container->get(FlashComponent::class);
        return $this->createResponseFromView($view);
    }

}
