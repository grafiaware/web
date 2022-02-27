<?php
namespace Component\View\Authored\PaperTemplate;

use Pes\View\Template\PhpTemplate;
use Pes\View\CompositeViewInterface;

use Component\View\Authored\AuthoredComponentAbstract;
use Component\ViewModel\Authored\Paper\PaperViewModelInterface;
use Component\Renderer\Html\Authored\Paper\PaperRenderer;
use Component\Renderer\Html\Authored\EmptyContentRenderer;

use Component\Renderer\Html\Authored\Paper\HeadlineRenderer;
use Component\Renderer\Html\Authored\Paper\PerexRenderer;
use Component\Renderer\Html\Authored\Paper\SectionsRenderer;
// zde jsou definovány konstanty se jmény proměnných pro template (headline, presex, sections) - společné pro PaperComponent
use Component\View\Authored\Paper\PaperComponent;

/**
 * Description of PaperComponent
 *
 * @author pes2704
 */
class PaperTemplateComponent extends AuthoredComponentAbstract implements PaperTemplateComponentInterface {

    /**
     *
     * @var PaperViewModelInterface
     */
    protected $contextData;

    private $selectedPaperTemplateFileName;

    /**
     * Přetěžuje metodu View. Generuje PHP template z názvu template objektu Paper a použije ji.
     */
    public function beforeRenderingHook(): void {
        $paperAggregate = $this->contextData->getPaper();
        if (isset($paperAggregate)) {
            if ($this->selectedPaperTemplateFileName) {
                try {
                    // konstruktor PhpTemplate vyhazuje výjimku NoTemplateFileException pro neexistující (nečitený) soubor s template
                    $template = new PhpTemplate($this->selectedPaperTemplateFileName);
                    $contentView = $this->createCompositeViewWithTemplate($template);

                    $this->setRendererName(PaperRenderer::class);
                    $this->addChildComponents($contentView);
                    $this->appendComponentView($contentView, PaperComponent::CONTENT);

                } catch (NoTemplateFileException $noTemplExc) {
                    throw new LogicException("Nelze vytvořit objekt template pro jméno nastavené metodou setPaperTemplateName()? {$this->selectedPaperTemplateFileName}");
                }
            } else {
                throw new LogicException("Nebyla nastavena template metodou setPaperTemplateName()");
            }
        } else {
            $this->setRendererName(EmptyContentRenderer::class);
        }
    }

    private function addChildComponents(CompositeViewInterface $view) {
        // renderery musí být definovány v Renderer kontejneru - tam mohou dostat classMap do konstruktoru
        $view->appendComponentView($this->createCompositeViewWithRenderer(HeadlineRenderer::class), PaperComponent::HEADLINE);
        $view->appendComponentView($this->createCompositeViewWithRenderer(PerexRenderer::class), PaperComponent::PEREX);
        $view->appendComponentView($this->createCompositeViewWithRenderer(SectionsRenderer::class), PaperComponent::SECTIONS);
    }

    public function setSelectedPaperTemplateFileName($name): void {
        $this->selectedPaperTemplateFileName = $name;
    }

}
