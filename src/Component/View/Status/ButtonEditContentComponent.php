<?php
namespace Component\View\Status;

use Component\View\StatusComponentAbstract;
use Component\Renderer\Html\NoPermittedContentRenderer;
use Component\Renderer\Html\Status\ButtonEditContentRenderer;
//use Pes\View\Template\PhpTemplate;

use Component\View\RoleEnum;
use Component\View\AllowedActionEnum;

/**
 * Description of ToggleEditButton
 *
 * @author pes2704
 */
class ButtonEditContentComponent extends StatusComponentAbstract {

    /**
     * Pro oprávnění 'edit' renderuje ButtonEditContentRenderer jinak NonPermittedContentRenderer.
     *
     * @return void
     */
    public function beforeRenderingHook(): void {
        if($this->contextData->presentEditableArticle() AND $this->isAllowed($this, AllowedActionEnum::EDIT)) {
            $this->setRendererName(ButtonEditContentRenderer::class);
//            $this->setTemplate(new PhpTemplate($this->configuration->getTemplateXXXXXXX()));
        } else {
            $this->setRendererName(NoPermittedContentRenderer::class);
        }
    }
}
