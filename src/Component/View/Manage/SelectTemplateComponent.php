<?php
namespace Component\View\Manage;

use Component\View\StatusComponentAbstract;
use Component\Renderer\Html\NoPermittedContentRenderer;
use Component\Renderer\Html\Manage\SelectTemplateRenderer;

use Access\Enum\AccessPresentationEnum;

use Pes\View\InheritDataViewInterface;
use Pes\View\ViewInterface;

/**
 * Description of ToggleEditButton
 *
 * @author pes2704
 */
class SelectTemplateComponent extends StatusComponentAbstract implements InheritDataViewInterface {

    /**
     *
     * @param iterable $data
     * @return ViewInterface
     */
    public function inheritData(iterable $data): ViewInterface {
        return $this->setData($data);
    }

    /**
     * Pro oprávnění 'edit' renderuje ButtonEditContentRenderer jinak NonPermittedContentRenderer.
     *
     * @return void
     */
    public function beforeRenderingHook(): void {
        if($this->isAllowedToPresent(AccessPresentationEnum::EDIT)) {
            $this->setRendererName(SelectTemplateRenderer::class);
        } else {
            $this->setRendererName(NoPermittedContentRenderer::class);
        }
    }
}
