<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Status;

use Component\View\CompositeComponentAbstract;
use Component\Renderer\Html\NoPermittedContentRenderer;
use Component\ViewModel\StatusViewModelInterface;
use Pes\View\Template\PhpTemplate;

/**
 * Description of ControlEditMenu
 *
 * @author pes2704
 */
class ControlEditMenuComponent  extends CompositeComponentAbstract {

    /**
     * @var StatusViewModelInterface
     */
    protected $contextData;

    //renderuje template nebo NonPermittedContentRenderer

    public function beforeRenderingHook(): void {
        $role = $this->contextData->getUserRole();
        $permission = [
            'sup' => true,
            'editor' => true
        ];
        if (isset($role) AND array_key_exists($role, $permission) AND $permission[$role]) {
            $this->setTemplate(new PhpTemplate($this->configuration->getTemplateControlEditMenu()));
        } else {
            $this->setRendererName(NoPermittedContentRenderer::class);
        }
    }
}
