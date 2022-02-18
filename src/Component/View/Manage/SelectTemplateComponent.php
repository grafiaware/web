<?php
namespace Component\View\Manage;

use Component\View\StatusComponentAbstract;
use Component\Renderer\Html\NoPermittedContentRenderer;
use Component\Renderer\Html\Manage\SelectTemplateRenderer;
//use Pes\View\Template\PhpTemplate;

use Access\Enum\AllowedViewEnum;

/**
 * Description of ToggleEditButton
 *
 * @author pes2704
 */
class SelectTemplateComponent extends StatusComponentAbstract {

    /**
     * Pro oprávnění 'edit' renderuje ButtonEditContentRenderer jinak NonPermittedContentRenderer.
     *
     * @return void
     */
    public function beforeRenderingHook(): void {
        if($this->contextData->presentEditableContent() AND $this->isAllowed(AllowedViewEnum::EDIT)) {
            $this->setRendererName(SelectTemplateRenderer::class);
        } else {
            $this->setRendererName(NoPermittedContentRenderer::class);
        }
    }
}
