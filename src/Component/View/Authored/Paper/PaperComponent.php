<?php
namespace Component\View\Authored\Paper;

use Pes\View\Template\PhpTemplate;
use Pes\View\Template\ImplodeTemplate;
use Pes\View\CompositeViewInterface;

use Pes\View\Template\Exception\NoTemplateFileException;

use Red\Model\Entity\PaperAggregatePaperContentInterface;

use Component\View\Authored\AuthoredComponentAbstract;
use Component\ViewModel\Authored\Paper\PaperViewModelInterface;

use Component\Renderer\Html\Authored\Paper\PaperRenderer;
use Component\Renderer\Html\Authored\Paper\PaperRendererEditable;
use Component\Renderer\Html\Authored\EmptyContentRenderer;

use Component\View\Authored\AuthoredElement;

use Component\Renderer\Html\Authored\Paper\SelectPaperTemplateRenderer;
use Component\Renderer\Html\Authored\Paper\ButtonsRenderer;

use Component\Renderer\Html\Authored\Paper\HeadlineRenderer;
use Component\Renderer\Html\Authored\Paper\PerexRenderer;
use Component\Renderer\Html\Authored\Paper\SectionsRenderer;
use Component\Renderer\Html\Authored\Paper\HeadlineRendererEditable;
use Component\Renderer\Html\Authored\Paper\PerexRendererEditable;
use Component\Renderer\Html\Authored\Paper\SectionsRendererEditable;

use Component\View\Manage\ToggleEditContentButtonComponent;

use Access\Enum\AllowedViewEnum;

/**
 * Description of PaperComponent
 *
 * @author pes2704
 */
class PaperComponent extends AuthoredComponentAbstract implements PaperComponentInterface {

    // hodnoty těchto konstant určují, jaká budou jména proměnných genrovaných template rendererem při renderování php template
    // - např, hodnota const QQQ='nazdar' způsobí, že obsah bude v proměnné $nazdar
    const CONTENT = 'content';
    const PEREX = 'perex';
    const HEADLINE = 'headline';
    const SECTIONS = 'sections';

    const SELECT_TEMPLATE = 'selectTemplate';

    /**
     *
     * @var PaperViewModelInterface
     */
    protected $contextData;

    /**
     * Přetěžuje metodu View. Generuje PHP template z názvu template objektu Paper a použije ji.
     * Pokud soubor template neexistuje, použije soubor default template, pokud ani ten neexistuje, použije PaperRenderer respektive PaperEditableRenderer.
     *
     *
     */
    public function beforeRenderingHook(): void {
        $test;
//        if($this->isAllowed(AllowedViewEnum::DISPLAY)) {
//            $contentView = $this->getComponentView(self::CONTENT);
//            $this->appendComponentView($contentView, self::CONTENT);
//// vytvoř headline, perex sections komponenty, append v kontejneru, tady také getComponentView(self::HEADLINE) atd. a jen jim nastavit renderer name
//        } else {
//            $this->setRendererName(EmptyContentRenderer::class);
//        }
    }

    public function __toString() {
        parent::__toString();
    }

    private function addChildComponents(CompositeViewInterface $view) {
        // renderery musí být definovány v Renderer kontejneru - tam mohou dostat classMap do konstruktoru
        $view->appendComponentView($this->createCompositeViewWithRenderer(HeadlineRenderer::class), self::HEADLINE);
        $view->appendComponentView($this->createCompositeViewWithRenderer(PerexRenderer::class), self::PEREX);
        $view->appendComponentView($this->createCompositeViewWithRenderer(SectionsRenderer::class), self::SECTIONS);
    }
}
