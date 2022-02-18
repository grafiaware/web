<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Manage;

use Component\View\StatusComponentAbstract;
use Component\Renderer\Html\NoContentForStatusRenderer;
use Component\ViewModel\StatusViewModelInterface;
use Pes\View\Template\PhpTemplate;

use Access\Enum\RoleEnum;
use Access\Enum\AllowedViewEnum;

/**
 * Description of LoginComponent
 *
 * @author pes2704
 */
class RegisterComponent extends StatusComponentAbstract {

    /**
     * @var StatusViewModelInterface
     */
    protected $contextData;

    //renderuje template, definováno v component kontejneru a konfiguraci component kontejneru

//  template nastevena v kontejneru, pokud není user přihlášen (není role) nahrazuji renderer - není to ideální řešení, v kontejneru vytvářím celý objekt template, jen ho nepoužiju
    public function beforeRenderingHook(): void {
        if($this->isAllowed(AllowedViewEnum::DISPLAY)) {
            $this->setTemplate(new PhpTemplate($this->configuration->getTemplateRegister()));
        } else {
            $this->setRendererName(NoContentForStatusRenderer::class);
        }
    }

    public function getComponentPermissions(): array {
        return [
            RoleEnum::ANONYMOUS => [AllowedViewEnum::DISPLAY => static::class]
        ];
    }
}
