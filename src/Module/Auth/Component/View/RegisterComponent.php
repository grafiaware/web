<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Component\View;

use Component\View\ComponentCompositeAbstract;
use Component\Renderer\Html\NoContentForStatusRenderer;
use Component\ViewModel\StatusViewModelInterface;
use Pes\View\Template\PhpTemplate;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

/**
 * Description of LoginComponent
 *
 * @author pes2704
 */
class RegisterComponent extends ComponentCompositeAbstract {

    //renderuje template, definováno v component kontejneru a konfiguraci component kontejneru

//  template nastevena v kontejneru, pokud není user přihlášen (není role) nahrazuji renderer - není to ideální řešení, v kontejneru vytvářím celý objekt template, jen ho nepoužiju
//    public function beforeRenderingHook(): void {
//        if($this->isAllowedToPresent(AccessPresentationEnum::DISPLAY)) {
//            $this->setTemplate(new PhpTemplate($this->configuration->getTemplateRegister()));
//        } else {
//            $this->setRendererName(NoContentForStatusRenderer::class);
//        }
//    }

    public static function getComponentPermissions(): array {
        // komponent vidí jen nepřihlášení
        return [
            RoleEnum::ANONYMOUS => [AccessPresentationEnum::DISPLAY => true]
        ];
    }
}
