<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Middleware\Component\Controller;

use FrontController\PresentationFrontControllerAbstract;


/**
 * Description of GetController
 *
 * @author pes2704
 */
abstract class XhrControllerAbstract extends PresentationFrontControllerAbstract {

    protected function isEditableLayout() {
        $userActions = $this->statusSecurityRepo->get()->getUserActions();
        return $userActions ? $userActions->presentEditableLayout() : false;
    }

    protected function isEditableArticle() {
        $userActions = $this->statusSecurityRepo->get()->getUserActions();
        return $userActions ? $userActions->presentEditableArticle() : false;
    }

}
