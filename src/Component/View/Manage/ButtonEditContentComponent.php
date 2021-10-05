<?php
namespace Component\View\Manage;

use Component\View\StatusComponentAbstract;
use Component\Renderer\Html\NoPermittedContentRenderer;
use Component\Renderer\Html\Manage\ButtonEditContentRenderer;
//use Pes\View\Template\PhpTemplate;

use Component\View\RoleEnum;
use Component\View\AllowedActionEnum;

/**
 * Description of ToggleEditButton
 *
 * @author pes2704
 */
class ButtonEditContentComponent extends StatusComponentAbstract {

    const CONTEXT_TYPE_FK = 'typeFk';
    const CONTEXT_ITEM_ID = 'itemId';
    const CONTEXT_USER_PERFORM_ACTION = 'userPerformActionWithContent';
    /**
     * Pro oprávnění 'edit' renderuje ButtonEditContentRenderer jinak NonPermittedContentRenderer.
     *
     * @return void
     */
    public function beforeRenderingHook(): void {
        if($this->contextData->presentEditableContent() AND $this->isAllowed($this, AllowedActionEnum::EDIT)) {
            $this->setRendererName(ButtonEditContentRenderer::class);
//            $this->setTemplate(new PhpTemplate($this->configuration->getTemplateXXXXXXX()));
        } else {
            $this->setRendererName(NoPermittedContentRenderer::class);
        }
    }
}
