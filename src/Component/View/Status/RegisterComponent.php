<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Status;

use Component\View\ComponentAbstract;

use Component\Renderer\Html\NonPermittedContentRenderer;

/**
 * Description of LoginComponent
 *
 * @author pes2704
 */
class RegisterComponent extends ComponentAbstract {
    //nepoužívá viewModel, renderuje template, definováno v component kontejneru a konfiguraci component kontejneru

//    toto do view modelu (status):
            $credentials = $this->statusSecurityRepo->get()->getLoginAggregate();
        if (!isset($credentials)) {
            NonPermittedContentRenderer
        } else {
            
        }
}
