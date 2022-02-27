<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Manage;

use Component\View\StatusComponentAbstract;
use Component\ViewModel\StatusViewModelInterface;
use Pes\View\Template\PhpTemplate;

/**
 * Description of LogoutComponent
 *
 * @author pes2704
 */
class LoginLogoutComponent extends StatusComponentAbstract {

    /**
     * @var StatusViewModelInterface
     */
    protected $contextData;

    //renderuje template login nebo logout

    public function beforeRenderingHook(): void {

        if ($this->contextData->isUserLoggedIn()) {
            $this->setTemplate(new PhpTemplate($this->configuration->getTemplateLogout()));
        } else {
            $this->setTemplate(new PhpTemplate($this->configuration->getTemplateLogin()));
        }
    }

}
