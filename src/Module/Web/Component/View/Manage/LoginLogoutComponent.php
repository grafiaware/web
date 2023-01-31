<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Web\Component\View\Manage;

use Web\Component\View\ComponentCompositeAbstract;
use Web\Component\Renderer\Html\NoContentForStatusRenderer;
use Web\Component\ViewModel\StatusViewModelInterface;
use Pes\View\Template\PhpTemplate;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

/**
 * Description of LogoutComponent
 *
 * @author pes2704
 */
class LoginLogoutComponent extends ComponentCompositeAbstract {

    /**
     * @var StatusViewModelInterface
     */
    protected $contextData;

    //renderuje template login nebo logout

//    public function beforeRenderingHook(): void {
//        if($this->isAllowedToPresent(AccessPresentationEnum::DISPLAY)) {
//            if ($this->contextData->isUserLoggedIn()) {
//                $this->setTemplate(new PhpTemplate($this->configuration->getTemplateLogout()));
//            } else {
//                $this->setTemplate(new PhpTemplate($this->configuration->getTemplateLogin()));
//            }
//        } else {
//            $this->setRendererName(NoContentForStatusRenderer::class);
//        }
//    }
}
