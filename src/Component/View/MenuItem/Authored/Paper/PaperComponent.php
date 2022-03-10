<?php
namespace Component\View\MenuItem\Authored\Paper;

use Component\View\MenuItem\Authored\AuthoredComponentAbstract;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

use Component\Renderer\Html\Authored\Paper\PaperRenderer;
use Component\Renderer\Html\Authored\Paper\HeadlineRenderer;
use Component\Renderer\Html\Authored\Paper\PerexRenderer;
use Component\Renderer\Html\Authored\Paper\SectionsRenderer;

use Component\Renderer\Html\Authored\Paper\PaperRendererEditable;
use Component\Renderer\Html\Authored\Paper\HeadlineRendererEditable;
use Component\Renderer\Html\Authored\Paper\PerexRendererEditable;
use Component\Renderer\Html\Authored\Paper\SectionsRendererEditable;

use Component\Renderer\Html\Manage\EditContentSwitchRenderer;

use Component\Renderer\Html\NoPermittedContentRenderer;

/**
 * Description of PaperComponent
 *
 * @author pes2704
 */
class PaperComponent extends AuthoredComponentAbstract implements PaperComponentInterface {

    // hodnoty těchto konstant určují, jaká budou jména proměnných použitých v kontejneru, zde a v rendereru nebo šabloně
    const CONTENT = 'content';
    const PEREX = 'perex';
    const HEADLINE = 'headline';
    const SECTIONS = 'sections';

    public function beforeRenderingHook(): void {
        if($this->getStatus()->presentEditableContent()) {
            if($this->isAllowedToPresent(AccessPresentationEnum::EDIT)) {
                if($this->contextData->getAuthoredContentAction()) {
                    $this->setEditableRenderers();
                    $this->getComponentView(self::BUTTON_EDIT_CONTENT)->setRendererName(EditContentSwitchRenderer::class);
                } else {
                    $this->setNoneditableRenderers();
                    $this->getComponentView(self::BUTTON_EDIT_CONTENT)->setRendererName(EditContentSwitchRenderer::class);
                }
            } else {
                if($this->isAllowedToPresent(AccessPresentationEnum::DISPLAY)) {
                    $this->setNoneditableRenderers();
                    $this->getComponentView(self::BUTTON_EDIT_CONTENT)->setRendererName(EditContentSwitchRenderer::class);
                } else {
                    $this->setNoneditableRenderers(); // musí být nastaveny, jinak při renderování vznikne výjimka a nedojde k renderování NoPermittedContentRenderer
                    $this->setRendererName(NoPermittedContentRenderer::class);
                }
            }
        } else {
            if($this->isAllowedToPresent(AccessPresentationEnum::DISPLAY)) {
                $this->setNoneditableRenderers();
            } else {
                $this->setNoneditableRenderers(); // musí být nastaveny, jinak při renderování vznikne výjimka a nedojde k renderování NoPermittedContentRenderer
                $this->setRendererName(NoPermittedContentRenderer::class);
            }
        }
    }

    protected function setNoneditableRenderers() {
        $this->setRendererName(PaperRenderer::class);
        $this->getComponentView(self::CONTENT)->getComponentView(self::HEADLINE)->setRendererName(HeadlineRenderer::class);
        $this->getComponentView(self::CONTENT)->getComponentView(self::PEREX)->setRendererName(PerexRenderer::class);
        $this->getComponentView(self::CONTENT)->getComponentView(self::SECTIONS)->setRendererName(SectionsRenderer::class);
    }

    protected function setEditableRenderers() {
        $this->setRendererName(PaperRendererEditable::class);
        $this->getComponentView(self::CONTENT)->getComponentView(self::HEADLINE)->setRendererName(HeadlineRendererEditable::class);
        $this->getComponentView(self::CONTENT)->getComponentView(self::PEREX)->setRendererName(PerexRendererEditable::class);
        $this->getComponentView(self::CONTENT)->getComponentView(self::SECTIONS)->setRendererName(SectionsRendererEditable::class);
    }

}
